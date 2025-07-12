<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserLoggedInNotification;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        try {
            // 1. Validate incoming request data
            $request->validate([
                'user' => ['required', 'string'],
                'password' => ['required', 'string'],
            ], [
                'user.required' => "Enter valid phone number or email address"
            ]);

            // 2. Determine if the input is an email or a phone number
            $loginField = filter_var($request->input('user'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
            $loginValue = $request->input('user');
            $password = $request->input('password');

            // 3. Find the user by the determined login field
            $user = User::where($loginField, $loginValue)->first();

            // 4. Verify user existence and password
            if (!$user || !Hash::check($password, $user->password)) {
                throw ValidationException::withMessages([
                    'user' => [trans('auth.failed')],
                ]);
            }

            if ($user->role === 'distributor') {
                $user->load('distributor');
            }
            
            // 5. Credentials are valid, create an API token
            $token = $user->createToken('api-token')->plainTextToken;

            // 6. Prepare data for email notification
            $ipAddress = $request->ip();
            $loginTime = Carbon::now()->toDateTimeString();

            // 7. Send email notification (still queued for performance)
            Mail::to($user->email)->queue(new UserLoggedInNotification($user, $ipAddress, $loginTime));

            // 8. Return success response with user data and the token
            return response()->json([
                'message' => 'Login successful.',
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Catch any other unexpected errors
            Log::error("API Login error: " . $e->getMessage() . " on line " . $e->getLine() . " in " . $e->getFile());
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => 'Please try again later.',
            ], 500);
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'message' => 'Successfully logged out',
            ], 200);
        }

        return response()->json([
            'message' => 'No active session.',
        ], 401);
    }  
}
