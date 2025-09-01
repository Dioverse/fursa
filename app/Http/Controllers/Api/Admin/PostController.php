<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Post;
use Illuminate\Support\Str;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['category:id,name', 'author:id,first_name,last_name']);

        if ($request->filled('category_id')) {
            $categoryIds = is_array($request->category_id)
                ? $request->category_id
                : explode(',', $request->category_id);

            $query->whereIn('post_category_id', $categoryIds);
        }

        if ($request->filled('author')) {
            $query->whereHas('author', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->author . '%')
                ->orWhere('last_name', 'like', '%' . $request->author . '%');
            });
        }

        if ($request->filled('published')) {
            $query->where('published', filter_var($request->published, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('excerpt', 'like', "%{$search}%")
                ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $perPage = $request->query('per_page', 10);
        $perPage = max(1, (int) $perPage);
        // Default ordering = latest posts
        $posts = $query->latest()->paginate($perPage);

        $categories = PostCategory::orderBy('name')->get(["id","name"]);

        return response()->json([
            "message" => "Posts retrieved successfully",
            "data" => $posts,
            "filters" => [
                "categories" => $categories,
                "published" => ["true" => 1, "false" => 0]
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'body'      => 'required',
            'published' => 'required|boolean',
            'post_category_id' => 'required|exists:post_categories,id',
            'featured_image'   => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['title','body','published','post_category_id']);
        $data['slug'] = Str::slug($request->title).'-'.uniqid();
        $data['user_id'] = $request->user()->id;
        $data['excerpt'] = Str::limit(strip_tags($request->body), 150);

        if ($request->hasFile('featured_image')) {
            $extension = $request->file('featured_image')->getClientOriginalExtension();
            $filename  = $data['slug'] . '.' . $extension;
            $path = $request->file('featured_image')->storeAs('posts',$filename,'public');
            $data['featured_image'] = $path;
        }

        $post = Post::create($data);

        return response()->json([
            'message' => "Post created successfully",
            'data' => $post
        ], 201);
    }

    public function show($id)
    {
        $post = Post::where('id', $id)->first();
        if (!$post) {
            return response()->json(["message"=>"Post not found"], 404);
        }
        $post->load(['category:id,name,slug','author:id,first_name,last_name']);
        return response()->json([
            'message' => "Post details retrieved successfully",
            'data' => $post
        ]);
    }

    public function update(Request $request, $id)
    {
        $post = Post::where('id', $id)->first();
        if (!$post) {
            return response()->json(["message"=>"Post not found"], 404);
        }
        $request->validate([
            'title'     => 'sometimes|string|max:255',
            'body'      => 'sometimes',
            'published' => 'required|boolean',
            'post_category_id' => 'required|exists:post_categories,id',
            'featured_image'   => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['title','body','published','post_category_id']);
        if ($request->filled('title')) {
            $data['slug'] = Str::slug($request->title).'-'.uniqid();
        }

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }

            $data['featured_image'] = $request->file('featured_image')
                ->storeAs('posts', $data['slug'].'.'.$request->file('featured_image')->extension(), 'public');
        } else {
            if ($post->featured_image && Storage::disk('public')->exists($post->featured_image)) {
                $extension = pathinfo($post->featured_image, PATHINFO_EXTENSION);
                $newPath = 'posts/'.$data['slug'].'.'.$extension;

                Storage::disk('public')->move($post->featured_image, $newPath);
                $data['featured_image'] = $newPath;
            }
        }

        $post->update($data);

        return response()->json([
            'message' => "Post updated successfully",
            'data' => $post
        ], 201);
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        $post->delete();

        return response()->json(null, 204);
    }
}
