<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::query()
            ->with(['category:id,name,slug', 'author:id,first_name,last_name']) // eager load relations
            ->where('published', true);

        // --- Search filter ---
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        // --- Multiple categories filter ---
        if ($request->filled('categories')) {
            $categoryIds = $request->input('categories');

            if (is_string($categoryIds)) {
                $categoryIds = explode(',', $categoryIds);
            }

            $query->whereIn('post_category_id', $categoryIds);
        }

        // --- Sort options ---
        if ($request->filled('sort')) {
            $request->sort == "oldest" ? $query->oldest() : $query->latest();
        } else {
            $query->latest();
        }

        // --- Pagination ---
        $perPage = $request->query('per_page', 10);
        $perPage = max(1, (int) $perPage);
        
        $posts = $query->paginate($perPage);
        $categories = PostCategory::orderBy('name')->get(["id","name","slug"]);
        // $tags = Post::pluck('tags')->flatten()->unique()->values();
        return response()->json([
            'message' => 'Posts retrieved successfully.',
            'posts' => $posts,
            'filters' => [
                'categories'    => $categories,
                'sort'          => ['latest','oldest','popular']
            ]
        ]);
    }

    /**
     * Show a single post
     */
    public function show($slug)
    {
        $post = Post::where('slug', $slug)->where("published", true)->first();
        if (!$post) {
            return response()->json(["message" => "Post not found"], 404);
        }

        $post->load(['category:id,name,slug', 'author:id,first_name,last_name']);

        $related = Post::where(function ($q) use ($post) {
            $q->where('post_category_id', $post->post_category_id)
            ->orWhere('title', 'like', '%' . $post->title . '%');
        })->where('id', '!=', $post->id)
        ->where('published', true)->inRandomOrder()->take(3)
        ->with(['category:id,name,slug', 'author:id,first_name,last_name'])->get();

        return response()->json([
            'message' => "Post details retrieved successfully",
            'data' => $post,
            'related' => $related
        ]);
    }
}