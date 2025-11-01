<?php
namespace App\Http\Controllers;

use App\Models\User;
use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OAuthController extends Controller
{
    public function loginOrRegister(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        $idToken = $request->input('id_token');

        // VERIFY ID TOKEN - recommended using Google_Client (production)
        $client  = new Google_Client(['client_id' => config('services.google.client_id')]);
        $payload = null;
        try {
            $payload = $client->verifyIdToken($idToken);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid ID token', 'error' => $e->getMessage()], 401);
        }

        if (! $payload) {
            // Fallback: tokeninfo endpoint (not recommended for production)
            // return response()->json(['message'=>'Invalid ID token'], 401);
            return response()->json(['message' => 'Invalid ID token'], 401);
        }

        // payload contains 'sub' (Google user id), email, name, picture, etc.
        $name           = $payload['name'] ?? null;
        $name_sort      = explode(" ", $name);
        $first_name     = $name_sort[0] ?? "User";
        $last_name     = $name_sort[1] ?? "Google";

        $googleId       = $payload['sub'];
        $email          = $payload['email'] ?? null;
        $avatar         = $payload['picture'] ?? null;
        $email_verified = $payload['email_verified'] ?? false;

        // Find or create user
        $user = User::where('provider', 'google')->where('provider_id', $googleId)->first();

        if (! $user && $email) {
            // Try find by email
            $user = User::where('email', $email)->first();
        }

        if (! $user) {
            $user = User::create([
                'first_name'  => $first_name,
                'last_name'   => $last_name,
                'email'       => $email,
                // you can generate a random password
                'password'    => bcrypt(Str::random(24)),
                'provider'    => 'google',
                'provider_id' => $googleId,
                'avatar'      => $avatar,
            ]);
        } else {
            // Update provider fields if needed
            $user->update([
                'provider'    => 'google',
                'provider_id' => $googleId,
                'avatar'      => $avatar,
            ]);
        }

        // Create Sanctum token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user'       => $user,
            'token'      => $token,
            'token_type' => 'Bearer',
        ], 200);
    }
}
