<?php

namespace App\Filament\Resources\HiddenGems\Pages;

use App\Filament\Resources\HiddenGems\HiddenGemResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Database\Seeders\DiscoverSeeder;

class ListHiddenGems extends ListRecords
{
    protected static string $resource = HiddenGemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateSampleData')
                ->label('Generate Sample Hidden Gems')
                ->icon('heroicon-o-sparkles')
                ->color('amber')
                ->requiresConfirmation()
                ->modalHeading('Generate Sample Hidden Gems')
                ->modalDescription('This will populate sample hidden gem records in the database.')
                ->action(function () {
                    try {
                        (new DiscoverSeeder())->run();
                        Notification::make()
                            ->title('Sample Hidden Gems Seeded! 💎')
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
