<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // Start building the query with eager loading for the category
        $query = Product::with('category:id,name,slug');
        

        // --- Filtering Options ---
        // Filter by category
        if ($request->has('category') && is_string($request->input('category'))) {
            $query->whereRelation('category', 'slug', $request->input('category'));
        }

        // Filter by product name (partial match)
        if ($request->has('name') && is_string($request->input('name'))) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        // Filter by base_price range
        if ($request->has('min_price') && is_numeric($request->input('min_price'))) {
            $query->where('base_price', '>=', $request->input('min_price'));
        }
        if ($request->has('max_price') && is_numeric($request->input('max_price'))) {
            $query->where('base_price', '<=', $request->input('max_price'));
        }

        // Filter by stock_quantity range
        if ($request->has('min_stock') && is_numeric($request->input('min_stock'))) {
            $query->where('stock_quantity', '>=', $request->input('min_stock'));
        }
        if ($request->has('max_stock') && is_numeric($request->input('max_stock'))) {
            $query->where('stock_quantity', '<=', $request->input('max_stock'));
        }

        // // --- Filter by Tags ---
        // // Expects a comma-separated string of tags, e.g., ?tags=electronics,smart-home
        // if ($request->has('tags') && is_string($request->input('tags'))) {
        //     $requestedTags = explode(',', $request->input('tags'));

        //     // We'll use a nested 'where' clause with 'orWhereJsonContains'
        //     // to find products that have ANY of the requested tags.
        //     $query->where(function ($q) use ($requestedTags) {
        //         foreach ($requestedTags as $tag) {
        //             $q->orWhereJsonContains('tags', trim($tag)); // trim to remove any whitespace around tags
        //         }
        //     });
        // }

        // --- Pagination ---
        // Get the number of items per page from the request, default to 10 if not provided
        $perPage = $request->query('per_page', 10);
        // Ensure per_page is a positive integer
        $perPage = max(1, (int) $perPage);

        
        $products = $query->paginate($perPage);

        $stats = Product::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
            SUM(CASE WHEN stock_quantity > 0 AND stock_quantity <= low_stock_threshold THEN 1 ELSE 0 END) as low_stock
        ')->first();

        return response()->json([
            'message' => 'Products retrieved successfully.',
            'data' => $products,
            'stats'=>$stats
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
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
            'tags'                => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Create product
        $slug = Str::slug($request->name);
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
            'slug'                => $slug
        ]);

        // Handle multiple images
        if ($request->hasFile('images')) {
            $images = [];

            foreach ($request->file('images') as $file) {
                $fileName = $slug . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs('products', $fileName, 'public');

                $images[] = [
                    'path' => $imagePath,
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
        $product = Product::with('category:id,name,slug')->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        return response()->json([
            'message' => 'Product retrieved successfully.',
            'data' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'                => 'sometimes|string|max:255',
            'category_id'         => ['nullable', Rule::exists('categories', 'id')->where('id', '!=', $request->category_id)->whereNotNull('parent_id')],
            'short_description'   => 'nullable|string',
            'description'         => 'nullable|string',
            'base_price'          => 'sometimes|numeric|min:0',
            'distributor_price'   => 'sometimes|numeric|min:0',
            'stock_quantity'      => 'sometimes|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'tags'                => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only([
            'name','category_id','short_description','description',
            'base_price','distributor_price','stock_quantity',
            'low_stock_threshold','tags'
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

        if (!$product) {
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

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
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

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        // return response()->json($request->image_ids);
        $validator = Validator::make($request->all(), [
            'image_ids'   => 'required|array',
            'image_ids.*' => 'exists:product_images,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

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

    public function stock(Request $request)
    {
        $validated = $request->validate([
            'product_id'       => 'required|exists:products,id',
            'operation'           => 'required|integer|in:1,2,3',
            'quantity'         => 'required|integer|min:0',
            'update_threshold' => 'nullable|boolean',
            'low_stock_threshold'    => 'nullable|integer|min:0',
            // 'reason'           => 'required|string'
        ]);

        $product = Product::findOrFail($validated['product_id']);

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
        if (!empty($validated['update_threshold']) && isset($validated['low_stock_threshold'])) {
            $product->low_stock_threshold = $validated['low_stock_threshold'];
        }

        $product->save();

        return response()->json([
            'message' => 'Stock updated successfully.',
            'data' => [
                'product_id'          => $product->id,
                'stock_quantity'      => $product->stock_quantity,
                'low_stock_threshold' => $product->low_stock_threshold,
            ]
        ]);
    }

    public function toggleStatus($id)
    {
        $product = Product::where('id',$id)->first();
        if (!$product) {
            return response()->json([
                'message' => "Product not found."
            ]);
        }
        // Toggle ban (if 1 → 0, if 0 → 1)
        $product->status = !$product->status;
        $product->save();

        return response()->json([
            'message' => $product->status ? 'Product is now active' : 'Product is now inactive',
        ]);
    }
}
