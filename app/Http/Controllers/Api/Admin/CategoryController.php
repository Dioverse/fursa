<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->query('sub') === 'true' && $request->query('category_id')) {
            // Only subcategories (parent_id not null)
            $categories = Category::withCount('subcategories')
                ->where('parent_id', $request->query('category_id'))
                ->whereNotNull('parent_id')
                ->get(['id', 'name', 'slug', 'parent_id']);
        } else {
            // Only parent categories with their subcategories
            $categories = Category::with(['subcategories' => function ($query) {
                    $query->withCount('products');
                }])
                ->withCount('products')
                ->whereNull('parent_id')
                ->get(['id', 'name', 'slug']);
        }

        return response()->json([
            'message' => 'Categories retrieved successfully.',
            'data'    => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $imagePath = null;

        if ($request->hasFile('image')) {
            // Store image in storage/app/public/categories
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
            'image'       => $imagePath, // make sure "image" column exists in categories table
        ]);

        return response()->json([
            'message' => 'Category created successfully.',
            'data'    => $category,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found.',
            ], 404);
        }

        return response()->json([
            'message' => 'Category retrieved successfully.',
            'data'    => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'sometimes|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        if ($request->filled('name')) {
            $category->name = $request->name;
            $category->slug = Str::slug($request->name);
        }

        if ($request->filled('description')) {
            $category->description = $request->description;
        }

        if ($request->filled('parent_id')) {
            $category->description = $request->description;
        }

        if ($request->hasFile('image')) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            $path = $request->file('image')->store('categories', 'public');
            $category->image = $path;
        }

        $category->save();

        return response()->json([
            'message' => 'Category updated successfully.',
            'data'    => $category,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found.',
            ], 404);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully.',
        ]);
    }
}
