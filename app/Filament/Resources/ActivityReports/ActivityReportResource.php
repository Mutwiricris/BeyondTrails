<?php

namespace App\Filament\Resources\ActivityReports;

use App\Filament\Resources\ActivityReports\Pages\CreateActivityReport;
use App\Filament\Resources\ActivityReports\Pages\EditActivityReport;
use App\Filament\Resources\ActivityReports\Pages\ListActivityReports;
use App\Filament\Resources\ActivityReports\Pages\ViewActivityReport;
use App\Filament\Resources\ActivityReports\Schemas\ActivityReportForm;
use App\Filament\Resources\ActivityReports\Schemas\ActivityReportInfolist;
use App\Filament\Resources\ActivityReports\Tables\ActivityReportsTable;
use App\Models\ActivityReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivityReportResource extends Resource
{
    protected static ?string $model = ActivityReport::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Moderation & System';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldExclamation;

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('user');
    }

    public static function form(Schema $schema): Schema
    {
        return ActivityReportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ActivityReportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivityReportsTable::configure($table);
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
            'index' => ListActivityReports::route('/'),
            'create' => CreateActivityReport::route('/create'),
            'view' => ViewActivityReport::route('/{record}'),
            'edit' => EditActivityReport::route('/{record}/edit'),
        ];
    }
}
