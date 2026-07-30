<?php

namespace App\Filament\Resources\Destinations\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DestinationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('short_description')
                    ->default(null),
                TextInput::make('category')
                    ->required(),
                TextInput::make('location')
                    ->required(),
                TextInput::make('county')
                    ->default(null),
                TextInput::make('region')
                    ->default(null),
                TextInput::make('country')
                    ->required()
                    ->default('Kenya'),
                TextInput::make('latitude')
                    ->numeric()
                    ->default(null),
                TextInput::make('longitude')
                    ->numeric()
                    ->default(null),
                FileUpload::make('cover_image_url')
                    ->image(),
                Textarea::make('gallery')
                    ->required()
                    ->default('[]')
                    ->columnSpanFull(),
                TextInput::make('price_kes')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('price_usd')
                    ->numeric()
                    ->default(null),
                TextInput::make('rating')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('review_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('duration_days')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('duration_label')
                    ->required()
                    ->default('1 Day'),
                TextInput::make('group_size_max')
                    ->required()
                    ->numeric()
                    ->default(6),
                TextInput::make('difficulty')
                    ->required()
                    ->default('Moderate'),
                TextInput::make('tour_type')
                    ->required()
                    ->default('Safari'),
                Textarea::make('highlights')
                    ->required()
                    ->default('[]')
                    ->columnSpanFull(),
                Textarea::make('included')
                    ->required()
                    ->default('[]')
                    ->columnSpanFull(),
                Textarea::make('excluded')
                    ->required()
                    ->default('[]')
                    ->columnSpanFull(),
                Textarea::make('what_to_bring')
                    ->required()
                    ->default('[]')
                    ->columnSpanFull(),
                Textarea::make('languages_spoken')
                    ->required()
                    ->default('["English","Swahili"]')
                    ->columnSpanFull(),
                Textarea::make('meeting_point')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('meeting_lat')
                    ->numeric()
                    ->default(null),
                TextInput::make('meeting_lng')
                    ->numeric()
                    ->default(null),
                Textarea::make('transport_info')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('meal_info')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('health_safety_info')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('cancellation_policy')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('faqs')
                    ->required()
                    ->default('[]')
                    ->columnSpanFull(),
                Select::make('operator_id')
                    ->relationship('operator', 'name'),
                Toggle::make('is_popular')
                    ->required(),
                Toggle::make('is_featured')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('xp_reward')
                    ->required()
                    ->numeric()
                    ->default(100),
                Textarea::make('metadata')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('location_node_id')
                    ->relationship('locationNode', 'name'),
                TextInput::make('busyness_score')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('crowd_density')
                    ->required()
                    ->default('Quiet'),
                TextInput::make('current_visitors')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('peak_hours')
                    ->default(null),
                TextInput::make('weather_note')
                    ->default(null),
                Toggle::make('instant_booking')
                    ->required(),
                Textarea::make('available_days')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
