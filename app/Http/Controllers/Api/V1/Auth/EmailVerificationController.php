<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class EmailVerificationController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    // ───────────────────────────────────────────────────────────────────────
    // POST /api/v1/auth/email/send-verification  [AUTH REQUIRED]
    // Resend email verification link
    // ───────────────────────────────────────────────────────────────────────
    public function send(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email is already verified.',
                'meta'    => $this->meta(),
            ], 422);
        }

        $this->authService->sendEmailVerification($user);

        return response()->json([
            'success' => true,
            'message' => "Verification email sent to {$user->email}",
            'meta'    => $this->meta(),
        ]);
    }

    // ───────────────────────────────────────────────────────────────────────
    // POST /api/v1/auth/email/verify
    // Called when user clicks link in email
    // Payload: { user_id, verification_code }
    // ───────────────────────────────────────────────────────────────────────
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'user_id'           => 'required|uuid',
            'verification_code' => 'required|string',
        ]);

        $user = User::findOrFail($request->user_id);

        // Verify the signed hash matches
        $expectedHash = sha1($user->email);

        if ($request->verification_code !== $expectedHash) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification link.',
                'errors'  => ['verification_code' => ['The verification code is invalid.']],
                'meta'    => $this->meta(),
            ], 422);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'success' => true,
                'message' => 'Email already verified.',
                'meta'    => $this->meta(),
            ]);
        }

        $user->update([
            'email_verified_at'  => now(),
            'profile_completion' => max($user->profile_completion, 35),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully! Your account is now active.',
            'meta'    => $this->meta(),
        ]);
    }

    private function meta(): array
    {
        return ['timestamp' => now()->toIso8601String(), 'version' => '1.0.0'];
    }
}
