<?php

namespace App\Filament\Resources\DiscoverRoutes;

use App\Filament\Resources\DiscoverRoutes\Pages\CreateDiscoverRoute;
use App\Filament\Resources\DiscoverRoutes\Pages\EditDiscoverRoute;
use App\Filament\Resources\DiscoverRoutes\Pages\ListDiscoverRoutes;
use App\Filament\Resources\DiscoverRoutes\Pages\ViewDiscoverRoute;
use App\Filament\Resources\DiscoverRoutes\RelationManagers\RouteSegmentsRelationManager;
use App\Filament\Resources\DiscoverRoutes\Schemas\DiscoverRouteForm;
use App\Filament\Resources\DiscoverRoutes\Schemas\DiscoverRouteInfolist;
use App\Filament\Resources\DiscoverRoutes\Tables\DiscoverRoutesTable;
use App\Models\DiscoverRoute;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DiscoverRouteResource extends Resource
{
    protected static ?string $model = DiscoverRoute::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Travel & Destinations';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return DiscoverRouteForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DiscoverRouteInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DiscoverRoutesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RouteSegmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDiscoverRoutes::route('/'),
            'create' => CreateDiscoverRoute::route('/create'),
            'view' => ViewDiscoverRoute::route('/{record}'),
            'edit' => EditDiscoverRoute::route('/{record}/edit'),
        ];
    }
}
