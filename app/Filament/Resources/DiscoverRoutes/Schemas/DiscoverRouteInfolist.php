<?php

namespace App\Filament\Resources\DiscoverRoutes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DiscoverRouteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('slug'),
                TextEntry::make('name'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('type'),
                TextEntry::make('difficulty'),
                TextEntry::make('region')
                    ->placeholder('-'),
                TextEntry::make('country'),
                TextEntry::make('start_point_name')
                    ->placeholder('-'),
                TextEntry::make('start_latitude')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('start_longitude')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('end_point_name')
                    ->placeholder('-'),
                TextEntry::make('end_latitude')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('end_longitude')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('waypoints')
                    ->columnSpanFull(),
                TextEntry::make('distance_km')
                    ->numeric(),
                TextEntry::make('duration_minutes')
                    ->numeric(),
                TextEntry::make('elevation_gain_meters')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('highlights')
                    ->columnSpanFull(),
                TextEntry::make('cover_photo_url')
                    ->placeholder('-'),
                TextEntry::make('photos')
                    ->columnSpanFull(),
                TextEntry::make('rating')
                    ->numeric(),
                TextEntry::make('completed_count')
                    ->numeric(),
                TextEntry::make('review_count')
                    ->numeric(),
                IconEntry::make('is_published')
                    ->boolean(),
                IconEntry::make('is_featured')
                    ->boolean(),
                TextEntry::make('xp_reward')
                    ->numeric(),
                TextEntry::make('created_by_user_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
