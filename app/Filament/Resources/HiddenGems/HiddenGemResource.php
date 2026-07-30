<?php

namespace App\Filament\Resources\HiddenGems;

use App\Filament\Resources\HiddenGems\Pages\CreateHiddenGem;
use App\Filament\Resources\HiddenGems\Pages\EditHiddenGem;
use App\Filament\Resources\HiddenGems\Pages\ListHiddenGems;
use App\Filament\Resources\HiddenGems\Pages\ViewHiddenGem;
use App\Filament\Resources\HiddenGems\Schemas\HiddenGemForm;
use App\Filament\Resources\HiddenGems\Schemas\HiddenGemInfolist;
use App\Filament\Resources\HiddenGems\Tables\HiddenGemsTable;
use App\Models\HiddenGem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HiddenGemResource extends Resource
{
    protected static ?string $model = HiddenGem::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Travel & Destinations';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return HiddenGemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return HiddenGemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HiddenGemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHiddenGems::route('/'),
            'create' => CreateHiddenGem::route('/create'),
            'view' => ViewHiddenGem::route('/{record}'),
            'edit' => EditHiddenGem::route('/{record}/edit'),
        ];
    }
}
