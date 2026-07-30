<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SystemHealthWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $dbStatus = 'Connected';
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $dbStatus = 'Disconnected';
        }

        $cacheStatus = 'Active';
        try {
            Cache::put('health_check', true, 10);
            $cacheStatus = Cache::get('health_check') ? 'Healthy' : 'Degraded';
        } catch (\Exception $e) {
            $cacheStatus = 'Offline';
        }

        return [
            Stat::make('Database Connection', $dbStatus)
                ->description(config('database.default') . ' engine')
                ->descriptionIcon('heroicon-m-server')
                ->color($dbStatus === 'Connected' ? 'success' : 'danger'),

            Stat::make('Cache Store', $cacheStatus)
                ->description(config('cache.default') . ' driver')
                ->descriptionIcon('heroicon-m-bolt')
                ->color($cacheStatus === 'Healthy' ? 'success' : 'warning'),

            Stat::make('PHP Environment', 'PHP ' . PHP_VERSION)
                ->description('Laravel ' . app()->version())
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color('info'),
        ];
    }
}

