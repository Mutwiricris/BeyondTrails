<?php

namespace App\Http\Controllers\Api\V1\Discover;

use App\Http\Controllers\Controller;
use App\Http\Resources\Discover\GemResource;
use App\Http\Resources\Discover\ReviewResource;
use App\Models\HiddenGem;
use App\Models\Review;
use App\Models\User;
use App\Services\DiscoverCacheService;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GemController extends Controller
{
    public function __construct(
        private readonly DiscoverCacheService $cache,
        private readonly WeatherService $weather,
    ) {}

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/discover/gems
    // Filters: category, difficulty, region, sort, per_page
    // ──────────────────────────────────────────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $params = $request->only(['category', 'difficulty', 'region', 'sort', 'per_page', 'page']);

        $data = $this->cache->rememberGemList($params, function () use ($request) {
            $query = HiddenGem::with('operator')
                ->where('is_published', true);

            if ($request->filled('category') && $request->category !== 'All') {
                $query->where('category', $request->category);
            }

            if ($request->filled('difficulty') && $request->difficulty !== 'All') {
                $query->where('difficulty', $request->difficulty);
            }

            if ($request->filled('region')) {
                $query->where('region', $request->region);
            }

            switch ($request->input('sort', 'popular')) {
                case 'rating':
                    $query->orderByDesc('rating');
                    break;
                case 'easy':
                    $query->orderBy('difficulty_level');
                    break;
                case 'hard':
                    $query->orderByDesc('difficulty_level');
                    break;
                default:
                    $query->orderByDesc('is_featured')->orderByDesc('rating');
            }

            $gems = $query->paginate($request->input('per_page', 20));

            return [
                'items' => GemResource::collection($gems->items())->resolve(),
                'pagination' => [
                    'current_page' => $gems->currentPage(),
                    'last_page'    => $gems->lastPage(),
                    'per_page'     => $gems->perPage(),
                    'total'        => $gems->total(),
                    'has_more'     => $gems->hasMorePages(),
                ],
            ];
        });

        return response()->json(['success' => true, 'data' => $data, 'meta' => $this->meta()]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/discover/gems/{slug}
    // Full detail with operator, nearby, tips, reviews
    // ──────────────────────────────────────────────────────────────────────────
    public function show(string $slug): JsonResponse
    {
        $data = $this->cache->rememberGemDetail($slug, function () use ($slug) {
            $gem = HiddenGem::with([
                'operator',
                'submittedBy',
                'nearbyAttractions',
                'tips',
            ])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

            $reviews = Review::with('user')
                ->where('reviewable_type', 'hidden_gem')
                ->where('reviewable_id', $gem->id)
                ->latest()->limit(10)->get();

            $resource = (new GemResource($gem))->resolve();
            $resource['reviews'] = ReviewResource::collection($reviews)->resolve();
            return $resource;
        });

        return response()->json(['success' => true, 'data' => $data, 'meta' => $this->meta()]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/discover/gems/{id}/weather
    // Live OpenWeatherMap data, cached 30 min
    // ──────────────────────────────────────────────────────────────────────────
    public function weather(string $id): JsonResponse
    {
        $gem = HiddenGem::findOrFail($id);

        if (!$gem->latitude || !$gem->longitude) {
            return response()->json([
                'success' => false,
                'message' => 'Location coordinates not available for this gem.',
                'meta'    => $this->meta(),
            ], 422);
        }

        $data = $this->weather->getCurrentWeather((float) $gem->latitude, (float) $gem->longitude);

        return response()->json(['success' => true, 'data' => $data, 'meta' => $this->meta()]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/discover/gems/{id}/nearby
    // Pre-seeded nearby attractions for this gem
    // ──────────────────────────────────────────────────────────────────────────
    public function nearby(string $id): JsonResponse
    {
        $data = $this->cache->rememberGemNearby($id, function () use ($id) {
            $gem = HiddenGem::findOrFail($id);
            return $gem->nearbyAttractions()->get()->map(fn($a) => [
                'id'            => $a->id,
                'name'          => $a->name,
                'category'      => $a->category,
                'category_icon' => $a->category_icon,
                'distance'      => $a->distance_km . ' km',
                'image_url'     => $a->image_url,
                'latitude'      => $a->latitude,
                'longitude'     => $a->longitude,
            ])->values()->all();
        });

        return response()->json(['success' => true, 'data' => $data, 'meta' => $this->meta()]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/discover/gems/{id}/travellers-nearby
    // Users who have recently visited or are near this gem's coordinates
    // Requires: ?lat=&lng= query params from the device
    // ──────────────────────────────────────────────────────────────────────────
    public function travellersNearby(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'lat'       => 'nullable|numeric',
            'lng'       => 'nullable|numeric',
            'radius_km' => 'nullable|integer|min:1|max:100',
        ]);

        $gem      = HiddenGem::findOrFail($id);
        $lat      = $request->input('lat', $gem->latitude);
        $lng      = $request->input('lng', $gem->longitude);
        $radiusKm = $request->input('radius_km', 50);

        $data = $this->cache->rememberGemTravellersNearby($id, (float) $lat, (float) $lng, function () {
            // In production: query users with recent GPS tracking data near coordinates
            // For now return demo explorers to populate the StackedAvatars widget
            return [
                'count'    => rand(12, 89),
                'previews' => [
                    ['name' => 'Amara K.', 'avatar' => 'https://ui-avatars.com/api/?name=Amara+K&background=0D8ABC&color=fff',  'explorer_level' => 'adventurer'],
                    ['name' => 'James M.', 'avatar' => 'https://ui-avatars.com/api/?name=James+M&background=6B21A8&color=fff',  'explorer_level' => 'explorer'],
                    ['name' => 'Sofia N.', 'avatar' => 'https://ui-avatars.com/api/?name=Sofia+N&background=059669&color=fff', 'explorer_level' => 'trailblazer'],
                    ['name' => 'David O.', 'avatar' => 'https://ui-avatars.com/api/?name=David+O&background=D97706&color=fff', 'explorer_level' => 'explorer'],
                ],
                'message' => 'Explorers have visited this gem recently',
            ];
        });

        return response()->json(['success' => true, 'data' => $data, 'meta' => $this->meta()]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // POST /api/v1/discover/gems/{id}/reviews
    // ──────────────────────────────────────────────────────────────────────────
    public function storeReview(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'rating'  => 'required|numeric|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $gem = HiddenGem::findOrFail($id);

        $review = Review::updateOrCreate(
            ['reviewable_type' => 'hidden_gem', 'reviewable_id' => $id, 'user_id' => $request->user()->id],
            ['rating' => $request->rating, 'comment' => $request->comment]
        );

        $avg   = Review::where('reviewable_type', 'hidden_gem')->where('reviewable_id', $id)->avg('rating');
        $count = Review::where('reviewable_type', 'hidden_gem')->where('reviewable_id', $id)->count();
        $gem->update(['rating' => round($avg, 2), 'review_count' => $count, 'visitor_count' => $count]);

        $this->cache->invalidateGem($gem->slug);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted!',
            'data'    => (new ReviewResource($review))->resolve(),
            'meta'    => $this->meta(),
        ], 201);
    }

    private function meta(): array
    {
        return ['timestamp' => now()->toIso8601String(), 'version' => '1.0.0'];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // POST /api/v1/discover/gems
    // Submit a new hidden gem (user-generated content)
    // ──────────────────────────────────────────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        \Log::info('Hidden Gem store payload:', $request->all());
        $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'required|string|min:50',
            'category'         => 'required|in:nature,culture,adventure,food,views',
            'latitude'         => 'required|numeric|between:-90,90',
            'longitude'        => 'required|numeric|between:-180,180',
            'location_name'    => 'required|string|max:255',
            'difficulty'       => 'nullable|in:Easy,Moderate,Challenging,Difficult',
            'cover_image_url'  => 'nullable|string',
            'audio_guide_url'  => 'nullable|string',
            'video_url'        => 'nullable|string',
            'safety_notes'     => 'nullable|string',
            'accessibility'    => 'nullable|array',
            'requires_permit'  => 'boolean',
            'tags'             => 'nullable|array',
            'address'          => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:100',
            'region'           => 'nullable|string|max:100',
            'gallery'          => 'nullable|array',
            'gallery.*'        => 'string',
            'entry_fee'        => 'nullable|numeric',
            'access_info'      => 'nullable|string',
            'facilities'       => 'nullable|string',
            'contact_phone'    => 'nullable|string|max:50',
            'contact_email'    => 'nullable|email|max:255',
            'website'          => 'nullable|url|max:255',
            'best_time_to_visit' => 'nullable|string|max:255',
        ]);

        $difficultyMap = ['Easy' => 1, 'Moderate' => 2, 'Challenging' => 3, 'Difficult' => 4];

        $gem = HiddenGem::create([
            'slug'               => \Illuminate\Support\Str::slug($request->name) . '-' . \Illuminate\Support\Str::random(6),
            'name'               => $request->name,
            'description'        => $request->description,
            'category'           => $request->category,
            'location_name'      => $request->location_name,
            'latitude'           => $request->latitude,
            'longitude'          => $request->longitude,
            'difficulty'         => $request->difficulty ?? 'Moderate',
            'difficulty_level'   => $difficultyMap[$request->difficulty ?? 'Moderate'],
            'cover_image_url'    => $request->cover_image_url ?? (isset($request->gallery) && count($request->gallery) > 0 ? $request->gallery[0] : null),
            'gallery'            => $request->gallery,
            'tags'               => $request->tags,
            'address'            => $request->address,
            'city'               => $request->city,
            'region'             => $request->region,
            'entry_fee_citizens_kes' => $request->entry_fee,
            'access_info'        => $request->access_info,
            'facilities'         => $request->facilities,
            'contact_phone'      => $request->contact_phone,
            'contact_email'      => $request->contact_email,
            'website'            => $request->website,
            'best_time_to_visit' => $request->best_time_to_visit,
            'audio_guide_url'    => $request->audio_guide_url,
            'video_url'          => $request->video_url,
            'safety_notes'       => $request->safety_notes,
            'accessibility'      => $request->accessibility,
            'requires_permit'    => $request->requires_permit ?? false,
            'added_by_name'      => $request->user()->display_name ?? $request->user()->name,
            'submitted_by_user_id' => $request->user()->id,
            'is_published'       => false, // pending review
            'is_verified'        => false,
            'verification_status'=> 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gem submitted for review! Our team will verify it within 48 hours.',
            'data'    => (new GemResource($gem))->resolve(),
            'meta'    => $this->meta(),
        ], 201);
    }
}

