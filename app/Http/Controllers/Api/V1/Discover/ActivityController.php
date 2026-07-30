<?php

namespace App\Http\Controllers\Api\V1\Discover;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityMessage;
use App\Models\ActivityReport;
use App\Http\Resources\Discover\ActivityResource;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with(['organizer', 'participants'])->withCount('participants')
            ->whereIn('status', ['upcoming', 'ongoing'])
            ->where('privacy', 'open')
            ->latest('created_at');
            
        $activities = $query->paginate(20);

        // Ensure creators are synced as Host in pivot table for all activities
        foreach ($activities->items() as $act) {
            if ($act->user_id) {
                $act->participants()->syncWithoutDetaching([$act->user_id => ['status' => 'Host 👑']]);
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => ActivityResource::collection($activities->items()),
            'meta' => [
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'total' => $activities->total(),
            ]
        ]);
    }
    
    public function show($id)
    {
        $activity = Activity::findOrFail($id);

        if ($activity->user_id) {
            $activity->participants()->syncWithoutDetaching([$activity->user_id => ['status' => 'Host 👑']]);
        }

        $activity->load(['organizer', 'participants'])->loadCount('participants');
        
        return response()->json([
            'success' => true,
            'data' => new ActivityResource($activity),
        ]);
    }
    
    public function store(Request $request)
    {
        \Log::info('Activity store payload:', $request->all());
        $validated = $request->validate([
            'category' => 'nullable|string',
            'type' => 'nullable|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'locationType' => 'nullable|string',
            'generalArea' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'date' => 'nullable|date',
            'timeType' => 'nullable|string',
            'specificTime' => 'nullable|string',
            'minAge' => 'integer',
            'maxAge' => 'integer',
            'privacy' => 'string|in:open,private,invite_only',
            'maxCapacity' => 'integer',
            'joinApproval' => 'string|in:instant,host_approval',
            'tags' => 'nullable|array',
            'durationHours' => 'nullable|integer',
            'locationName' => 'nullable|string',
        ]);
        
        $activity = Activity::create([
            'user_id' => $request->user()->id,
            'category' => $validated['category'] ?? null,
            'type' => $validated['type'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location_type' => $validated['locationType'] ?? null,
            'general_area' => $validated['generalArea'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'date' => $validated['date'] ?? null,
            'time_type' => $validated['timeType'] ?? null,
            'specific_time' => $validated['specificTime'] ?? null,
            'min_age' => $validated['minAge'] ?? 18,
            'max_age' => $validated['maxAge'] ?? 65,
            'privacy' => $validated['privacy'] ?? 'open',
            'max_capacity' => $validated['maxCapacity'] ?? 20,
            'join_approval' => $validated['joinApproval'] ?? 'instant',
            'tags' => $validated['tags'] ?? [],
            'duration_hours' => $validated['durationHours'] ?? null,
            'location_name' => $validated['locationName'] ?? null,
            'is_host_verified' => $request->user()->email_verified_at !== null,
            'status' => 'upcoming',
        ]);
        
        // Add creator as the first participant (Host 👑)
        $activity->participants()->syncWithoutDetaching([$request->user()->id => ['status' => 'Host 👑']]);
        
        $activity->load(['organizer', 'participants'])->loadCount('participants');
        
        return response()->json([
            'success' => true,
            'message' => 'Activity created successfully.',
            'data' => new ActivityResource($activity),
        ], 201);
    }
    
    public function join(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);
        $user = $request->user();
        
        $status = $activity->join_approval === 'instant' ? 'joined' : 'pending';
        
        $activity->participants()->syncWithoutDetaching([$user->id => ['status' => $status]]);
        
        return response()->json([
            'success' => true,
            'message' => $status === 'joined' ? 'You joined this activity!' : 'Request to join sent.',
        ]);
    }
    
    public function getMessages($id)
    {
        $activity = Activity::findOrFail($id);
        
        $messages = ActivityMessage::with('user')
            ->where('activity_id', $activity->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'activity_id' => $msg->activity_id,
                    'user_id' => $msg->user_id,
                    'message' => $msg->message,
                    'type' => $msg->type ?? 'text',
                    'media_url' => $msg->media_url,
                    'created_at' => $msg->created_at ? $msg->created_at->toIso8601String() : now()->toIso8601String(),
                    'user' => $msg->user ? [
                        'id' => $msg->user->id,
                        'name' => $msg->user->display_name ?? $msg->user->name ?? trim("{$msg->user->first_name} {$msg->user->last_name}"),
                        'avatar' => $msg->user->photo_thumbnail_url ?? $msg->user->photo_url,
                        'avatar_url' => $msg->user->photo_thumbnail_url ?? $msg->user->photo_url,
                    ] : null,
                ];
            });
            
        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'nullable|string|max:1000',
            'type' => 'nullable|string|max:50',
            'media_url' => 'nullable|string|max:2048',
            'file' => 'nullable|file|max:20480',
            'media' => 'nullable|file|max:20480',
        ]);

        $activity = Activity::findOrFail($id);
        $user = $request->user();

        $mediaUrl = $request->input('media_url');
        $type = $request->input('type', 'text');
        $messageText = $request->input('message') ?? '';

        $uploadedFile = $request->file('file') ?? $request->file('media');
        if ($uploadedFile) {
            $path = $uploadedFile->store('chat_media', 'public');
            $mediaUrl = url('/api/v1/media/' . $path);
            if (empty($messageText)) {
                $messageText = $uploadedFile->getClientOriginalName();
            }
            if ($type === 'text') {
                $mime = $uploadedFile->getMimeType();
                if (str_contains($mime, 'image')) $type = 'image';
                elseif (str_contains($mime, 'video')) $type = 'video';
                else $type = 'file';
            }
        }

        $message = ActivityMessage::create([
            'activity_id' => $activity->id,
            'user_id' => $user->id,
            'message' => $messageText,
            'type' => $type,
            'media_url' => $mediaUrl,
        ]);

        // Automatically ensure message sender is attached as participant if not host
        if ($user->id != $activity->user_id) {
            $activity->participants()->syncWithoutDetaching([$user->id => ['status' => 'joined']]);
        }

        $payload = [
            'id' => $message->id,
            'activity_id' => $message->activity_id,
            'user_id' => $message->user_id,
            'message' => $message->message,
            'type' => $message->type,
            'media_url' => $message->media_url,
            'created_at' => $message->created_at ? $message->created_at->toIso8601String() : now()->toIso8601String(),
            'user' => [
                'id' => $user->id,
                'name' => $user->display_name ?? $user->name ?? trim("{$user->first_name} {$user->last_name}"),
                'avatar' => $user->photo_thumbnail_url ?? $user->photo_url,
                'avatar_url' => $user->photo_thumbnail_url ?? $user->photo_url,
            ],
        ];

        try {
            \App\Events\ActivityMessageSent::dispatch((string)$id, $payload);
            if ((string)$activity->id !== (string)$id) {
                \App\Events\ActivityMessageSent::dispatch((string)$activity->id, $payload);
            }
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'data' => $payload,
        ]);
    }

    public function leave(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);
        $user = $request->user();

        $activity->participants()->detach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'You have left the activity.',
        ]);
    }

    public function report(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $activity = Activity::findOrFail($id);
        $user = $request->user();

        $report = ActivityReport::create([
            'activity_id' => $activity->id,
            'reporter_id' => $user->id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Activity reported successfully.',
            'data' => $report,
        ]);
    }
}
