<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityGameCard extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'pack_id',
        'category',
        'prompt',
        'answer',
        'forbidden_words',
        'hint',
        'points',
        'timer_seconds',
        'metadata',
        'sort_order',
    ];

    protected $casts = [
        'forbidden_words' => 'array',
        'metadata' => 'array',
        'points' => 'integer',
        'timer_seconds' => 'integer',
        'sort_order' => 'integer',
    ];

    public function pack(): BelongsTo
    {
        return $this->belongsTo(ActivityGamePack::class, 'pack_id');
    }
}
