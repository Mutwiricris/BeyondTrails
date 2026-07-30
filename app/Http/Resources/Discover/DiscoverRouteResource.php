<?php

namespace App\Http\Resources\Discover;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscoverRouteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'slug'           => $this->slug,
            'name'           => $this->name,
            'description'    => $this->description,
            'type'           => $this->type,
            'difficulty'     => $this->difficulty,
            'region'         => $this->region,
            'country'        => $this->country,
            'start_point'    => ['name' => $this->start_point_name, 'lat' => $this->start_latitude, 'lng' => $this->start_longitude],
            'end_point'      => ['name' => $this->end_point_name,   'lat' => $this->end_latitude,   'lng' => $this->end_longitude],
            'waypoints'      => $this->waypoints ?? [],
            'stats'          => [
                'distance'         => $this->formatted_distance,
                'distance_km'      => (float) $this->distance_km,
                'duration'         => $this->formatted_duration,
                'duration_minutes' => $this->duration_minutes,
                'elevation_gain'   => $this->elevation_gain_meters ? $this->elevation_gain_meters . 'm' : null,
            ],
            'highlights'     => $this->highlights ?? [],
            'cover_photo_url'=> $this->cover_photo_url,
            'photos'         => $this->photos ?? [],
            'rating'         => (float) $this->rating,
            'review_count'   => $this->review_count,
            'completed_count'=> $this->completed_count,
            'xp_reward'      => $this->xp_reward,
            'is_featured'    => $this->is_featured,
            'segments'       => $this->when(
                $this->relationLoaded('segments'),
                fn() => $this->segments->map(fn($s) => [
                    'id'                  => $s->id,
                    'name'                => $s->name,
                    'description'         => $s->description,
                    'type'                => $s->type,
                    'start'               => ['lat' => $s->start_latitude, 'lng' => $s->start_longitude],
                    'end'                 => ['lat' => $s->end_latitude, 'lng' => $s->end_longitude],
                    'distance_km'         => (float) $s->distance_km,
                    'best_time_minutes'   => $s->best_time_minutes,
                    'points_reward'       => $s->points_reward,
                    'discovered_by_count' => $s->discovered_by_count,
                    'photos'              => $s->photos ?? [],
                ])->values()
            ),
        ];
    }
}
