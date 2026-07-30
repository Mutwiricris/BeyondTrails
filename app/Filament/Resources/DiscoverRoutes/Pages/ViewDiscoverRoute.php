<?php

namespace App\Filament\Resources\DiscoverRoutes\Pages;

use App\Filament\Resources\DiscoverRoutes\DiscoverRouteResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDiscoverRoute extends ViewRecord
{
    protected static string $resource = DiscoverRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
