<?php

namespace App\Filament\Resources\DiscoverRoutes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DiscoverRouteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('type')
                    ->required(),
                TextInput::make('difficulty')
                    ->required(),
                TextInput::make('region')
                    ->default(null),
                TextInput::make('country')
                    ->required()
                    ->default('Kenya'),
                TextInput::make('start_point_name')
                    ->default(null),
                TextInput::make('start_latitude')
                    ->numeric()
                    ->default(null),
                TextInput::make('start_longitude')
                    ->numeric()
                    ->default(null),
                TextInput::make('end_point_name')
                    ->default(null),
                TextInput::make('end_latitude')
                    ->numeric()
                    ->default(null),
                TextInput::make('end_longitude')
                    ->numeric()
                    ->default(null),
                Textarea::make('waypoints')
                    ->required()
                    ->default('[]')
                    ->columnSpanFull(),
                TextInput::make('distance_km')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('duration_minutes')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('elevation_gain_meters')
                    ->numeric()
                    ->default(null),
                Textarea::make('highlights')
                    ->required()
                    ->default('[]')
                    ->columnSpanFull(),
                TextInput::make('cover_photo_url')
                    ->url()
                    ->default(null),
                Textarea::make('photos')
                    ->required()
                    ->default('[]')
                    ->columnSpanFull(),
                TextInput::make('rating')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('completed_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('review_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_published')
                    ->required(),
                Toggle::make('is_featured')
                    ->required(),
                TextInput::make('xp_reward')
                    ->required()
                    ->numeric()
                    ->default(200),
                TextInput::make('created_by_user_id')
                    ->numeric()
                    ->default(null),
            ]);
    }
}
