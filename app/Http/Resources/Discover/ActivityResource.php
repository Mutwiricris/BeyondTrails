<?php

namespace App\Http\Resources\Discover;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $participantsList = collect();
        $seenUserIds = [];

        // 1. Host / Creator User
        $hostUser = null;
        if ($this->relationLoaded('organizer') && $this->organizer) {
            $hostUser = $this->organizer;
        } elseif ($this->user_id) {
            $hostUser = \App\Models\User::find($this->user_id);
        }

        if ($hostUser) {
            $participantsList->push([
                'id' => $hostUser->id,
                'name' => $hostUser->display_name ?? $hostUser->name ?? trim("{$hostUser->first_name} {$hostUser->last_name}"),
                'avatar' => $hostUser->photo_thumbnail_url ?? $hostUser->photo_url,
                'avatar_url' => $hostUser->photo_thumbnail_url ?? $hostUser->photo_url,
                'status' => 'Host 👑',
            ]);
            $seenUserIds[] = (string) $hostUser->id;
        }

        // 2. Other Joined Participants
        if ($this->relationLoaded('participants') && $this->participants) {
            foreach ($this->participants as $u) {
                if (in_array((string) $u->id, $seenUserIds)) {
                    continue; // Skip host already added
                }
                $participantsList->push([
                    'id' => $u->id,
                    'name' => $u->display_name ?? $u->name ?? trim("{$u->first_name} {$u->last_name}"),
                    'avatar' => $u->photo_thumbnail_url ?? $u->photo_url,
                    'avatar_url' => $u->photo_thumbnail_url ?? $u->photo_url,
                    'status' => $u->pivot->status ?? 'joined',
                ]);
                $seenUserIds[] = (string) $u->id;
            }
        }

        return [
            'id' => $this->id,
            'category' => $this->category,
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'locationType' => $this->location_type,
            'generalArea' => $this->general_area,
            'latitude' => $this->latitude ? (float) $this->latitude : null,
            'longitude' => $this->longitude ? (float) $this->longitude : null,
            'date' => $this->date ? $this->date->format('Y-m-d') : null,
            'timeType' => $this->time_type,
            'specificTime' => $this->specific_time,
            'minAge' => $this->min_age,
            'maxAge' => $this->max_age,
            'privacy' => $this->privacy,
            'maxCapacity' => $this->max_capacity,
            'isHostVerified' => $this->is_host_verified,
            'joinApproval' => $this->join_approval,
            'tags' => $this->tags ?? [],
            'durationHours' => $this->duration_hours,
            'locationName' => $this->location_name,
            'status' => $this->status,
            'organizer' => $this->whenLoaded('organizer', function () {
                return [
                    'id' => $this->organizer->id,
                    'name' => $this->organizer->display_name ?? $this->organizer->name,
                    'avatar' => $this->organizer->photo_thumbnail_url ?? $this->organizer->photo_url,
                    'avatar_url' => $this->organizer->photo_thumbnail_url ?? $this->organizer->photo_url,
                    'is_verified' => $this->organizer->email_verified_at !== null,
                ];
            }),
            'participants_count' => $participantsList->count(),
            'participants' => $participantsList->all(),
        ];
    }
}
