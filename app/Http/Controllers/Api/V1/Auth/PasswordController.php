<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    // ───────────────────────────────────────────────────────────────────────
    // POST /api/v1/auth/password/forgot
    // From: login_screen.dart "Forgot Password?" button
    // ───────────────────────────────────────────────────────────────────────
    public function forgot(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Always return success (security: don't leak whether email exists)
        Password::sendResetLink($request->only('email'));

        return response()->json([
            'success' => true,
            'message' => 'If this email is registered, a password reset link has been sent.',
            'meta'    => $this->meta(),
        ]);
    }

    // ───────────────────────────────────────────────────────────────────────
    // POST /api/v1/auth/password/reset
    // User arrives via email deep link: zuritrails://reset-password?token=...
    // ───────────────────────────────────────────────────────────────────────
    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'email'                 => 'required|email',
            'token'                 => 'required|string',
            'password'              => ['required', 'confirmed', PasswordRule::min(8)],
            'password_confirmation' => 'required|string',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Revoke all existing tokens for security
                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset successful. Please login with your new password.',
                'meta'    => $this->meta(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Password reset failed',
            'errors'  => ['token' => [__($status)]],
            'meta'    => $this->meta(),
        ], 422);
    }

    // ───────────────────────────────────────────────────────────────────────
    // POST /api/v1/auth/password/change  [AUTH REQUIRED]
    // Authenticated user changing their password
    // ───────────────────────────────────────────────────────────────────────
    public function change(Request $request): JsonResponse
    {
        $request->validate([
            'current_password'      => 'required|string',
            'password'              => ['required', 'confirmed', PasswordRule::min(8)],
            'password_confirmation' => 'required|string',
        ]);

        $user = $request->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Revoke all other tokens (keep current session active)
        $currentTokenId = $user->currentAccessToken()->id;
        $user->tokens()->where('id', '!=', $currentTokenId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
            'meta'    => $this->meta(),
        ]);
    }

    private function meta(): array
    {
        return ['timestamp' => now()->toIso8601String(), 'version' => '1.0.0'];
    }
}
