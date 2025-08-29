<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\AdminSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    protected $keys_to_hide = ['gateways'];
    /**
     * Get all admin settings.
     */
    public function index()
    {
        $settings = AdminSetting::pluck('value', 'key')->toArray();

        return response()->json([
            'success' => true,
            'data'    => $settings,
        ]);
    }

    /**
     * Update admin settings.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'key'   => 'required|string|max:255|exists:admin_settings,key|not_in:'.implode(",",$this->keys_to_hide),
            'value' => 'required',
        ]);

        // If value is array/object, encode to JSON. If scalar, keep as is.
        $value = is_array($data['value']) ? $data['value'] : json_decode($data['value'], true);

        // If decoding failed (plain string), keep raw value
        if (json_last_error() !== JSON_ERROR_NONE) {
            $value = $data['value'];
        }

        AdminSetting::where('key', $data['key'])
                    ->update(['value' => json_encode($value)]);

        return response()->json([
            'success' => true,
            'message' => 'Admin setting updated successfully.',
            'data'    => [
                $data['key'] => $value,
            ],
        ]);
    }

    /**
     * Get a multiple admin setting by key(s).
     */
    public function show(string $keys)
    {
        // Split keys by comma
        $keysArray = explode(',', $keys);

        $settings = [];
        foreach ($keysArray as $key) {
            // Use getNested to fetch value, fallback to null if not found
            $settings[$key] = AdminSetting::getNested($key, null);
        }

        if (empty(array_filter($settings))) {
            return response()->json([
                'success' => false,
                'message' => 'No matching settings found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $settings,
        ]);
    }
}
