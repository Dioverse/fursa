<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\CartItem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\AdminSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Payments\PaymentManager;

class PaymentController extends Controller
{
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

        $shippingAddys = $user->shippingAddress()->orderByDesc("is_default")->get();

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
        $cart = Cart::with('cartItems')
            ->where('user_id', $user->id)
            ->first();

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

        // Build cart summary
        $cartItems = $cart->cartItems()->with(['product.category'])->get()
        ->map(function ($item) use($user) {
            $product  = $item->product;

            return [
                'id'             => $item->id,
                'product_id'     => $product->id,
                'product_name'   => $product->name,
                'product_slug'   => $product->slug,
                'quantity'       => $item->quantity,
                'price'          => $product->price,
                'stock_quantity' => $product->stock_quantity,
                'category_name'  => $product->category?->name,   // safe navigation operator
                'category_slug'  => $product->category?->slug,   // safe navigation operator
            ];
        });

        return [
            'error'       => false,
            'id'          => $user->id,
            'first_name'  => $user->first_name,
            'last_name'   => $user->last_name,
            'email'       => $user->email,
            'amount'      => $cart->getTotalAmount(),
            'cart_items'  => $cartItems,
        ];
    }


    public function checkout(Request $request, $gateway, $transId, PaymentManager $manager)
    {
        $user = $request->user();

        // verify payment with gateway
        $gateCheck = $manager->gateway($gateway);
        if ($gateCheck['error']) {
            return response()->json($gateCheck);
        }

        
        try {
            DB::beginTransaction();

            // cart validation
            $cartSummary = $this->getCartSummary($user);
            if ($cartSummary['error'] === true) {
                return response()->json($cartSummary, 422);
            }
            $cartTotal = $cartSummary['amount'];
    
            $orderCreate = $this->createOrder($user, $cartTotal);
            if ($orderCreate['error'] === true) {
                DB::rollBack();
                return response()->json($orderCreate, 422);
            }
    
    
    
            // $existingPayment = Payment::where('user_id', $user->id)
            //         ->where('transaction_reference', $transId)
            //         ->first();
    
            // if ($existingPayment) {
            //     if ($existingPayment->status === 'successful') {
            //         $err = ['error'=>true,'message'=>'Payment is already successful','payment'=>$existingPayment->transaction_reference];
            //     } elseif ($existingPayment->status === 'refunded' || $existingPayment->status === 'reversed') {
            //         $err = ['error'=>true,'message'=>'Payment was refunded/reversed','payment'=>$existingPayment->transaction_reference];
            //     }
            //     return response()->json($err, 422);
            // }
    
            
    
            $payment = $gateCheck['gate']->verifyPayment($transId);
            if (!$payment['success']) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Payment verification failed',
                    $payment
                ], 422);
            }
            $processes = $this->runProcesses($payment, $orderCreate['order'], $cartTotal);
            if ($processes['error'] === true) {
                DB::rollBack();
                return response()->json($processes, 422);
            }


            // if ((float) $payment['amount'] !== (float) $cartSummary['amount']) {
            //     return response()->json(['refund']);
            // }

            DB::commit();

            return response()->json([
                'error'   => false,
                'message' => 'Order placed successfully',
                'order'   => $orderCreate['order']->load(['payment']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error'   => true,
                'message' => 'Checkout failed',
            ], 500);
        }
    }

    public function createOrder($user, $total)
    {
        try {
            $shipping_address = $user->shippingAddress()->orderByDesc("is_default")->first();
            if (!$shipping_address) {
                return ["error"=>true,"message"=>"No shipping address attached to order."];
            }
            // Generate a unique order ID
            $orderId = 'ORD-' . Order::orderByDesc("id")->first(["id"])->id + 1 . strtoupper(Str::random(8));

            // Create the order
            $order = Order::create([
                'user_id'             => $user->id,
                'shipping_address'    => $shipping_address,
                'order_id'            => $orderId,
                'total_amount'        => $total,
                'status'              => 'pending',
            ]);

            // clear cart
            $user->cart->cartItems()->delete();

            return [
                'error' => false,
                'order' => $order,
            ];
        } catch (\Exception $e) {
            return [
                'error'   => true,
                'message' => 'Order processing failed',
            ];
        }
    }

    public function runProcesses($payment, $order, $cartTotal) {
        try {
            $amount = $payment['amount']/100;
            if ((float) $amount !== $cartTotal) {
                return ['error'=>true,'message'=>'Invalid transaction amount'];
            }

            $payment = Payment::updateOrCreate(
            ['transaction_reference' => $payment['reference']],
                [
                    'user_id'   => $order->user_id,
                    'order_id'  => $order->id,
                    'status'    => $payment['status'],
                    'amount'    => $payment['amount'],
                    'currency'  => $payment['currency'],
                    'method'    => $payment['method'],
                    'gateway'   => $payment['gateway'],
                    'raw'       => json_encode($payment['raw']),
                ]
            );

            return ["error"=>false];

        } catch (\Exception $e) {
            return [
                'error'   => true,
                'message' => 'Payment processing failed',
            ];
        }
    }

    // public function processOrder($total, $user, $shipping_address)
    // {
    //     try {
    //         // Generate a unique order ID
    //         $orderId = 'ORD-' . Order::orderByDesc("id")->first(["id"])->id + 1 . strtoupper(Str::random(8));

    //         // Create the order
    //         $order = Order::create([
    //             'user_id'             => $user->id,
    //             'shipping_address_id' => $shipping_address,
    //             'order_id'            => $orderId,
    //             'total_amount'        => $total,
    //             'status'              => 'pending',
    //         ]);

    //         // Loop through cart items -> decrement stock + attach to order
    //         foreach ($user->cart->cartItems as $item) {
    //             $product = $item->product;

    //             // decrement stock
    //             $product->decrement('stock_quantity', $item->quantity);

    //             // create pivot/order_item (if you have order_items table)
    //             $order->orderItem()->create([
    //                 'product_id' => $product->id,
    //                 'quantity'   => $item->quantity,
    //                 'unit_price'      => $user->role == "distributor" 
    //                                     ? $product->distributor_price 
    //                                     : $product->base_price,
    //             ]);
    //         }

    //         // clear cart
    //         // $user->cart->cartItems()->delete();

    //         return [
    //             'error' => false,
    //             'order' => $order,
    //         ];
    //     } catch (\Exception $e) {
    //         return [
    //             'error'   => true,
    //             'message' => 'Order processing failed',
    //             'raw'     => $e->getMessage()
    //         ];
    //     }
    // }

    protected function getDefaultGateway() {
        // Get gateways object
        $gateways = AdminSetting::getNested("gateways", []);
        // Find the first active gateway
        return collect($gateways)
            ->filter(fn ($status) => $status === 'active')->keys()->first();
        if (!$defaultGateway) {
            return response()->json(['message'=>'All gateways are inactive'], 422);
        }
    }
}