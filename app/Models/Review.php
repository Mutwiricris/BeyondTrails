<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasUuids;

    protected $fillable = [
        'reviewable_type', 'reviewable_id', 'user_id',
        'rating', 'comment', 'photos',
        'is_verified_visit', 'helpful_count',
    ];

    protected $casts = [
        'photos'            => 'array',
        'rating'            => 'decimal:1',
        'is_verified_visit' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
