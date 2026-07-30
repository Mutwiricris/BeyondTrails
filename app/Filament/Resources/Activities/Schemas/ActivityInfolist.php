<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('category')
                    ->placeholder('-'),
                TextEntry::make('type')
                    ->placeholder('-'),
                TextEntry::make('title')
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('location_type')
                    ->placeholder('-'),
                TextEntry::make('general_area')
                    ->placeholder('-'),
                TextEntry::make('location_name')
                    ->placeholder('-'),
                TextEntry::make('latitude')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('longitude')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('time_type')
                    ->placeholder('-'),
                TextEntry::make('specific_time')
                    ->placeholder('-'),
                TextEntry::make('duration_hours')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('min_age')
                    ->numeric(),
                TextEntry::make('max_age')
                    ->numeric(),
                TextEntry::make('privacy'),
                TextEntry::make('max_capacity')
                    ->numeric(),
                TextEntry::make('join_approval'),
                TextEntry::make('tags')
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_host_verified')
                    ->boolean(),
                TextEntry::make('status'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
