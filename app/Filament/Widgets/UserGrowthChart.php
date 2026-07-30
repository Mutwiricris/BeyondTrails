<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class UserGrowthChart extends ChartWidget
{
    protected ?string $heading = 'User Registrations & Growth Trend';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Cache::remember('widget_user_growth_data', 600, function () {
            $months = collect(range(1, 6))->map(fn ($m) => now()->subMonths(6 - $m)->format('M Y'));
            $counts = collect(range(1, 6))->map(function ($m) {
                $date = now()->subMonths(6 - $m);
                return User::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
            });

            return [
                'labels' => $months->toArray(),
                'counts' => $counts->toArray(),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'New Users',
                    'data' => $data['counts'],
                    'fill' => 'start',
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

