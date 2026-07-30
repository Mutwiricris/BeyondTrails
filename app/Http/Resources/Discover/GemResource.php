<?php

namespace App\Http\Resources\Discover;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'slug'              => $this->slug,
            'name'              => $this->name,
            'description'       => $this->description,
            'short_description' => $this->short_description,
            'category'          => $this->category,
            'type'              => $this->type,
            'tags'              => $this->tags ?? [],
            'location'          => [
                'name'    => $this->location_name,
                'address' => $this->address,
                'city'    => $this->city,
                'county'  => $this->county,
                'region'  => $this->region,
                'country' => $this->country,
                'lat'     => $this->latitude,
                'lng'     => $this->longitude,
            ],
            'cover_image_url'  => $this->cover_image_url,
            'gallery'          => $this->gallery ?? [],
            'difficulty'       => $this->difficulty,
            'difficulty_level' => $this->difficulty_level,
            'best_time_to_visit' => $this->best_time_to_visit,
            'entry_fee'        => $this->entry_fee_info,
            'access_info'      => $this->access_info,
            'amenities'        => $this->amenities ?? [],
            'facilities'       => $this->facilities ?? [],
            'what_to_bring'    => $this->what_to_bring ?? [],
            'best_for'         => $this->best_for ?? [],
            'transport_options' => $this->transport_options ?? [],
            'parking_info'     => $this->parking_info,
            'contact'          => [
                'phone'   => $this->contact_phone,
                'email'   => $this->contact_email,
                'website' => $this->website,
            ],
            'rating'           => (float) $this->rating,
            'review_count'     => $this->review_count,
            'visitor_count'    => $this->visitor_count,
            'is_verified'      => $this->is_verified,
            'is_featured'      => $this->is_featured,
            'xp_reward'        => $this->xp_reward,
            'audio_guide_url'  => $this->audio_guide_url,
            'video_url'        => $this->video_url,
            'discovered_by'    => $this->discovered_by_count,
            'upvotes'          => $this->upvotes,
            'downvotes'        => $this->downvotes,
            'added_by_name'    => $this->added_by_name,
            'is_local_guide'   => $this->is_local_guide,
            'verification_status'=> $this->verification_status,
            'location_node_id' => $this->location_node_id,
            'safety_notes'     => $this->safety_notes,
            'requires_permit'  => $this->requires_permit,
            'is_quest_unlock'  => $this->is_quest_unlock,
            'accessibility'    => $this->accessibility ?? [],
            'operator'         => $this->when(
                $this->relationLoaded('operator') && $this->operator,
                fn() => [
                    'id'         => $this->operator->id,
                    'name'       => $this->operator->name,
                    'logo_url'   => $this->operator->logo_url,
                    'is_verified'=> $this->operator->is_verified,
                    'badge'      => $this->operator->verification_badge,
                ]
            ),
            'submitted_by'     => $this->when(
                $this->relationLoaded('submittedBy') && $this->submittedBy,
                fn() => [
                    'id'             => $this->submittedBy->id,
                    'name'           => $this->submittedBy->display_name,
                    'avatar'         => $this->submittedBy->photo_thumbnail_url,
                    'explorer_level' => $this->submittedBy->explorer_level,
                ]
            ),
            'nearby'           => $this->when(
                $this->relationLoaded('nearbyAttractions'),
                fn() => $this->nearbyAttractions->map(fn($a) => [
                    'name'          => $a->name,
                    'category'      => $a->category,
                    'category_icon' => $a->category_icon,
                    'distance'      => $a->distance_km . ' km',
                    'image_url'     => $a->image_url,
                ])->values()
            ),
            'tips'             => $this->when(
                $this->relationLoaded('tips'),
                fn() => $this->tips->map(fn($t) => [
                    'title'        => $t->title,
                    'description'  => $t->description,
                    'icon'         => $t->icon,
                    'is_important' => $t->is_important,
                ])->values()
            ),
        ];
    }
}
