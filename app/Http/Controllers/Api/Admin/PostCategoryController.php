<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Support\Str;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PostCategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $postCategories = PostCategory::withCount('posts')
            ->orderBy('name')
            ->paginate(10);
    
        return response()->json([
            "message" => "Categories retrieved successfully",
            'data' => $postCategories
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255|unique:post_categories,name',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // optional image validation
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public'); // store in storage/app/public/categories
        }

        $category = PostCategory::create([
            'name'  => $request->name,
            'slug'  => Str::slug($request->name),
            'image' => $imagePath, // can be null
        ]);

        return response()->json([
            'message' => "Category created successfully",
            'data'    => $category
        ], 201);
    }

    /**
     * Display the specified category.
     */
    public function show($id)
    {
        $postCategory = PostCategory::where('id', $id)->first();
        if (!$postCategory) {
            return response()->json(["message"=>"Category not found"], 404);
        }
        $postCategory->load('posts:id,title,slug,excerpt,featured_image,');

        // Return the category and its posts as a JSON response
        return response()->json([
            'message' => "Category details retrieved successfully",
            'data' => $postCategory
        ]);
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, $id)
    {
        $postCategory = PostCategory::find($id);

        if (!$postCategory) {
            return response()->json(["message" => "Category not found"], 404);
        }

        $request->validate([
            'name'  => 'required|string|max:255|unique:post_categories,name,' . $id,
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePath = $postCategory->image;

        // if new image uploaded, delete old one and replace
        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $postCategory->update([
            'name'  => $request->name,
            'slug'  => Str::slug($request->name),
            'image' => $imagePath,
        ]);

        return response()->json([
            'message' => "Category updated successfully",
            'data'    => $postCategory
        ]);
    }

    /**
     * Remove the specified category.
     */
    public function destroy($id)
    {
        $postCategory = PostCategory::find($id);

        if (!$postCategory) {
            return response()->json(["message" => "Category not found"], 404);
        }

        // delete stored image if exists
        if ($postCategory->image && Storage::disk('public')->exists($postCategory->image)) {
            Storage::disk('public')->delete($postCategory->image);
        }

        $postCategory->delete();

        return response()->json([
            'message' => "Category deleted successfully"
        ]);
    }
}
