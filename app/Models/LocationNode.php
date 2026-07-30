<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationNode extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'parent_id',
        'tier',
        'latitude',
        'longitude',
        'banner_image_url',
        'description',
        'spot_count',
        'active_explorers',
        'metadata',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(LocationNode::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(LocationNode::class, 'parent_id');
    }

    public function gems(): HasMany
    {
        return $this->hasMany(HiddenGem::class, 'location_node_id');
    }

    public function destinations(): HasMany
    {
        return $this->hasMany(Destination::class, 'location_node_id');
    }
}
