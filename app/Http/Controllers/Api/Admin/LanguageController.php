<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\LanguageStoreRequest;
use App\Http\Requests\LanguageUpdateRequest;
use App\Models\Language;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class LanguageController extends Controller
{
    /**
     * Store a whole group (overwrites the group if it exists).
     * Request shape:
     *  - name: "blog"
     *  - content: { "header1": {"en":"hello","fr":"bonjour"}, ... }
     */
    public function store(LanguageStoreRequest $request): JsonResponse
    {
        $payload = $request->validated();

        // Normalize (trim strings)
        $normalized = $this->normalizeContent($payload['content']);

        $lang = DB::transaction(function () use ($payload, $normalized) {
            // Upsert: overwrite group content fully
            return Language::updateOrCreate(
                ['name' => $payload['name']],
                ['content' => $normalized]
            );
        });

        return response()->json([
            'message' => 'Language set stored successfully.',
            'data'    => $lang,
        ], 201);
    }

    /**
     * Update/merge sections & languages into an existing group.
     * Request shape:
     *  - name: "blog"
     *  - content: { "header1": {"es":"hola"} } // merges into existing
     */
    public function update(LanguageUpdateRequest $request, $name): JsonResponse
    {
        $payload = $request->validated();

        $lang = Language::where('name', $name)->first();
        if (!$lang) {
            return response()->json([
                'message' => 'Language set not found.',
                'errors'  => ['name' => ['The specified language set does not exist.']],
            ], 404);
        }

        $updated = DB::transaction(function () use ($lang, $payload) {
            $currentContent = $lang->content ?? [];

            // Request content (sections to replace)
            $newContent = $payload['content'] ?? [];

            // Replace entire keys (not deep merge)
            foreach ($newContent as $key => $value) {
                $currentContent[$key] = $value;
            }

            // Save back
            $lang->content = $currentContent;
            $lang->save();

            return $lang->refresh();
        });

        return response()->json([
            'message' => 'Language set updated successfully.',
            'data'    => $updated,
        ]);
    }

    /**
     * Helper: deep-trim all strings in content (section=>[lang=>value])
     */
    private function normalizeContent(array $content): array
    {
        $out = [];
        foreach ($content as $section => $translations) {
            $out[$section] = [];
            foreach ($translations as $lang => $value) {
                $out[$section][$this->normalizeLocale($lang)] = trim($value);
            }
        }
        return $out;
    }

    /**
     * Normalize locale: keep pattern like en, fr, en-US (lower-upper)
     */
    private function normalizeLocale(string $locale): string
    {
        // en-us -> en-US ; FR -> fr ; pt-br -> pt-BR
        if (str_contains($locale, '-')) {
            [$l, $r] = explode('-', $locale, 2);
            return strtolower($l) . '-' . strtoupper($r);
        }
        return strtolower($locale);
    }
}
