<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use App\Models\ActivityReport;
use App\Models\Destination;
use App\Models\DiscoverRoute;
use App\Models\HiddenGem;
use App\Models\Operator;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class AppSummaryOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Cache stats for 10 minutes (600s) to minimize DB load on high dashboard visits
        $totalUsers = Cache::remember('stat_total_users', 600, fn () => User::count());
        $totalDestinations = Cache::remember('stat_total_destinations', 600, fn () => Destination::count());
        $totalActivities = Cache::remember('stat_total_activities', 600, fn () => Activity::count());
        $totalOperators = Cache::remember('stat_total_operators', 600, fn () => Operator::count());
        $totalRoutes = Cache::remember('stat_total_routes', 600, fn () => DiscoverRoute::count());
        $totalGems = Cache::remember('stat_total_gems', 600, fn () => HiddenGem::count());
        $pendingReports = Cache::remember('stat_pending_reports', 600, fn () => ActivityReport::count());

        return [
            Stat::make('Total Users', number_format($totalUsers))
                ->description('Registered Explorers & Travelers')
                ->descriptionIcon('heroicon-m-user-group')
                ->chart([7, 12, 18, 25, 32, 45, 60, max(1, $totalUsers)])
                ->color('success'),

            Stat::make('Destinations & Spots', number_format($totalDestinations))
                ->description('Curated Travel Destinations')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('info'),

            Stat::make('Active Activities', number_format($totalActivities))
                ->description('Bookable Tours & Outdoor Experiences')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('warning'),

            Stat::make('Tour Operators', number_format($totalOperators))
                ->description('Verified Local Tour Partners')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('primary'),

            Stat::make('Discover Routes', number_format($totalRoutes))
                ->description('Interactive Hiking & Trail Routes')
                ->descriptionIcon('heroicon-m-map')
                ->color('success'),

            Stat::make('Hidden Gems', number_format($totalGems))
                ->description('User-Discovered Secret Spots')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('danger'),
        ];
    }
}

