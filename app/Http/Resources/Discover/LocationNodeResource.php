<?php

namespace App\Http\Resources\Discover;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationNodeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'parent_id' => $this->parent_id,
            'tier' => $this->tier,
            'latitude' => $this->latitude ? (float) $this->latitude : null,
            'longitude' => $this->longitude ? (float) $this->longitude : null,
            'banner_image_url' => $this->banner_image_url,
            'description' => $this->description,
            'spot_count' => $this->spot_count,
            'active_explorers' => $this->active_explorers,
            'metadata' => $this->metadata,
            'is_active' => $this->is_active,
            
            // Nested relations if loaded
            'children' => LocationNodeResource::collection($this->whenLoaded('children')),
        ];
    }
}
