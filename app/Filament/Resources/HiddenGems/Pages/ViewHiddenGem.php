<?php

namespace App\Filament\Resources\HiddenGems\Pages;

use App\Filament\Resources\HiddenGems\HiddenGemResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewHiddenGem extends ViewRecord
{
    protected static string $resource = HiddenGemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
