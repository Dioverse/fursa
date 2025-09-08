<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category:id,name,slug']);

        // --- Filtering Options ---
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('stock_status')) {
            if ($request->input('stock_status') === 'out_of_stock') {
                $query->where('stock_quantity', '=', 0);
            } elseif ($request->input('stock_status') === 'low_stock') {
                $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                    ->where('stock_quantity', '>', 0);
            } elseif ($request->input('stock_status') === 'in_stock') {
                $query->where('stock_quantity', '>', 0);
            }
        }

        if ($request->filled('price_from')) {
            $query->where(function ($q) use ($request) {
                $q->where('base_price', '>=', $request->input('price_from'))
                ->orWhere('distributor_price', '>=', $request->input('price_from'));
            });
        }

        if ($request->filled('price_to')) {
            $query->where(function ($q) use ($request) {
                $q->where('base_price', '<=', $request->input('price_to'))
                ->orWhere('distributor_price', '<=', $request->input('price_to'));
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        if ($request->filled('featured')) {
            $query->where('is_featured', (bool) $request->input('featured'));
        }

        // Sorting
        $sortBy    = $request->query('sort_by', 'id');
        $sortOrder = $request->query('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage  = max(1, (int) $request->query('per_page', 10));
        $products = $query->paginate($perPage);

        // Stats
        $stats = Product::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
            SUM(CASE WHEN stock_quantity > 0 AND stock_quantity <= low_stock_threshold THEN 1 ELSE 0 END) as low_stock
        ')->first();

        $categories = Category::where('parent_id', null)->orWhere('parent_id', '')->with('subcategories:id,name,parent_id')->get(['id', 'name']);
        return response()->json([
            'message' => 'Products retrieved successfully.',
            'data'    => $products,
            'stats'   => $stats,
            'filters' => [
                'status'       => ['inactive' => 0, 'active' => 1],
                'stock_status' => ['in_stock', 'out_of_stock', 'low_stock'],
                'is_featured'  => ['not_featured' => 0, 'featured' => 1],
                'sort_by'      => [
                    'Created'           => 'created_at',
                    'Name'              => 'name',
                    'Price'             => 'base_price',
                    'Distributor Price' => 'distributor_price',
                    'Stock Quantity'    => 'stock_quantity',
                ],
                'sort_order'   => ['Ascending' => 'asc', 'Descending' => 'desc'],
                'categories'   => $categories,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'                => 'required|string|max:255|unique:products,name',
            'category_id'         => ['nullable', Rule::exists('categories', 'id')->whereNotNull('parent_id')],
            'short_description'   => 'nullable|string',
            'description'         => 'nullable|string',
            'base_price'          => 'required|numeric|min:0',
            'distributor_price'   => 'required|numeric|min:0',
            'images'              => 'nullable|array',
            'images.*'            => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'stock_quantity'      => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            // 'tags'                => 'nullable|array',
        ]);

        // Create product
        $slug    = Str::slug($request->name);
        $product = Product::create([
            'name'                => $request->name,
            'category_id'         => $request->category_id,
            'short_description'   => $request->short_description,
            'description'         => $request->description,
            'base_price'          => $request->base_price,
            'distributor_price'   => $request->distributor_price,
            'stock_quantity'      => $request->stock_quantity,
            'low_stock_threshold' => $request->low_stock_threshold,
            'tags'                => $request->tags,
            'slug'                => $slug,
        ]);

        // Handle multiple images
        if ($request->hasFile('images')) {
            $images = [];

            foreach ($request->file('images') as $file) {
                $fileName  = $slug . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs('products', $fileName, 'public');

                $images[] = [
                    'path'       => $imagePath,
                    'product_id' => $product->id, // required since bulk insert
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            // Bulk insert all images at once
            $product->images()->insert($images);
        }

        return response()->json([
            'message' => 'Product created successfully.',
            'data'    => $product->load('images'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $product = Product::with(['category:id,name,slug', 'activeDiscount:product_id,type,value,start_date,start_date'])->find($id);

        if (! $product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        return response()->json([
            'message' => 'Product retrieved successfully.',
            'data'    => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $product = Product::find($id);

        if (! $product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $request->validate([

            'name'                => 'sometimes|string|max:255',
            'category_id'         => ['nullable', Rule::exists('categories', 'id')->where('id', '!=',$request->category_id)->whereNotNull('parent_id')],
            'short_description'   => 'nullable|string',
            'description'         => 'nullable|string',
            'base_price'          => 'sometimes|numeric|min:0',
            'distributor_price'   => 'sometimes|numeric|min:0',
            'stock_quantity'      => 'sometimes|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            // 'tags'                => 'nullable|array',
        ]);

        $data = $request->only([
            'name', 'category_id', 'short_description', 'description',
            'base_price', 'distributor_price', 'stock_quantity',
            'low_stock_threshold', 'tags',
        ]);
        $data['slug'] = Str::slug($request->name);

        $product->update($data);

        return response()->json([
            'message' => 'Product updated successfully.',
            'data'    => $product->load('images'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $product = Product::find($id);

        if (! $product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }
        // Delete product image if exists
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return response()->json(['message' => 'Product deleted successfully.']);
    }

    /**
     * Add multiple images to product.
     */
    public function addImages(Request $request, string $id): JsonResponse
    {
        $product = Product::find($id);

        if (! $product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $request->validate([
            'images'   => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $imagesToInsert = [];
        $storedImages   = [];

        foreach ($request->file('images') as $file) {
            $slug      = Str::slug($product->name);
            $extension = $file->getClientOriginalExtension();
            $fileName  = $slug . '-' . uniqid() . '.' . $extension;

            $path = $file->storeAs('products', $fileName, 'public');

            $imagesToInsert[] = [
                'product_id' => $product->id,
                'path'       => $path,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $storedImages[] = [
                'path' => $path,
            ];
        }

        // Bulk insert
        ProductImage::insert($imagesToInsert);

        return response()->json([
            'message' => 'Images added successfully.',
            'data'    => $storedImages,
        ]);
    }

    /**
     * Delete specific images from product.
     */
    public function deleteImages(Request $request, string $id): JsonResponse
    {
        $product = Product::find($id);

        if (! $product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        // return response()->json($request->image_ids);
        $request->validate([

            'image_ids'   => 'required|array',
            'image_ids.*' => 'exists:product_images,id',
        ]);

        $deleted = [];

        foreach ($request->image_ids as $imageId) {
            $image = ProductImage::where('product_id', $product->id)->find($imageId);

            if ($image) {
                if (Storage::disk('public')->exists($image->path)) {
                    Storage::disk('public')->delete($image->path);
                }

                $image->delete();
                $deleted[] = $imageId;
            }
        }

        return response()->json([
            'message' => 'Selected images deleted successfully.',
            'deleted' => $deleted,
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
        ]);

        switch ($validated['operation']) {
            case 1: // Add
                $product->stock_quantity += $validated['quantity'];
                break;
            case 2: // Subtract
                $product->stock_quantity = max(0, $product->stock_quantity - $validated['quantity']);
                break;
            case 3: // Set
                $product->stock_quantity = $validated['quantity'];
                break;
        }

        // Update threshold if requested
        if (! empty($validated['update_threshold']) && isset($validated['low_stock_threshold'])) {
            $product->low_stock_threshold = $validated['low_stock_threshold'];
        }

        $product->save();

        return response()->json([
            'message' => 'Stock updated successfully.',
            'data'    => [
                'product_id'          => $id,
                'stock_quantity'      => $product->stock_quantity,
                'low_stock_threshold' => $product->low_stock_threshold,
            ],
        ]);
    }

    public function toggleStatus($id)
    {
        $product = Product::where('id', $id)->first();
        if (! $product) {
            return response()->json([
                'message' => "Product not found.",
            ]);
        }
        // Toggle ban (if 1 → 0, if 0 → 1)
        $product->status = !$product->status;
        $product->save();

        return response()->json([
            'message' => $product->status ? 'Product is now active' : 'Product is now inactive',
        ]);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action'        => 'required|string|in:activate,deactivate,feature,unfeature,delete',
            'product_ids'   => 'required|array|min:1',
            'product_ids.*' => 'integer|exists:products,id',
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

                        if ($inUse) {
                            $product->status = 0; // soft delete (inactive)
                            $product->save();
                        } else {
                            $product->delete();
                        }
                    }
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