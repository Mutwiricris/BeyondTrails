<?php

namespace App\Http\Controllers\Api\V1\Discover;

use App\Http\Controllers\Controller;
use App\Http\Resources\Discover\OperatorResource;
use App\Http\Resources\Discover\DestinationResource;
use App\Http\Resources\Discover\ReviewResource;
use App\Models\Operator;
use App\Models\Review;
use App\Services\DiscoverCacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OperatorController extends Controller
{
    public function __construct(private readonly DiscoverCacheService $cache) {}

    // GET /api/v1/discover/operators
    public function index(Request $request): JsonResponse
    {
        $params = $request->only(['specialty', 'verified_only', 'per_page', 'page']);

        $data = $this->cache->rememberOperatorList($params, function () use ($request) {
            $query = Operator::where('is_active', true);

            if ($request->filled('specialty') && $request->specialty !== 'All') {
                $query->whereJsonContains('specializations', $request->specialty);
            }

            if ($request->boolean('verified_only')) {
                $query->where('is_verified', true);
            }

            $query->orderByDesc('rating');
            $operators = $query->paginate($request->input('per_page', 20));

            return [
                'items' => OperatorResource::collection($operators->items())->resolve(),
                'pagination' => [
                    'current_page' => $operators->currentPage(),
                    'last_page'    => $operators->lastPage(),
                    'per_page'     => $operators->perPage(),
                    'total'        => $operators->total(),
                    'has_more'     => $operators->hasMorePages(),
                ],
            ];
        });

        return response()->json(['success' => true, 'data' => $data, 'meta' => $this->meta()]);
    }

    // GET /api/v1/discover/operators/{slug}
    public function show(string $slug): JsonResponse
    {
        $data = $this->cache->rememberOperatorDetail($slug, function () use ($slug) {
            $operator = Operator::where(function($q) use ($slug) {
                $q->where('slug', $slug)->orWhere('id', $slug);
            })->where('is_active', true)->firstOrFail();

            $reviews = Review::with('user')
                ->where('reviewable_type', 'operator')
                ->where('reviewable_id', $operator->id)
                ->latest()->limit(10)->get();

            $resource = (new OperatorResource($operator))->resolve();
            $resource['reviews'] = ReviewResource::collection($reviews)->resolve();
            return $resource;
        });

        return response()->json(['success' => true, 'data' => $data, 'meta' => $this->meta()]);
    }

    // GET /api/v1/discover/operators/{id}/tours
    public function tours(string $id): JsonResponse
    {
        $data = $this->cache->rememberSimilar('operator_tours', $id, function () use ($id) {
            $operator = Operator::where('id', $id)->orWhere('slug', $id)->firstOrFail();
            $tours = $operator->destinations()->where('is_active', true)->orderByDesc('rating')->get();
            return DestinationResource::collection($tours)->resolve();
        });

        return response()->json(['success' => true, 'data' => $data, 'meta' => $this->meta()]);
    }

    private function meta(): array
    {
        return ['timestamp' => now()->toIso8601String(), 'version' => '1.0.0'];
    }
}
