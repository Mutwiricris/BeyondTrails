<?php

namespace App\Http\Resources\Discover;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GeofenceZoneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'center_lat' => (float) $this->center_lat,
            'center_lng' => (float) $this->center_lng,
            'radius_meters' => $this->radius_meters,
            'category' => $this->category,
            'trigger_on_entry' => $this->trigger_on_entry,
            'trigger_on_dwell' => $this->trigger_on_dwell,
            'dwell_seconds' => $this->dwell_seconds,
            'trigger_on_exit' => $this->trigger_on_exit,
            'throttle_hours' => $this->throttle_hours,
            'notification_title' => $this->notification_title,
            'notification_body' => $this->notification_body,
            'notification_icon' => $this->notification_icon,
            'linked_model_type' => $this->linked_model_type,
            'linked_model_id' => $this->linked_model_id,
        ];
    }
}
