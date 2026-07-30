<?php

namespace App\Filament\Resources\HiddenGems\Pages;

use App\Filament\Resources\HiddenGems\HiddenGemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHiddenGems extends ListRecords
{
    protected static string $resource = HiddenGemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
