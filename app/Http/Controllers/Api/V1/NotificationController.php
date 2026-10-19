<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\HiddenGem;
use App\Models\GeofenceZone;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NotificationController extends Controller
{
    /**
     * Get user notifications with optional category filter
     */
    public function index(Request $request)
    {
        $userId = $request->user()?->id;
        $category = $request->query('category');

        $query = Notification::query()
            ->when($userId, fn($q) => $q->where(fn($sub) => $sub->where('user_id', $userId)->orWhereNull('user_id')))
            ->when($category && strtolower($category) !== 'all', fn($q) => $q->where('category', strtolower($category)))
            ->orderBy('created_at', 'desc');

        $notifications = $query->paginate(30);

        $unreadCount = Notification::query()
            ->when($userId, fn($q) => $q->where(fn($sub) => $sub->where('user_id', $userId)->orWhereNull('user_id')))
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'data' => NotificationResource::collection($notifications->items()),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'total_items' => $notifications->total(),
                'unread_count' => $unreadCount,
            ],
        ]);
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead(Request $request, string $id)
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
            'data' => new NotificationResource($notification),
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $userId = $request->user()?->id;

        Notification::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Delete a notification
     */
    public function destroy(Request $request, string $id)
    {
        $notification = Notification::find($id);

        if ($notification) {
            $notification->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully',
        ]);
    }

    /**
     * Get user notification preferences
     */
    public function getSettings(Request $request)
    {
        $userId = $request->user()?->id;

        $settings = null;
        if ($userId) {
            $settings = NotificationSetting::where('user_id', $userId)->first();
        }

        if (!$settings) {
            $settings = new NotificationSetting([
                'chat_notifications_enabled' => true,
                'trip_reminders_enabled' => true,
                'nearby_gems_enabled' => true,
                'local_activities_enabled' => true,
                'quiet_hours_enabled' => true,
                'quiet_hours_start' => '22:00',
                'quiet_hours_end' => '07:00',
                'cluster_cooldown_hours' => 12.0,
                'max_proximity_distance_km' => 2.0,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'chat_notifications_enabled' => (bool) $settings->chat_notifications_enabled,
                'trip_reminders_enabled' => (bool) $settings->trip_reminders_enabled,
                'nearby_gems_enabled' => (bool) $settings->nearby_gems_enabled,
                'local_activities_enabled' => (bool) $settings->local_activities_enabled,
                'quiet_hours_enabled' => (bool) $settings->quiet_hours_enabled,
                'quiet_hours_start' => $settings->quiet_hours_start,
                'quiet_hours_end' => $settings->quiet_hours_end,
                'cluster_cooldown_hours' => (float) $settings->cluster_cooldown_hours,
                'max_proximity_distance_km' => (float) $settings->max_proximity_distance_km,
            ],
        ]);
    }

    /**
     * Update user notification preferences
     */
    public function updateSettings(Request $request)
    {
        $userId = $request->user()?->id;

        $validated = $request->validate([
            'chat_notifications_enabled' => 'nullable|boolean',
            'trip_reminders_enabled' => 'nullable|boolean',
            'nearby_gems_enabled' => 'nullable|boolean',
            'local_activities_enabled' => 'nullable|boolean',
            'quiet_hours_enabled' => 'nullable|boolean',
            'quiet_hours_start' => 'nullable|string',
            'quiet_hours_end' => 'nullable|string',
            'cluster_cooldown_hours' => 'nullable|numeric',
            'max_proximity_distance_km' => 'nullable|numeric',
        ]);

        if ($userId) {
            $settings = NotificationSetting::updateOrCreate(
                ['user_id' => $userId],
                $validated
            );
        } else {
            $settings = new NotificationSetting($validated);
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'data' => $settings,
        ]);
    }

    /**
     * Intelligent Nearby Proximity Ping Engine
     * Evaluates real-time coordinates, checks geofences/gems, enforces velocity filters,
     * quiet hours, anti-spam cluster cooldowns, and generates actionable notification.
     */
    public function locationPing(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'speed_kmh' => 'nullable|numeric',
        ]);

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;
        $speed = (float) ($request->speed_kmh ?? 0);
        $user = $request->user();
        $userId = $user?->id;

        // 1. Fetch user settings
        $settings = $userId
            ? NotificationSetting::where('user_id', $userId)->first()
            : null;

        $nearbyEnabled = $settings ? (bool) $settings->nearby_gems_enabled : true;
        if (!$nearbyEnabled) {
            return response()->json([
                'triggered' => false,
                'reason' => 'nearby_notifications_disabled',
            ]);
        }

        // 2. Velocity Check: Suppress walking-scale discoveries if traveling fast (> 35 km/h)
        if ($speed > 35.0) {
            return response()->json([
                'triggered' => false,
                'reason' => 'velocity_suppressed',
            ]);
        }

        // 3. Quiet Hours Check (Default 22:00 to 07:00)
        $quietHoursEnabled = $settings ? (bool) $settings->quiet_hours_enabled : true;
        if ($quietHoursEnabled) {
            $nowTime = Carbon::now()->format('H:i');
            $start = $settings->quiet_hours_start ?? '22:00';
            $end = $settings->quiet_hours_end ?? '07:00';

            if ($start > $end) {
                // Overnight window
                if ($nowTime >= $start || $nowTime < $end) {
                    return response()->json([
                        'triggered' => false,
                        'reason' => 'quiet_hours',
                    ]);
                }
            } else {
                if ($nowTime >= $start && $nowTime < $end) {
                    return response()->json([
                        'triggered' => false,
                        'reason' => 'quiet_hours',
                    ]);
                }
            }
        }

        // 4. Cluster Cooldown (Avoid spamming in the same 4 km radius within cooldown hours)
        $clusterHours = $settings ? (float) $settings->cluster_cooldown_hours : 12.0;
        $recentClusterAlert = Notification::where('category', 'gem')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->where('created_at', '>=', Carbon::now()->subHours($clusterHours))
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->first(function ($item) use ($lat, $lng) {
                return $this->haversineKm($lat, $lng, (float) $item->latitude, (float) $item->longitude) <= 4.0;
            });

        if ($recentClusterAlert) {
            return response()->json([
                'triggered' => false,
                'reason' => 'cluster_cooldown',
            ]);
        }

        // 5. Query Active Gems & Geofences in proximity
        $maxRadiusKm = $settings ? (float) $settings->max_proximity_distance_km : 2.0;
        $earthRadiusMeters = 6371000;

        // Bounding box pre-filter for DB performance
        $latDelta = ($maxRadiusKm * 1000) / 111320.0;
        $cosLat = cos(deg2rad($lat));
        $lngDelta = $cosLat > 0 ? ($maxRadiusKm * 1000) / (111320.0 * $cosLat) : 0;

        $gems = HiddenGem::where('is_published', true)
            ->whereBetween('latitude', [$lat - $latDelta, $lat + $latDelta])
            ->whereBetween('longitude', [$lng - $lngDelta, $lng + $lngDelta])
            ->get();

        $closestGem = null;
        $minDistMeters = $maxRadiusKm * 1000;

        foreach ($gems as $gem) {
            $distMeters = $this->haversineMeters($lat, $lng, (float) $gem->latitude, (float) $gem->longitude);
            if ($distMeters <= $minDistMeters) {
                // 6. Check 30-day decay rule for this specific spot
                $recentlySent = Notification::where('category', 'gem')
                    ->when($userId, fn($q) => $q->where('user_id', $userId))
                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                    ->whereJsonContains('payload->id', $gem->id)
                    ->exists();

                if (!$recentlySent) {
                    $minDistMeters = $distMeters;
                    $closestGem = $gem;
                }
            }
        }

        if (!$closestGem) {
            return response()->json([
                'triggered' => false,
                'reason' => 'no_eligible_places_nearby',
            ]);
        }

        // 7. Create Rich Actionable Notification (Zero emojis)
        $distText = $minDistMeters < 1000
            ? round($minDistMeters) . ' m'
            : round($minDistMeters / 1000, 1) . ' km';

        $notification = Notification::create([
            'user_id' => $userId,
            'category' => 'gem',
            'title' => 'Nearby Hidden Gem',
            'body' => "You are {$distText} away from {$closestGem->name}. Tap to view details and directions.",
            'image_url' => $closestGem->cover_image_url,
            'target_screen' => 'GemDetailScreen',
            'latitude' => $lat,
            'longitude' => $lng,
            'payload' => [
                'id' => $closestGem->id,
                'title' => $closestGem->name,
                'location' => $closestGem->location_name ?? 'Kenya',
                'category' => $closestGem->category ?? 'Nature',
                'rating' => (float) ($closestGem->rating ?? 4.8),
                'distance_meters' => round($minDistMeters),
            ],
            'action_buttons' => [
                [
                    'label' => 'Directions',
                    'action_key' => 'directions',
                    'payload' => ['id' => $closestGem->id],
                ],
                [
                    'label' => 'Save',
                    'action_key' => 'save',
                    'payload' => ['id' => $closestGem->id],
                ],
            ],
            'is_read' => false,
        ]);

        return response()->json([
            'triggered' => true,
            'distance_meters' => round($minDistMeters),
            'data' => new NotificationResource($notification),
        ]);
    }

    /**
     * Haversine distance in meters
     */
    private function haversineMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        return $this->haversineKm($lat1, $lon1, $lat2, $lon2) * 1000;
    }

    /**
     * Haversine distance in kilometers
     */
    private function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
