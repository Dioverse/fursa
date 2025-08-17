<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Payment;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // Start building the query for payments
        $query = Payment::query();

        // Eager load relationships for efficiency and to include related data
        // Always include 'id' when selectively loading columns for relationships
        $query->with([
            'order:id,order_id,total_amount,status',
            'user:id,first_name,last_name,email,role'
        ]);

        // --- Filtering Options ---

        // Filter by Payment Mathod
        if ($request->has('payment_method') && is_string($request->input('payment_method'))) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        // Filter by Payment Gateway
        if ($request->has('payment_gateway') && is_string($request->input('payment_gateway'))) {
            $query->where('payment_gateway', $request->input('payment_gateway'));
        }

        // Assuming 'status' for payment also has an enum-like set of values
        $allowedPaymentStatuses = ['completed', 'pending', 'failed', 'refunded']; // Adjust as per your payment status enum
        if ($request->has('payment_status') && in_array($request->input('payment_status'), $allowedPaymentStatuses)) {
            $query->where('status', $request->input('payment_status'));
        }

        if ($request->has('min_amount') && is_numeric($request->input('min_amount'))) {
            $query->where('amount', '>=', $request->input('min_amount'));
        }
        if ($request->has('max_amount') && is_numeric($request->input('max_amount'))) {
            $query->where('amount', '<=', $request->input('max_amount'));
        }

        if ($request->has('transaction_id') && is_string($request->input('transaction_id'))) {
            $query->where('transaction_reference', 'like', '%' . $request->input('transaction_id') . '%');
        }

        // Filter by orders.order_id
        if ($request->has('order_id') && is_string($request->input('order_id'))) {
            $searchTerm = '%' . $request->input('order_id') . '%';
            $query->whereHas('order', function ($q) use ($searchTerm) {
                $q->where('order_id', 'LIKE', $searchTerm);
            });
        }

        // Filter by users.first_name or users.last_name
        if ($request->has('user_name_search') && is_string($request->input('user_name_search'))) {
            $searchTerm = '%' . $request->input('user_name_search') . '%';
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('first_name', 'LIKE', $searchTerm)
                  ->orWhere('last_name', 'LIKE', $searchTerm);
            });
        }

        // Filter by user roles: customer, distributor
        if ($request->has('user_role') && is_string($request->input('user_role'))) {
            $allowedRoles = ['customer', 'distributor']; // Add other roles if applicable
            if (in_array($request->input('user_role'), $allowedRoles)) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('role', $request->input('user_role'));
                });
            }
        }

        $sortBy = $request->query('sort_by', 'created_at');
        $sortOrder = $request->query('sort_order', 'desc');

        // Whitelist allowed sortable columns for payments table
        $allowedSortColumns = ['id', 'amount', 'status', 'created_at'];
        if (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // --- Pagination ---
        $perPage = $request->query('per_page', 10);
        $perPage = max(1, (int) $perPage);

        $payments = $query->paginate($perPage);

        // Return a JSON response
        return response()->json([
            'message' => 'Payments retrieved successfully.',
            'data' => $payments,
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
        $payment = Payment::with(['user:id,first_name,last_name,role','orders:id,order_id'])->find($id);

        if (!$payment) {
            return response()->json(['message' => "Order not found."], 404);
        }

        return response()->json([
            'message' => "Payment details retrieved successfully.",
            'data' => $payment,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
