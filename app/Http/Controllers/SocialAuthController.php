<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Handle the social login via token from mobile app.
     */
    public function login(Request $request, $provider)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $token = $request->input('token');

        try {
            // Apple uses stateless JWTs, others might use stateful OAuth
            // For stateless mobile auth, we generally use stateless()
            if ($provider === 'apple') {
                $socialUser = Socialite::driver($provider)->stateless()->userFromToken($token);
            } else {
                $socialUser = Socialite::driver($provider)->stateless()->userFromToken($token);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Invalid token provided.',
                'error' => $e->getMessage()
            ], 401);
        }

        // Find or create user
        $user = User::where('provider', $provider)
                    ->where('provider_id', $socialUser->getId())
                    ->first();

        if (!$user) {
            // Check if email already exists
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // Link account
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                ]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $socialUser->getName() ?? 'User',
                    'email' => $socialUser->getEmail(),
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'password' => Hash::make(Str::random(24)),
                    'selectables' => [], // Initialize JSON column
                ]);
            }
        }

        // Create token
        $authToken = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully',
            'user' => $user,
            'token' => $authToken
        ]);
    }
}
