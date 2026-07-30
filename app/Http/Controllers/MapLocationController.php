<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\HiddenGem;
use App\Models\Activity;
use Illuminate\Http\Request;

class MapLocationController extends Controller
{
    /**
     * Display a listing of the map locations within bounds.
     */
    public function index(Request $request)
    {
        $request->validate([
            'ne_lat' => 'nullable|numeric',
            'ne_lng' => 'nullable|numeric',
            'sw_lat' => 'nullable|numeric',
            'sw_lng' => 'nullable|numeric',
            'type' => 'nullable|string',
            'search' => 'nullable|string',
        ]);

        $destQuery = Destination::where('is_active', true);
        $gemQuery = HiddenGem::where('is_published', true);
        $activityQuery = Activity::where('privacy', 'open')->whereIn('status', ['upcoming', 'ongoing']);

        // Bounding box filter
        if ($request->filled(['ne_lat', 'ne_lng', 'sw_lat', 'sw_lng'])) {
            $neLat = (float) $request->ne_lat;
            $neLng = (float) $request->ne_lng;
            $swLat = (float) $request->sw_lat;
            $swLng = (float) $request->sw_lng;

            if ($swLng <= $neLng) {
                $destQuery->whereBetween('longitude', [$swLng, $neLng]);
                $gemQuery->whereBetween('longitude', [$swLng, $neLng]);
                $activityQuery->whereBetween('longitude', [$swLng, $neLng]);
            } else {
                $destQuery->where(function ($q) use ($swLng, $neLng) {
                    $q->whereBetween('longitude', [$swLng, 180])
                      ->orWhereBetween('longitude', [-180, $neLng]);
                });
                $gemQuery->where(function ($q) use ($swLng, $neLng) {
                    $q->whereBetween('longitude', [$swLng, 180])
                      ->orWhereBetween('longitude', [-180, $neLng]);
                });
                $activityQuery->where(function ($q) use ($swLng, $neLng) {
                    $q->whereBetween('longitude', [$swLng, 180])
                      ->orWhereBetween('longitude', [-180, $neLng]);
                });
            }

            $destQuery->whereBetween('latitude', [$swLat, $neLat]);
            $gemQuery->whereBetween('latitude', [$swLat, $neLat]);
            $activityQuery->whereBetween('latitude', [$swLat, $neLat]);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $destQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('location', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
            $gemQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('city', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
            $activityQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('location_name', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
        }

        $destinations = $destQuery->get()->map(function ($d) {
            return array_merge($d->toArray(), [
                'title' => $d->title,
                'location_name' => $d->location,
                'latitude' => (float) $d->latitude,
                'longitude' => (float) $d->longitude,
                'category' => $d->category ?? 'Destination',
                'rating' => (float) ($d->rating ?? 0),
                'description' => $d->description,
                'type' => 'Destinations',
                'images' => $d->gallery ?? ($d->cover_image_url ? [$d->cover_image_url] : []),
            ]);
        });

        $gems = $gemQuery->get()->map(function ($g) {
            return array_merge($g->toArray(), [
                'title' => $g->name,
                'location_name' => $g->city ?? $g->location_name ?? 'Unknown',
                'latitude' => (float) $g->latitude,
                'longitude' => (float) $g->longitude,
                'category' => $g->category ?? 'Gem',
                'rating' => (float) ($g->rating ?? 0),
                'description' => $g->description,
                'type' => 'Hidden Gems',
                'images' => $g->gallery ?? ($g->cover_image_url ? [$g->cover_image_url] : []),
            ]);
        });

        $activities = $activityQuery->get()->map(function ($a) {
            return array_merge($a->toArray(), [
                'title' => $a->title,
                'location_name' => $a->location_name ?? $a->general_area ?? 'Unknown',
                'latitude' => (float) $a->latitude,
                'longitude' => (float) $a->longitude,
                'category' => $a->category ?? 'Activity',
                'rating' => 0.0,
                'description' => $a->description,
                'type' => 'Activities',
                'images' => [], // Activities might not have images yet
            ]);
        });

        $allLocations = $destinations->concat($gems)->concat($activities);

        // Type filter (in memory)
        if ($request->filled('type') && $request->type !== 'All') {
            $allLocations = $allLocations->filter(function ($item) use ($request) {
                return $item['type'] === $request->type || $item['category'] === $request->type;
            })->values();
        }

        return response()->json([
            'status' => 'success',
            'data' => $allLocations,
        ]);
    }
}
