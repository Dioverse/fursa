<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Mail\UserStatusChange;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query()
            ->with('distributor');

        // --- User filters ---
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('first_name')) {
            $query->where('first_name', 'like', '%' . $request->first_name . '%');
        }
        if ($request->filled('last_name')) {
            $query->where('last_name', 'like', '%' . $request->last_name . '%');
        }
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        $perPage = $request->query('per_page', 10);
        $perPage = max(1, (int) $perPage);
        $users = $query->paginate($perPage);

        return response()->json([
            'message' => 'Users list retrieved successfully.',
            'data'    => $users,
            'role' => ['distributor','customer']
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $user = User::find($id);

        if ($user && $user->isDistributor()) {
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

    public function toggleBan(Request $request, $id)
    {
        $user = User::where('id',$id)->first();
        if (!$user || $user->id == $request->user()->id) {
            return response()->json([
                'message' => "User not found."
            ]);
        }
        // Toggle ban (if 1 → 0, if 0 → 1)
        $user->ban = !$user->ban;
        $user->save();

        return response()->json([
            'message' => $user->ban ? 'User has been banned' : 'User has been unbanned',
        ]);
    }

}
