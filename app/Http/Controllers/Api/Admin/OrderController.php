<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Mail\OrderStatusChange;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::query();

        $query->with([
            'user:id,first_name,last_name,email',
        ])->withCount('orderItem');

        // --- Filtering ---

        // Filter by user first_name or last_name
        if ($request->filled('user_name_search')) {
            $searchTerm = '%' . $request->input('user_name_search') . '%';
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('first_name', 'LIKE', $searchTerm)
                ->orWhere('last_name', 'LIKE', $searchTerm);
            });
        }

        // Filter by status
        $allowedStatuses = ['pending', 'out for delivery', 'delivered', 'cancelled', 'failed'];
        if ($request->filled('status') && in_array($request->input('status'), $allowedStatuses)) {
            $query->where('status', $request->input('status'));
        }

        // Filter by total_amount range
        if ($request->filled('min_amount') && is_numeric($request->min_amount)) {
            $query->where('total_amount', '>=', $request->min_amount);
        }
        if ($request->filled('max_amount') && is_numeric($request->max_amount)) {
            $query->where('total_amount', '<=', $request->max_amount);
        }

        // --- Ordering ---
        $sortBy = $request->query('sort_by', 'created_at');
        $sortOrder = $request->query('sort_order', 'desc');

        $allowedSortColumns = ['order_id', 'total_amount', 'status', 'created_at'];
        if (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // --- Pagination ---
        $perPage = max(1, (int) $request->query('per_page', 10));
        $orders = $query->paginate($perPage);

        return response()->json([
            'message' => 'Orders retrieved successfully.',
            'data' => $orders,
            'filters' => [
                'sort_order' => ['Ascending' => 'asc', 'Descending' => 'desc'],
                'sort_by' => $allowedSortColumns,
                'status' => $allowedStatuses,
            ],
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $order = Order::with(['user:id,first_name,last_name,email,role','shippingAddress'])->find($id);

        if (!$order) {
            return response()->json(['message' => "Order not found."], 404);
        }

        return response()->json([
            'message' => "Order details retrieved successfully.",
            'data' => $order,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStatus(Request $request, string $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $request->validate([
            'status' => ['required', 'string', "in:shipped,out for delivery,delivered"]
        ]);

        $status = $request->status;

        $order->fill(['status'=>$status]);
        
        Mail::to($order->user->email)->queue(new OrderStatusChange($order, $status));   
        $order->save();
        return response()->json([
            'message' => 'Order updated successfully.',
            'data' => $order,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
