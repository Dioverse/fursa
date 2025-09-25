<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ShippingAddressController extends Controller
{
     /**
     * Display a listing of the user's shipping addresses.
     */
    public function index()
    {
        $addresses = Auth::user()->shippingAddress()->orderBy("is_default", "desc")->get();

        return response()->json([
            'message' => 'Shipping addresses retrieved successfully.',
            'data' => $addresses,
        ]);
    }

    /**
     * Store a newly created shipping address.
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name'        => 'required|string|max:255',
            'phone'            => 'required|string|max:20',
            'address_line_one' => 'required|string|max:255',
            'address_line_two' => 'nullable|string|max:255',
            'city'             => 'required|string|max:100',
            'province'         => ['required', Rule::exists('shippings', 'province')->where('is_active', true)],
            'state'       => ['required', Rule::exists('shippings', 'state')->where('is_active', true)],
            'postal_code' => 'nullable|string|max:20',
            'country'     => ['required', Rule::exists('shippings', 'country')->where('is_active', true)],
            'is_default'  => 'boolean',
        ]);

        $user = Auth::user();

        $address = DB::transaction(function () use ($request, $user) {
            $ships = $user->shippingAddress();

            // If the new address is set as default, reset others
            if ($request->boolean('is_default')) {
                $ships->update(['is_default' => false]);
            } else {
                // Ensure at least one default exists
                if ($ships->where('is_default', true)->doesntExist()) {
                    $request->merge(['is_default' => true]);
                }
            }

            return $ships->create($request->only([
                'full_name','phone','address_line_one',
                'address_line_two','province','city',
                'state','postal_code','country','is_default'
            ]));
        });

        return response()->json([
            'message' => 'Shipping address created successfully.',
            'data'    => $address,
        ], 201);
    }

    /**
     * Display a specific shipping address.
     */
    public function show($id)
    {
        $address = Auth::user()->shippingAddress()->where('id', $id)->first();
        if (!$address) {
            return response()->json(['message' => 'Shipping address not found'], 404);
        }

        return response()->json([
            'message' => 'Shipping address retrieved successfully.',
            'data' => $address,
        ]);
    }

    /**
     * Update a shipping address.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $address = $user->shippingAddress()->where('id', $id)->first();

        if (! $address) {
            return response()->json(['message' => 'Shipping address not found'], 404);
        }

        $request->validate([
            'full_name'        => 'sometimes|string|max:255',
            'phone'            => 'sometimes|string|max:20',
            'address_line_one' => 'sometimes|string|max:255',
            'address_line_two' => 'nullable|string|max:255',
            'city'             => 'sometimes|string|max:100',
            // 'province'         => 'sometimes|string|exists:shippings,province',
            'province'         => ['required', Rule::exists('shippings', 'province')->where('is_active', true)],
            'state'       => ['required', Rule::exists('shippings', 'state')->where('is_active', true)],
            'postal_code' => 'nullable|string|max:20',
            'country'     => ['required', Rule::exists('shippings', 'country')->where('is_active', true)],
            'is_default'       => 'boolean',
        ]);

        DB::transaction(function () use ($request, $user, $address) {
            $ships = $user->shippingAddress();

            if ($request->boolean('is_default')) {
                // Reset other defaults
                $ships->update(['is_default' => false]);
            } else {
                // Ensure at least one default exists
                if ($ships->where('is_default', true)->where('id', '!=', $address->id)->doesntExist()) {
                    $request->merge(['is_default' => true]);
                }
            }

            $address->update($request->only([
                'full_name','phone','address_line_one',
                'address_line_two','province','city',
                'state','postal_code','country','is_default'
            ]));
        });

        return response()->json([
            'message' => 'Shipping address updated successfully.',
            'data'    => $address->fresh(), // return updated values
        ]);
    }

    /**
     * Remove a shipping address.
     */
    public function destroy($id)
    {
        $address = Auth::user()->shippingAddress()->where('id', $id)->first();
        if (!$address) {
            return response()->json(['message' => 'Shipping address not found'], 404);
        }

        $address->delete();

        return response()->json([
            'message' => 'Shipping address deleted successfully.',
        ]);
    }

    public function setDefaultAddress(Request $request, $id)
    {
        $user = Auth::user();

        // Find the address belonging to the user
        $address = $user->shippingAddress()->where('id', $id)->first();

        if (!$address) {
            return response()->json(['message' => 'Address not found.'], 404);
        }

        // Use transaction for atomic update
        DB::transaction(function () use ($user, $address) {

            // Set current default to false
            $user->shippingAddress()->where('is_default', true)->update(['is_default' => false]);

            // Set selected address as default
            $address->update(['is_default' => true]);
        });

        return response()->json([
            'message' => 'Default shipping address updated successfully.',
            'data' => $address
        ]);
    }
}
