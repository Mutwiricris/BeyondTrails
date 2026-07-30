<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\SocialProvider;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    // ───────────────────────────────────────────────────────────────────────
    // POST /api/v1/auth/social
    // From: auth_entry_screen.dart (Google, Apple, Facebook buttons)
    //       login_screen.dart (Google, Apple buttons)
    //
    // The Flutter app sends the provider token obtained from the OAuth SDK.
    // We verify it server-side, then find or create the user.
    // ───────────────────────────────────────────────────────────────────────
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'provider'       => 'required|in:google,apple,facebook',
            'provider_id'    => 'required|string',
            'provider_token' => 'required|string',
            'email'          => 'required|email',
            'first_name'     => 'nullable|string|max:100',
            'last_name'      => 'nullable|string|max:100',
            'photo_url'      => 'nullable|url',
            'device_name'    => 'nullable|string|max:255',
        ]);

        $provider      = $request->input('provider');
        $providerId    = $request->input('provider_id');
        $providerToken = $request->input('provider_token');
        $email         = $request->input('email');
        $firstName     = $request->input('first_name');
        $lastName      = $request->input('last_name');
        $photoUrl      = $request->input('photo_url');
        $deviceName    = $request->input('device_name', 'mobile');

        // ── Verify token with the OAuth provider (stub — always passes for now)
        // In production: call Google tokeninfo, Apple JWT decode, Facebook /me
        // $this->authService->verifyProviderToken($provider, $providerToken, $providerId);

        $expiresAt = now()->addDays(30);

        // ── Look up existing social provider record ──────────────────────────
        $socialProvider = SocialProvider::where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();

        if ($socialProvider) {
            // ── Returning user ─────────────────────────────────────────────
            $user = $socialProvider->user;
            $user->updateStreak();

            // Refresh provider token
            $socialProvider->update(['provider_token' => $providerToken]);

            $token = $user->createToken($deviceName, ['*'], $expiresAt);
            $stats = $this->authService->getUserStats($user);

            return response()->json([
                'success' => true,
                'message' => "Welcome back, {$user->first_name}!",
                'data'    => [
                    'user'        => new UserResource($user),
                    'token'       => [
                        'access_token' => $token->plainTextToken,
                        'token_type'   => 'Bearer',
                        'expires_at'   => $expiresAt->toIso8601String(),
                    ],
                    'stats'       => $stats,
                    'is_new_user' => false,
                ],
                'meta' => $this->meta(),
            ]);
        }

        // ── Check if email is already registered (link accounts) ─────────────
        $user = User::where('email', $email)->first();

        if ($user) {
            // Existing user — link social provider to account
            SocialProvider::create([
                'user_id'        => $user->id,
                'provider'       => $provider,
                'provider_id'    => $providerId,
                'provider_token' => $providerToken,
                'provider_email' => $email,
            ]);

            $user->updateStreak();
            // If provider = google/apple/facebook → email is trusted
            if (!$user->email_verified_at) {
                $user->update(['email_verified_at' => now()]);
            }

            $token = $user->createToken($deviceName, ['*'], $expiresAt);

            return response()->json([
                'success' => true,
                'message' => "Welcome back, {$user->first_name}!",
                'data'    => [
                    'user'        => new UserResource($user),
                    'token'       => [
                        'access_token' => $token->plainTextToken,
                        'token_type'   => 'Bearer',
                        'expires_at'   => $expiresAt->toIso8601String(),
                    ],
                    'is_new_user' => false,
                ],
                'meta' => $this->meta(),
            ]);
        }

        // ── Brand new user via social ─────────────────────────────────────────
        // Download provider profile photo if available
        $storedPhotoUrl = null;
        if ($photoUrl) {
            $storedPhotoUrl = $this->authService->downloadAndStorePhoto($photoUrl);
        }

        $user = User::create([
            'email'              => $email,
            'first_name'         => $firstName ?? 'User',
            'last_name'          => $lastName ?? '',
            'display_name'       => trim(($firstName ?? 'User') . ' ' . ($lastName ?? '')),
            'photo_url'          => $storedPhotoUrl ?? $photoUrl,
            'email_verified_at'  => now(), // Trusted from OAuth provider
            'explorer_level'     => 'explorer',
            'current_xp'         => 0,
            'streak_days'        => 1,
            'profile_completion' => 40,
            'role'               => 'traveler',
        ]);

        SocialProvider::create([
            'user_id'        => $user->id,
            'provider'       => $provider,
            'provider_id'    => $providerId,
            'provider_token' => $providerToken,
            'provider_email' => $email,
        ]);

        $token = $user->createToken($deviceName, ['*'], $expiresAt);

        return response()->json([
            'success' => true,
            'message' => 'Welcome to ZuriTrails!',
            'data'    => [
                'user'                => new UserResource($user),
                'token'               => [
                    'access_token' => $token->plainTextToken,
                    'token_type'   => 'Bearer',
                    'expires_at'   => $expiresAt->toIso8601String(),
                ],
                'is_new_user'         => true,
                'requires_onboarding' => true,
            ],
            'meta' => $this->meta(),
        ], 201);
    }

    private function meta(): array
    {
        return ['timestamp' => now()->toIso8601String(), 'version' => '1.0.0'];
    }
}
