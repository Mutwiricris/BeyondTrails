<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscoverRoute extends Model
{
    use HasUuids;

    protected $table = 'discover_routes';

    protected $fillable = [
        'slug', 'name', 'description', 'type', 'difficulty', 'region', 'country',
        'start_point_name', 'start_latitude', 'start_longitude',
        'end_point_name', 'end_latitude', 'end_longitude',
        'waypoints', 'distance_km', 'duration_minutes',
        'elevation_gain_meters', 'highlights',
        'cover_photo_url', 'photos',
        'rating', 'completed_count', 'review_count',
        'is_published', 'is_featured', 'xp_reward',
        'created_by_user_id',
    ];

    protected $casts = [
        'waypoints'  => 'array',
        'highlights' => 'array',
        'photos'     => 'array',
        'rating'     => 'decimal:2',
        'distance_km' => 'decimal:2',
        'elevation_gain_meters' => 'decimal:2',
        'start_latitude'  => 'decimal:7',
        'start_longitude' => 'decimal:7',
        'end_latitude'    => 'decimal:7',
        'end_longitude'   => 'decimal:7',
        'is_published' => 'boolean',
        'is_featured'  => 'boolean',
    ];

    public function segments(): HasMany
    {
        return $this->hasMany(RouteSegment::class, 'route_id')->orderBy('sort_order');
    }

    public function routeSegments(): HasMany
    {
        return $this->segments();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewable_id')
            ->where('reviewable_type', 'route')->latest();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function getFormattedDurationAttribute(): string
    {
        $hours   = intdiv($this->duration_minutes, 60);
        $minutes = $this->duration_minutes % 60;
        return $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
    }

    public function getFormattedDistanceAttribute(): string
    {
        return number_format($this->distance_km, 1) . ' km';
    }
}
