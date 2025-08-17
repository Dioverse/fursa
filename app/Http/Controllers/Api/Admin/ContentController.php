<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Content;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ContentController extends Controller
{
    /**
     * Display a listing of the content.
     */
    public function index()
    {
        // Fetch all content items
        $contents = Content::all();
        return response()->json($contents);
    }

    /**
     * Store a newly created content item in storage.
     */
    public function store(Request $request)
    {
        // Define base validation rules
        $rules = [
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:text,image,testimonial,imageMultiple,textMultiple', // Added new types
            'data' => 'required|array', // Data should be a JSON object (PHP array)
        ];

        // Add specific validation rules for 'data' based on 'type'
        switch ($request->type) {
            case 'text':
                $rules['data.heading'] = 'required|string|max:255';
                $rules['data.body'] = 'required|string';
                $rules['data.buttonText'] = 'nullable|string|max:255';
                $rules['data.buttonLink'] = 'nullable|string|max:255';
                break;
            case 'image':
                $rules['data.imageUrl'] = 'required|url|max:2048'; // Validate as URL, not file
                $rules['data.caption'] = 'nullable|string|max:255';
                break;
            case 'testimonial':
                $rules['data.author'] = 'required|string|max:255';
                $rules['data.quote'] = 'required|string';
                $rules['data.authorTitle'] = 'nullable|string|max:255';
                $rules['data.avatarUrl'] = 'nullable|url|max:2048'; // Validate as URL
                break;
            case 'imageMultiple':
                $rules['data.*.imageUrl'] = 'required|url|max:2048'; // Array of objects, each with imageUrl
                $rules['data.*.caption'] = 'nullable|string|max:255';
                break;
            case 'textMultiple':
                $rules['data.*.title'] = 'required|string|max:255'; // Array of objects, each with title
                $rules['data.*.description'] = 'required|string';
                break;
            default:
                // No specific rules for other types, or add a generic rule
                break;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create new content item
        $content = Content::create([
            'title' => $request->title,
            'type' => $request->type,
            'data' => $request->data, // Eloquent will automatically cast this to JSON
        ]);

        return response()->json($content, 201); // 201 Created
    }

    /**
     * Display the specified content item.
     */
    public function show(string $id)
    {
        // Find content by ID, or return 404 if not found
        $content = Content::find($id);

        if (!$content) {
            return response()->json(['message' => 'Content not found'], 404);
        }

        return response()->json($content);
    }

    /**
     * Update the specified content item in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find content by ID
        $content = Content::find($id);

        if (!$content) {
            return response()->json(['message' => 'Content not found'], 404);
        }

        // Define base validation rules for update (using 'sometimes' for optional fields)
        $rules = [
            'title' => 'sometimes|string|max:255',
            'type' => 'sometimes|string|in:text,image,testimonial,imageMultiple,textMultiple', // Added new types
            'data' => 'sometimes|array',
        ];

        // Add specific validation rules for 'data' based on 'type'
        // Use $request->input('type') to get the type from the request,
        // or $content->type if 'type' is not being updated
        $contentType = $request->input('type', $content->type);

        switch ($contentType) {
            case 'text':
                $rules['data.heading'] = 'sometimes|string|max:255';
                $rules['data.body'] = 'sometimes|string';
                $rules['data.buttonText'] = 'nullable|string|max:255';
                $rules['data.buttonLink'] = 'nullable|string|max:255';
                break;
            case 'image':
                $rules['data.imageUrl'] = 'sometimes|url|max:2048';
                $rules['data.caption'] = 'nullable|string|max:255';
                break;
            case 'testimonial':
                $rules['data.author'] = 'sometimes|string|max:255';
                $rules['data.quote'] = 'sometimes|string';
                $rules['data.authorTitle'] = 'nullable|string|max:255';
                $rules['data.avatarUrl'] = 'nullable|url|max:2048';
                break;
            case 'imageMultiple':
                $rules['data.*.imageUrl'] = 'sometimes|url|max:2048';
                $rules['data.*.caption'] = 'nullable|string|max:255';
                break;
            case 'textMultiple':
                $rules['data.*.title'] = 'sometimes|string|max:255';
                $rules['data.*.description'] = 'sometimes|string';
                break;
            default:
                break;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update content item
        $content->update($request->only(['title', 'type', 'data']));

        return response()->json($content);
    }

    /**
     * Remove the specified content item from storage.
     */
    public function destroy(string $id)
    {
        // Find content by ID
        $content = Content::find($id);

        if (!$content) {
            return response()->json(['message' => 'Content not found'], 404);
        }

        // Delete content item
        $content->delete();

        return response()->json(['message' => 'Content deleted successfully'], 200); // 200 OK
    }
}