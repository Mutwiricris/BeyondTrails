<?php

namespace App\Filament\Resources\Operators\Pages;

use App\Filament\Resources\Operators\OperatorResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Database\Seeders\DiscoverSeeder;

class ListOperators extends ListRecords
{
    protected static string $resource = OperatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateSampleData')
                ->label('Generate Sample Operators')
                ->icon('heroicon-o-sparkles')
                ->color('amber')
                ->requiresConfirmation()
                ->modalHeading('Generate Sample Tour Operators')
                ->modalDescription('This will populate sample verified tour operator records in the database.')
                ->action(function () {
                    try {
                        (new DiscoverSeeder())->run();
                        Notification::make()
                            ->title('Sample Tour Operators Seeded! 🧭')
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
