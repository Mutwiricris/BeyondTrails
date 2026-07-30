<?php

namespace App\Http\Controllers\Api\V1\Discover;

use App\Http\Controllers\Controller;
use App\Models\GeofenceZone;
use App\Http\Resources\Discover\GeofenceZoneResource;
use Illuminate\Http\Request;

class GeofenceController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1',
        ]);

        $lat = (float) $request->lat;
        $lng = (float) $request->lng;
        // User requested radius in meters (e.g. 800m)
        $radiusMeters = (int) $request->input('radius', 50000);
        
        // Fast bounding box pre-filter in DB (1 degree ~ 111,320m)
        $latDelta = $radiusMeters / 111320.0;
        $cosLat = cos(deg2rad($lat));
        $lngDelta = $cosLat > 0 ? $radiusMeters / (111320.0 * $cosLat) : 0;

        $zones = GeofenceZone::where('is_active', true)
            ->whereBetween('center_lat', [$lat - $latDelta, $lat + $latDelta])
            ->whereBetween('center_lng', [$lng - $lngDelta, $lng + $lngDelta])
            ->get();
        
        // Precise Haversine distance filtering
        $earthRadius = 6371000; // meters
        
        $filtered = $zones->filter(function ($zone) use ($lat, $lng, $radiusMeters, $earthRadius) {
            $dLat = deg2rad($zone->center_lat - $lat);
            $dLng = deg2rad($zone->center_lng - $lng);
            $a = sin($dLat / 2) * sin($dLat / 2) +
                 cos(deg2rad($lat)) * cos(deg2rad($zone->center_lat)) *
                 sin($dLng / 2) * sin($dLng / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $distance = $earthRadius * $c;

            // Optional: You could also use the zone's own radius_meters here if you only 
            // want zones the user is ALREADY inside, instead of zones nearby.
            // E.g. $distance <= $zone->radius_meters
            // But since this is an explore endpoint, we return zones within the requested radius.
            $zone->distance_away = $distance;
            return $distance <= $radiusMeters;
        })->values();
        
        return response()->json([
            'success' => true,
            'data' => GeofenceZoneResource::collection($filtered),
        ]);
    }
    
    public function trigger(Request $request)
    {
        $request->validate([
            'zone_id' => 'required|uuid|exists:geofence_zones,id',
            'event_type' => 'required|in:entry,dwell,exit',
            'timestamp' => 'required|date',
        ]);
        
        // Here we could log the user's geofence event
        // e.g. UserGeofenceEvent::create(...)
        // And optionally award XP if it's a quest zone or unvisited gem
        
        return response()->json([
            'success' => true,
            'message' => 'Event logged successfully'
        ]);
    }
}
