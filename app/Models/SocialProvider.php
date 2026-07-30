<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialProvider extends Model
{
    protected $table = 'social_providers';

    protected $fillable = [
        'user_id',
        'provider',           // google|apple|facebook
        'provider_id',        // OAuth UID from provider
        'provider_token',     // Access token
        'provider_refresh_token',
        'provider_email',
    ];

    protected $hidden = [
        'provider_token',
        'provider_refresh_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
