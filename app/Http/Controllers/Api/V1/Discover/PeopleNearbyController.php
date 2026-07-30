<?php

namespace App\Http\Controllers\Api\V1\Discover;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PeopleNearbyController extends Controller
{
    /**
     * Get explorers exploring nearby with 100% accurate Haversine distance calculations from DB.
     *
     * GET /api/v1/discover/people-nearby
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius'    => 'nullable|numeric|min:1',
        ]);

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        // Sync authenticated user's location & preferences to DB
        $currentUser = $request->user();
        if ($currentUser) {
            $currentUser->update([
                'latitude' => $lat,
                'longitude' => $lng,
                'location_enabled' => true,
                'sharing_mode' => ($currentUser->sharing_mode === 'off') ? 'explorers' : ($currentUser->sharing_mode ?? 'explorers'),
            ]);
        }

        $rawRadius = (float) $request->input('radius', 50);
        // If radius <= 100, treat as km and convert to meters (e.g., 50km -> 50,000m). If > 100, treat as meters.
        $radiusMeters = (int) ($rawRadius <= 100 ? $rawRadius * 1000 : $rawRadius);

        // Fetch all active DB users who have location enabled or coordinates set
        $queryBuilder = User::where(function ($q) {
            $q->where('location_enabled', true)
              ->orWhereNotNull('latitude');
        })
        ->where(function ($q) {
            $q->whereNull('sharing_mode')
              ->orWhere('sharing_mode', '!=', 'off');
        })
        ->where(function ($q) {
            $q->whereNull('is_profile_public')
              ->orWhere('is_profile_public', true);
        })
        ->whereNotNull('latitude')
        ->whereNotNull('longitude');

        $candidates = $queryBuilder->get();

        // Calculate exact mathematical Haversine distance for every DB user
        $earthRadius = 6371000; // meters

        $formatted = $candidates->map(function ($u) use ($lat, $lng, $currentUser, $earthRadius) {
            $isMe = $currentUser && ((string)$u->id === (string)$currentUser->id);

            if ($isMe) {
                $u->distance = 0.0;
                $u->is_me = true;
                return $u;
            }

            $uLat = (float) $u->latitude;
            $uLng = (float) $u->longitude;

            $dLat = deg2rad($uLat - $lat);
            $dLng = deg2rad($uLng - $lng);
            $a = sin($dLat / 2) * sin($dLat / 2) +
                 cos(deg2rad($lat)) * cos(deg2rad($uLat)) *
                 sin($dLng / 2) * sin($dLng / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $distanceMeters = $earthRadius * $c;

            $u->distance = round($distanceMeters, 1);
            $u->is_me = false;
            return $u;
        });

        // Filter within requested radius (except current user who is always included)
        $filtered = $formatted->filter(function ($u) use ($radiusMeters) {
            return ($u->is_me ?? false) || ($u->distance <= $radiusMeters);
        });

        // If filtering returns only current user, include closest DB users sorted by exact distance
        if ($filtered->count() <= 1 && $formatted->count() > 1) {
            $filtered = $formatted;
        }

        // Sort by distance ascending so closest users appear first
        $sorted = $filtered->sortBy('distance')->values()->take(20);

        return response()->json([
            'success' => true,
            'data'    => \App\Http\Resources\Discover\TravellerNearbyResource::collection($sorted),
            'meta'    => [
                'timestamp'     => now()->toIso8601String(),
                'latitude'      => $lat,
                'longitude'     => $lng,
                'radius_meters' => $radiusMeters,
                'total_results' => $sorted->count(),
            ]
        ]);
    }
}
