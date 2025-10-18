<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Order;
use App\Models\CartItem;
use App\Models\Shipping;
use App\Constants\Status;
use Illuminate\Support\Arr;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
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
        $gateways = GeneralSetting::get("gateways");
        $gateways = collect($gateways)->map(function ($gateway) {
            return Arr::only($gateway, ['status','currency','image']);
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
        
        return empty($sA) ? [] : [
            'userAddress' => $sA,
            'shipCost' => $this->getShippingCost($sA->country, $sA->state, $sA->province)
        ];
    }

    protected function getCartSummary($user, $make=false)
    {
        $taxVal = (float)(gs("tax"));
        $taxVal = (float)($taxVal > 0 ? $taxVal/ 100 : 0);

        $cart = $user->cart;

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

            $originalPrice   = (float)$product->price;              // base/distributor depending on user
            $discountedPrice = (float)$product->discounted_price;   // dynamic calculation
            $quantity = $item->quantity;

            return [
                'id'              => $item->id,
                'product_id'      => $product->id,
                'product_name'    => $product->name,
                'product_slug'    => $product->slug,
                'quantity'        => $quantity,
                'price'           => $originalPrice,       // keep original
                'discounted_price'=> $discountedPrice,     // if no discount, same as price
                'subtotal'        => (float)bcmul($discountedPrice, $quantity, 2),
                'originalSubtotal'=> (float)bcmul($originalPrice, $quantity, 2),
                'stock_quantity'  => $product->stock_quantity,
            ];
        });

        $ship = $this->shippingCostLogic($user);
        if(!$make && (empty($ship['userAddress']??"") || empty($ship['shipCost']??""))) { return ['error'=>false, 'sp'=>true, 'message'=>"No shipping address found."]; }
        else { $shipCost = $ship['shipCost']; }

        $amt = (float)round($cartItems->sum('subtotal'), 2);
        $payable = bcadd($amt, (float)$shipCost->cost, 2); // Payable (amount + shipping cost)
        $tax = bcmul($payable, $taxVal, 2);
        $payable = (float)bcadd($payable, $tax, 2);
        return [
            'error'             => false,
            'id'                => $user->id,
            'first_name'        => $user->first_name,
            'last_name'         => $user->last_name,
            'email'             => $user->email,
            'payable'           => $payable, // Payable (amount + shipping_cost + tax)
            'amount'            => $amt, // sum of discounted subtotals
            'originalAmount'    => (float)round($cartItems->sum('originalSubtotal'), 2), // sum of original subtotals
            'cart_items'        => $cartItems,
            'shippingAddress'   => $ship['userAddress'],
            'shippingCost'      => $shipCost,
            'tax'               => $tax,
            'tax_value'         => $taxVal
        ];
    }

    public function makeOrder(Request $request, $gateway, PaymentManager $manager)
    {
        $user = $request->user();

        $gateCheck = $manager->gateway($gateway);
        if ($gateCheck['error']) {
            return response()->json($gateCheck, 422);
        }

        // 1. Validate cart & stock
        $cartSummary = $this->getCartSummary($user,true);
        if ($cartSummary['error']) {
            return response()->json($cartSummary, 422);
        } elseif ($cartSummary['sp']) {
            return response()->json(['response'=>'no-shipping','message'=>'No shipping address found'], 422);
        }

        try {
            DB::beginTransaction();

            // Clean up abandoned pending orders with no payment
            $user->order()->where('status', 'pending')
                ->whereDoesntHave('payment')->delete();

            // 2. Create fresh order
            $orderCreate = $this->orders->createOrder(
                $user,
                $cartSummary['payable'],
                $cartSummary['shippingCost'],
                $cartSummary['tax']
            );

            if ($orderCreate['error']) {
                DB::rollBack();
                return response()->json($orderCreate, 422);
            }

            // 3. Get gateway config without secret_key
            $gatewayConfig = GeneralSetting::getNested("gateways.$gateway");
            $pubKey = Arr::except($gatewayConfig, ['secret_key']);

            DB::commit();

            return response()->json([
                "message" => "Order Created Successfully",
                "data"    => [
                    "orderId"       => $orderCreate['orderId'],
                    "trans_ref"     => $orderCreate['trans_ref'],
                    "total_amount"  => $orderCreate['payable'],
                    "gws"           => $pubKey
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Checkout error: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error'   => true,
                'message' => 'Failed to place order',
            ], 500);
        }
    }


    public function checkout(Request $request, $gateway, $transRef, $orderId, PaymentManager $manager)
    {
        $user = $request->user();

        $gateCheck = $manager->gateway($gateway);
        if ($gateCheck['error']) {
            return response()->json($gateCheck, 422);
        }

        $order = $user->order()->where("order_id", $orderId)->where("trans_ref", $transRef)
            ->whereDoesntHave('payment', function ($q) {
                $q->where("status","!=","pending");
            })->first();
        if (!$order) {
            return response()->json(["message"=>"Order not found/has been resolved."], 404);
        }

        try {
            DB::beginTransaction();

            $paymentResult = $this->payments->verifyAndSave($gateCheck['gate'],$transRef,$order->id,$order->user_id,$order->total_amount);
            if ($paymentResult['error']) {
                DB::rollBack();
                return response()->json($paymentResult, 422);
            }

            if (in_array($paymentResult['status'], ['failed', 'cancelled'])) {
                $order->update(['status' => 'failed']);
                $msg = 'Order cancelled. Payment failed';
                $msg .= !empty($paymentResult['reason']) ? " - " . $paymentResult['reason'] : '';
                $msg .= '.';
                DB::commit();
                return response()->json(['error' => true,'message' => $msg], 422);
            }

            // Eager load items + products before use
            $order->loadMissing(['orderItem.product']);

            // 4. Deduct stock (bulk + safe update)
            $productUpdates = [];
            foreach ($order->orderItem as $item) {
                $product = $item->product;
                if ($product->stock_quantity < $item->quantity) {
                    DB::rollBack();
                    return response()->json([
                        'error'   => true,
                        'message' => "Insufficient stock for {$product->name}"
                    ], 422);
                }
                $productUpdates[$product->id] = $item->quantity;
            }

            if (!empty($productUpdates)) {
                $ids = implode(',', array_keys($productUpdates));

                $cases = '';
                foreach ($productUpdates as $id => $qty) {
                    $cases .= " WHEN id = {$id} AND stock_quantity >= {$qty} THEN stock_quantity - {$qty}";
                }

                $query = "UPDATE products SET stock_quantity = CASE{$cases} ELSE stock_quantity END
                    WHERE id IN ({$ids})";

                DB::statement($query);
                $failed = DB::table('products')->whereIn('id', array_keys($productUpdates))
                    ->whereRaw("stock_quantity < 0")->exists();

                if ($failed) {
                    DB::rollBack();
                    //refund account
                    return response()->json(['error' => true,'message' => 'Stock update failed — some products ran out of stock.',], 422);
                }
            }

            // 5. Confirm order + clear cart (single query delete)
            CartItem::where('cart_id', $user->cart->id)->delete();
            $this->orders->updateStatus($order, Status::O_CONFIRM, false);

            DB::commit();

            dispatch(function () use ($user, $order) {
                // Build products + history arrays
                $products = $order->orderItem->map(function ($item, $index) {
                    $imagePath = optional($item->product->images->first())->path;
                    $image = config("app.storage_url") . '/' . $imagePath;
                    return [
                        'sno'      => $index + 1,
                        'image'    => $image,
                        'name'     => $item->product->name,
                        'price'    => number_format($item->unit_price, 2),
                        'quantity' => $item->quantity,
                        'total'    => number_format($item->unit_price * $item->quantity, 2),
                    ];
                })->toArray();

                $statushistory = $order->statusHstry->map(function ($history, $index) {
                    return [
                        'sno'    => $index + 1,
                        'status' => ucfirst($history->status),
                        'date'   => $history->created_at->format('Y-m-d H:i:s'),
                    ];
                })->toArray();
                notify(
                    "ORDER_CONFIRMED", $user,
                    [
                        "order_number" => $order->order_id,"order_date" => $order->created_at,"total_amount" => $order->total_amount,
                        'shipping_cost'=>$order->shipping_cost,'delivery_date'=>$order->delivery_days,
                    ],
                    ["email"], false, ["products" => $products,"status_history" => $statushistory]
                );
            });

            return response()->json([
                'message' => 'Order placed successfully',
                // 'order'   => $order->load('payment:id,order_id,status,transaction_reference,payment_gateway,paid_at')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Checkout error: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error'   => true,
                'message' => 'Checkout failed',
            ], 500);
        }
    }

    // public function checkout(Request $request, $gateway, $transRef, PaymentManager $manager)
    // {
    //     $user = $request->user();

    //     $gateCheck = $manager->gateway($gateway);
    //     if ($gateCheck['error']) {
    //         return response()->json($gateCheck, 422);
    //     }

    //     // 1. Validate cart & stock
    //     $cartSummary = $this->getCartSummary($user);
    //     if ($cartSummary['error']) {
    //         return response()->json($cartSummary, 422);
    //     }

    //     $orderCreate = $this->orders->createOrder($user,$transRef,$cartSummary['amount'],$cartSummary['shippingCost'],$gateway);
    //     if ($orderCreate['error']) {
    //         return response()->json($orderCreate, 422);
    //     }
    //     $order = $orderCreate['order'];

    //     try {
    //         DB::beginTransaction();

    //         // 3. Verify payment with gateway (external API, may be slow)
    //         $paymentResult = $this->payments->verifyAndSave($gateCheck['gate'],$transRef,$order->id,$order->user_id,($cartSummary['amount'] + $cartSummary['shippingCost']->cost));
    //         if ($paymentResult['error']) {
    //             DB::rollBack();
    //             return response()->json($paymentResult, 422);
    //         }

    //         $payment = $order->payment;
    //         if (in_array($paymentResult['status'], ['failed', 'cancelled'])) {
    //             $order->update(['status' => 'cancelled']);
    //             $payment->update(['status' => 'failed']);
    //             DB::rollBack();
    //             return response()->json(['error'   => true,'message' => 'Order cancelled. Payment failed.'], 422);
    //         }

    //         // Eager load items + products + images before use
    //         $order->loadMissing(['orderItem.product.images', 'statusHstry']);

    //         // 4. Deduct stock (bulk + safe update)
    //         $productUpdates = [];
    //         foreach ($order->orderItem as $item) {
    //             $product = $item->product;
    //             if ($product->stock_quantity < $item->quantity) {
    //                 DB::rollBack();
    //                 return response()->json([
    //                     'error'   => true,
    //                     'message' => "Insufficient stock for {$product->name}"
    //                 ], 422);
    //             }
    //             $productUpdates[$product->id] = $item->quantity;
    //         }

    //         if (!empty($productUpdates)) {
    //             $ids = implode(',', array_keys($productUpdates));

    //             $cases = '';
    //             foreach ($productUpdates as $id => $qty) {
    //                 $cases .= " WHEN id = {$id} AND stock_quantity >= {$qty} THEN stock_quantity - {$qty}";
    //             }

    //             $query = "UPDATE products SET stock_quantity = CASE{$cases} ELSE stock_quantity END
    //                 WHERE id IN ({$ids})";

    //             DB::statement($query);
    //             $failed = DB::table('products')->whereIn('id', array_keys($productUpdates))
    //                 ->whereRaw("stock_quantity < 0")->exists();

    //             if ($failed) {
    //                 DB::rollBack();
    //                 //refund account
    //                 return response()->json(['error' => true,'message' => 'Stock update failed — some products ran out of stock.',], 422);
    //             }
    //         }

    //         // 5. Confirm order + clear cart (single query delete)
    //         CartItem::where('cart_id', $user->cart->id)->delete();
    //         $this->orders->updateStatus($order, Status::O_CONFIRM, false);

    //         DB::commit();

    //         dispatch(function () use ($user, $order) {
    //             // Build products + history arrays
    //             $products = $order->orderItem->map(function ($item, $index) {
    //                 $imagePath = optional($item->product->images->first())->path;
    //                 $image = config("app.storage_url") . '/' . $imagePath;
    //                 return [
    //                     'sno'      => $index + 1,
    //                     'image'    => $image,
    //                     'name'     => $item->product->name,
    //                     'price'    => number_format($item->unit_price, 2),
    //                     'quantity' => $item->quantity,
    //                     'total'    => number_format($item->unit_price * $item->quantity, 2),
    //                 ];
    //             })->toArray();

    //             $statushistory = $order->statusHstry->map(function ($history, $index) {
    //                 return [
    //                     'sno'    => $index + 1,
    //                     'status' => ucfirst($history->status),
    //                     'date'   => $history->created_at->format('Y-m-d H:i:s'),
    //                 ];
    //             })->toArray();
    //             notify(
    //                 "ORDER_CONFIRMED", $user,
    //                 [
    //                     "order_number" => $order->order_id,"order_date" => $order->created_at,"total_amount" => $order->total_amount,
    //                     'shipping_cost'=>$order->shipping_cost,'delivery_date'=>$order->delivery_days,
    //                 ],
    //                 ["email"], false, ["products" => $products,"status_history" => $statushistory]
    //             );
    //         });

    //         return response()->json([
    //             'message' => 'Order placed successfully',
    //             'order'   => $order->load('payment:id,order_id,status,transaction_reference,payment_gateway,paid_at'),
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error("Checkout error: " . $e->getMessage(), [
    //             'trace' => $e->getTraceAsString(),
    //         ]);
    //         return response()->json([
    //             'error'   => true,
    //             'message' => 'Checkout failed',
    //         ], 500);
    //     }
    // }
    

    public function retryCheckout(Request $request, $transRef, PaymentManager $manager)
    {

    }
}