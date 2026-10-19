<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActivityGamePack extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'slug',
        'title',
        'subtitle',
        'description',
        'game_type',
        'occasion',
        'min_players',
        'max_players',
        'duration_minutes',
        'difficulty',
        'icon_name',
        'color_hex',
        'is_featured',
        'tags',
        'rules',
        'metadata',
    ];

    protected $casts = [
        'tags' => 'array',
        'metadata' => 'array',
        'is_featured' => 'boolean',
        'min_players' => 'integer',
        'max_players' => 'integer',
        'duration_minutes' => 'integer',
    ];

    public function cards(): HasMany
    {
        return $this->hasMany(ActivityGameCard::class, 'pack_id')->orderBy('sort_order');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ActivityGameSession::class, 'pack_id');
    }
}
