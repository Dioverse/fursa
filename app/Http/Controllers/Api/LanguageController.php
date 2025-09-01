<?php

namespace App\Http\Controllers\Api;

use App\Models\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LanguageController extends Controller
{
    public function fetch(Request $request,$lang,$name)
    {
        // $data = $request->validate([
        //     'name' => 'required|string', // comma-separated list
        //     'lang' => 'nullable|string'
        // ]);

        $names = explode(',', $name);
        $lang = $lang ?? null;

        $response = [];

        foreach ($names as $item) {
            $parts = explode('.', $item);
            $group = $parts[0];         // e.g. "about"
            $section = $parts[1] ?? null; // e.g. "vision"

            $language = Language::where('name', $group)->first();

            if (!$language) {
                continue;
            }

            if ($section) {
                // Single section requested
                if (isset($language->content[$section])) {
                    $translations = $language->content[$section];
                    $response[$group][$section] = $lang && isset($translations[$lang])
                        ? $translations[$lang]
                        : $translations;
                }
            } else {
                // All sections requested
                foreach ($language->content as $sec => $translations) {
                    $response[$group][$sec] = $lang && isset($translations[$lang])
                        ? $translations[$lang]
                        : $translations;
                }
            }
        }
        // $response = count($response) === 1 ? $response[0] : $response;
        // print_r($response);
        return response()->json($response);
    }
}
