<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use App\Models\Destination;
use App\Models\HiddenGem;
use App\Models\DiscoverRoute;
use App\Models\Operator;
use App\Models\Activity;
use App\Models\Booking;
use App\Models\User;

class ManageDatabaseSeederPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-circle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Moderation & System';

    protected static ?string $navigationLabel = 'Seed & Sample Data';

    protected static ?string $title = 'Production & Sample Data Seeder';

    protected string $view = 'filament.pages.manage-database-seeder';

    public function getStats(): array
    {
        return [
            'destinations' => Destination::count(),
            'gems'         => HiddenGem::count(),
            'routes'       => DiscoverRoute::count(),
            'operators'    => Operator::count(),
            'activities'   => Activity::count(),
            'bookings'     => Booking::count(),
            'users'        => User::count(),
        ];
    }

    public function runSeeder(): void
    {
        try {
            Artisan::call('db:seed', [
                '--force' => true,
            ]);

            Notification::make()
                ->title('Database Seeded Successfully! 🎉')
                ->body('Destinations, Hidden Gems, Routes, Operators, and Activities have been populated.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Seeding Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
