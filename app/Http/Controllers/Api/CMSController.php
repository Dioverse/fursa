<?php

namespace App\Http\Controllers\Api;

use App\Models\CMS;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CMSController extends Controller
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

            $cms = CMS::where('name', $group)->first();

            if (!$cms) {
                continue;
            }

            if ($section) {
                // Single section requested
                if (isset($cms->content[$section])) {
                    $translations = $cms->content[$section];
                    $response[$group][$section] = $lang && isset($translations[$lang])
                        ? $translations[$lang]
                        : $translations;
                }
            } else {
                // All sections requested
                foreach ($cms->content as $sec => $translations) {
                    $response[$group][$sec] = $lang && isset($translations[$lang])
                        ? $translations[$lang]
                        : $translations;
                }
            }
        }

        return response()->json($response);
    }
}
