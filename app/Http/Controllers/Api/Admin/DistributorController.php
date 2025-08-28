<?php

namespace App\Http\Controllers\Api\Admin;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DistributorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $distributors = User::with('distributor')->where('role', 'distributor')->get();
        return response()->json([
            'message' => 'Distributors list retrieved successfully.',
            'data' => $distributors
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $user = User::findOrFail($id);

        // Ensure user is distributor
        if ($user->role !== 'distributor') {
            return response()->json([
                'message' => 'Only distributors can have their status updated.'
            ], 403);
        }

        // Prevent re-approval
        if ($request->status === 'approved' && $user->status === 'approved') {
            return response()->json([
                'message' => 'Distributor already approved.'
            ], 422);
        }

        // Update user status
        $user->status = $request->status;
        $user->save();

        // Update distributor table if approved
        if ($request->status === 'approved') {
            if (!$user->distributor) {
                return response()->json([
                    'message' => 'Distributor record not found for this user.'
                ], 404);
            }

            $user->distributor->approved_at = Carbon::now();
            $user->distributor->save();
        } elseif ($request->status === 'rejected') {
            if ($user->distributor) {
                $user->distributor->approved_at = null; // optional reset
                $user->distributor->save();
            }
        }

        return response()->json([
            'message' => 'Distributor status updated successfully.',
            'data' => [
                'user' => $user,
                'distributor' => $user->distributor ?? null
            ]
        ]);
    }
}
