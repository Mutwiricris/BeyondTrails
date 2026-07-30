<?php

namespace App\Http\Resources\Discover;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TravellerNearbyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $displayName = $this->display_name ?? $this->name ?? trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? '')) ?: 'Explorer';

        return [
            'user_id' => $this->id,
            'name' => $displayName,
            'display_name' => $displayName,
            'username' => $this->username,
            'avatar' => $this->photo_url,
            'avatar_url' => $this->photo_url,
            'distance_meters' => isset($this->distance) ? (float) $this->distance : null,
            'latitude' => null,
            'longitude' => null,
            'status' => $this->traveller_status ?? 'exploring',
            'current_activity' => $this->current_activity,
            'interests' => is_array($this->interests) ? $this->interests : [],
            'explorer_level' => $this->explorer_level ?? 'Explorer',
            'streak_days' => $this->streak_days ?? 0,
            'is_verified' => $this->email_verified_at !== null,
            'is_online' => $this->last_seen_at && $this->last_seen_at->diffInMinutes(now()) < 15,
            'last_seen' => $this->last_seen_at ? $this->last_seen_at->toIso8601String() : null,
            'is_local_guide' => false,
            'sharing_mode' => $this->sharing_mode ?? 'explorers',
            'allow_dms' => $this->allow_dms ?? true,
            'gems_discovered_count' => $this->gems_discovered_count ?? 0,
            'is_me' => isset($this->is_me) ? (bool) $this->is_me : false,
        ];
    }
}
