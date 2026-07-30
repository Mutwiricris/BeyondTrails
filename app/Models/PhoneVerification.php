<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneVerification extends Model
{
    protected $table = 'phone_verifications';

    public $timestamps = false;

    protected $fillable = [
        'phone_number',
        'otp_code',
        'verification_id',
        'is_verified',
        'attempts',
        'expires_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'attempts'    => 'integer',
        'expires_at'  => 'datetime',
        'created_at'  => 'datetime',
    ];

    /**
     * Check if the OTP has expired.
     */
    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }

    /**
     * Check if the max attempts have been reached (3).
     */
    public function hasExceededAttempts(): bool
    {
        return $this->attempts >= config('auth.otp_max_attempts', 3);
    }

    /**
     * Increment attempts and save.
     */
    public function recordFailedAttempt(): void
    {
        $this->increment('attempts');
    }
}
