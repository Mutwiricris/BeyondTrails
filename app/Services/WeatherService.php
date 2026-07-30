<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * WeatherService
 *
 * Fetches live weather from OpenWeatherMap and caches it per GPS coordinate.
 * Cache key rounds lat/lng to 2 decimal places to group nearby spots.
 * TTL: 30 minutes (weather changes slowly enough for travel context).
 *
 * Also returns static "best months to visit" advice per season.
 */
class WeatherService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.openweathermap.org/data/2.5';

    // Kenya seasonal data (static — not from API)
    private array $seasons = [
        '1,2'          => ['name' => 'Short Dry Season', 'label' => 'Hot & Dry', 'icon' => '☀️', 'advice' => 'Excellent for wildlife. Very dry and hot.'],
        '3,4,5'        => ['name' => 'Long Rains', 'label' => 'Wet Season', 'icon' => '🌧️', 'advice' => 'Lush green landscapes. Fewer crowds. Some roads may flood.'],
        '6,7,8,9,10'   => ['name' => 'Long Dry Season', 'label' => 'Peak Season', 'icon' => '🌤️', 'advice' => 'Best time to visit. Great Migration in July–Oct.'],
        '11,12'        => ['name' => 'Short Rains', 'label' => 'Green Season', 'icon' => '🌦️', 'advice' => 'Short afternoon showers. Good birdwatching.'],
    ];

    public function __construct()
    {
        $this->apiKey = config('services.weather.openweathermap_key', '');
    }

    /**
     * Get current weather for a lat/lng pair.
     * Cached in Redis for 30 minutes.
     */
    public function getCurrentWeather(float $lat, float $lng): array
    {
        // Round to 2dp to group nearby locations
        $lat = round($lat, 2);
        $lng = round($lng, 2);
        $cacheKey = "discover:weather:{$lat}:{$lng}";

        return Cache::remember($cacheKey, 1800, function () use ($lat, $lng) {
            return $this->fetchFromApi($lat, $lng);
        });
    }

    private function fetchFromApi(float $lat, float $lng): array
    {
        if (!$this->apiKey) {
            Log::info('🌤 OpenWeatherMap key not set — returning mock weather');
            return $this->mockWeather($lat, $lng);
        }

        try {
            $response = Http::timeout(8)->get("{$this->baseUrl}/weather", [
                'lat'   => $lat,
                'lon'   => $lng,
                'appid' => $this->apiKey,
                'units' => 'metric',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->transformResponse($data);
            }

            Log::warning('🌤 OpenWeatherMap API error', ['status' => $response->status()]);
        } catch (\Exception $e) {
            Log::error('🌤 WeatherService exception: ' . $e->getMessage());
        }

        return $this->mockWeather($lat, $lng);
    }

    private function transformResponse(array $data): array
    {
        $month    = (int) date('n');
        $season   = $this->getSeasonForMonth($month);

        return [
            'temperature'    => round($data['main']['temp']),
            'feels_like'     => round($data['main']['feels_like']),
            'humidity'       => $data['main']['humidity'],
            'wind_speed_kmh' => round(($data['wind']['speed'] ?? 0) * 3.6),
            'condition'      => ucfirst($data['weather'][0]['description'] ?? 'Clear'),
            'condition_code' => $data['weather'][0]['main'] ?? 'Clear',
            'icon_code'      => $data['weather'][0]['icon'] ?? '01d',
            'icon_url'       => 'https://openweathermap.org/img/wn/' . ($data['weather'][0]['icon'] ?? '01d') . '@2x.png',
            'city'           => $data['name'] ?? 'Unknown',
            'sunrise'        => isset($data['sys']['sunrise']) ? date('H:i', $data['sys']['sunrise']) : null,
            'sunset'         => isset($data['sys']['sunset']) ? date('H:i', $data['sys']['sunset']) : null,
            'season'         => $season,
            'best_months'    => $this->getBestMonths(),
            'cached_at'      => now()->toIso8601String(),
            'source'         => 'openweathermap',
        ];
    }

    private function mockWeather(float $lat, float $lng): array
    {
        $month  = (int) date('n');
        $season = $this->getSeasonForMonth($month);

        // Simulate realistic Kenya weather
        $temps = [25, 27, 28, 26, 22, 20, 19, 19, 20, 23, 24, 25];
        $temp  = $temps[$month - 1] + rand(-2, 2);

        return [
            'temperature'    => $temp,
            'feels_like'     => $temp - 2,
            'humidity'       => rand(55, 75),
            'wind_speed_kmh' => rand(8, 22),
            'condition'      => $season['label'],
            'condition_code' => 'Clear',
            'icon_code'      => '01d',
            'icon_url'       => 'https://openweathermap.org/img/wn/01d@2x.png',
            'city'           => 'Kenya',
            'sunrise'        => '06:21',
            'sunset'         => '18:40',
            'season'         => $season,
            'best_months'    => $this->getBestMonths(),
            'cached_at'      => now()->toIso8601String(),
            'source'         => 'mock',
        ];
    }

    private function getSeasonForMonth(int $month): array
    {
        foreach ($this->seasons as $monthsStr => $season) {
            $months = explode(',', $monthsStr);
            if (in_array((string) $month, $months)) return $season;
        }
        return ['name' => 'Dry Season', 'label' => 'Dry & Warm', 'icon' => '☀️', 'advice' => 'Good time to visit.'];
    }

    private function getBestMonths(): array
    {
        return [
            ['month' => 'Jan', 'rating' => 4, 'label' => 'Good'],
            ['month' => 'Feb', 'rating' => 5, 'label' => 'Best'],
            ['month' => 'Mar', 'rating' => 3, 'label' => 'Wet'],
            ['month' => 'Apr', 'rating' => 2, 'label' => 'Rainy'],
            ['month' => 'May', 'rating' => 3, 'label' => 'Wet'],
            ['month' => 'Jun', 'rating' => 4, 'label' => 'Good'],
            ['month' => 'Jul', 'rating' => 5, 'label' => 'Peak'],
            ['month' => 'Aug', 'rating' => 5, 'label' => 'Peak'],
            ['month' => 'Sep', 'rating' => 5, 'label' => 'Best'],
            ['month' => 'Oct', 'rating' => 4, 'label' => 'Good'],
            ['month' => 'Nov', 'rating' => 3, 'label' => 'Rainy'],
            ['month' => 'Dec', 'rating' => 4, 'label' => 'Good'],
        ];
    }
}
