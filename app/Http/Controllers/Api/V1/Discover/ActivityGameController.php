<?php

namespace App\Http\Controllers\Api\V1\Discover;

use App\Http\Controllers\Controller;
use App\Models\ActivityGamePack;
use App\Models\ActivityGameSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityGameController extends Controller
{
    /**
     * List activity game packs with optional filtering.
     *
     * GET /api/v1/discover/activities/games/packs
     */
    public function index(Request $request): JsonResponse
    {
        $query = ActivityGamePack::query()->withCount('cards');

        if ($request->filled('occasion')) {
            $query->where('occasion', $request->query('occasion'));
        }

        if ($request->filled('game_type')) {
            $query->where('game_type', $request->query('game_type'));
        }

        if ($request->filled('region') && $request->query('region') !== 'all') {
            $region = $request->query('region');
            $query->where(function ($q) use ($region) {
                $q->where('metadata->region', $region)
                  ->orWhereJsonContains('tags', $region);
            });
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('subtitle', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $packs = $query->orderBy('is_featured', 'desc')
                       ->orderBy('title', 'asc')
                       ->get();

        return response()->json([
            'success' => true,
            'data'    => $packs,
        ]);
    }

    /**
     * Get a specific game pack with all its cards.
     *
     * GET /api/v1/discover/activities/games/packs/{id_or_slug}
     */
    public function show(string $idOrSlug): JsonResponse
    {
        $pack = ActivityGamePack::with('cards')
            ->where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->first();

        if (!$pack) {
            return response()->json([
                'success' => false,
                'message' => 'Game pack not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $pack,
        ]);
    }

    /**
     * Record a completed game session.
     *
     * POST /api/v1/discover/activities/games/sessions
     */
    public function storeSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pack_id'          => 'required|string',
            'team_names'       => 'nullable|array',
            'scores'           => 'nullable|array',
            'cards_played'     => 'nullable|integer|min:0',
            'duration_seconds' => 'nullable|integer|min:0',
            'metadata'         => 'nullable|array',
        ]);

        $pack = ActivityGamePack::where('id', $validated['pack_id'])
            ->orWhere('slug', $validated['pack_id'])
            ->first();

        if (!$pack) {
            return response()->json([
                'success' => false,
                'message' => 'Game pack not found for session record.',
            ], 404);
        }

        $user = $request->user();

        $session = ActivityGameSession::create([
            'user_id'          => $user?->id,
            'pack_id'          => $pack->id,
            'team_names'       => $validated['team_names'] ?? [],
            'scores'           => $validated['scores'] ?? [],
            'cards_played'     => $validated['cards_played'] ?? 0,
            'duration_seconds' => $validated['duration_seconds'] ?? 0,
            'completed_at'     => now(),
            'metadata'         => $validated['metadata'] ?? [],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Game session recorded successfully.',
            'data'    => $session->load('pack'),
        ], 201);
    }

    /**
     * Return available occasions and game types metadata for filters.
     *
     * GET /api/v1/discover/activities/games/metadata
     */
    public function metadata(): JsonResponse
    {
        $occasions = [
            ['id' => 'all', 'label' => 'All Arcade & Games', 'icon' => 'sports_esports'],
            ['id' => 'outdoor', 'label' => 'Expeditions & Hiking', 'icon' => 'terrain'],
            ['id' => 'bbq', 'label' => 'Campfire & BBQ', 'icon' => 'outdoor_grill'],
            ['id' => 'roadtrip', 'label' => 'Road Trips & Highway', 'icon' => 'directions_car'],
            ['id' => 'party', 'label' => 'Party & Social Arena', 'icon' => 'local_fire_department'],
            ['id' => 'family', 'label' => 'Squad & Family', 'icon' => 'family_restroom'],
        ];

        $gameTypes = [
            ['id' => 'split_duel', 'label' => '2P Split-Screen Duel', 'icon' => 'touch_app', 'description' => 'Face-to-face simultaneous reflex duel on one device'],
            ['id' => 'shutter_snapshot', 'label' => 'Wildlife Shutter Reticle', 'icon' => 'camera', 'description' => 'Target tracking and lens focus timing action game'],
            ['id' => 'math_blitz', 'label' => 'Highway 24 Math Blitz', 'icon' => 'calculate', 'description' => 'Arcade equation puzzle with glowing operator chips'],
            ['id' => 'survival_quest', 'label' => 'Alpine Survival Simulator', 'icon' => 'health_and_safety', 'description' => 'Expedition survival with HP, Stamina and Morale gauges'],
            ['id' => 'secret_roles', 'label' => 'Campfire Secret Roles', 'icon' => 'fingerprint', 'description' => 'Biometric fingerprint scanner social deduction terminal'],
            ['id' => 'geo_scramble', 'label' => 'Geo-Scramble Anagram Rush', 'icon' => 'extension', 'description' => 'Tile drag-and-drop word rush before the fuse burns out'],
            ['id' => 'bingo', 'label' => 'Spotting Matrix 3x3', 'icon' => 'grid_view', 'description' => 'Tactile observation grid with automatic line detection'],
            ['id' => 'trivia_taboo', 'label' => 'Pass & Play Console Duel', 'icon' => 'style', 'description' => '3D flip console with taboo filters and team HUD'],
        ];

        return response()->json([
            'success' => true,
            'occasions' => $occasions,
            'game_types' => $gameTypes,
            'play_styles' => $gameTypes,
        ]);
    }
}
