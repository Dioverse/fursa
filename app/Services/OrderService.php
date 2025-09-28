<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder($user, $total, $shipCost, $gateway)
    {
        try {
            $shippingAddress = $user->shippingAddress()->orderByDesc("is_default")->first(['id','full_name','phone','address_line_one','city','state','postal_code','country']);

            if (!$shippingAddress) { return ["error" => true, "message" => "No shipping address attached to order."]; }

            $lastOrder = Order::latest('id')->first();
            $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
            $orderId = 'ORD-' . $nextId . strtoupper(Str::random(9));
            $now = Carbon::now();
            $transRef = Str::random(9) . $now->format("Ymd");
            $total_amount = $total + $shipCost->cost;
            $order = Order::create([
                'user_id'          => $user->id,
                'shipping_address' => $shippingAddress->toJson(), // serialize address
                'order_id'         => $orderId,
                'trans_ref'        => $transRef,
                'total_amount'     => $total_amount,
                'status'           => 'pending',
                'shipping_cost'    => $shipCost->cost,
                'delivery_days'    => $now->add('days', $shipCost->min_day)->format('Y-m-d') . ' - ' . $now->add('days', $shipCost->max_day)->format('Y-m-d'),
            ]);

            $orderItems = [];

            foreach ($user->cart->cartItems as $cartItem) {
                $product = $cartItem->product;
                $unitPrice = $product->discounted_price ?? $product->price;

                $orderItems[] = [
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $cartItem->quantity,
                    'unit_price' => $unitPrice,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (!empty($orderItems)) { OrderItem::insert($orderItems); }

            // 3. Create pending payment
            // Payment::create([
            //     'order_id'             => $order->id,
            //     'user_id'              => $user->id,
            //     'status'               => 'pending',
            //     'refund_status'        => null,
            //     'payment_gateway'      => $gateway,
            //     'transaction_reference'=> $transRef,
            //     'amount'               => $total + $shipCost->cost,
            // ]);

            return [
                'error'     => false,
                'orderId'   => $orderId,
                'trans_ref' => $transRef,
                'amount'    => $total_amount
            ];
        } catch (\Exception $e) {
            return [
                'error'   => true,
                'message' => 'Order processing failed: ' . $e->getMessage(),
            ];
        }
    }


    public function markFailed($order, $user)
    {
        $order->update(['status' => 'cancelled']);

        $cart = $user->cart;

        if (!$cart) {
            $cart = $user->cart()->create(); // create empty cart if missing
        }

        $itemsToInsert = [];
        $now = now();

        foreach ($order->items as $item) {
            // check if product already exists in cart -> update quantity
            $existingItem = $cart->cartItems()->where('product_id', $item->product_id)->first();

            if ($existingItem) {
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $item->quantity,
                    'updated_at' => $now
                ]);
            } else {
                $itemsToInsert[] = [
                    'cart_id'    => $cart->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (!empty($itemsToInsert)) {
            $cart->cartItems()->insert($itemsToInsert);
        }
    }

    public function updateStatus(Order $order, string $newStatus, bool $notify = true, bool $queue = true): array
    {
        try{
            $validStatuses = [
                'pending',
                'confirmed',
                'processing',
                'shipping',
                'shipped',
                'out for delivery',
                'delivered',
                'cancelled',
                'failed'
            ];

            $currentStatus = $order->status;

            $currentIndex = array_search($currentStatus, $validStatuses, true);
            $newIndex     = array_search($newStatus, $validStatuses, true);

            if ($currentIndex === false || $newIndex === false) {
                return [
                    'error'   => true,
                    'message' => "Invalid status transition.",
                ];
            }

            // Prevent moving backwards (except for cancelled/failed)
            if ($newIndex < $currentIndex && !in_array($newStatus, ['cancelled', 'failed'])) {
                return [
                    'error'   => true,
                    'message' => "Cannot move order status backwards.",
                ];
            }

            // Build statuses to insert
            $statusesToLog = in_array($newStatus, ['cancelled', 'failed'])
                ? [$newStatus] // cancelled/failed go directly
                : array_slice($validStatuses, $currentIndex + 1, $newIndex - $currentIndex);

            if (!empty($statusesToLog)) {
                $now = now();
                $userId = auth()->id();
                $role   = auth()->check() ? 'admin' : 'system';

                $insertData = [];
                foreach ($statusesToLog as $status) {
                    $insertData[] = [
                        'order_id'    => $order->id,
                        'status'      => $status,
                        'changed_by'  => $userId,
                        'change_role' => $role,
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ];
                }
                OrderStatusHistory::insert($insertData);
            }

            // Update final order status
            $order->update(['status' => $newStatus]);

            // Notify order owner
            if ($notify && $newStatus !== $currentStatus) {
                $statusHistories = OrderStatusHistory::where('order_id', $order->id)
                    ->orderBy('created_at', 'asc')
                    ->get(['status', 'created_at']);
                $loopItems = [
                    'status_history' => $statusHistories->map(function ($history) {
                        return [
                            'status' => ucfirst($history->status),
                            'date'   => $history->created_at->format('M d, Y H:i'),
                        ];
                    })->toArray(),
                ];
                notify($order->user,'ORDER_STATUS',
                ['order_id' => $order->id,'status_date' => $now,'status' => ucfirst($newStatus)],
                ['email'],true,$loopItems,
                );
            }

            return [
                'error'   => false,
                'message' => "Order status updated to {$newStatus}.",
            ];
        } catch (\Exception $e) {
            return [
                'error'   => true,
                'message' => "Failed to update order status.",
                'debug'   => $e->getMessage(),
            ];
        }
    }



}
