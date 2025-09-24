<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Product;
use App\Models\Discount;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class InventoryController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $query = Inventory::with(['product:id,name,sku', 'user:id,first_name,last_name,email']);

        // --- Global Search across user, product, and inventory.reason ---
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                // Search inventory.reason
                $q->where('reason', 'like', "%{$search}%")
                // Search product name
                ->orWhereHas('product', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                })
                // Search user first_name or last_name
                ->orWhereHas('user', function ($q3) use ($search) {
                    $q3->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            });
        }

        // --- Filter by operation ---
        if ($request->filled('operation')) {
            $query->where('operation', $request->input('operation'));
        }

        // --- Date filters ---
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->input('date_from'),
                $request->input('date_to')
            ]);
        } elseif ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        } elseif ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // --- Sorting ---
        $sortBy    = $request->query('sort_by', 'id');
        $sortOrder = $request->query('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // --- Pagination ---
        $perPage    = max(1, (int) $request->query('per_page', 15));
        $inventories = $query->paginate($perPage);

        // --- Stats ---
        $stats = Inventory::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN operation = "add" THEN 1 ELSE 0 END) as added,
            SUM(CASE WHEN operation = "subtract" THEN 1 ELSE 0 END) as subtracted
        ')->first();

        return response()->json([
            'message' => 'Inventory logs retrieved successfully.',
            'data'    => $inventories,
            'stats'   => $stats,
            'filters' => [
                'operation'   => ['add', 'subtract', 'set', 'delete'],
                // 'operation'   => ['add', 'subtract', 'set', 'delete', 'restore'],
                'sort_by'     => [
                    'Created'      => 'created_at',
                    'Quantity'     => 'quantity',
                    'Stock Before' => 'stock_before',
                    'Stock After'  => 'stock_after',
                ],
                'sort_order'  => ['Ascending' => 'asc', 'Descending' => 'desc'],
            ],
        ]);
    }

    public function show($id): JsonResponse
    {
        $inventory = Inventory::find($id);

        if (!$inventory) {
            return response()->json(['message' => 'Inventory log not found.'], 404);
        }

        return response()->json([
            'message' => 'Inventory log retrieved successfully.',
            'data' => $inventory,
        ]);
    }

    public function stock(Request $request, $id)
    {
        $product = Product::find($id);
        if (! $product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $validated = $request->validate([
            'operation'           => 'required|integer|in:1,2,3',
            'quantity'            => 'required|integer|min:0',
            'update_threshold'    => 'nullable|boolean',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'reason'              => 'nullable|string|max:255',
        ]);

        $before = $product->stock_quantity;
        $operation = null;

        switch ($validated['operation']) {
            case 1: // Add
                $product->stock_quantity += $validated['quantity'];
                $operation = 'add';
                break;
            case 2: // Subtract
                $product->stock_quantity = max(0, $product->stock_quantity - $validated['quantity']);
                $operation = 'subtract';
                break;
            case 3: // Set
                $product->stock_quantity = $validated['quantity'];
                $operation = 'set';
                break;
        }

        // Update threshold if requested
        if (! empty($validated['update_threshold']) && isset($validated['low_stock_threshold'])) {
            $product->low_stock_threshold = $validated['low_stock_threshold'];
        }

        $product->save();

        // Log inventory change
        Inventory::create([
            'product_id'   => $product->id,
            'user_id'      => $request->user()->id ?? null,
            'operation'    => $operation,
            'quantity'     => $validated['quantity'],
            'stock_before' => $before,
            'stock_after'  => $product->stock_quantity,
            'reason'       => $validated['reason'] ?? null,
        ]);

        return response()->json([
            'message' => 'Stock updated successfully.',
            'data'    => [
                'product_id'          => $id,
                'stock_quantity'      => $product->stock_quantity,
                'low_stock_threshold' => $product->low_stock_threshold,
            ],
        ]);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action'                => 'required|string|in:activate,deactivate,feature,unfeature,delete,discount,undiscount',
            'product_ids'           => 'required|array|min:1',
            'product_ids.*'         => 'integer|exists:products,id',
            'discount_type'         => 'required_if:action,discount|in:percentage,fixed',
            'discount_value'        => 'required_if:action,discount|numeric|min:0',
            'discount_start_date'   => 'required_if:action,discount|date',
            'discount_end_date'     => 'required_if:action,discount|date|after_or_equal:discount_start_date',
        ]);

        $action     = $request->input('action');
        $productIds = $request->input('product_ids');

        try {
            switch ($action) {
                case 'activate':
                    Product::whereIn('id', $productIds)->update(['status' => 1]);
                    break;

                case 'deactivate':
                    Product::whereIn('id', $productIds)->update(['status' => 0]);
                    break;

                case 'feature':
                    Product::whereIn('id', $productIds)->update(['is_featured' => 1]);
                    break;

                case 'unfeature':
                    Product::whereIn('id', $productIds)->update(['is_featured' => 0]);
                    break;

                case 'delete':
                    $products = Product::whereIn('id', $productIds)->get();

                    foreach ($products as $product) {
                        $inUse = $product->orderItems()->exists();
                        $before = $product->stock_quantity;

                        if ($inUse) {
                            $product->status = 0; // soft delete (inactive)
                            $product->save();

                            Inventory::create([
                                'product_id'   => $product->id,
                                'user_id'      => $request->user()->id ?? null,
                                'operation'    => 'delete',
                                'quantity'     => 0,
                                'stock_before' => $before,
                                'stock_after'  => $product->stock_quantity,
                                'reason'       => 'Product deactivated (in use)',
                            ]);
                        } else {
                            $product->delete();

                            Inventory::create([
                                'product_id'   => $product->id,
                                'user_id'      => $request->user()->id ?? null,
                                'operation'    => 'delete',
                                'quantity'     => 0,
                                'stock_before' => $before,
                                'stock_after'  => 0,
                                'reason'       => 'Product permanently deleted',
                            ]);
                        }
                    }
                    break;
                
                case 'discount':
                    foreach ($productIds as $id) {
                        Discount::updateOrCreate(
                            ['product_id' => $id],
                            [
                                'type'       => $request->discount_type,
                                'value'      => $request->discount_value,
                                'start_date' => $request->discount_start_date,
                                'end_date'   => $request->discount_end_date,
                            ]
                        );
                    }
                    $action .= 'e';
                    break;

                case 'undiscount':
                    Discount::whereIn('product_id', $productIds)->delete();
                    $action .= 'e';
                    break;
            }

            return response()->json([
                'message' => "Select products $action" . 'd' . " successfully.",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'API Error: ' . $e->getMessage(),
            ]);
        }
    }
}
