<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Mail\OrderStatusChange;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    private $allowedStatusesUpdate = ['processing','shipping','shipped','out for delivery','delivered','cancelled'];
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

        // Filter by order_id (non-numeric)
        if ($request->filled('order_id_search')) {
            $searchTerm = '%' . $request->input('order_id_search') . '%';
            $query->where('order_id', 'LIKE', $searchTerm);
        }

        // Filter by status
        $allowedStatuses = [
            'pending','confirmed','processing','shipping','shipped',
            'out for delivery','delivered','cancelled','failed','expired'
        ];

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
        $order = Order::with([
            'user:id,first_name,last_name,email,role','payment',
            'statusHstry:id,order_id,status,changed_by,created_at',
            'orderItem' => function ($q) {
                $q->with([
                    'product' => function ($p) {
                        $p->with(['firstImage:id,product_id,path']);
                    }
                ]);
            },
        ])->find($id);

        if (!$order) {
            return response()->json(['message' => "Order not found."], 404);
        }

        return response()->json([
            'message' => "Order details retrieved successfully.",
            'data' => $order,
            'allowedStatuses' => $this->allowedStatusesUpdate
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    // public function updateStatus(Request $request, string $id)
    // {
    //     $order = Order::find($id);

    //     if (!$order) {
    //         return response()->json(['message' => 'Order not found.'], 404);
    //     }

    //     $request->validate([
    //         'status' => ['required', 'string', "in:shipped,out for delivery,delivered"]
    //     ]);

    //     $status = $request->status;

    //     $order->fill(['status'=>$status]);
        
    //     Mail::to($order->user->email)->queue(new OrderStatusChange($order, $status));   
    //     $order->save();
    //     return response()->json([
    //         'message' => 'Order updated successfully.',
    //         'data' => $order,
    //     ]);
    // }

        public function updateStatus(Request $request, string $id, OrderService $orderService)
        {
            $order = Order::find($id);

            if (!$order) {
                return response()->json(['message' => 'Order not found.'], 404);
            }

            // Only allow these statuses from admin side
            $request->validate([
                'status' => ['required','string',"in:".implode(",", $this->allowedStatusesUpdate)],
                'notify' => ['required','boolean']
            ]);

            $status = $request->status;
            try {
                DB::beginTransaction();

                $result = $orderService->updateStatus($order, $status, $request->notify);

                if ($result['error']) {
                    DB::rollBack();
                    return response()->json($result, 422);
                }

                DB::commit();

                return response()->json([
                    'message' => $result['message'],
                    'data'    => $order->fresh(), // return updated order
                ]);

            } catch (\Throwable $e) {
                DB::rollBack();

                return response()->json([
                    'error'   => true,
                    'message' => 'Failed to update order status.',
                    'debug'   => app()->environment('local') ? $e->getMessage() : null,
                ], 500);
            }
        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
