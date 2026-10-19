<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityGameSession extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'pack_id',
        'team_names',
        'scores',
        'cards_played',
        'duration_seconds',
        'completed_at',
        'metadata',
    ];

    protected $casts = [
        'team_names' => 'array',
        'scores' => 'array',
        'metadata' => 'array',
        'cards_played' => 'integer',
        'duration_seconds' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pack(): BelongsTo
    {
        return $this->belongsTo(ActivityGamePack::class, 'pack_id');
    }
}
