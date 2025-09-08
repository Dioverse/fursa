<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
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

        $shippingAddys = $user->shippingAddress()->orderByDesc("is_default")->first(['id','full_name','phone','address_line_one','city','state','postal_code','country']);

        return response()->json([
            "message" => "Checkout",
            "data" => [
                "gateways"              => $gateways,
                "user_cart"             => $cartSummary,
                "shipping_addresses"    => $shippingAddys
            ]
        ]);
    }

    public function getCartSummary($user)
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
        $cartItems = $cart->cartItems()->with(['product.category'])->get()
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
                    'category_name'   => $product->category?->name,
                    'category_slug'   => $product->category?->slug,
                ];
            });

        return [
            'error'             => false,
            'id'                => $user->id,
            'first_name'        => $user->first_name,
            'last_name'         => $user->last_name,
            'email'             => $user->email,
            'amount'            => $cartItems->sum('subtotal'), // sum of discounted subtotals
            'originalAmount'    => $cartItems->sum('originalSubtotal'), // sum of original subtotals
            'cart_items'        => $cartItems,
        ];
    }

    public function checkout(Request $request, $gateway, $transId, PaymentManager $manager)
    {
        $user = $request->user();

        $gateCheck = $manager->gateway($gateway);
        if ($gateCheck['error']) {
            return response()->json($gateCheck);
        }

        try {
            DB::beginTransaction();

            $cartSummary = $this->getCartSummary($user);
            if ($cartSummary['error']) {
                return response()->json($cartSummary, 422);
            }

            $orderCreate = $this->orders->createOrder($user, $cartSummary['amount']);
            if ($orderCreate['error']) {
                DB::rollBack();
                return response()->json($orderCreate, 422);
            }

            $order = $orderCreate['order'];

            $paymentResult = $this->payments->verifyAndSave($gateCheck['gate'], $transId, $order, $cartSummary['amount']);

            if ($paymentResult['error']) {
                DB::rollBack();
                return response()->json($paymentResult, 422);
            }

            if ($paymentResult['status'] === 'failed') {
                $this->orders->markFailed($order, $user);
                DB::rollBack();
                return response()->json([
                    'error'   => true,
                    'message' => 'Payment failed. Order cancelled.'
                ], 422);
            }

            DB::commit();

            return response()->json([
                'error'   => false,
                'message' => 'Order placed successfully',
                'order'   => $order->load(['payment']),
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

    // public function checkout(Request $request, $gateway, $transId, PaymentManager $manager)
    // {
    //     $user = $request->user();

    //     // verify payment with gateway
    //     $gateCheck = $manager->gateway($gateway);
    //     if ($gateCheck['error']) {
    //         return response()->json($gateCheck);
    //     }

        
    //     try {
    //         DB::beginTransaction();

    //         // cart validation
    //         $cartSummary = $this->getCartSummary($user);
    //         if ($cartSummary['error'] === true) {
    //             return response()->json($cartSummary, 422);
    //         }
    //         $cartTotal = $cartSummary['amount'];
    
    //         $orderCreate = $this->createOrder($user, $cartTotal);
    //         if ($orderCreate['error'] === true) {
    //             DB::rollBack();
    //             return response()->json($orderCreate, 422);
    //         }
    
    //         // $existingPayment = Payment::where('user_id', $user->id)
    //         //         ->where('transaction_reference', $transId)
    //         //         ->first();
    
    //         // if ($existingPayment) {
    //         //     if ($existingPayment->status === 'successful') {
    //         //         $err = ['error'=>true,'message'=>'Payment is already successful','payment'=>$existingPayment->transaction_reference];
    //         //     } elseif ($existingPayment->status === 'refunded' || $existingPayment->status === 'reversed') {
    //         //         $err = ['error'=>true,'message'=>'Payment was refunded/reversed','payment'=>$existingPayment->transaction_reference];
    //         //     }
    //         //     return response()->json($err, 422);
    //         // }
    
            
    
    //         $payment = $gateCheck['gate']->verifyPayment($transId);
    //         if (!$payment['success']) {
    //             return response()->json([
    //                 'error'   => true,
    //                 'message' => 'Payment verification failed',
    //                 $payment
    //             ], 422);
    //         }

    //         $order = $orderCreate['order'];
    //         if ($payment['status']=='failed') {
    //             $order->update(['status' => 'cancelled']);
    //             // restore cart
    //             $user->cart()->restoreItems($order->id); // You’ll need a restore method
    //             return response()->json([
    //                 'error'   => true,
    //                 'message' => 'Payment failed, order failed'
    //             ], 422);
    //         }

    //         $processes = $this->runProcesses($payment, $order, $cartTotal);
    //         if ($processes['error'] === true) {
    //             DB::rollBack();
    //             //Then update 
    //             return response()->json($processes, 422);
    //         }


    //         // if ((float) $payment['amount'] !== (float) $cartSummary['amount']) {
    //         //     return response()->json(['refund']);
    //         // }

    //         DB::commit();

    //         return response()->json([
    //             'error'   => false,
    //             'message' => 'Order placed successfully',
    //             'order'   => $order->load(['payment']),
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return response()->json([
    //             'error'   => true,
    //             'message' => 'Checkout failed',
    //         ], 500);
    //     }
    // }

    // public function createOrder($user, $total)
    // {
    //     try {
    //         $shipping_address = $user->shippingAddress()->orderByDesc("is_default")->first(['id','full_name','phone','address_line_one','city','state','postal_code','country']);
    //         if (!$shipping_address) {
    //             return ["error"=>true,"message"=>"No shipping address attached to order."];
    //         }
    //         // Generate a unique order ID
    //         $lastOrder = Order::latest('id')->first();
    //         $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
    //         $orderId = 'ORD-' . $nextId . strtoupper(Str::random(8));
            
    //         // Create the order
    //         $order = Order::create([
    //             'user_id'             => $user->id,
    //             'shipping_address'    => $shipping_address,
    //             'order_id'            => $orderId,
    //             'total_amount'        => $total,
    //             'status'              => 'pending',
    //         ]);

    //         // clear cart
    //         $user->cart->cartItems()->delete();

    //         return [
    //             'error' => false,
    //             'order' => $order,
    //         ];
    //     } catch (\Exception $e) {
    //         return [
    //             'error'   => true,
    //             'message' => 'Order processing failed',
    //         ];
    //     }
    // }

    // public function runProcesses($payment, $order, $cartTotal) {
    //     try {
    //         $amount = $payment['amount']/100;
    //         if (bccomp((string)$amount, (string)$cartTotal, 2) !== 0) {
    //             return ['error' => true, 'message' => 'Invalid transaction amount'];
    //         }

    //         Payment::updateOrCreate(
    // ['transaction_reference' => $payment['reference']],
    //     [
    //                 'user_id'   => $order->user_id,
    //                 'order_id'  => $order->id,
    //                 'status'    => $payment['status'],
    //                 'amount'    => $payment['amount'],
    //                 'currency'  => $payment['currency'],
    //                 'method'    => $payment['method'],
    //                 'gateway'   => $payment['gateway'],
    //                 'raw'       => json_encode($payment['raw']),
    //             ]
    //         );

    //         return ["error"=>false];

    //     } catch (\Exception $e) {
    //         return [
    //             'error'   => true,
    //             'message' => 'Payment processing failed',
    //         ];
    //     }
    // }
}