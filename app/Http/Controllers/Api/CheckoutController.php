<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Shipping;
use App\Constants\Status;
use Illuminate\Support\Arr;
use App\Models\AdminSetting;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Payments\PaymentManager;

class CheckoutController extends Controller
{
    protected $orders;
    protected $payments;

    public function __construct(OrderService $orders, PaymentService $payments)
    {
        $this->orders = $orders;
        $this->payments = $payments;
    }
    
    public function initialize(Request $request)
    {
        $user = $request->user();
        $cartSummary = $this->getCartSummary($user);

        // if error, return immediately
        if ($cartSummary['error'] === true) {
            return response()->json($cartSummary, 422);
        }

        // get gateways (hide secret_key)
        $gateways = AdminSetting::get("gateways");
        $gateways = collect($gateways)->map(function ($gateway) {
            return Arr::except($gateway, ['secret_key','encryption_key']);
        })->toArray();


        return response()->json([
            "message" => "Checkout",
            "data" => [
                "gateways"              => $gateways,
                "user_cart"             => $cartSummary
            ]
        ]);
    }

    protected function getShippingAddress($user)
    {
        $ushipAdd = $user->shippingAddress()->orderByDesc("is_default")->first(['id','full_name','province','phone','address_line_one','city','state','postal_code','country']);
        return $ushipAdd;
    }

    protected function getShippingCost($country, $state, $province)
    {
        $shipping = Shipping::where('country', $country)
                ->where('state', $state)
                ->where('province', $province)
                ->first(['cost','min_days','max_days']);
        return $shipping ?? "unserviceable";
    }

    protected function shippingCostLogic($user)
    {
        $sA = $this->getShippingAddress($user);
        return [
            'userAddress' => $sA,
            'shipCost' => $this->getShippingCost($sA->country, $sA->state, $sA->province)
        ];
    }

    protected function getCartSummary($user)
    {
        $cart = Cart::with('cartItems')->where('user_id', $user->id)->first();

        if (!$cart || $cart->cartItems->isEmpty()) {
            return [
                'error'   => true,
                'message' => 'Cart is empty, add some items',
            ];
        }

        // Check stock availability
        $unavailable = [];
        foreach ($cart->cartItems as $item) {
            $product = $item->product;
            if (!$product || $product->stock_quantity < $item->quantity) {
                $unavailable[] = [
                    'product_id' => $product->id ?? null,
                    'name'       => $product->name ?? 'Unknown Product',
                    'requested'  => $item->quantity,
                    'available'  => $product->stock_quantity ?? 0,
                ];
            }
        }

        if (!empty($unavailable)) {
            return [
                'error'   => true,
                'message' => 'unavailable',
                'errors'  => $unavailable,
            ];
        }

        // Build cart summary with discount consideration
        $cartItems = $cart->cartItems()->with(['product'])->get()
        // $cartItems = $cart->cartItems()->with(['product.category'])->get()
            ->map(function ($item) use ($user) {
                $product  = $item->product;

                $originalPrice   = $product->price;              // base/distributor depending on user
                $discountedPrice = $product->discounted_price;   // dynamic calculation

                return [
                    'id'              => $item->id,
                    'product_id'      => $product->id,
                    'product_name'    => $product->name,
                    'product_slug'    => $product->slug,
                    'quantity'        => $item->quantity,
                    'price'           => $originalPrice,       // keep original
                    'discounted_price'=> $discountedPrice,     // if no discount, same as price
                    'subtotal'        => (float) ($discountedPrice * $item->quantity),
                    'originalSubtotal'=> (float) ($originalPrice * $item->quantity),
                    'stock_quantity'  => $product->stock_quantity,
                    // 'category_name'   => $product->category?->name,
                    // 'category_slug'   => $product->category?->slug,
                ];
            });

        $ship = $this->shippingCostLogic($user);
        $shipCost = $ship['shipCost'];

        return [
            'error'             => false,
            'id'                => $user->id,
            'first_name'        => $user->first_name,
            'last_name'         => $user->last_name,
            'email'             => $user->email,
            'amount'            => $cartItems->sum('subtotal'), // sum of discounted subtotals
            'originalAmount'    => $cartItems->sum('originalSubtotal'), // sum of original subtotals
            'cart_items'        => $cartItems,
            'shippingAddress'   => $ship['userAddress'],
            'shippingCost'      => $shipCost
        ];
    }

    public function checkout(Request $request, $gateway, $transRef, PaymentManager $manager)
    {
        $user = $request->user();

        $gateCheck = $manager->gateway($gateway);
        if ($gateCheck['error']) {
            return response()->json($gateCheck, 422);
        }

        // 1. Validate cart & stock
        $cartSummary = $this->getCartSummary($user);
        if ($cartSummary['error']) {
            return response()->json($cartSummary, 422);
        }

        $orderCreate = $this->orders->createOrder($user, $transRef, $cartSummary['amount'], $cartSummary['shippingCost'], $gateway); 
        if ($orderCreate['error']) {
            // DB::rollBack();
            return response()->json($orderCreate, 422);
        }
        $order = $orderCreate['order'];
        
        try {
            DB::beginTransaction();

            // 3. Verify payment with gateway
            $paymentResult = $this->payments->verifyAndSave($gateCheck['gate'], $transRef, $order->id, $order->user_id, ($cartSummary['amount'] + $cartSummary['shippingCost']->cost));
            if ($paymentResult['error']) {
                DB::rollBack();
                return response()->json($paymentResult, 422);
            }

            $payment = $order->payment; // since created in createOrder()
            if (in_array($paymentResult['status'], ['failed', 'cancelled'])) {
                $order->update(['status' => 'cancelled']);
                $payment->update(['status' => 'failed']); // keep sync
                DB::rollBack();
                return response()->json([
                    'error'   => true,
                    'message' => 'Order cancelled. Payment failed.'
                ], 422);
            }

            // 4. Deduct stock
            foreach ($order->orderItem as $item) {
                $product = $item->product;
                if ($product->stock_quantity < $item->quantity) {
                    DB::rollBack();
                    return response()->json([
                        'error'   => true,
                        'message' => "Insufficient stock for {$product->name}"
                    ], 422);
                }
                $product->decrement('stock_quantity', $item->quantity);
            }

            // 5. Confirm order + clear cart
            $user->cart->cartItems()->delete();

            $this->orders->updateStatus($order, Status::O_CONFIRM, true);
            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully',
                'order'   => $order->load('payment:id,order_id,status,transaction_reference,payment_gateway,paid_at'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Checkout error: ".$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'error'   => true,
                'message' => 'Checkout failed',
            ], 500);
        }
    }

    public function retryCheckout(Request $request, $transRef, PaymentManager $manager)
    {

    }
}