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
                'cartItems' => function ($q) {
                    $q->select(['id', 'cart_id', 'product_id', 'quantity']);
                },
                'cartItems.product' => function ($q) {
                    $q->select([
                        'id', 'name', 'category_id', 'slug', 'stock_quantity', 'sku',
                        'low_stock_threshold', 'is_featured', 'distributor_price', 'base_price'
                    ])->with([
                        'category:id,name,slug,parent_id',
                        'discount:id,product_id,value,type',
                        'images' => fn($img) => $img->select('id', 'product_id', 'path')->limit(1),
                    ]);
                },
            ])
            ->first();

        return response()->json([
            'cart' => $cart ? $cart->cartItems : [],
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'cart' => 'required|array',
            'cart.*.product_id' => 'required|exists:products,id',
            'cart.*.quantity'   => 'required|integer|min:1',
        ]);

        $user = Auth::user();

        // Ensure the user has a cart
        $cart = $user->cart()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        $productIds = collect($validated['cart'])->pluck('product_id');

        // Fetch all existing items at once
        $existingItems = $cart->cartItems()
            ->whereIn('product_id', $productIds)
            ->get()
            ->keyBy('product_id');

        $toInsert = [];
        $toUpdate = [];

        foreach ($validated['cart'] as $item) {
            $productId = $item['product_id'];
            $quantity  = $item['quantity'];

            if ($existingItems->has($productId)) {
                // Prepare for update
                $existingItem = $existingItems[$productId];
                $existingItem->quantity = $quantity;
                $toUpdate[] = $existingItem;
            } else {
                // Prepare for insert
                $toInsert[] = [
                    'cart_id'    => $cart->id,
                    'product_id' => $productId,
                    'quantity'   => $quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // ✅ Batch update (via collection)
        if (!empty($toUpdate)) {
            foreach ($toUpdate as $item) {
                $item->save();
            }
        }

        // ✅ Batch create
        if (!empty($toInsert)) {
            $cart->cartItems()->insert($toInsert);
        }

        // Return updated cart
        $cart = $this->cartWithRelations($cart);

        return response()->json([
            'message' => 'Cart updated successfully',
            'cart'    => $cart->cartItems,
        ], 201);
    }


    public function unSetItem(Request $request)
    {
        $user = Auth::user();
        $cart = $user->cart()->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $cartItem = $cart->cartItems()->where('product_id', $request->product_id)->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Item not found in cart'], 404);
        }

        $cartItem->delete();

        $cart = $this->cartWithRelations($cart);

        return response()->json([
            'message' => 'Item removed from cart successfully',
            'cart'    => $cart->cartItems,
        ], 200);
    }

    public function unSetAllItem(Request $request)
    {
        $user = Auth::user();
        $cart = $user->cart()->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $cart->delete();

        $cart = $this->cartWithRelations($cart);

        return response()->json([
            'message' => 'Cart cleared successfully',
            'cart'    => $cart->cartItems,
        ], 200);
    }

    public function updateItemQuantity(Request $request)
    {
        if (!is_int((int) $request->quantity)) {
            return response()->json(['message' => "Invalid quantity entered"], 422);
        }

        $user = Auth::user();
        $cart = $user->cart()->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $cartItem = $cart->cartItems()->where('product_id', $request->product_id)->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Item not found in cart'], 404);
        }

        // Check stock availability
        $product = $cartItem->product;
        if ($request->quantity > $product->stock_quantity) {
            return response()->json([
                'message' => "Only {$product->stock_quantity} units available currently."
            ], 422);
        }

        // Remove item if quantity set to 0
        if ($request->quantity == 0) {
            $cartItem->delete();

            $cart = $this->cartWithRelations($cart);

            return response()->json([
                'message' => 'Item removed from cart (quantity set to 0)',
                'cart'    => $cart->cartItems,
            ]);
        }

        // Otherwise, update the quantity
        $cartItem->update(['quantity' => $request->quantity]);

        $cart = $this->cartWithRelations($cart);

        return response()->json([
            'message' => 'Cart item updated successfully',
            'cart'    => $cart->cartItems,
        ]);
    }

    public function syncUserCart(User $user, array $cartData): \Illuminate\Support\Collection
    {
        // 1. Ensure the user has a cart, creating it if necessary.
        $cart = $user->cart()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        $productIds = collect($cartData)->pluck('product_id');

        // 2. Fetch all existing items for the relevant products in one query.
        $existingItems = $cart->cartItems()
            ->whereIn('product_id', $productIds)
            ->get()
            ->keyBy('product_id');

        $toInsert = [];
        $toUpdate = [];

        // 3. Separate items into update and insert arrays.
        foreach ($cartData as $item) {
            $productId = $item['product_id'];
            $quantity  = $item['quantity'];

            if ($existingItems->has($productId)) {
                // Item exists: Prepare for update (directly modify the model instance)
                $existingItem = $existingItems[$productId];
                
                // Only update if the quantity has actually changed
                if ((int)$existingItem->quantity !== (int)$quantity) {
                     $existingItem->quantity = $quantity;
                     $toUpdate[] = $existingItem;
                }
            } else {
                // Item does not exist: Prepare for batch insert
                $toInsert[] = [
                    'cart_id'    => $cart->id,
                    'product_id' => $productId,
                    'quantity'   => $quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        // 4. Execute persistence operations within a transaction.
        DB::transaction(function () use ($toUpdate, $toInsert, $cart) {
            // Batch update existing items (uses individual saves, better suited for smaller updates)
            if (!empty($toUpdate)) {
                foreach ($toUpdate as $item) {
                    $item->save();
                }
            }
            
            // Batch create new items (efficient single query)
            if (!empty($toInsert)) {
                $cart->cartItems()->insert($toInsert);
            }
        });

        // 5. Refresh the cart model to ensure all relations (cartItems) are up-to-date
        // before returning the collection of items.
        return $this->cartWithRelations($cart)->cartItems;
    }

    /**
     * Helper method: load full relations for a cart.
     */
    private function cartWithRelations($cart)
    {
        return $cart->load([
            'cartItems' => fn($q) => $q->select(['id', 'cart_id', 'product_id', 'quantity']),
            'cartItems.product' => fn($q) => $q->select([
                'id', 'name', 'category_id', 'slug', 'stock_quantity', 'sku',
                'low_stock_threshold', 'is_featured', 'distributor_price', 'base_price'
            ])->with([
                'category:id,name,slug,parent_id',
                'discount:id,product_id,value,type',
                'images' => fn($q2) => $q2->select('id', 'product_id', 'path')->limit(1),
            ]),
        ]);
    }

}
