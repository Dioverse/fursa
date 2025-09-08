<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $cart = $user->cart()
            ->with([
                'cartItems' => function ($query) {
                    $query->select(['id', 'cart_id', 'product_id', 'quantity']);
                },
                'cartItems.product' => function ($query) {
                    $query->select(['id','name','category_id','slug','stock_quantity',
                        'low_stock_threshold','is_featured','distributor_price','base_price'
                    ]);
                },'cartItems.product.images' => function ($query) {
                    $query->select(['product_id','path']);
                }
            ])
            ->first();

        return response()->json($cart?->cartItems ?? []);
    }

    /**
     * Add item to cart (user or guest).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        // Ensure the user has a cart (or create it)
        $cart = $user->cart()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        // Add item (or update if product already exists in cart)
        $cartItem = $cart->cartItems()->updateOrCreate(
            ['product_id' => $validated['product_id']],
            ['quantity' => DB::raw("quantity + {$validated['quantity']}")]
        );

        return response()->json([
            'message'   => 'Item added to cart successfully',
        ], 201);
    }

    public function unSetItem(Request $request)
    {
        $user = Auth::user();
        $cart = $user->cart()->first();

        if (!$cart) {
            return response()->json([
                'message' => 'Cart not found'
            ], 404);
        }

        $cartItem = $cart->cartItems()->where('product_id', $request->product_id)->first();

        if (!$cartItem) {
            return response()->json([
                'message' => 'Item not found in cart'
            ], 404);
        }

        $cartItem->delete();

        return response()->json([
            'message' => 'Item removed from cart successfully'
        ], 200);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateItemQuantity(Request $request)
    {
        if (!is_int((int)$request->quantity)) { return response()->json(['message' => "Invalid quantity entered"], 422); }

        $user = Auth::user();
        $cart = $user->cart()->first();

        if (!$cart) {
            return response()->json([
                'message' => 'Cart not found'
            ], 404);
        }

        $cartItem = $cart->cartItems()->where('product_id', $request->product_id)->first();

        if (!$cartItem) {
            return response()->json([
                'message' => 'Item not found in cart'
            ], 404);
        }

        // Check stock availability
        $product = $cartItem->product;
        if ($request->quantity > $product->stock_quantity) {
            return response()->json([
                'message' => "Only {$product->stock_quantity} units available currently."
            ], 422);
        }

        // If quantity is 0, remove the item
        if ($request->quantity == 0) {
            $cartItem->delete();

            return response()->json([
                'message' => 'Item removed from cart (quantity set to 0)'
            ]);
        }

        // Otherwise update the quantity
        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'message' => 'Cart item updated successfully',
            'item'    => $cartItem->fresh()
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
    }
}
