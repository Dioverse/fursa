<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
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
        return response()->json([
            'message' => 'Products retrieved successfully.',
            'data' => $products,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'               => 'required|string|max:255',
            'category_id'        => 'nullable|exists:categories,id',
            'short_description'  => 'nullable|string',
            'description'        => 'nullable|string',
            'base_price'         => 'required|numeric|min:0',
            'distributor_price'  => 'required|numeric|min:0',
            'image'              => 'nullable|url',
            'stock_quantity'     => 'required|integer|min:0',
            'low_stock_threshold'=> 'nullable|integer|min:0',
            'tags'               => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $product = Product::create([
            'name'                => $request->name,
            'category_id'         => $request->category_id,
            'short_description'   => $request->short_description,
            'description'         => $request->description,
            'base_price'          => $request->base_price,
            'distributor_price'   => $request->distributor_price,
            'image'               => $request->image,
            'stock_quantity'      => $request->stock_quantity,
            'low_stock_threshold' => $request->low_stock_threshold,
            'tags'                => $request->tags,
        ]);

        return response()->json([
            'message' => 'Product created successfully.',
            'data' => $product,
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
            'name'               => 'sometimes|string|max:255',
            'category_id'        => 'nullable|exists:categories,id',
            'short_description'  => 'nullable|string',
            'description'        => 'nullable|string',
            'base_price'         => 'sometimes|numeric|min:0',
            'distributor_price'  => 'sometimes|numeric|min:0',
            'image'              => 'nullable|url',
            'stock_quantity'     => 'sometimes|integer|min:0',
            'low_stock_threshold'=> 'nullable|integer|min:0',
            'tags'               => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product->update($request->only(['name','category_id','short_description','description','base_price','distributor_price','image','stock_quantity','low_stock_threshold','tags']));
        return response()->json([
            'message' => 'Product updated successfully.',
            'data' => $product,
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

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.']);
    }
}
