<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ShippingAddressController extends Controller
{
     /**
     * Display a listing of the user's shipping addresses.
     */
    public function index()
    {
        $addresses = Auth::user()->shippingAddress()->get();

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
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line_one' => 'required|string|max:255',
            'address_line_two' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'is_default' => 'boolean',
        ]);

        // If new address is set as default, reset others
        if ($request->boolean('is_default')) {
            Auth::user()->shippingAddress()->update(['is_default' => false]);
        }

        $address = Auth::user()->shippingAddress()->create($request->all());

        return response()->json([
            'message' => 'Shipping address created successfully.',
            'data' => $address,
        ], 201);
    }

    /**
     * Display a specific shipping address.
     */
    public function show($id)
{
    try {
        $address = Auth::user()->shippingAddress()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $address
        ]);
    } catch (ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Shipping address not found for this user.'
        ], 404);
    }
}



    /**
     * Update a shipping address.
     */
    public function update(Request $request, $id)
    {
        $address = Auth::user()->shippingAddress()->findOrFail($id);

        $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'address_line_one' => 'sometimes|string|max:255',
            'address_line_two' => 'nullable|string|max:255',
            'city' => 'sometimes|string|max:100',
            'state' => 'sometimes|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'is_default' => 'boolean',
        ]);

        if ($request->boolean('is_default')) {
            Auth::user()->shippingAddress()->update(['is_default' => false]);
        }

        $address->update($request->all());

        return response()->json([
            'message' => 'Shipping address updated successfully.',
            'data' => $address,
        ]);
    }

    /**
     * Remove a shipping address.
     */
    public function destroy($id)
    {
        $address = Auth::user()->shippingAddress()->findOrFail($id);
        $address->delete();

        return response()->json([
            'message' => 'Shipping address deleted successfully.',
        ]);
    }
}
