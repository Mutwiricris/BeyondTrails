<?php

namespace App\Http\Controllers\Api\V1\Discover;

use App\Http\Controllers\Controller;
use App\Http\Resources\Discover\DestinationResource;
use App\Http\Resources\Discover\ReviewResource;
use App\Models\Destination;
use App\Models\Review;
use App\Services\DiscoverCacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DiscoverController extends Controller
{
    public function __construct(
        private readonly DiscoverCacheService $cache,
    ) {}

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/discover/destinations
    // Filters: category, sort (popular|rating|price_low|price_high), per_page
    // ──────────────────────────────────────────────────────────────────────────
    public function indexDestinations(Request $request): JsonResponse
    {
        $params = $request->only(['category', 'sort', 'per_page', 'page']);

        $data = $this->cache->rememberDestinationList($params, function () use ($request) {
            $query = Destination::with(['operator'])
                ->where('is_active', true);

            // Category filter
            if ($request->filled('category') && $request->category !== 'All') {
                $query->where('category', $request->category);
            }

            // Sorting
            switch ($request->input('sort', 'popular')) {
                case 'rating':
                    $query->orderByDesc('rating');
                    break;
                case 'price_low':
                    $query->orderBy('price_kes');
                    break;
                case 'price_high':
                    $query->orderByDesc('price_kes');
                    break;
                default: // popular
                    $query->orderByDesc('is_popular')->orderByDesc('rating');
            }

            $destinations = $query->paginate($request->input('per_page', 20));

            return [
                'items' => DestinationResource::collection($destinations->items())->resolve(),
                'pagination' => [
                    'current_page' => $destinations->currentPage(),
                    'last_page'    => $destinations->lastPage(),
                    'per_page'     => $destinations->perPage(),
                    'total'        => $destinations->total(),
                    'has_more'     => $destinations->hasMorePages(),
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
            'meta'    => $this->meta(),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/discover/destinations/featured
    // ──────────────────────────────────────────────────────────────────────────
    public function featuredDestinations(): JsonResponse
    {
        $data = $this->cache->rememberFeaturedDestinations(function () {
            $destinations = Destination::with('operator')
                ->where('is_active', true)
                ->where('is_featured', true)
                ->orderByDesc('rating')
                ->limit(10)
                ->get();

            return DestinationResource::collection($destinations)->resolve();
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
            'meta'    => $this->meta(),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/discover/destinations/{slug}
    // Full detail with itinerary, accommodations, tips, reviews
    // ──────────────────────────────────────────────────────────────────────────
    public function showDestination(string $slug): JsonResponse
    {
        $cacheId = $slug;
        $data = $this->cache->rememberDestinationDetail($cacheId, function () use ($slug) {
            $destination = Destination::with([
                'operator',
                'itinerary',
                'accommodations',
                'tips',
            ])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

            $reviews = Review::with('user')
                ->where('reviewable_type', 'destination')
                ->where('reviewable_id', $destination->id)
                ->latest()
                ->limit(10)
                ->get();

            $resource = (new DestinationResource($destination))->resolve();
            $resource['reviews'] = ReviewResource::collection($reviews)->resolve();
            return $resource;
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
            'meta'    => $this->meta(),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/discover/destinations/{id}/similar
    // ──────────────────────────────────────────────────────────────────────────
    public function similarDestinations(string $slug): JsonResponse
    {
        $data = $this->cache->rememberSimilar('destination', $slug, function () use ($slug) {
            $destination = Destination::where('slug', $slug)->firstOrFail();
            $similar = Destination::where('category', $destination->category)
                ->where('id', '!=', $destination->id)
                ->where('is_active', true)
                ->orderByDesc('rating')
                ->limit(6)
                ->get();
            return DestinationResource::collection($similar)->resolve();
        });

        return response()->json(['success' => true, 'data' => $data, 'meta' => $this->meta()]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // POST /api/v1/discover/destinations/{id}/reviews
    // ──────────────────────────────────────────────────────────────────────────
    public function storeDestinationReview(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'rating'  => 'required|numeric|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $destination = Destination::findOrFail($id);

        $review = Review::updateOrCreate(
            ['reviewable_type' => 'destination', 'reviewable_id' => $id, 'user_id' => $request->user()->id],
            ['rating' => $request->rating, 'comment' => $request->comment]
        );

        // Recalculate destination rating
        $avg = Review::where('reviewable_type', 'destination')->where('reviewable_id', $id)->avg('rating');
        $count = Review::where('reviewable_type', 'destination')->where('reviewable_id', $id)->count();
        $destination->update(['rating' => round($avg, 2), 'review_count' => $count]);

        // Invalidate cache
        $this->cache->invalidateDestination($destination->slug);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted!',
            'data'    => (new ReviewResource($review))->resolve(),
            'meta'    => $this->meta(),
        ], 201);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/discover/search
    // ──────────────────────────────────────────────────────────────────────────
    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2|max:100']);
        $query = $request->input('q');

        $data = $this->cache->rememberSearch($query, function () use ($query) {
            $term = '%' . $query . '%';

            $destinations = Destination::where('is_active', true)
                ->where(fn($q) => $q->where('title', 'like', $term)->orWhere('location', 'like', $term)->orWhere('category', 'like', $term))
                ->limit(5)->get();

            $gems = \App\Models\HiddenGem::where('is_published', true)
                ->where(fn($q) => $q->where('name', 'like', $term)->orWhere('city', 'like', $term)->orWhere('category', 'like', $term))
                ->limit(5)->get();

            $operators = \App\Models\Operator::where('is_active', true)
                ->where(fn($q) => $q->where('name', 'like', $term)->orWhere('city', 'like', $term))
                ->limit(3)->get();

            return [
                'destinations' => DestinationResource::collection($destinations)->resolve(),
                'gems'         => \App\Http\Resources\Discover\GemResource::collection($gems)->resolve(),
                'operators'    => \App\Http\Resources\Discover\OperatorResource::collection($operators)->resolve(),
                'total'        => $destinations->count() + $gems->count() + $operators->count(),
            ];
        });

        return response()->json(['success' => true, 'data' => $data, 'meta' => $this->meta()]);
    }

    private function meta(): array
    {
        return ['timestamp' => now()->toIso8601String(), 'version' => '1.0.0'];
    }
}
