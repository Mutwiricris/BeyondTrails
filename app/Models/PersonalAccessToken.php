<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    /**
     * Find the token instance for the given token.
     *
     * @param  string  $token
     * @return static|null
     */
    public static function findToken($token)
    {
        if (str_contains($token, '|')) {
            [$id, $token] = explode('|', $token, 2);
        }

        $hashedToken = hash('sha256', $token);
        $cacheKey = 'sanctum_token_' . $hashedToken;

        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($hashedToken) {
            return static::where('token', $hashedToken)->first();
        });
    }

    /**
     * Boot the model.
     */
    public static function boot()
    {
        parent::boot();

        static::saved(function ($token) {
            Cache::forget('sanctum_token_' . $token->token);
        });

        static::deleted(function ($token) {
            Cache::forget('sanctum_token_' . $token->token);
        });
    }
}
