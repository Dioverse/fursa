<?php
namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    // public function index(Request $request): JsonResponse
    // {
    //     $query = Product::with([
    //         'category:id,name,slug,parent_id',
    //         'discount:id,product_id,value,type',
    //         'images' => fn($q) => $q->select('id', 'product_id', 'path')->limit(1),
    //     ]);

    //     // --- CATEGORY FILTER (using slug) ---
    //     if ($request->filled('category')) {
    //         $categorySlug = $request->input('category');
    //         $category = Category::with('subcategories:id,parent_id,slug')
    //             ->where('slug', $categorySlug)
    //             ->first();

    //         if ($category) {
    //             // Parent category: include products from subcategories too
    //             if (is_null($category->parent_id)) {
    //                 $subcategoryIds = $category->subcategories->pluck('id')->toArray();

    //                 $query->where(function ($q) use ($category, $subcategoryIds) {
    //                     $q->where('category_id', $category->id)
    //                     ->orWhereIn('category_id', $subcategoryIds);
    //                 });
    //             } else {
    //                 // Subcategory: only its own products
    //                 $query->where('category_id', $category->id);
    //             }
    //         }
    //     }

    //     // --- NAME FILTER ---
    //     if ($request->filled('name')) {
    //         $query->where('name', 'like', '%' . $request->input('name') . '%');
    //     }

    //     // --- TAGS FILTER ---
    //     if ($request->filled('tags')) {
    //         $tags = array_map('trim', explode(',', $request->input('tags')));
            
    //         $query->where(function ($q) use ($tags) {
    //             foreach ($tags as $tag) {
    //                 $q->orWhereJsonContains('tags', $tag);
    //             }
    //         });
    //     }

    //     // --- PRICE FILTERS ---
    //     $user = auth('sanctum')->user();
    //     $priceField = ($user && $user->isDistributorApprov()) ? 'distributor_price' : 'base_price';

    //     if ($request->filled('min_price') && is_numeric($request->min_price)) {
    //         $query->where($priceField, '>=', $request->min_price);
    //     }
    //     if ($request->filled('max_price') && is_numeric($request->max_price)) {
    //         $query->where($priceField, '<=', $request->max_price);
    //     }

    //     // --- SORTING ---
    //     if ($request->filled('sort_by')) {
    //         switch ($request->sort_by) {
    //             case 'lp': $query->orderBy($priceField, 'asc'); break;
    //             case 'hp': $query->orderBy($priceField, 'desc'); break;
    //             case 'if': $query->orderBy('is_featured', 'desc'); break;
    //         }
    //     } else {
    //         // Default order by latest
    //         $query->latest('id');
    //     }

    //     // --- PAGINATION ---
    //     $perPage = max(1, (int) $request->query('per_page', 30));
    //     $products = $query->paginate($perPage, [
    //         'id','category_id','name','slug','sku',
    //         'short_description','stock_quantity',
    //         'low_stock_threshold','is_featured',
    //         'distributor_price','base_price',
    //     ]);

    //     // --- FETCH UNIQUE TAGS FOR FILTER LIST ---
    //     // Note: This relies on your 'tags' column being a JSON field.
    //     $uniqueTags = DB::table('products')
    //         ->whereNotNull('tags')
    //         ->pluck('tags')
    //         ->flatMap(function ($tagArray) {
    //             // Laravel's pluck will return a JSON string, so decode it
    //             return json_decode($tagArray, true) ?? [];
    //         })
    //         ->unique()
    //         ->values()
    //         ->all();


    //     // --- CATEGORY LIST (with subcategories) ---
    //     $categories = Category::with(['subcategories' => function ($query) {
    //         $query->select(['id', 'icon', 'name', 'slug', 'parent_id'])
    //             ->withCount('products');
    //     }])
    //     ->whereNull('parent_id')
    //     ->get(['id', 'icon', 'name', 'slug']);

    //     // --- RESPONSE ---
    //     return response()->json([
    //         'message' => 'Products retrieved successfully.',
    //         'data' => [
    //             'products' => $products,
    //             'categories' => $categories,
    //             'available_tags' => $uniqueTags, // <-- NEW LIST OF TAGS
    //             'filters' => [
    //                 'sort_by' => [
    //                     'Highest Price' => 'hp',
    //                     'Lowest Price'  => 'lp',
    //                     'Featured'      => 'if',
    //                 ],
    //             ],
    //         ],
    //     ]);
    // }


    public function index(Request $request): JsonResponse
    {
        $query = Product::with([
            'category:id,name,slug,parent_id',
            'discount:id,product_id,value,type',
            'images' => fn($q) => $q->select('id', 'product_id', 'path')->limit(1),
        ]);

        // --- CATEGORY FILTER (using slug) ---
        if ($request->filled('category')) {
            $categorySlug = $request->input('category');
            $category = Category::with('subcategories:id,parent_id,slug')
                ->where('slug', $categorySlug)
                ->first();

            if ($category) {
                // Parent category: include products from subcategories too
                if (is_null($category->parent_id)) {
                    $subcategoryIds = $category->subcategories->pluck('id')->toArray();

                    $query->where(function ($q) use ($category, $subcategoryIds) {
                        $q->where('category_id', $category->id)
                            ->orWhereIn('category_id', $subcategoryIds);
                    });
                } else {
                    // Subcategory: only its own products
                    $query->where('category_id', $category->id);
                }
            }
        }

        // --- NAME FILTER ---
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        // --- TAGS FILTER ---
        if ($request->filled('tags')) {
            $tags = array_map('trim', explode(',', $request->input('tags')));
            
            $query->where(function ($q) use ($tags) {
                foreach ($tags as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            });
        }

        // --- PRICE FILTERS ---
        $user = auth('sanctum')->user();
        $priceField = ($user && $user->isDistributorApproved()) ? 'distributor_price' : 'base_price';

        if ($request->filled('min_price') && is_numeric($request->min_price)) {
            $query->where($priceField, '>=', $request->min_price);
        }
        if ($request->filled('max_price') && is_numeric($request->max_price)) {
            $query->where($priceField, '<=', $request->max_price);
        }

        // --- SORTING ---
        if ($request->filled('sort_by')) {
            switch ($request->sort_by) {
                case 'lp': $query->orderBy($priceField, 'asc'); break;
                case 'hp': $query->orderBy($priceField, 'desc'); break;
                case 'if': $query->orderBy('is_featured', 'desc'); break;
            }
        } else {
            // Default order by latest
            $query->latest('id');
        }

        // --- PAGINATION ---
        $perPage = max(1, (int) $request->query('per_page', 30));
        $products = $query->paginate($perPage, [
            'id','category_id','name','slug','sku',
            'short_description','stock_quantity',
            'low_stock_threshold','is_featured',
            'distributor_price','base_price',
        ]);

        // --- FETCH UNIQUE TAGS FOR FILTER LIST ---
        // Note: This relies on your 'tags' column being a JSON field.
        $uniqueTags = DB::table('products')
            ->whereNotNull('tags')
            ->pluck('tags')
            ->flatMap(function ($tagArray) {
                // Laravel's pluck will return a JSON string, so decode it
                return json_decode($tagArray, true) ?? [];
            })
            ->unique()
            ->sort()
            ->values()
            ->all();

        // --- CATEGORY LIST (with subcategories) ---
        $categories = Category::with(['subcategories' => function ($query) {
            $query->select(['id', 'icon', 'name', 'slug', 'parent_id'])
                ->withCount('products');
        }])
        ->whereNull('parent_id')
        ->get(['id', 'icon', 'name', 'slug']);

        // --- RESPONSE ---
        return response()->json([
            'message' => 'Products retrieved successfully.',
            'data' => [
                'products' => $products,
                'categories' => $categories,
                'available_tags' => $uniqueTags,
                'filters' => [
                    'sort_by' => [
                        'Highest Price' => 'hp',
                        'Lowest Price'  => 'lp',
                        'Featured'      => 'if',
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
        $catGridLimit   = (int) $request->query('cat_grid_limit', 12);

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
            ->with(['subcategories:id,name,slug,parent_id'])
            ->get(['id', 'name', 'slug', 'image'])
            ->map(function ($category) use ($productsPerCat, $productFields) {
                // Collect category IDs: the category itself + its subcategories
                $catIds = collect([$category->id])
                    ->merge($category->subcategories->pluck('id'));

                // Fetch products for these IDs
                $products = Product::select($productFields)
                    ->with([
                        'category:id,name,slug',
                        'images:id,product_id,path',
                        'discount:product_id,value,type',
                    ])
                    ->whereIn('category_id', $catIds)
                    ->where('status', 1)
                    ->inRandomOrder()
                    ->take($productsPerCat)
                    ->get();

                // Attach them dynamically
                $category->setRelation('products', $products);

                return $category;
            });


        $categoryGrid = Category::whereNull('parent_id')
            ->inRandomOrder()
            ->take($catGridLimit)
            ->get(['id', 'name', 'slug', 'image'])
            ->map(function ($category) use ($productsPerCat, $productFields) {
                $catIds = collect([$category->id])
                    ->merge(Category::where('parent_id', $category->id)->pluck('id'));

                $products = Product::select($productFields)
                    ->with([
                        'category:id,name,slug',
                        'images:id,product_id,path',
                        'discount:product_id,value,type',
                    ])
                    ->whereIn('category_id', $catIds)
                    ->where('status', 1)
                    ->inRandomOrder()
                    ->take($productsPerCat)
                    ->get();

                $category->setRelation('products', $products);
                return $category;
            });


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
        $sub       = $request->boolean('sub');         // auto-cast "true"/"false"
        $slug      = $request->query('category_slug'); // <-- use slug instead of id
        $parentCat = null;

        if ($slug) {
            $parentCat = Category::where('slug', $slug)->first();
            if (! $parentCat) {
                return response()->json([
                    'message' => 'Category not found',
                    'data'    => [],
                ], 404);
            }
        }

        if ($sub && $parentCat) {
            // Fetch subcategories of this parent by id
            $categories = Category::withCount('products')
                ->where('parent_id', $parentCat->id)
                ->get(['id', 'name', 'description', 'image', 'slug', 'parent_id']);

        } elseif ($sub) {
            // Fetch ALL parents with their subcategories + product count
            $categories = Category::with([
                'subcategories' => function ($query) {
                    $query->select('id', 'name', 'description', 'image', 'slug', 'parent_id')
                        ->withCount('products');
                },
            ])
                ->withCount('subcategories')
                ->whereNull('parent_id')
                ->get(['id', 'name', 'description', 'image', 'slug']);

        } elseif ($parentCat) {
            // Fetch specific parent with subcategories
            $categories = Category::where('id', $parentCat->id)
                ->with([
                    'subcategories' => function ($query) {
                        $query->select('id', 'name', 'description', 'image', 'slug', 'parent_id')
                            ->withCount('products');
                    },
                ])
                ->withCount('subcategories')
                ->first(['id', 'name', 'description', 'image', 'slug']); // use first() here for single

        } else {
            // Fetch all parent categories
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
    public function show(string $slug): JsonResponse
    {
        $product = Product::with(['category:id,name,slug', 'images:id,product_id,path', 'discount:product_id,typevalue'])->where("slug", $slug)->first();

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
