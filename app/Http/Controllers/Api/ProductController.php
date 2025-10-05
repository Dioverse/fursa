<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // Start building the query with eager loading for the category
        $query = Product::with(['category:id,name,slug', 'images:id,product_id,path', 'discount:product_id,value,type']);

        // --- Filtering Options ---
        // Filter by category
        if ($request->filled('category')) {
            $categoryIds = $request->input('category');

            // Handle both comma-separated string and array
            if (is_string($categoryIds)) {
                $categoryIds = explode(',', $categoryIds);
            }

            $query->whereInRelation('category', 'id', $categoryIds);
        }

        // Filter by product name (partial match)
        if ($request->has('name') && is_string($request->input('name'))) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        // Filter by base_price range
        // Determine price field based on user role (if authenticated)
        $price_field = 'base_price';
        $user        = auth('sanctum')->user();

        if ($user && $user->isDistributorApprov()) {$price_field = 'distributor_price';}

        if ($request->has('min_price') && is_numeric($request->input('min_price'))) {
            $query->where($price_field, '>=', $request->input('min_price'));
        }
        if ($request->has('max_price') && is_numeric($request->input('max_price'))) {
            $query->where($price_field, '<=', $request->input('max_price'));
        }

        if ($request->has('sort_by')) {
            switch ($request->input('sort_by')) {
                case 'lp':
                    $query->orderBy($price_field, 'asc');
                    break;
                case 'hp':
                    $query->orderBy($price_field, 'desc');
                    break;
                case 'if':
                    $query->orderBy('is_featured', 'desc');
                    break;
            }
        }

        // --- Pagination ---
        // Get the number of items per page from the request, default to 10 if not provided
        $perPage = $request->query('per_page', 30);
        // Ensure per_page is a positive integer
        $perPage = max(1, (int) $perPage);

        $products   = $query->paginate($perPage);
        $categories = Category::with(['subcategories' => function ($query) {
            $query->select(['id', 'image', 'name', 'slug', 'parent_id'])->withCount('products');
        }])
            ->whereNull('parent_id')
            ->get(['id', 'image', 'name', 'slug']);

        return response()->json([
            'message' => 'Products retrieved successfully.',
            'data'    => [
                'products'   => $products,
                'categories' => $categories,
                'filters'    => [
                    'sort_by' => [
                        "Highest Price" => "hp",
                        "Lowest Price"  => "lp",
                        "Featured"      => "if",
                    ],
                ],
            ],
        ]);
    }

    public function shop(Request $request): JsonResponse
    {
        // --- Query params with defaults ---
        $featuredLimit  = (int) $request->query('featured_limit', 6);
        $productsPerCat = (int) $request->query('products_per_category', 7);
        $catGridLimit    = (int) $request->query('cat_grid_limit', 12);

        // Helper: select only required product fields
        $productFields = ['products.id', 'category_id', 'products.name', 'products.slug', 'short_description', 'base_price', 'distributor_price'];

        // --- Featured Products ---
        $featuredProducts = Product::select($productFields)
            ->with([
                'category:id,name,slug',
                'images' => function ($query) {
                    $query->select(['id', 'product_id', 'path'])->limit(1);
                },
                'discount:product_id,value,type',
            ])
            ->where('is_featured', true)->inRandomOrder()->take($featuredLimit)->get();

        // --- Random Categories with Products ---
        $categoriesWithProducts = Category::whereNull('parent_id')
            ->with([
                'productsBySubcats' => function ($query) use ($productsPerCat, $productFields) {
                    $query->select($productFields) // this applies to products table
                        ->with([
                            'category:id,slug',
                            'images' => function ($q) {
                                $q->select(['id', 'product_id', 'path'])->limit(1);
                            },
                            'discount:product_id,value,type',
                        ])
                        ->where('status', 1)
                        ->inRandomOrder()
                        ->take($productsPerCat);
                },
            ])
            ->get(['id', 'name', 'slug', 'image']);

        $categoryGrid = Category::select('name','slug','image','parent_id')->with('parent:id,slug')->whereNotNull('parent_id')->inRandomOrder()->take($catGridLimit)->get();

        $categories = Category::with(['subcategories' => function ($query) {
            $query->select(['id', 'image', 'name', 'slug', 'parent_id'])->withCount('products');
        }])
            ->whereNull('parent_id')
            ->get(['id', 'image', 'name', 'slug']);

        return response()->json([
            'message' => 'Shop data retrieved successfully.',
            'data'    => [
                'categories'               => $categories,
                'featured_products'        => $featuredProducts,
                'categories_with_products' => $categoriesWithProducts,
                'categoryGrid'             => $categoryGrid,
            ],
        ]);
    }

    public function cats(Request $request)
    {
        $sub      = $request->boolean('sub'); // auto-cast "true"/"false"
        $parentId = $request->query('category_id');

        if ($sub && $parentId) {
            $categories = Category::withCount('products')
                ->where('parent_id', $parentId)
                ->get(['id', 'name', 'description', 'image', 'slug', 'parent_id']);

        } elseif ($sub) {
            // Fetch specific parent with subcategories + product count
            $categories = Category::with([
                'subcategories' => function ($query) {
                    $query->select('id', 'name', 'description', 'image', 'slug', 'parent_id')
                        ->withCount('products');
                },
            ])
                ->withCount('subcategories')
                ->whereNull('parent_id')
                ->get(['id', 'name', 'description', 'image', 'slug']);
        } elseif ($parentId) {
            // Fetch specific parent with subcategories + product count
            $categories = Category::where("id", $parentId)->with([
                'subcategories' => function ($query) {
                    $query->select('id', 'name', 'parent_id', 'image')
                        ->withCount('products');
                },
            ])
                ->withCount('subcategories')
                ->whereNull('parent_id')
                ->get(['id', 'name', 'description', 'image', 'slug']);

        } else {
            // Fetch parent categories with subcategories + product count
            $categories = Category::withCount('subcategories')
                ->whereNull('parent_id')
                ->get(['id', 'name', 'description', 'image', 'slug']);
        }

        return response()->json([
            'message' => 'Categories retrieved successfully.',
            'data'    => $categories,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $product = Product::with(['category:id,name,slug', 'images:id,product_id,path'])->find($id);

        if (! $product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $related = Product::where(function ($q) use ($product) {
            $q->where('category_id', $product->category_id)
                ->orWhere('name', 'like', '%' . $product->name . '%');
        })->where('id', '!=', $product->id)
            ->inRandomOrder()->take(3)->with(['category:id,name,slug', 'images:id,product_id,path'])
            ->get(['id', 'name', 'sku', 'category_id', 'slug', 'short_description', 'stock_quantity', 'low_stock_threshold', 'is_featured', 'distributor_price', 'base_price']);

        return response()->json([
            'message' => 'Product retrieved successfully.',
            'data'    => $product,
            'related' => $related,
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
