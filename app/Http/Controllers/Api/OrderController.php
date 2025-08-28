<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Order::with([
            'shippingAddress:id,address_line_one'
        ])
        ->withCount('orderItem')
        ->where('user_id', $user->id);

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Pagination: default 10 per page
        $perPage = $request->query('per_page', 10);
        $perPage = max(1, (int) $perPage); // Ensure per_page is a positive integer
        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($orders);
    }

    /**
     * Display the specified order.
     */
    public function show(string $id)
    {
        $user = Auth::user();

        $order = Order::with([
            'orderItem' => function ($query) {
                $query->select('id', 'order_id', 'product_id', 'quantity', 'unit_price'); 
            },
            'orderItem.product' => function ($query) {
                $query->select('id', 'category_id', 'name', 'base_price', 'distributor_price', 'slug', 'short_description'); 
            },
            'orderItem.product.category' => function ($query) {
                $query->select('id', 'name', 'slug'); 
            },
            'payment:id,order_id,status,payment_gateway,payment_method,transaction_reference,amount,paid_at',
            'shippingAddress:id,user_id,full_name,phone,address_line_one,address_line_two,city,state,postal_code,country,is_default'
        ])
        ->where('user_id', $user->id)
        ->where('order_id',$id)
        ->first();
        if (!$order) {
            return response()->json([
                'message' => 'Order not found.'
            ], 404);
        }

        return response()->json([
            'message' => 'Order retrieved successfully.',
            'data' => $order
        ]);
    }

    /**
     * Update the specified order.
     * Only allow cancelling pending orders.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();

        $order = Order::where('user_id', $user->id)->where('order_id', $id)->first();
        if (!$order) {
            return response()->json([
                'message' => 'Order not found.'
            ], 404);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending orders can be cancelled.'
            ], 400);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Order cancelled successfully.',
            'data' => $order
        ]);
    }
}
