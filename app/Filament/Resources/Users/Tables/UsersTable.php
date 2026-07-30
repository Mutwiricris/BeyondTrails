<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('phone_number')
                    ->searchable(),
                TextColumn::make('first_name')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->searchable(),
                TextColumn::make('username')
                    ->searchable(),
                TextColumn::make('display_name')
                    ->searchable(),
                TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable(),
                TextColumn::make('photo_url')
                    ->searchable(),
                TextColumn::make('photo_thumbnail_url')
                    ->searchable(),
                TextColumn::make('gender')
                    ->searchable(),
                TextColumn::make('nationality')
                    ->searchable(),
                TextColumn::make('home_country')
                    ->searchable(),
                TextColumn::make('referral_source')
                    ->searchable(),
                TextColumn::make('id_number')
                    ->searchable(),
                TextColumn::make('passport_number')
                    ->searchable(),
                TextColumn::make('city')
                    ->searchable(),
                TextColumn::make('country')
                    ->searchable(),
                TextColumn::make('postal_code')
                    ->searchable(),
                TextColumn::make('preferred_currency')
                    ->searchable(),
                IconColumn::make('email_notifications')
                    ->boolean(),
                IconColumn::make('sms_notifications')
                    ->boolean(),
                IconColumn::make('push_notifications')
                    ->boolean(),
                IconColumn::make('location_enabled')
                    ->boolean(),
                IconColumn::make('show_distance_away')
                    ->boolean(),
                TextColumn::make('emergency_contact_name')
                    ->searchable(),
                TextColumn::make('emergency_contact_phone')
                    ->searchable(),
                TextColumn::make('emergency_contact_relation')
                    ->searchable(),
                TextColumn::make('travel_style')
                    ->searchable(),
                IconColumn::make('travel_insurance')
                    ->boolean(),
                TextColumn::make('explorer_level')
                    ->searchable(),
                TextColumn::make('current_xp')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('streak_days')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('role')
                    ->searchable(),
                TextColumn::make('profile_completion')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_profile_public')
                    ->boolean(),
                IconColumn::make('share_location_with_friends')
                    ->boolean(),
                TextColumn::make('phone_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('last_active_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('provider')
                    ->searchable(),
                TextColumn::make('provider_id')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('latitude')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('longitude')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('current_activity')
                    ->searchable(),
                TextColumn::make('sharing_mode')
                    ->searchable(),
                TextColumn::make('traveller_status')
                    ->searchable(),
                TextColumn::make('last_seen_at')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('allow_dms')
                    ->boolean(),
                TextColumn::make('gems_discovered_count')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
