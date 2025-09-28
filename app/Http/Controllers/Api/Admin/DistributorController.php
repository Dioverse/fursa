<?php

namespace App\Http\Controllers\Api\Admin;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class DistributorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $perPage = max(1, (int) $perPage);

        $distributors = User::with('distributor')->where('role', 'distributor')->paginate($perPage);
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
        $user = User::findOrFail($id);

        // Ensure user is distributor
        if ($user->role !== 'distributor') {
            return response()->json(['message' => 'Only distributors can have their status updated.'], 403);
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'reason' => 'required_if:status,rejected|string'
        ]);


        // Prevent re-approval
        $stat = $request->status;
        $reason = $request->reason ?? "";
        if ($stat === 'approved' && $user->status === 'approved') {
            return response()->json(['message' => 'Distributor already approved.'], 422);
        }

        // Update user status
        $user->status = $stat;
        $user->save();

        // Update distributor table if approved
        if ($stat === 'approved') {
            if (!$user->distributor) {
                return response()->json(['message' => 'Distributor record not found for this user.'], 404);
            }
            $user->distributor->approved_at = Carbon::now();
            $user->distributor->save();
        } elseif ($stat === 'rejected') {
            if ($user->distributor) {
                $user->distributor->approved_at = null;
                $user->distributor->reason = $reason;
                $user->distributor->save();
            }
        }

        notify($user,$stat == "approved" ? 'DISTRIBUTOR_APPROVE' : 'DISTRIBUTOR_REJECT',
        ['reason'=>$reason],
        ['email'],false
        );

        return response()->json([
            'message' => 'Distributor status updated successfully.',
            'data' => [
                'user' => $user,
                'distributor' => $user->distributor ?? null
            ]
        ]);
    }
}
