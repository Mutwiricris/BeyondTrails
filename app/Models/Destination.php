<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class Destination extends Model
{
    use HasUuids;

    protected $fillable = [
        'slug', 'title', 'description', 'short_description', 'category',
        'location', 'county', 'region', 'country',
        'latitude', 'longitude',
        'cover_image_url', 'gallery',
        'price_kes', 'price_usd',
        'rating', 'review_count',
        'duration_days', 'duration_label', 'group_size_max',
        'difficulty', 'tour_type',
        'highlights', 'included', 'excluded', 'what_to_bring',
        'languages_spoken',
        'meeting_point', 'meeting_lat', 'meeting_lng',
        'transport_info', 'meal_info', 'health_safety_info',
        'cancellation_policy', 'faqs',
        'operator_id',
        'is_popular', 'is_featured', 'is_active',
        'xp_reward', 'metadata',
        
        // New Explore Fields
        'location_node_id', 'busyness_score', 'crowd_density',
        'current_visitors', 'peak_hours', 'weather_note',
        'instant_booking', 'available_days',
    ];

    protected $casts = [
        'gallery'          => 'array',
        'highlights'       => 'array',
        'included'         => 'array',
        'excluded'         => 'array',
        'what_to_bring'    => 'array',
        'languages_spoken' => 'array',
        'transport_info'   => 'array',
        'meal_info'        => 'array',
        'health_safety_info' => 'array',
        'faqs'             => 'array',
        'metadata'         => 'array',
        'price_kes'        => 'integer',
        'price_usd'        => 'decimal:2',
        'rating'           => 'decimal:2',
        'latitude'         => 'decimal:7',
        'longitude'        => 'decimal:7',
        'is_popular'       => 'boolean',
        'is_featured'      => 'boolean',
        'is_active'        => 'boolean',
        'instant_booking'  => 'boolean',
        'available_days'   => 'array',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function locationNode(): BelongsTo
    {
        return $this->belongsTo(LocationNode::class);
    }

    public function itinerary(): HasMany
    {
        return $this->hasMany(DestinationItinerary::class)->orderBy('day_number');
    }

    public function accommodations(): HasMany
    {
        return $this->hasMany(DestinationAccommodation::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewable_id')
            ->where('reviewable_type', 'destination')
            ->latest();
    }

    public function tips(): HasMany
    {
        return $this->hasMany(LocalTip::class, 'tippable_id')
            ->where('tippable_type', 'destination')
            ->orderBy('sort_order');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function getPriceDisplayAttribute(): string
    {
        return 'KSh ' . number_format($this->price_kes);
    }
}
