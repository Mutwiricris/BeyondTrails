<?php

namespace App\Filament\Resources\Destinations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class DestinationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('short_description')
                    ->searchable(),
                TextColumn::make('category')
                    ->searchable(),
                TextColumn::make('location')
                    ->searchable(),
                TextColumn::make('county')
                    ->searchable(),
                TextColumn::make('region')
                    ->searchable(),
                TextColumn::make('country')
                    ->searchable(),
                TextColumn::make('latitude')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('longitude')
                    ->numeric()
                    ->sortable(),
                ImageColumn::make('cover_image_url'),
                TextColumn::make('price_kes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price_usd')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('rating')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('review_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('duration_days')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('duration_label')
                    ->searchable(),
                TextColumn::make('group_size_max')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('difficulty')
                    ->searchable(),
                TextColumn::make('tour_type')
                    ->searchable(),
                TextColumn::make('meeting_lat')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('meeting_lng')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('operator.name')
                    ->searchable(),
                IconColumn::make('is_popular')
                    ->boolean(),
                IconColumn::make('is_featured')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('xp_reward')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('locationNode.name')
                    ->searchable(),
                TextColumn::make('busyness_score')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('crowd_density')
                    ->searchable(),
                TextColumn::make('current_visitors')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('peak_hours')
                    ->searchable(),
                TextColumn::make('weather_note')
                    ->searchable(),
                IconColumn::make('instant_booking')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
