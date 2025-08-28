<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['category','author'])
            ->latest()->paginate(10);
    
        return response()->json([
            "message" => "Posts retrieved successfully",
            'data' => $posts
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'body'      => 'required',
            'post_category_id' => 'nullable|exists:post_categories,id',
            'featured_image'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['title','body','post_category_id']);
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

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $request->validate([
            'title'     => 'sometimes|string|max:255',
            'body'      => 'sometimes',
            'post_category_id' => 'nullable|exists:post_categories,id',
            'featured_image'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['title','body','post_category_id']);
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
