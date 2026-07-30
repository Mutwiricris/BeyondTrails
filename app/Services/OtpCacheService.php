<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * OTP Cache Service
 *
 * Replaces the database-backed phone_verifications table with a Redis-first
 * approach for speed and atomic operations. All OTP state lives in Redis
 * with TTL-based auto-expiry. The DB table is used only as a fallback audit log.
 *
 * Redis Key Conventions:
 *   otp:data:{verification_id}     → OTP payload (code, phone, attempts, expiry)
 *   otp:phone:{phone_number}       → active verification_id for this phone
 *   otp:rate:{phone_number}        → resend rate-limit counter
 *   otp:attempts:{verification_id} → failed attempt counter
 *   otp:lock:{phone_number}        → temporary lock after max failed attempts
 *
 *   email_otp:data:{verification_id}     → Email OTP payload
 *   email_otp:rate:{email}               → resend rate limit for email OTP
 *   email_otp:attempts:{verification_id} → failed attempts
 *
 *   user_cache:{user_id}           → cached UserResource JSON
 *   user_stats:{user_id}           → cached user stats
 *   rate_limit:login:{email}       → login attempt counter (brute force protection)
 *   rate_limit:register:{ip}       → registration rate limiter
 */
class OtpCacheService
{
    // ── TTL Constants ─────────────────────────────────────────────────────────
    private const OTP_TTL_SECONDS       = 300;   // 5 minutes OTP validity
    private const RESEND_COOLDOWN       = 60;    // 60s before resend allowed
    private const LOCK_TTL_SECONDS      = 900;   // 15 min lock after max attempts
    private const MAX_OTP_ATTEMPTS      = 3;     // Max wrong guesses
    private const MAX_RESEND_PER_HOUR   = 5;     // Max OTP requests per phone per hour

    // ── Email OTP TTL ─────────────────────────────────────────────────────────
    private const EMAIL_OTP_TTL         = 600;   // 10 minutes for email OTP
    private const EMAIL_RESEND_COOLDOWN = 120;   // 2 min email resend cooldown
    private const MAX_EMAIL_RESEND      = 3;     // Max email OTP resends per hour

    // ─────────────────────────────────────────────────────────────────────────
    // PHONE OTP — WRITE
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Store a new Phone OTP in Redis.
     * Atomically replaces any existing OTP for this phone number.
     *
     * Returns the verification_id.
     */
    public function storePhoneOtp(string $phoneNumber, string $otpCode, string $verificationId): void
    {
        $payload = [
            'phone_number'    => $phoneNumber,
            'otp_code'        => $otpCode,
            'verification_id' => $verificationId,
            'is_verified'     => false,
            'attempts'        => 0,
            'created_at'      => now()->toIso8601String(),
            'expires_at'      => now()->addSeconds(self::OTP_TTL_SECONDS)->toIso8601String(),
        ];

        // Store OTP data keyed by verification_id
        Cache::put(
            "otp:data:{$verificationId}",
            $payload,
            self::OTP_TTL_SECONDS
        );

        // Map phone_number → verification_id (so we can invalidate old OTPs)
        Cache::put(
            "otp:phone:{$phoneNumber}",
            $verificationId,
            self::OTP_TTL_SECONDS
        );

        Log::info("📱 OTP stored in Redis", [
            'phone'           => $this->maskPhone($phoneNumber),
            'verification_id' => $verificationId,
            'expires_in'      => self::OTP_TTL_SECONDS . 's',
        ]);
    }

    /**
     * Invalidate any existing OTP for a phone number.
     * Called before generating a new OTP.
     */
    public function invalidatePhoneOtp(string $phoneNumber): void
    {
        $existingId = Cache::get("otp:phone:{$phoneNumber}");
        if ($existingId) {
            Cache::forget("otp:data:{$existingId}");
            Cache::forget("otp:phone:{$phoneNumber}");
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PHONE OTP — READ & VERIFY
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Retrieve OTP data from Redis.
     * Returns null if expired or not found.
     */
    public function getPhoneOtp(string $verificationId): ?array
    {
        return Cache::get("otp:data:{$verificationId}");
    }

    /**
     * Verify an OTP code against stored data.
     * Handles attempt tracking and auto-cleanup on success.
     *
     * Returns one of:
     *   'valid'         — OTP matched
     *   'invalid'       — Wrong code
     *   'expired'       — TTL elapsed
     *   'not_found'     — Verification ID doesn't exist
     *   'locked'        — Max attempts exceeded
     *   'already_used'  — Already verified
     */
    public function verifyPhoneOtp(string $verificationId, string $inputCode): string
    {
        // Check if phone is locked (too many failures)
        $otp = $this->getPhoneOtp($verificationId);

        if (!$otp) {
            return 'not_found';
        }

        if ($otp['is_verified'] ?? false) {
            return 'already_used';
        }

        if ($otp['attempts'] >= self::MAX_OTP_ATTEMPTS) {
            return 'locked';
        }

        if ($otp['otp_code'] !== $inputCode) {
            // Increment attempt counter atomically
            $otp['attempts']++;
            Cache::put("otp:data:{$verificationId}", $otp, self::OTP_TTL_SECONDS);

            if ($otp['attempts'] >= self::MAX_OTP_ATTEMPTS) {
                // Lock the phone number
                $phoneNumber = $otp['phone_number'];
                Cache::put("otp:lock:{$phoneNumber}", true, self::LOCK_TTL_SECONDS);
                Log::warning("🔒 Phone OTP locked after max attempts", ['phone' => $this->maskPhone($phoneNumber)]);
            }

            return 'invalid';
        }

        // ✅ Success — mark as verified and clean up
        $otp['is_verified'] = true;
        Cache::put("otp:data:{$verificationId}", $otp, 60); // Keep 60s for the controller to read phone_number
        Cache::forget("otp:phone:{$otp['phone_number']}");

        Log::info("✅ Phone OTP verified", ['verification_id' => $verificationId]);
        return 'valid';
    }

    /**
     * Check if a phone number is rate-limited (too many resend requests).
     */
    public function isPhoneRateLimited(string $phoneNumber): bool
    {
        $resendKey = "otp:rate:{$phoneNumber}";
        $count     = (int) Cache::get($resendKey, 0);
        return $count >= self::MAX_RESEND_PER_HOUR;
    }

    /**
     * Increment the resend rate limiter for a phone number.
     */
    public function incrementPhoneRateLimit(string $phoneNumber): void
    {
        $resendKey = "otp:rate:{$phoneNumber}";
        if (Cache::has($resendKey)) {
            Cache::increment($resendKey);
        } else {
            Cache::put($resendKey, 1, 3600); // 1-hour window
        }
    }

    /**
     * Seconds remaining before resend is allowed (60s cooldown).
     */
    public function phoneResendCooldownSeconds(string $phoneNumber): int
    {
        $existingId = Cache::get("otp:phone:{$phoneNumber}");
        if (!$existingId) return 0;

        $otp = Cache::get("otp:data:{$existingId}");
        if (!$otp) return 0;

        $createdAt = \Carbon\Carbon::parse($otp['created_at']);
        $cooldownEnd = $createdAt->addSeconds(self::RESEND_COOLDOWN);
        return max(0, (int) now()->diffInSeconds($cooldownEnd, false));
    }

    /**
     * Check if phone is temporarily locked.
     */
    public function isPhoneLocked(string $phoneNumber): bool
    {
        return Cache::has("otp:lock:{$phoneNumber}");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // EMAIL OTP — WRITE
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Store an Email OTP (for email verification flows).
     */
    public function storeEmailOtp(string $email, string $otpCode, string $verificationId): void
    {
        $payload = [
            'email'           => $email,
            'otp_code'        => $otpCode,
            'verification_id' => $verificationId,
            'is_verified'     => false,
            'attempts'        => 0,
            'created_at'      => now()->toIso8601String(),
            'expires_at'      => now()->addSeconds(self::EMAIL_OTP_TTL)->toIso8601String(),
        ];

        Cache::put("email_otp:data:{$verificationId}", $payload, self::EMAIL_OTP_TTL);
        Cache::put("email_otp:email:{$email}", $verificationId, self::EMAIL_OTP_TTL);

        Log::info("📧 Email OTP stored in Redis", [
            'email'           => $this->maskEmail($email),
            'verification_id' => $verificationId,
        ]);
    }

    /**
     * Invalidate any existing email OTP.
     */
    public function invalidateEmailOtp(string $email): void
    {
        $existingId = Cache::get("email_otp:email:{$email}");
        if ($existingId) {
            Cache::forget("email_otp:data:{$existingId}");
            Cache::forget("email_otp:email:{$email}");
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // EMAIL OTP — READ & VERIFY
    // ─────────────────────────────────────────────────────────────────────────

    public function getEmailOtp(string $verificationId): ?array
    {
        return Cache::get("email_otp:data:{$verificationId}");
    }

    public function verifyEmailOtp(string $verificationId, string $inputCode): string
    {
        $otp = $this->getEmailOtp($verificationId);

        if (!$otp) return 'not_found';
        if ($otp['is_verified'] ?? false) return 'already_used';
        if ($otp['attempts'] >= self::MAX_OTP_ATTEMPTS) return 'locked';

        if ($otp['otp_code'] !== $inputCode) {
            $otp['attempts']++;
            Cache::put("email_otp:data:{$verificationId}", $otp, self::EMAIL_OTP_TTL);
            return 'invalid';
        }

        $otp['is_verified'] = true;
        Cache::put("email_otp:data:{$verificationId}", $otp, 120);
        Cache::forget("email_otp:email:{$otp['email']}");
        return 'valid';
    }

    public function isEmailRateLimited(string $email): bool
    {
        return (int) Cache::get("email_otp:rate:{$email}", 0) >= self::MAX_EMAIL_RESEND;
    }

    public function incrementEmailRateLimit(string $email): void
    {
        $key = "email_otp:rate:{$email}";
        if (Cache::has($key)) {
            Cache::increment($key);
        } else {
            Cache::put($key, 1, 3600);
        }
    }

    public function emailResendCooldownSeconds(string $email): int
    {
        $existingId = Cache::get("email_otp:email:{$email}");
        if (!$existingId) return 0;
        $otp = Cache::get("email_otp:data:{$existingId}");
        if (!$otp) return 0;
        $cooldownEnd = \Carbon\Carbon::parse($otp['created_at'])->addSeconds(self::EMAIL_RESEND_COOLDOWN);
        return max(0, (int) now()->diffInSeconds($cooldownEnd, false));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // USER CACHE
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Cache a user's data after login/register.
     * Avoids repeated DB lookups on every /me request.
     */
    public function cacheUser(string $userId, array $userData): void
    {
        Cache::put("user_cache:{$userId}", $userData, 3600); // 1 hour
    }

    public function getCachedUser(string $userId): ?array
    {
        return Cache::get("user_cache:{$userId}");
    }

    public function invalidateUserCache(string $userId): void
    {
        Cache::forget("user_cache:{$userId}");
        Cache::forget("user_stats:{$userId}");
    }

    /**
     * Cache user stats (expensive aggregation queries).
     */
    public function cacheUserStats(string $userId, array $stats): void
    {
        Cache::put("user_stats:{$userId}", $stats, 300); // 5 minutes
    }

    public function getCachedUserStats(string $userId): ?array
    {
        return Cache::get("user_stats:{$userId}");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LOGIN BRUTE-FORCE PROTECTION
    // ─────────────────────────────────────────────────────────────────────────

    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOGIN_LOCKOUT_TTL  = 900; // 15 minutes

    public function recordFailedLogin(string $email, string $ip): void
    {
        foreach (["rate_limit:login:email:{$email}", "rate_limit:login:ip:{$ip}"] as $key) {
            if (Cache::has($key)) {
                Cache::increment($key);
            } else {
                Cache::put($key, 1, self::LOGIN_LOCKOUT_TTL);
            }
        }
    }

    public function isLoginLockedOut(string $email, string $ip): bool
    {
        return (int) Cache::get("rate_limit:login:email:{$email}", 0) >= self::MAX_LOGIN_ATTEMPTS
            || (int) Cache::get("rate_limit:login:ip:{$ip}", 0) >= self::MAX_LOGIN_ATTEMPTS * 3;
    }

    public function clearLoginAttempts(string $email, string $ip): void
    {
        Cache::forget("rate_limit:login:email:{$email}");
        Cache::forget("rate_limit:login:ip:{$ip}");
    }

    public function getRemainingLoginAttempts(string $email): int
    {
        $used = (int) Cache::get("rate_limit:login:email:{$email}", 0);
        return max(0, self::MAX_LOGIN_ATTEMPTS - $used);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    private function maskPhone(string $phone): string
    {
        return substr($phone, 0, 6) . '****' . substr($phone, -2);
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email);
        return substr($local, 0, 2) . '***@' . $domain;
    }
}
