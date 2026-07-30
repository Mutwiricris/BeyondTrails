<?php

namespace App\Filament\Resources\HiddenGems\Tables;

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

class HiddenGemsTable
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
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('short_description')
                    ->searchable(),
                TextColumn::make('category')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('location_name')
                    ->searchable(),
                TextColumn::make('address')
                    ->searchable(),
                TextColumn::make('city')
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
                TextColumn::make('difficulty')
                    ->searchable(),
                TextColumn::make('difficulty_level')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('best_time_to_visit')
                    ->searchable(),
                TextColumn::make('entry_fee_citizens_kes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('entry_fee_residents_kes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('entry_fee_non_residents_usd')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('entry_fee_children_kes')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_free_entry')
                    ->boolean(),
                TextColumn::make('contact_phone')
                    ->searchable(),
                TextColumn::make('contact_email')
                    ->searchable(),
                TextColumn::make('website')
                    ->searchable(),
                TextColumn::make('operator.name')
                    ->searchable(),
                TextColumn::make('submitted_by_user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('rating')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('review_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('visitor_count')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_verified')
                    ->boolean(),
                IconColumn::make('is_featured')
                    ->boolean(),
                IconColumn::make('is_published')
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
                TextColumn::make('audio_guide_url')
                    ->searchable(),
                TextColumn::make('video_url')
                    ->searchable(),
                TextColumn::make('discovered_by_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('upvotes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('downvotes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('added_by_name')
                    ->searchable(),
                IconColumn::make('is_local_guide')
                    ->boolean(),
                TextColumn::make('verification_status')
                    ->searchable(),
                TextColumn::make('locationNode.name')
                    ->searchable(),
                IconColumn::make('requires_permit')
                    ->boolean(),
                IconColumn::make('is_quest_unlock')
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
