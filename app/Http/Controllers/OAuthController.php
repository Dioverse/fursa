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
        // ... (All existing validation and ID token verification logic remains correct)
        $request->validate([
            'id_token' => 'required|string',
        ]);

        $idToken = $request->input('id_token');

        $client = new Google_Client(['client_id' => config('services.google.client_id')]);

        try {
            // This is the standard server-side verification for a Google ID token,
            // which is independent of how the client obtained it (GSI or FedCM).
            $payload = $client->verifyIdToken($idToken);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid ID token', 'error' => $e->getMessage()], 401);
        }

        if (! $payload) {
            return response()->json(['message' => 'Invalid ID token'], 401);
        }

        // Extract user data
        $name       = $payload['name'] ?? null;
        $nameParts  = explode(' ', $name);
        $first_name = $nameParts[0] ?? 'User';
        $last_name  = $nameParts[1] ?? 'Google';
        $googleId   = $payload['sub'];
        $email      = $payload['email'] ?? null;
        $avatar     = $payload['picture'] ?? null;

        $user = User::where('provider', 'google')
            ->where('provider_id', $googleId)
            ->first();

        if (! $user && $email) {
            $user = User::where('email', $email)->first();
        }

        if (! $user) {
            $user = User::create([
                'first_name'        => $first_name,
                'last_name'         => $last_name,
                'email'             => $email,
                'phone'             => '',
                'status'            => 'approved',
                'email_verified_at' => now(),
                'password'          => bcrypt(Str::random(24)),
                'provider'          => 'google',
                'provider_id'       => $googleId,
                'avatar'            => $avatar,
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

        // ✅ Sync user cart (just like in `login()`)
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
