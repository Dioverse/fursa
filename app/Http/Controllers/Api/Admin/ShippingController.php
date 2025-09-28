<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Shipping;
use Illuminate\Http\Request;
use App\Models\ShippingAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class ShippingController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $query = Shipping::query();

        // --- Filtering Options ---
        if ($request->filled('country')) {
            $query->where('country', $request->input('country'));
        }

        if ($request->filled('state')) {
            $query->where('state', $request->input('state'));
        }

        if ($request->filled('province')) {
            $query->where('province', $request->input('province'));
        }

        if ($request->filled('provider')) {
            $query->where('provider', 'like', '%' . $request->input('provider') . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', (bool) $request->input('status'));
        }

        if ($request->filled('cost_from')) {
            $query->where('cost', '>=', $request->input('cost_from'));
        }

        if ($request->filled('cost_to')) {
            $query->where('cost', '<=', $request->input('cost_to'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Sorting
        $sortBy    = $request->query('sort_by', 'id');
        $sortOrder = $request->query('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage   = max(1, (int) $request->query('per_page', 10));
        $shippings = $query->paginate($perPage);

        // Stats
        $stats = Shipping::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive,
            MIN(cost) as min_cost,
            MAX(cost) as max_cost
        ')->first();

        // Available filter options
        $countries = Shipping::select('country')->distinct()->pluck('country');
        $states    = Shipping::select('state')->distinct()->pluck('state');
        $provinces = Shipping::select('province')->distinct()->pluck('province');
        $providers = Shipping::select('provider')->distinct()->whereNotNull('provider')->pluck('provider');

        return response()->json([
            'message' => 'Shipping rules retrieved successfully.',
            'data'    => $shippings,
            'stats'   => $stats,
            'filters' => [
                'status'      => ['inactive' => 0, 'active' => 1],
                'sort_by'     => [
                    'Created'      => 'created_at',
                    'Country'      => 'country',
                    'State'        => 'state',
                    'Province'     => 'province',
                    'Cost'         => 'cost',
                    'Provider'     => 'provider',
                ],
                'sort_order'  => ['Ascending' => 'asc', 'Descending' => 'desc'],
                'countries'   => $countries,
                'states'      => $states,
                'provinces'   => $provinces,
                'providers'   => $providers,
            ],
        ]);
    }


    // // Checkout logic only applies active rules
    public function show(string $id)
    {
        $rule = Shipping::find($id);
        if (!$rule) {
            return response()->json(['message' => 'Shipping rule not found.'], 404);
        }

        return response()->json([
            "message" => "Shipping rule details",
            "data"=> $rule
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validate base fields first
        $validatedData = $request->validate([
            'rules'                       => 'required|array',
            'rules.*.country'             => 'required|string',
            'rules.*.state'               => 'required|string',
            'rules.*.province'            => 'required|string',
            'rules.*.min_days'            => 'required|integer|min:1',
            'rules.*.max_days'            => 'nullable|integer|min:1',
            'rules.*.cost'                => 'required|numeric|min:0',
            'rules.*.provider'            => 'nullable|string',
        ]);

        $rules = $validatedData['rules'];

        // 2. Per-item validation (min_days <= max_days)
        foreach ($rules as $key => $rule) {
            if (isset($rule['max_days']) && $rule['max_days'] < $rule['min_days']) {
                throw ValidationException::withMessages([
                    "rules.$key.max_days" => [
                        'The max_days field must be greater than or equal to the min_days field.'
                    ],
                ]);
            }
        }

        // 3. Check for duplicates within the same request payload
        $seen = [];
        foreach ($rules as $key => $rule) {
            $combo = strtolower($rule['country'] . '|' . $rule['state'] . '|' . $rule['province']);
            if (isset($seen[$combo])) {
                throw ValidationException::withMessages([
                    "rules.$key.province" => [
                        'Duplicate entry found within request for country/state/province.'
                    ],
                ]);
            }
            $seen[$combo] = true;
        }

        // 4. Check for duplicates already in the database
        foreach ($rules as $key => $rule) {
            $exists = Shipping::where('country', $rule['country'])
                ->where('state', $rule['state'])
                ->where('province', $rule['province'])
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    "rules.$key.province" => [
                        "A shipping rule already exists for {$rule['country']} / {$rule['state']} / {$rule['province']}."
                    ],
                ]);
            }
        }

        // 5. Add timestamps and prepare for bulk insert
        $now = now();
        $rulesToInsert = array_map(function ($rule) use ($now) {
            $rule['country'] = uc_first($rule['country']);
            $rule['state'] = uc_first($rule['state']);
            $rule['province'] = uc_first($rule['province']);
            $rule['created_at'] = $now;
            $rule['updated_at'] = $now;
            return $rule;
        }, $rules);

        // 6. Bulk insert
        Shipping::insert($rulesToInsert);

        return response()->json(['message' => 'Shipping rules added successfully.'], 201);
    }

    public function update(Request $request, $id)
    {
        $rule = Shipping::find($id);
        if (!$rule) {
            return response()->json(['message' => 'Shipping rule not found.'], 404);
        }

        $validated = $request->validate([
            'state'     => 'sometimes|string',
            'province'  => 'sometimes|string',
            'min_days'  => 'sometimes|integer|min:1',
            'max_days'  => 'sometimes|integer|min:' . ($request->min_days ?? $rule->min_days),
            'cost'      => 'sometimes|numeric|min:0',
            'provider'  => 'nullable|string',
        ]);

        $rule->update($validated);

        return response()->json($rule);
    }
    
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action'   => 'required|string|in:delete,toggle',
            'ids'      => 'required|array|min:1',
            'ids.*'    => 'integer|exists:shippings,id',
        ]);

        $ids = $validated['ids'];
        $action = $validated['action'];

        if ($action === 'toggle') {
            // Flip is_active for all selected
            Shipping::whereIn('id', $ids)
                ->update(['is_active' => DB::raw('NOT is_active')]);

            return response()->json([
                'message' => 'Rules updated successfully',
                'action'  => 'toggle',
                'ids'     => $ids,
            ]);
        }

        if ($action === 'delete') {
            // Fetch rules in one query
            $rules = Shipping::whereIn('id', $ids)->get();

            $softDeleteIds = [];
            $hardDeleteIds = [];

            foreach ($rules as $rule) {
                $inUse = ShippingAddress::where('country', $rule->country)
                    ->where('state', $rule->state)
                    ->where('province', $rule->province)
                    ->exists();

                if ($inUse) {
                    $softDeleteIds[] = $rule->id;
                } else {
                    $hardDeleteIds[] = $rule->id;
                }
            }

            // Perform soft deletes
            if (!empty($softDeleteIds)) {
                Shipping::whereIn('id', $softDeleteIds)->delete();
            }

            // Perform hard deletes
            if (!empty($hardDeleteIds)) {
                Shipping::withTrashed()->whereIn('id', $hardDeleteIds)->forceDelete();
            }

            return response()->json([
                'message' => 'Bulk delete completed',
                'soft_deleted' => $softDeleteIds,
                'hard_deleted' => $hardDeleteIds,
            ]);
        }

        return response()->json(['message' => 'Invalid action'], 400);
    }


    // Admin: Delete rule
    // public function destroy($id)
    // {
    //     $rule = Shipping::find($id);
    //     if (!$rule) {
    //         return response()->json(['message' => 'Shipping rule not found.'], 404);
    //     }
    
    //     // Check if in use (shipping_addresses table)
    //     $inUse = ShippingAddress::where('country', $rule->country)
    //         ->where('state', $rule->state)
    //         ->where('province', $rule->province)
    //         ->exists();
    
    //     if ($inUse) {
    //         // Soft delete if in use
    //         $rule->delete(); // soft delete
    //         return response()->json([
    //             'message' => 'Rule is in use and has been set to inactive.'
    //         ]);
    //     } else {
    //         // Hard delete if not in use
    //         $rule->forceDelete();
    //         return response()->json([
    //             'message' => 'Rule deleted permanently.'
    //         ]);
    //     }
    // }    
    
    // public function toggleActive($id)
    // {
    //     $rule = Shipping::findOrFail($id);
    
    //     $rule->is_active = ! $rule->is_active;
    //     $rule->save();
    
    //     return response()->json([
    //         'message' => 'Rule updated successfully',
    //         'is_active' => $rule->is_active,
    //     ]);
    // }
}
