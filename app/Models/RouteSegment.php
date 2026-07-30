<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteSegment extends Model
{
    use HasUuids;

    protected $fillable = [
        'route_id', 'name', 'description', 'type',
        'start_latitude', 'start_longitude',
        'end_latitude', 'end_longitude',
        'distance_km', 'best_time_minutes',
        'points_reward', 'discovered_by_count',
        'photos', 'sort_order',
    ];

    protected $casts = [
        'photos' => 'array',
        'distance_km'       => 'decimal:3',
        'best_time_minutes' => 'decimal:2',
        'start_latitude'    => 'decimal:7',
        'start_longitude'   => 'decimal:7',
        'end_latitude'      => 'decimal:7',
        'end_longitude'     => 'decimal:7',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(DiscoverRoute::class, 'route_id');
    }
}
