<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PhoneOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use App\Services\SmsService;
use App\Services\OtpCacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PhoneAuthController extends Controller
{
    public function __construct(
        private readonly SmsService $smsService,
        private readonly AuthService $authService,
        private readonly OtpCacheService $otpCache,
    ) {}

    // ───────────────────────────────────────────────────────────────────────
    // POST /api/v1/auth/phone/request-otp
    // From: phone_auth_screen.dart  (+254712345678 → gets 6-digit SMS)
    // Now: OTP lives ONLY in Redis (no DB writes for pending OTPs)
    // ───────────────────────────────────────────────────────────────────────
    public function requestOtp(PhoneOtpRequest $request): JsonResponse
    {
        $data        = $request->validated();
        $phoneNumber = $data['phone_number'];
        $isSignup    = $data['is_signup'];

        // ── 1. Check if phone is locked (brute force) ─────────────────────
        if ($this->otpCache->isPhoneLocked($phoneNumber)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many failed attempts. Please try again in 15 minutes.',
                'meta'    => $this->meta(),
            ], 429);
        }

        // ── 2. Rate limit: max 5 OTP requests per phone per hour ───────────
        if ($this->otpCache->isPhoneRateLimited($phoneNumber)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many OTP requests. Please wait before requesting a new code.',
                'errors'  => ['phone_number' => ['Rate limit exceeded. Try again later.']],
                'meta'    => $this->meta(),
            ], 429);
        }

        // ── 3. Resend cooldown check (60s) ────────────────────────────────
        $cooldown = $this->otpCache->phoneResendCooldownSeconds($phoneNumber);
        if ($cooldown > 0) {
            return response()->json([
                'success' => false,
                'message' => "Please wait {$cooldown} seconds before requesting a new code.",
                'data'    => ['cooldown_seconds' => $cooldown],
                'meta'    => $this->meta(),
            ], 429);
        }

        // ── 4. Signup/login gate checks ────────────────────────────────────
        if ($isSignup) {
            if (User::where('phone_number', $phoneNumber)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone number already registered',
                    'errors'  => ['phone_number' => ['This number is already linked to an account. Try logging in instead.']],
                    'meta'    => $this->meta(),
                ], 422);
            }
        } else {
            if (!User::where('phone_number', $phoneNumber)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone number not found',
                    'errors'  => ['phone_number' => ['No account found with this number. Sign up first.']],
                    'meta'    => $this->meta(),
                ], 422);
            }
        }

        // ── 5. Invalidate any previous OTP in Redis ────────────────────────
        $this->otpCache->invalidatePhoneOtp($phoneNumber);

        // ── 6. Generate and store fresh OTP in Redis ───────────────────────
        $otpCode        = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $verificationId = 'ver_' . Str::random(16);
        $expiresAt      = now()->addSeconds(300); // 5 minutes

        $this->otpCache->storePhoneOtp($phoneNumber, $otpCode, $verificationId);
        $this->otpCache->incrementPhoneRateLimit($phoneNumber);

        // ── 7. Send SMS via Africa's Talking ──────────────────────────────
        $this->smsService->sendOtp($phoneNumber, $otpCode);

        return response()->json([
            'success' => true,
            'message' => "Verification code sent to {$this->maskPhone($phoneNumber)}",
            'data'    => [
                'verification_id'     => $verificationId,
                'phone_number'        => $phoneNumber,
                'expires_at'          => $expiresAt->toIso8601String(),
                'resend_available_at' => now()->addSeconds(60)->toIso8601String(),
                'expires_in_seconds'  => 300,
            ],
            'meta' => $this->meta(),
        ]);
    }

    // ───────────────────────────────────────────────────────────────────────
    // POST /api/v1/auth/phone/verify-otp
    // From: verification_code_screen.dart  (6 boxes, auto-submits)
    // OTP verified purely from Redis — no DB hit
    // ───────────────────────────────────────────────────────────────────────
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $data           = $request->validated();
        $verificationId = $data['verification_id'];
        $otpCode        = $data['otp_code'];
        $isSignup       = $data['is_signup'];

        // ── 1. Verify OTP from Redis ───────────────────────────────────────
        $result = $this->otpCache->verifyPhoneOtp($verificationId, $otpCode);

        switch ($result) {
            case 'not_found':
                return response()->json([
                    'success' => false,
                    'message' => 'Verification session expired or not found',
                    'errors'  => ['verification_id' => ['This verification session has expired. Please request a new code.']],
                    'meta'    => $this->meta(),
                ], 422);

            case 'already_used':
                return response()->json([
                    'success' => false,
                    'message' => 'Code already used',
                    'errors'  => ['otp_code' => ['This code has already been used.']],
                    'meta'    => $this->meta(),
                ], 422);

            case 'locked':
                return response()->json([
                    'success' => false,
                    'message' => 'Too many attempts — please request a new code',
                    'errors'  => ['otp_code' => ['Max attempts reached. Please request a new verification code.']],
                    'meta'    => $this->meta(),
                ], 429);

            case 'invalid':
                // Read remaining attempts from Redis
                $otp      = $this->otpCache->getPhoneOtp($verificationId);
                $attempts = $otp['attempts'] ?? 3;
                $remaining = max(0, 3 - $attempts);

                return response()->json([
                    'success' => false,
                    'message' => 'Incorrect verification code',
                    'errors'  => ['otp_code' => ['The verification code is incorrect.']],
                    'data'    => ['attempts_remaining' => $remaining],
                    'meta'    => $this->meta(),
                ], 422);
        }

        // ── ✅ OTP is valid ────────────────────────────────────────────────
        $otp         = $this->otpCache->getPhoneOtp($verificationId);
        $phoneNumber = $otp['phone_number'];
        $signupData  = $data['signup_data'] ?? [];
        $expiresAt   = now()->addDays(30);

        if ($isSignup) {
            // ── New user via phone ─────────────────────────────────────────
            $user = User::create([
                'phone_number'       => $phoneNumber,
                'first_name'         => $signupData['first_name'] ?? 'Explorer',
                'last_name'          => $signupData['last_name'] ?? '',
                'display_name'       => trim(($signupData['first_name'] ?? 'Explorer') . ' ' . ($signupData['last_name'] ?? '')),
                'travel_styles'      => $signupData['travel_styles'] ?? [],
                'interests'          => $signupData['interests'] ?? [],
                'explorer_level'     => 'explorer',
                'current_xp'         => 0,
                'streak_days'        => 1,
                'profile_completion' => 30,
                'role'               => 'traveler',
                'phone_verified_at'  => now(),
            ]);

            $token = $user->createToken('mobile', ['*'], $expiresAt);

            // Cache user data for fast subsequent /me calls
            $this->otpCache->cacheUser($user->id, (new UserResource($user))->resolve());

            return response()->json([
                'success' => true,
                'message' => 'Phone verified! Welcome to ZuriTrails. 🦁',
                'data'    => [
                    'user'                => new UserResource($user),
                    'token'               => [
                        'access_token' => $token->plainTextToken,
                        'token_type'   => 'Bearer',
                        'expires_at'   => $expiresAt->toIso8601String(),
                    ],
                    'requires_onboarding' => true,
                ],
                'meta' => $this->meta(),
            ], 201);
        }

        // ── Existing user — login via phone ────────────────────────────────
        $user = User::where('phone_number', $phoneNumber)->firstOrFail();
        $user->updateStreak();

        $token = $user->createToken('mobile', ['*'], $expiresAt);
        $stats = $this->authService->getUserStats($user);

        // Warm user cache
        $this->otpCache->cacheUser($user->id, (new UserResource($user))->resolve());

        return response()->json([
            'success' => true,
            'message' => "Welcome back, {$user->first_name}! 👋",
            'data'    => [
                'user'  => new UserResource($user),
                'token' => [
                    'access_token' => $token->plainTextToken,
                    'token_type'   => 'Bearer',
                    'expires_at'   => $expiresAt->toIso8601String(),
                ],
                'stats' => $stats,
            ],
            'meta' => $this->meta(),
        ]);
    }

    // ───────────────────────────────────────────────────────────────────────
    // POST /api/v1/auth/phone/resend-otp
    // From: verification_code_screen.dart "Resend code" button
    // Respects 60-second cooldown stored in Redis
    // ───────────────────────────────────────────────────────────────────────
    public function resendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'verification_id' => 'required|string',
        ]);

        // Load the existing OTP from Redis
        $otp = $this->otpCache->getPhoneOtp($request->verification_id);

        if (!$otp) {
            return response()->json([
                'success' => false,
                'message' => 'Verification session expired or not found. Please start again.',
                'meta'    => $this->meta(),
            ], 404);
        }

        $phoneNumber = $otp['phone_number'];

        // Check resend cooldown
        $cooldown = $this->otpCache->phoneResendCooldownSeconds($phoneNumber);
        if ($cooldown > 0) {
            return response()->json([
                'success' => false,
                'message' => "Please wait {$cooldown} seconds before resending.",
                'data'    => [
                    'cooldown_seconds'    => $cooldown,
                    'resend_available_at' => now()->addSeconds($cooldown)->toIso8601String(),
                ],
                'meta' => $this->meta(),
            ], 429);
        }

        // Check hourly rate limit
        if ($this->otpCache->isPhoneRateLimited($phoneNumber)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many OTP requests today. Please try again later.',
                'meta'    => $this->meta(),
            ], 429);
        }

        // Generate fresh OTP and replace in Redis
        $this->otpCache->invalidatePhoneOtp($phoneNumber);

        $newOtpCode     = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $verificationId = 'ver_' . Str::random(16);
        $expiresAt      = now()->addSeconds(300);

        $this->otpCache->storePhoneOtp($phoneNumber, $newOtpCode, $verificationId);
        $this->otpCache->incrementPhoneRateLimit($phoneNumber);

        $this->smsService->sendOtp($phoneNumber, $newOtpCode);

        return response()->json([
            'success' => true,
            'message' => "New verification code sent to {$this->maskPhone($phoneNumber)}",
            'data'    => [
                'verification_id'     => $verificationId,
                'phone_number'        => $phoneNumber,
                'expires_at'          => $expiresAt->toIso8601String(),
                'expires_in_seconds'  => 300,
                'resend_available_at' => now()->addSeconds(60)->toIso8601String(),
            ],
            'meta' => $this->meta(),
        ]);
    }

    private function maskPhone(string $phone): string
    {
        return substr($phone, 0, 6) . '****' . substr($phone, -2);
    }

    private function meta(): array
    {
        return ['timestamp' => now()->toIso8601String(), 'version' => '1.0.0'];
    }
}
