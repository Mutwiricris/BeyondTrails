<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * DiscoverCacheService
 *
 * Centralised Redis caching layer for all Discover module entities.
 * Uses cache tags (if driver supports it) for efficient invalidation.
 *
 * TTL Strategy:
 *   Lists  → 30 min  (frequently filtered, low staleness risk)
 *   Detail → 1 hour  (rich data, updated rarely)
 *   Featured → 1 hour (editorial updates)
 *   Search → 10 min  (user-typed queries)
 *   Weather → 30 min  (see WeatherService)
 *   Nearby  → 6 hours (geographic, very static)
 */
class DiscoverCacheService
{
    // ── TTLs ─────────────────────────────────────────────────────────────────
    const LIST_TTL      = 1800;    // 30 min
    const DETAIL_TTL    = 3600;    // 1 hour
    const FEATURED_TTL  = 3600;    // 1 hour
    const SEARCH_TTL    = 600;     // 10 min
    const NEARBY_TTL    = 21600;   // 6 hours
    const OPERATORS_TTL = 7200;    // 2 hours

    // ── Key Helpers ───────────────────────────────────────────────────────────

    private function listKey(string $entity, array $params): string
    {
        ksort($params);
        return "discover:{$entity}:list:" . md5(serialize($params));
    }

    private function detailKey(string $entity, string $id): string
    {
        return "discover:{$entity}:detail:{$id}";
    }

    // ── Destinations ──────────────────────────────────────────────────────────

    public function rememberDestinationList(array $params, callable $callback): mixed
    {
        return Cache::remember($this->listKey('destinations', $params), self::LIST_TTL, $callback);
    }

    public function rememberDestinationDetail(string $id, callable $callback): mixed
    {
        return Cache::remember($this->detailKey('destinations', $id), self::DETAIL_TTL, $callback);
    }

    public function rememberFeaturedDestinations(callable $callback): mixed
    {
        return Cache::remember('discover:destinations:featured', self::FEATURED_TTL, $callback);
    }

    public function invalidateDestination(string $id): void
    {
        Cache::forget($this->detailKey('destinations', $id));
        Cache::forget('discover:destinations:featured');
        // Note: list keys will expire naturally (30 min)
    }

    // ── Hidden Gems ───────────────────────────────────────────────────────────

    public function rememberGemList(array $params, callable $callback): mixed
    {
        return Cache::remember($this->listKey('gems', $params), self::LIST_TTL, $callback);
    }

    public function rememberGemDetail(string $id, callable $callback): mixed
    {
        return Cache::remember($this->detailKey('gems', $id), self::DETAIL_TTL, $callback);
    }

    public function rememberGemNearby(string $id, callable $callback): mixed
    {
        return Cache::remember("discover:gems:nearby:{$id}", self::NEARBY_TTL, $callback);
    }

    public function rememberGemTravellersNearby(string $id, float $lat, float $lng, callable $callback): mixed
    {
        $key = "discover:gems:travellers:{$id}:" . round($lat, 2) . ':' . round($lng, 2);
        return Cache::remember($key, 300, $callback); // 5 min — more dynamic
    }

    public function invalidateGem(string $id): void
    {
        Cache::forget($this->detailKey('gems', $id));
        Cache::forget("discover:gems:nearby:{$id}");
    }

    // ── Operators ─────────────────────────────────────────────────────────────

    public function rememberOperatorList(array $params, callable $callback): mixed
    {
        return Cache::remember($this->listKey('operators', $params), self::OPERATORS_TTL, $callback);
    }

    public function rememberOperatorDetail(string $id, callable $callback): mixed
    {
        return Cache::remember($this->detailKey('operators', $id), self::OPERATORS_TTL, $callback);
    }

    // ── Routes ────────────────────────────────────────────────────────────────

    public function rememberRouteList(array $params, callable $callback): mixed
    {
        return Cache::remember($this->listKey('routes', $params), self::OPERATORS_TTL, $callback);
    }

    public function rememberRouteDetail(string $id, callable $callback): mixed
    {
        return Cache::remember($this->detailKey('routes', $id), self::OPERATORS_TTL, $callback);
    }

    // ── Search ────────────────────────────────────────────────────────────────

    public function rememberSearch(string $query, callable $callback): mixed
    {
        $key = 'discover:search:' . md5(strtolower(trim($query)));
        return Cache::remember($key, self::SEARCH_TTL, $callback);
    }

    // ── Similar Destinations ──────────────────────────────────────────────────

    public function rememberSimilar(string $type, string $id, callable $callback): mixed
    {
        return Cache::remember("discover:similar:{$type}:{$id}", self::DETAIL_TTL, $callback);
    }
}
