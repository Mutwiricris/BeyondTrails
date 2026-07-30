<?php

namespace App\Filament\Resources\Destinations\Pages;

use App\Filament\Resources\Destinations\DestinationResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Database\Seeders\DiscoverSeeder;

class ListDestinations extends ListRecords
{
    protected static string $resource = DestinationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateSampleData')
                ->label('Generate Sample Destinations')
                ->icon('heroicon-o-sparkles')
                ->color('amber')
                ->requiresConfirmation()
                ->modalHeading('Generate Sample Destinations')
                ->modalDescription('This will populate sample Kenyan destination & safari records in the database.')
                ->action(function () {
                    try {
                        (new DiscoverSeeder())->run();
                        Notification::make()
                            ->title('Sample Destinations Seeded! 🌿')
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
