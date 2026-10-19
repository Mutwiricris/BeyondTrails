<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'title' => $this->title,
            'body' => $this->body,
            'image_url' => $this->image_url,
            'target_screen' => $this->target_screen,
            'payload' => $this->payload,
            'action_buttons' => $this->action_buttons ?? [],
            'is_read' => (bool) $this->is_read,
            'read_at' => $this->read_at?->toIso8601String(),
            'timestamp' => $this->created_at?->toIso8601String(),
            'latitude' => $this->latitude ? (float) $this->latitude : null,
            'longitude' => $this->longitude ? (float) $this->longitude : null,
        ];
    }
}
