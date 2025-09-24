<?php

namespace App\Http\Controllers\Api;

use App\Models\Wishlist;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $wishlist = $user->wishlist()
            ->with([
                'wishlistItems' => function ($query) {
                    $query->select(['id', 'wishlist_id', 'product_id']);
                },
                'wishlistItems.product' => function ($query) {
                    $query->select(['id','name','category_id','slug','stock_quantity',
                        'low_stock_threshold','is_featured','distributor_price','base_price'
                    ]);
                },'wishlistItems.product.images' => function ($query) {
                    $query->select(['product_id','path']);
                }
            ])
            ->first();

        return response()->json($wishlist?->wishlistItems ?? []);
    }

    /**
     * Add item to wishlist (user or guest).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'wishlist' => 'required|array',
            'wishlist.*.product_id' => 'required|exists:products,id',
        ]);

        $user = Auth::user();

        // Ensure the user has a wishlist (or create it)
        $wishlist = $user->wishlist()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        $nm = 0;
        foreach ($validated['wishlist'] as $item) {
            $nm+=1;
            $wishlist->wishlistItems()->updateOrCreate(
                ['product_id' => $item['product_id']]
            );
        }

        return response()->json([
            'message' => ($nm > 1 ? 'Items': 'Item') . ' added to wishlist successfully',
        ], 201);
    }


    public function unSetItem(Request $request)
    {
        $user = Auth::user();
        $wishlist = $user->wishlist()->first();

        if (!$wishlist) {
            return response()->json([
                'message' => 'Wishlist not found'
            ], 404);
        }

        $wishlistItem = $wishlist->wishlistItems()->where('product_id', $request->product_id)->first();

        if (!$wishlistItem) {
            return response()->json([
                'message' => 'Item not found in wishlist'
            ], 404);
        }

        $wishlistItem->delete();

        return response()->json([
            'message' => 'Item removed from wishlist successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
    }
}
