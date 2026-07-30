<?php

namespace App\Http\Controllers\Api\V1\Discover;

use App\Http\Controllers\Controller;
use App\Http\Resources\Discover\DiscoverRouteResource;
use App\Http\Resources\Discover\ReviewResource;
use App\Models\DiscoverRoute;
use App\Models\Review;
use App\Services\DiscoverCacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiscoverRouteController extends Controller
{
    public function __construct(private readonly DiscoverCacheService $cache) {}

    // GET /api/v1/discover/routes
    public function index(Request $request): JsonResponse
    {
        $params = $request->only(['type', 'difficulty', 'region', 'per_page', 'page']);

        $data = $this->cache->rememberRouteList($params, function () use ($request) {
            $query = DiscoverRoute::where('is_published', true);

            if ($request->filled('type')) $query->where('type', $request->type);
            if ($request->filled('difficulty')) $query->where('difficulty', $request->difficulty);
            if ($request->filled('region')) $query->where('region', $request->region);

            $query->orderByDesc('is_featured')->orderByDesc('rating');
            $routes = $query->paginate($request->input('per_page', 20));

            return [
                'items' => DiscoverRouteResource::collection($routes->items())->resolve(),
                'pagination' => [
                    'current_page' => $routes->currentPage(),
                    'last_page'    => $routes->lastPage(),
                    'total'        => $routes->total(),
                    'has_more'     => $routes->hasMorePages(),
                ],
            ];
        });

        return response()->json(['success' => true, 'data' => $data, 'meta' => $this->meta()]);
    }

    // GET /api/v1/discover/routes/{slug}
    public function show(string $slug): JsonResponse
    {
        $data = $this->cache->rememberRouteDetail($slug, function () use ($slug) {
            $route = DiscoverRoute::with(['segments', 'createdBy'])
                ->where('slug', $slug)
                ->where('is_published', true)
                ->firstOrFail();

            $reviews = Review::with('user')
                ->where('reviewable_type', 'route')
                ->where('reviewable_id', $route->id)
                ->latest()->limit(10)->get();

            $resource = (new DiscoverRouteResource($route))->resolve();
            $resource['reviews'] = ReviewResource::collection($reviews)->resolve();

            // Fetch active trail users
            $activeUsers = \App\Models\User::where('current_activity', $slug)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->select('id as user_id', 'name', 'avatar_url', 'latitude', 'longitude')
                ->get()
                ->map(function ($user) {
                    $user->progress_percentage = rand(10, 90); // Mock progress for now
                    return $user;
                });
            
            $resource['active_trail_users'] = $activeUsers;

            return $resource;
        });

        return response()->json(['success' => true, 'data' => $data, 'meta' => $this->meta()]);
    }

    private function meta(): array
    {
        return ['timestamp' => now()->toIso8601String(), 'version' => '1.0.0'];
    }
}
