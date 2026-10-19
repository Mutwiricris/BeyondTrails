<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'category',
        'title',
        'body',
        'image_url',
        'target_screen',
        'payload',
        'action_buttons',
        'is_read',
        'read_at',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'payload' => 'array',
        'action_buttons' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
