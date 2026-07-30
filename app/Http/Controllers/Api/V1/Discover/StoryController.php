<?php

namespace App\Http\Controllers\Api\V1\Discover;

use App\Http\Controllers\Controller;
use App\Models\Story;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    /**
     * Get active traveller stories.
     *
     * GET /api/v1/discover/stories
     */
    public function index(Request $request): JsonResponse
    {
        $stories = Story::with(['user', 'frames'])->latest()->get();

        $formatted = $stories->map(function ($s) {
            $firstFrame = $s->frames->first();
            return [
                'id'            => $s->id,
                'name'          => $s->user->display_name ?? $s->user->first_name ?? $s->user->name,
                'location'      => $s->location_name ?? 'Kenya',
                'image'         => $firstFrame ? $firstFrame->image_url : 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=200',
                'emoji'         => $s->emoji,
                'frames_count'  => $s->frames->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $formatted,
        ]);
    }
}
