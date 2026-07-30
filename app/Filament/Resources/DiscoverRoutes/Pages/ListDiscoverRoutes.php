<?php

namespace App\Filament\Resources\DiscoverRoutes\Pages;

use App\Filament\Resources\DiscoverRoutes\DiscoverRouteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDiscoverRoutes extends ListRecords
{
    protected static string $resource = DiscoverRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
