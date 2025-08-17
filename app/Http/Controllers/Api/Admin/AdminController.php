<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = User::where('role', 'admin')->get();
        return response()->json([
            'message' => 'List of admin users retrieved successfully.',
            'data' => $admins,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Validate input (only allow 'admin' as role)
        $validator = Validator::make($request->all(), [
            'first_name'    => ['sometimes', 'string', 'max:255'],
            'last_name'     => ['sometimes', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'phone'    => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Force role to 'admin' (no role input allowed)
        $user = User::create([
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'password'          => Hash::make($request->password),
            'role'              => 'admin',
            'status'            => 'approved',
            'email_verified_at' => now()
        ]);

        return response()->json([
            'message' => 'Admin user created successfully',
            'user'    => $user,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $admin = User::where('role', 'admin')->find($id);

        if (!$admin) {
            return response()->json([
                'message' => 'Admin user not found.',
            ], 404);
        }

        return response()->json([
            'message' => 'Admin user retrieved successfully.',
            'user'    => $admin,
        ]);
    }

    /**
     * Update the specified admin user in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $admin = User::where('role', 'admin')->find($id);

        if (!$admin) {
            return response()->json([
                'message' => 'Admin user not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name'  => ['sometimes', 'string', 'max:255'],
            'email'      => ['sometimes', 'email', "unique:users,email,{$admin->id}"],
            'phone'      => ['sometimes', 'string', 'max:20'],
            'password'   => ['sometimes', 'string', 'min:6'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $admin->update([
            'first_name' => $request->first_name ?? $admin->first_name,
            'last_name'  => $request->last_name ?? $admin->last_name,
            'email'      => $request->email ?? $admin->email,
            'phone'      => $request->phone ?? $admin->phone,
            'password'   => $request->filled('password') ? Hash::make($request->password) : $admin->password,
        ]);

        return response()->json([
            'message' => 'Admin user updated successfully.',
            'user'    => $admin,
        ]);
    }

    /**
     * Remove the specified admin user from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $admin = User::where('role', 'admin')->find($id);

        if (!$admin) {
            return response()->json([
                'message' => 'Admin user not found.',
            ], 404);
        }

        $admin->delete();

        return response()->json([
            'message' => 'Admin user deleted successfully.',
        ]);
    }
}