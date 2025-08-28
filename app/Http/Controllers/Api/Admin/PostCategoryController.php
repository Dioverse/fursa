<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            'name' => 'required|string|max:255|unique:post_categories,name',
        ]);

        $category = PostCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . uniqid(),
        ]);

        return response()->json([
            'message' => "Category created successfully",
            'data' => $category
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
        $postCategory = PostCategory::where('id', $id)->first();
        if (!$postCategory) {
            return response()->json(["message"=>"Category not found"], 404);
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:post_categories,name,' . $id,
        ]);

        $postCategory->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . uniqid(),
        ]);

        return response()->json([
            'message' => "Category updated successfully",
            'data' => $postCategory
        ]);
    }

    /**
     * Remove the specified category.
     */
    public function destroy($id)
    {
        $postCategory = PostCategory::where('id', $id)->first();
        if (!$postCategory) {
            return response()->json(["message"=>"Category not found"], 404);
        }

        $postCategory->delete();

        return response()->json([
            'message' => "Category deleted successfully"
        ], 201);
    }
}
