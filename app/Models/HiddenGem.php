<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HiddenGem extends Model
{
    use HasUuids;

    protected $fillable = [
        'slug', 'name', 'description', 'short_description',
        'category', 'type', 'tags',
        'location_name', 'address', 'city', 'county', 'region', 'country',
        'latitude', 'longitude',
        'cover_image_url', 'gallery',
        'difficulty', 'difficulty_level', 'best_time_to_visit',
        'entry_fee_citizens_kes', 'entry_fee_residents_kes',
        'entry_fee_non_residents_usd', 'entry_fee_children_kes',
        'is_free_entry',
        'access_info', 'facilities', 'amenities',
        'what_to_bring', 'best_for',
        'transport_options', 'parking_info',
        'contact_phone', 'contact_email', 'website',
        'operator_id', 'submitted_by_user_id',
        'rating', 'review_count', 'visitor_count',
        'is_verified', 'is_featured', 'is_published',
        'xp_reward',
        
        // New Explore Fields
        'audio_guide_url', 'video_url', 'discovered_by_count',
        'upvotes', 'downvotes', 'added_by_name', 'is_local_guide',
        'verification_status', 'location_node_id', 'safety_notes',
        'requires_permit', 'is_quest_unlock', 'accessibility',
    ];

    protected $casts = [
        'tags'              => 'array',
        'gallery'           => 'array',
        'facilities'        => 'array',
        'amenities'         => 'array',
        'what_to_bring'     => 'array',
        'best_for'          => 'array',
        'transport_options' => 'array',
        'latitude'          => 'decimal:7',
        'longitude'         => 'decimal:7',
        'rating'            => 'decimal:2',
        'entry_fee_citizens_kes'      => 'decimal:2',
        'entry_fee_residents_kes'     => 'decimal:2',
        'entry_fee_non_residents_usd' => 'decimal:2',
        'entry_fee_children_kes'      => 'decimal:2',
        'is_free_entry'   => 'boolean',
        'is_verified'     => 'boolean',
        'is_featured'     => 'boolean',
        'is_published'    => 'boolean',
        'is_local_guide'  => 'boolean',
        'requires_permit' => 'boolean',
        'is_quest_unlock' => 'boolean',
        'accessibility'   => 'array',
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

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewable_id')
            ->where('reviewable_type', 'hidden_gem')
            ->latest();
    }

    public function nearbyAttractions(): HasMany
    {
        return $this->hasMany(NearbyAttraction::class, 'source_id')
            ->where('source_type', 'hidden_gem')
            ->orderBy('distance_km');
    }

    public function tips(): HasMany
    {
        return $this->hasMany(LocalTip::class, 'tippable_id')
            ->where('tippable_type', 'hidden_gem')
            ->orderBy('sort_order');
    }

    // ── Computed ───────────────────────────────────────────────────────────────

    public function getEntryFeeInfoAttribute(): array
    {
        if ($this->is_free_entry) {
            return ['is_free' => true];
        }
        return [
            'is_free'       => false,
            'citizens'      => $this->entry_fee_citizens_kes ? 'KSh ' . number_format($this->entry_fee_citizens_kes) : null,
            'residents'     => $this->entry_fee_residents_kes ? 'KSh ' . number_format($this->entry_fee_residents_kes) : null,
            'non_residents' => $this->entry_fee_non_residents_usd ? '$' . number_format($this->entry_fee_non_residents_usd) : null,
            'children'      => $this->entry_fee_children_kes ? 'KSh ' . number_format($this->entry_fee_children_kes) : null,
        ];
    }
}
