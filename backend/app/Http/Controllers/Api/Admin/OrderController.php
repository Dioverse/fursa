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
        // Start building the query for orders
        $query = Order::query();

        
        $query->with([
            'user:id,first_name,last_name,email', // Select specific user columns
            'shippingAddress:address_line_one,city,state' // Load all columns for shipping address, adjust if needed
        ])->withCount('items'); // Count related order items

        // --- Filtering Options ---

        // Filter by user first_name or last_name
        if ($request->has('user_name_search') && is_string($request->input('user_name_search'))) {
            $searchTerm = '%' . $request->input('user_name_search') . '%';
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('first_name', 'LIKE', $searchTerm)
                  ->orWhere('last_name', 'LIKE', $searchTerm);
            });
        }

        // Filter by status
        // Ensure the status is one of the allowed enum values
        $allowedStatuses = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];
        if ($request->has('status') && in_array($request->input('status'), $allowedStatuses)) {
            $query->where('status', $request->input('status'));
        }

        // Filter by total_amount range
        if ($request->has('min_amount') && is_numeric($request->input('min_amount'))) {
            $query->where('total_amount', '>=', $request->input('min_amount'));
        }
        if ($request->has('max_amount') && is_numeric($request->input('max_amount'))) {
            $query->where('total_amount', '<=', $request->input('max_amount'));
        }

        // --- Ordering (Optional but Recommended for API lists) ---
        // Default order by latest orders
        $sortBy = $request->query('sort_by', 'created_at');
        $sortOrder = $request->query('sort_order', 'desc'); // 'asc' or 'desc'

        // Whitelist allowed sortable columns to prevent SQL injection
        $allowedSortColumns = ['id', 'total_amount', 'status', 'created_at'];
        if (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            // Fallback to default if invalid sort_by is provided
            $query->orderBy('created_at', 'desc');
        }


        // --- Pagination ---

        // Get the number of items per page from the request, default to 10 if not provided
        $perPage = $request->query('per_page', 10);
        // Ensure per_page is a positive integer
        $perPage = max(1, (int) $perPage);

        // Apply pagination
        $orders = $query->paginate($perPage);

        // Return a JSON response
        return response()->json([
            'message' => 'Orders retrieved successfully.',
            'data' => $orders, // Laravel's paginate method returns a comprehensive data structure
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
        
        $validator = Validator::make($request->all(), [
            'status' => ['required', 'string', "in:shipped,out for delivery,delivered"]
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

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
