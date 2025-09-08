<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class GeneralController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $ordersSummary = [
            'total'      => $user->order()->count(),
            'pending'    => $user->order()->where('status', 'pending')->count(),
            'out_for_delivery' => $user->order()->where('status', 'out for delivery')->count(),
            'delivered'  => $user->order()->where('status', 'delivered')->count(),
            'cancelled'  => $user->order()->where('status', 'cancelled')->count(),
        ];
        $recentOrders = $user->order()->withCount('orderItem')->latest()->take(5)
            ->get(['id', 'status', 'total_amount', 'created_at']);

        $cartCount = $user->cartItems()->count();

        $recommendations = $this->personalizedRecommendations($user);

        return response()->json([
            'message' => 'Dashboard data retrieved successfully.',
            'data' => [
                'orders_summary'   => $ordersSummary,
                'recent_orders'    => $recentOrders,
                'cart_count'       => $cartCount,
                'recommendations'  => $recommendations,
            ],
        ]);
    }

    protected function personalizedRecommendations($user)
    {
        $purchasedProductIds = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $user->id)
            ->pluck('order_items.product_id');

        if ($purchasedProductIds->isEmpty()) {
            // fallback: random recommendations
            $recommendations = Product::class;
        } else {
            // 2. Find categories of purchased products
            $categoryIds = Product::whereIn('id', $purchasedProductIds)->pluck('category_id');

            $recommendations = Product::whereIn('category_id', $categoryIds);
        }

        return $recommendations->with(['category:id,name,slug','images:id,product_id,path'])
            ->inRandomOrder()->take(5)
            ->get(['id','name','category_id','slug','short_description','stock_quantity','low_stock_threshold','is_featured','distributor_price','base_price']);
    }
}
