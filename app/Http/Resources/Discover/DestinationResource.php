<?php

namespace App\Http\Resources\Discover;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DestinationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'slug'              => $this->slug,
            'title'             => $this->title,
            'description'       => $this->description,
            'short_description' => $this->short_description,
            'category'          => $this->category,
            'location'          => $this->location,
            'county'            => $this->county,
            'region'            => $this->region,
            'country'           => $this->country,
            'coordinates'       => ['lat' => $this->latitude, 'lng' => $this->longitude],
            'cover_image_url'   => $this->cover_image_url,
            'gallery'           => $this->gallery ?? [],
            'price'             => [
                'kes'     => $this->price_kes,
                'usd'     => $this->price_usd,
                'display' => $this->price_display,
            ],
            'rating'            => (float) $this->rating,
            'review_count'      => $this->review_count,
            'duration'          => ['days' => $this->duration_days, 'label' => $this->duration_label],
            'group_size_max'    => $this->group_size_max,
            'difficulty'        => $this->difficulty,
            'tour_type'         => $this->tour_type,
            'is_popular'        => $this->is_popular,
            'is_featured'       => $this->is_featured,
            'highlights'        => $this->highlights ?? [],
            'included'          => $this->whenLoaded('itinerary', $this->included ?? []),
            'excluded'          => $this->excluded ?? [],
            'what_to_bring'     => $this->what_to_bring ?? [],
            'languages_spoken'  => $this->languages_spoken ?? ['English', 'Swahili'],
            'meeting_point'     => $this->meeting_point,
            'transport_info'    => $this->transport_info,
            'meal_info'         => $this->meal_info,
            'health_safety'     => $this->health_safety_info,
            'cancellation_policy' => $this->cancellation_policy,
            'faqs'              => $this->faqs ?? [],
            'xp_reward'         => $this->xp_reward,
            'location_node_id'  => $this->location_node_id,
            'busyness'          => $this->busyness_score,
            'crowd_density'     => $this->crowd_density,
            'current_visitors'  => $this->current_visitors,
            'peak_hours'        => $this->peak_hours,
            'weather_note'      => $this->weather_note,
            'instant_booking'   => $this->instant_booking,
            'available_days'    => $this->available_days ?? [],
            'operator'          => $this->when($this->relationLoaded('operator') && $this->operator, [
                'id'         => $this->operator?->id,
                'slug'       => $this->operator?->slug,
                'name'       => $this->operator?->name,
                'logo_url'   => $this->operator?->logo_url,
                'is_verified'=> $this->operator?->is_verified,
                'badge'      => $this->operator?->verification_badge,
                'rating'     => $this->operator?->rating,
                'emoji'      => '🌍',
            ]),
            'itinerary'         => $this->when(
                $this->relationLoaded('itinerary'),
                fn() => $this->itinerary->map(fn($day) => [
                    'day'         => 'Day ' . $day->day_number,
                    'day_number'  => $day->day_number,
                    'title'       => $day->title,
                    'description' => $day->description,
                    'activities'  => $day->activities ?? [],
                    'meals'       => $day->meals ?? [],
                ])->values()
            ),
            'accommodations'    => $this->when(
                $this->relationLoaded('accommodations'),
                fn() => $this->accommodations->map(fn($acc) => [
                    'name'              => $acc->name,
                    'type'              => $acc->type,
                    'rating'            => $acc->rating,
                    'room_type'         => $acc->room_type,
                    'bed_configuration' => $acc->bed_configuration,
                    'amenities'         => $acc->amenities ?? [],
                    'image_url'         => $acc->image_url,
                ])->values()
            ),
            'tips'              => $this->when(
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
