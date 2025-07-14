<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::whereNot('role', 'admin')->get();
        return response()->json([
            'message' => 'Users list retrieved successfully.',
            'data' => $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $user = User::find($id);

        if ($user && $user->role === 'distributor') {
            $type = "Distributor";
            $user->load('distributor');
        } elseif ($user->role === 'admin') {
            $type = "Admin";
        } else {
            $type = "Customer";
        }

        if (!$user) {
            return response()->json(['message' => "$type not found."], 404);
        }

        return response()->json([
            'message' => "$type details retrieved successfully.",
            'data' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
