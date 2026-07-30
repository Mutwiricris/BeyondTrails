<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SimpleRegisterRequest;
use App\Http\Requests\Auth\MultiStepRegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use App\Services\ProfileCompletionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly ProfileCompletionService $profileService,
    ) {}

    // ───────────────────────────────────────────────────────────────────────
    // POST /api/v1/auth/check-email
    // Step 1 check before proceeding with signup
    // ───────────────────────────────────────────────────────────────────────
    public function checkEmail(\App\Http\Requests\Auth\CheckEmailRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $exists = User::where('email', $data['email'])->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Email already exists',
                'errors'  => [
                    'email' => ['This email address is already registered. Please login instead.']
                ],
                'meta'    => $this->meta(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Email is available',
            'meta'    => $this->meta(),
        ]);
    }




    public function usernameSuggestions(\Illuminate\Http\Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
        ]);

        $firstName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->input('first_name')));
        $lastName  = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->input('last_name')));

        $bases = [
            "{$firstName}{$lastName}",
            "{$firstName}.{$lastName}",
            substr($firstName, 0, 1) . $lastName,
            $firstName . substr($lastName, 0, 1),
        ];

        $suggestions = [];
        foreach ($bases as $base) {
            if (\App\Models\User::where('username', $base)->doesntExist() && !in_array($base, $suggestions)) {
                $suggestions[] = $base;
            }
            if (count($suggestions) >= 4) break;
        }

        $attempts = 0;
        while (count($suggestions) < 4 && $attempts < 10) {
            $base = $bases[array_rand($bases)] . rand(10, 999);
            if (\App\Models\User::where('username', $base)->doesntExist() && !in_array($base, $suggestions)) {
                $suggestions[] = $base;
            }
            $attempts++;
        }

        return response()->json([
            'success' => true,
            'suggestions' => array_values($suggestions),
        ]);
    }

    // ───────────────────────────────────────────────────────────────────────
    // POST /api/v1/auth/register
    // Simple email signup — from signup_screen.dart
    // Fields: first_name, last_name, date_of_birth, email, password
    // ───────────────────────────────────────────────────────────────────────
    public function register(SimpleRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $username = $data['username'] ?? null;
        if (empty($username)) {
            $username = User::generateUniqueUsername($data['first_name'], $data['last_name']);
        }

        $email = strtolower(trim($data['email']));
        $dob   = $data['date_of_birth'] ?? '2000-01-01';

        // Create user with basic profile
        $user = User::create([
            'name'               => $data['first_name'] . ' ' . $data['last_name'],
            'username'           => $username,
            'first_name'         => $data['first_name'],
            'last_name'          => $data['last_name'],
            'display_name'       => $data['first_name'] . ' ' . $data['last_name'],
            'date_of_birth'      => $dob,
            'email'              => $email,
            'password'           => Hash::make($data['password']),
            'explorer_level'     => 'explorer',
            'current_xp'         => 0,
            'streak_days'        => 0,
            'profile_completion' => 25,
            'role'               => 'traveler',
            'selectables'        => $data['selectables'] ?? null,
        ]);

        // Award welcome XP + send email verification
        $this->authService->sendEmailVerification($user);

        // Generate Sanctum token (12 months default)
        $expiresAt  = now()->addMonths(12);
        $tokenName  = $request->input('device_name', 'mobile');
        $token      = $user->createToken($tokenName, ['*'], $expiresAt);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful! Welcome to ZuriTrails.',
            'data'    => [
                'user'  => new UserResource($user),
                'token' => [
                    'access_token' => $token->plainTextToken,
                    'token_type'   => 'Bearer',
                    'expires_at'   => $expiresAt->toIso8601String(),
                ],
            ],
            'meta' => $this->meta(),
        ], 201);
    }

    // ───────────────────────────────────────────────────────────────────────
    // POST /api/v1/auth/register/multi-step
    // 13-step signup — from SignupFlowCoordinator + SignupData model
    // Steps: welcome, birthday, referral, name, gender, country,
    //        travel_style, interests, photo, notifications,
    //        activity_preferences, location, privacy (email+password)
    // ───────────────────────────────────────────────────────────────────────
    public function registerMultiStep(MultiStepRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Handle profile photo upload (moved from temp storage)
        $photoUrl          = null;
        $photoThumbnailUrl = null;
        if (!empty($data['profile_photo_path'])) {
            [$photoUrl, $photoThumbnailUrl] = $this->authService->processProfilePhoto(
                $data['profile_photo_path']
            );
        }

        // Derive nationality from home_country
        $nationality = $this->authService->getNationalityFromCountry($data['home_country'] ?? null);

        $user = User::create([
            // Identity
            'name'               => $data['first_name'] . ' ' . $data['last_name'],
            'username'           => $data['username'],
            'first_name'         => $data['first_name'],
            'last_name'          => $data['last_name'],
            'display_name'       => $data['first_name'] . ' ' . $data['last_name'],
            'date_of_birth'      => $data['birthday'],
            'email'              => $data['email'],
            'password'           => Hash::make($data['password']),

            // Multi-step fields
            'gender'             => $data['gender'] ?? null,
            'home_country'       => $data['home_country'] ?? null,
            'nationality'        => $nationality,
            'referral_source'    => $data['referral_source'] ?? null,

            // Preferences (JSONB)
            'travel_styles'         => $data['travel_styles'] ?? [],
            'interests'             => $data['interests'] ?? [],
            'activity_preferences'  => $data['activity_preferences'] ?? [],

            // Settings
            'push_notifications'  => $data['notifications_enabled'] ?? true,
            'location_enabled'    => $data['location_enabled'] ?? false,
            'show_distance_away'  => $data['show_distance_away'] ?? true,

            // Photos
            'photo_url'           => $photoUrl,
            'photo_thumbnail_url' => $photoThumbnailUrl,

            // Gamification starting values
            'explorer_level'      => 'explorer',
            'current_xp'          => 50, // New Explorer bonus
            'streak_days'         => 0,
            'unlocked_badges'     => ['new_explorer'],
            'profile_completion'  => 80,
            'role'                => 'traveler',
        ]);

        // Send email verification
        $this->authService->sendEmailVerification($user);

        // Generate token
        $rememberMe = $request->boolean('remember_me', true);
        $expiresAt  = $rememberMe ? now()->addMonths(12) : now()->addMonths(1);
        $token      = $user->createToken($request->input('device_name', 'mobile'), ['*'], $expiresAt);

        return response()->json([
            'success' => true,
            'message' => 'Welcome to ZuriTrails! Your adventure begins now.',
            'data'    => [
                'user'               => new UserResource($user),
                'token'              => [
                    'access_token' => $token->plainTextToken,
                    'token_type'   => 'Bearer',
                    'expires_at'   => $expiresAt->toIso8601String(),
                ],
                'onboarding_complete' => true,
            ],
            'meta' => $this->meta(),
        ], 201);
    }

    // ───────────────────────────────────────────────────────────────────────
    // POST /api/v1/auth/login
    // Email + password login — from login_screen.dart
    // Fields: email, password, device_name, remember_me
    // ───────────────────────────────────────────────────────────────────────
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $email = strtolower(trim($data['email']));

        // Find user
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Update streak + last_active_at
        $user->updateStreak();

        // Token expiry based on remember_me toggle (login_screen.dart has checkbox)
        $rememberMe = $request->boolean('remember_me', true);
        $expiresAt  = $rememberMe ? now()->addMonths(12) : now()->addMonths(1);
        $tokenName  = $data['device_name'] ?? 'mobile';
        $token      = $user->createToken($tokenName, ['*'], $expiresAt);

        // Compute quick stats for the login response
        $stats = $this->authService->getUserStats($user);

        return response()->json([
            'success' => true,
            'message' => "Welcome back, {$user->first_name}!",
            'data'    => [
                'user'                 => new UserResource($user),
                'token'                => [
                    'access_token' => $token->plainTextToken,
                    'token_type'   => 'Bearer',
                    'expires_at'   => $expiresAt->toIso8601String(),
                ],
                'stats'                => $stats,
                'has_active_journey'   => false, // TODO: check journeys table
                'pending_notifications' => 0,    // TODO: check notifications table
            ],
            'meta' => $this->meta(),
        ]);
    }

    // ───────────────────────────────────────────────────────────────────────
    // GET /api/v1/auth/me
    // Returns current authenticated user
    // ───────────────────────────────────────────────────────────────────────
    public function me(Request $request): JsonResponse
    {
        $user  = $request->user();
        $stats = $this->authService->getUserStats($user);

        return response()->json([
            'success' => true,
            'data'    => [
                'user'  => new UserResource($user),
                'stats' => $stats,
            ],
            'meta' => $this->meta(),
        ]);
    }

    // ───────────────────────────────────────────────────────────────────────
    // POST /api/v1/auth/logout
    // Revoke current token
    // ───────────────────────────────────────────────────────────────────────
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
            'meta'    => $this->meta(),
        ]);
    }

    // ───────────────────────────────────────────────────────────────────────
    // POST /api/v1/auth/logout-all
    // Revoke ALL tokens for this user (all devices)
    // ───────────────────────────────────────────────────────────────────────
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out from all devices.',
            'meta'    => $this->meta(),
        ]);
    }

    // ───────────────────────────────────────────────────────────────────────
    // POST /api/v1/auth/refresh
    // Delete current token and issue a fresh one
    // ───────────────────────────────────────────────────────────────────────
    public function refresh(Request $request): JsonResponse
    {
        $user       = $request->user();
        $oldToken   = $user->currentAccessToken();
        $tokenName  = $oldToken->name;
        $expiresAt  = now()->addDays(7);

        // Revoke old token
        $oldToken->delete();

        // Issue new token
        $newToken = $user->createToken($tokenName, ['*'], $expiresAt);

        return response()->json([
            'success' => true,
            'data'    => [
                'access_token' => $newToken->plainTextToken,
                'token_type'   => 'Bearer',
                'expires_at'   => $expiresAt->toIso8601String(),
            ],
            'meta' => $this->meta(),
        ]);
    }

    // ───────────────────────────────────────────────────────────────────────
    // Helpers
    // ───────────────────────────────────────────────────────────────────────
    private function meta(): array
    {
        return [
            'timestamp' => now()->toIso8601String(),
            'version'   => '1.0.0',
        ];
    }
}
