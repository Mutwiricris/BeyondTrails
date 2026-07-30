<?php

namespace App\Filament\Resources\HiddenGems\Pages;

use App\Filament\Resources\HiddenGems\HiddenGemResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditHiddenGem extends EditRecord
{
    protected static string $resource = HiddenGemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
