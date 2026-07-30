<?php

namespace App\Filament\Resources\DiscoverRoutes\Pages;

use App\Filament\Resources\DiscoverRoutes\DiscoverRouteResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Database\Seeders\DiscoverSeeder;

class ListDiscoverRoutes extends ListRecords
{
    protected static string $resource = DiscoverRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateSampleData')
                ->label('Generate Sample Trails & Routes')
                ->icon('heroicon-o-sparkles')
                ->color('amber')
                ->requiresConfirmation()
                ->modalHeading('Generate Sample Trails & Routes')
                ->modalDescription('This will populate sample hiking routes & safari trail records in the database.')
                ->action(function () {
                    try {
                        (new DiscoverSeeder())->run();
                        Notification::make()
                            ->title('Sample Trails & Routes Seeded! 🥾')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Seeding Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            CreateAction::make(),
        ];
    }
}
