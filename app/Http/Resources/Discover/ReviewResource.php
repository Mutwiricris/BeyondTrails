<?php

namespace App\Http\Resources\Discover;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'rating'            => (float) $this->rating,
            'comment'           => $this->comment,
            'photos'            => $this->photos ?? [],
            'is_verified_visit' => $this->is_verified_visit,
            'helpful_count'     => $this->helpful_count,
            'date'              => $this->created_at->diffForHumans(),
            'date_iso'          => $this->created_at->toIso8601String(),
            'user'              => $this->when($this->relationLoaded('user') && $this->user, [
                'id'             => $this->user?->id,
                'name'           => $this->user?->display_name ?? $this->user?->first_name,
                'avatar'         => $this->user?->photo_thumbnail_url,
                'explorer_level' => $this->user?->explorer_level,
            ]),
        ];
    }
}
