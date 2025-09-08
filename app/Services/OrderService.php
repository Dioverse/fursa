<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder($user, $total)
    {
        try {
            $shippingAddress = $user->shippingAddress()->orderByDesc("is_default")->first([
                'id','full_name','phone','address_line_one','city','state','postal_code','country'
            ]);

            if (!$shippingAddress) {
                return ["error" => true, "message" => "No shipping address attached to order."];
            }

            $lastOrder = Order::latest('id')->first();
            $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
            $orderId = 'ORD-' . $nextId . strtoupper(Str::random(8));

            // Create the order
            $order = Order::create([
                'user_id'          => $user->id,
                'shipping_address' => $shippingAddress->toArray(),
                'order_id'         => $orderId,
                'total_amount'     => $total,
                'status'           => 'pending',
            ]);

            // Build order items array
            $orderItems = [];
            $now = now();

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

            if (!empty($orderItems)) {
                OrderItem::insert($orderItems); // bulk insert
            }

            // Clear cart after saving
            $user->cart->cartItems()->delete();

            return ['error' => false, 'order' => $order];
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
}
