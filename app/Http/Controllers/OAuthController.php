<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Api\CartController;
use App\Models\User;
use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OAuthController extends Controller
{
    public function loginOrRegister(Request $request)
    {
        $request->validate([
            'code' => 'required|string', // ✅ Expect authorization code, not ID token
        ]);

        $code = $request->input('code');

        // Step 1: Exchange authorization code for tokens
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code'          => $code,
            'client_id'     => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri'  => 'postmessage', // ✅ required for popup-based apps
            'grant_type'    => 'authorization_code',
        ]);

        if ($response->failed()) {
            return response()->json([
                'message' => 'Failed to exchange authorization code',
                'error'   => $response->json(),
            ], 401);
        }

        $tokens  = $response->json();
        $idToken = $tokens['id_token'] ?? null;

        if (! $idToken) {
            return response()->json(['message' => 'Missing ID token'], 401);
        }

        // Step 2: Verify ID token
        $client = new Google_Client(['client_id' => config('services.google.client_id')]);

        try {
            $payload = $client->verifyIdToken($idToken);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Invalid ID token',
                'error'   => $e->getMessage(),
            ], 401);
        }

        if (! $payload) {
            return response()->json(['message' => 'Invalid ID token'], 401);
        }

        // Step 3: Extract user info
        $name       = $payload['name'] ?? null;
        $nameParts  = explode(' ', $name);
        $first_name = $nameParts[0] ?? 'User';
        $last_name  = $nameParts[1] ?? 'Google';
        $googleId   = $payload['sub'];
        $email      = $payload['email'] ?? null;
        $avatar     = $payload['picture'] ?? null;

        // Step 4: Find or create user
        $user = User::where('provider', 'google')
            ->where('provider_id', $googleId)
            ->first();

        if (! $user && $email) {
            $user = User::where('email', $email)->first();
        }

        if (! $user) {
            $user = User::create([
                'first_name'     => $first_name,
                'last_name'      => $last_name,
                'email'          => $email,
                'phone'          => '',
                'status'         => 'approved',
                'email_verified' => now(),
                'password'       => bcrypt(Str::random(24)),
                'provider'       => 'google',
                'provider_id'    => $googleId,
                'avatar'         => $avatar,
            ]);
        } else {
            $user->update([
                'provider'    => 'google',
                'provider_id' => $googleId,
                'avatar'      => $avatar,
            ]);
        }

        // ❌ Prevent admins from using Google login
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Admins cannot log in using Google.'], 403);
        }

        // ✅ Sync user cart
        $cart = [];
        if ($user->role !== 'admin') {
            $cartController = new CartController();
            $cart           = $cartController->syncUserCart($user, $request->cart);
        }

        // ✅ Create token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message'    => 'Login successful.',
            'user'       => $user,
            'token'      => $token,
            'token_type' => 'Bearer',
            'cart'       => $cart,
        ], 200);
    }

}
