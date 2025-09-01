<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\CMSStoreRequest;
use App\Http\Requests\CMSUpdateRequest;
use App\Models\CMS;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CMSController extends Controller
{
    /**
     * Store a whole group (overwrites the group if it exists).
     * Request shape:
     *  - name: "blog"
     *  - content: { "header1": {"en":"hello","fr":"bonjour"}, ... }
     */
    public function store(CMSStoreRequest $request): JsonResponse
    {
        $payload = $request->validated();

        // Normalize (trim strings)
        $normalized = $this->normalizeContent($payload['content']);

        $cms = DB::transaction(function () use ($payload, $normalized) {
            // Upsert: overwrite group content fully
            return CMS::updateOrCreate(
                ['name' => $payload['name']],
                ['content' => $normalized]
            );
        });

        return response()->json([
            'message' => 'Content stored successfully.',
            'data'    => $cms,
        ], 201);
    }

    /**
     * Update/merge sections & languages into an existing group.
     * Request shape:
     *  - name: "blog"
     *  - content: { "header1": {"es":"hola"} } // merges into existing
     */
    public function update(CMSUpdateRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $cms = CMS::where('name', $payload['name'])->first();
        if (!$cms) {
            return response()->json([
                'message' => 'Group not found.',
                'errors'  => ['name' => ['The specified group does not exist.']],
            ], 404);
        }

        $incoming = $this->normalizeContent($payload['content']);

        $updated = DB::transaction(function () use ($cms, $incoming) {
            $existing = $cms->content ?? [];

            // Merge section by section
            foreach ($incoming as $section => $translations) {
                $existing[$section] = array_merge(
                    is_array($existing[$section] ?? null) ? $existing[$section] : [],
                    $translations
                );
            }

            $cms->update(['content' => $existing]);
            return $cms->refresh();
        });

        return response()->json([
            'message' => 'Content updated successfully.',
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
