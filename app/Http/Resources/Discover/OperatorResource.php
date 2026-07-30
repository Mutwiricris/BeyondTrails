<?php

namespace App\Http\Resources\Discover;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OperatorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'slug'                  => $this->slug,
            'emoji'                 => '🌍',
            'name'                  => $this->name,
            'tagline'               => $this->tagline,
            'description'           => $this->description,
            'logo_url'              => $this->logo_url,
            'cover_image_url'       => $this->cover_image_url,
            'gallery'               => $this->gallery ?? [],
            'contact'               => [
                'email'   => $this->email,
                'phone'   => $this->phone,
                'website' => $this->website,
                'address' => $this->address,
                'city'    => $this->city,
                'country' => $this->country,
            ],
            'business_type'         => $this->business_type,
            'specializations'       => $this->specializations ?? [],
            'certifications'        => $this->certifications ?? [],
            'services'              => $this->services ?? [],
            'languages'             => $this->languages ?? [],
            'social_links'          => $this->social_links ?? [],
            'is_verified'           => $this->is_verified,
            'verification_badge'    => $this->verification_badge,
            'license_number'        => $this->license_number,
            'member_since'          => $this->member_since?->toDateString(),
            'member_years'          => $this->member_years,
            'rating'                => (float) $this->rating,
            'review_count'          => $this->review_count,
            'total_bookings'        => $this->total_bookings,
            'tours_offered'         => $this->tours_offered,
            'accommodations_offered'=> $this->accommodations_offered,
            'is_featured'           => $this->is_featured,
            'cancellation_policy'   => $this->cancellation_policy,
            'payment_terms'         => $this->payment_terms,
            'safety_measures'       => $this->safety_measures,
        ];
    }
}
