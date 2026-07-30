<?php

namespace App\Filament\Widgets;

use App\Models\ActivityReport;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class PendingModerationWidget extends TableWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Recent Moderation Queue & Reports';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ActivityReport::query()->with('user')->latest())
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Reporter')
                    ->searchable()
                    ->default('System Alert'),
                Tables\Columns\TextColumn::make('event')
                    ->badge()
                    ->color('warning')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Action::make('resolve')
                    ->label('Mark Resolved')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (ActivityReport $record) => $record->delete()),
            ])
            ->emptyStateHeading('No pending moderation reports')
            ->emptyStateDescription('All user reports and safety alerts have been cleared.')
            ->emptyStateIcon('heroicon-o-shield-check');
    }
}

