<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Operator extends Model
{
    use HasUuids;

    protected $fillable = [
        'slug', 'name', 'tagline', 'description',
        'logo_url', 'cover_image_url', 'gallery',
        'email', 'phone', 'website', 'address', 'city', 'country',
        'business_type', 'specializations', 'certifications',
        'services', 'languages', 'operating_hours', 'social_links',
        'cancellation_policy', 'payment_terms', 'safety_measures',
        'is_verified', 'verification_badge', 'license_number',
        'rating', 'review_count', 'total_bookings',
        'tours_offered', 'accommodations_offered',
        'member_since', 'is_active', 'is_featured',
    ];

    protected $casts = [
        'gallery'         => 'array',
        'specializations' => 'array',
        'certifications'  => 'array',
        'services'        => 'array',
        'languages'       => 'array',
        'operating_hours' => 'array',
        'social_links'    => 'array',
        'rating'          => 'decimal:2',
        'is_verified'     => 'boolean',
        'is_active'       => 'boolean',
        'is_featured'     => 'boolean',
        'member_since'    => 'date',
    ];

    public function destinations(): HasMany
    {
        return $this->hasMany(Destination::class);
    }

    public function hiddenGems(): HasMany
    {
        return $this->hasMany(HiddenGem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewable_id')
            ->where('reviewable_type', 'operator')
            ->latest();
    }

    public function getMemberYearsAttribute(): int
    {
        return $this->member_since
            ? (int) $this->member_since->diffInYears(now())
            : 0;
    }
}
