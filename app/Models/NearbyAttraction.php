<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NearbyAttraction extends Model
{
    use HasUuids;

    protected $fillable = [
        'source_type', 'source_id', 'name', 'category',
        'category_icon', 'distance_km', 'image_url',
        'latitude', 'longitude', 'sort_order',
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
        'latitude'    => 'decimal:7',
        'longitude'   => 'decimal:7',
    ];
}
