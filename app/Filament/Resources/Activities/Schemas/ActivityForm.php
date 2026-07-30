<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('category')
                    ->default(null),
                TextInput::make('type')
                    ->default(null),
                TextInput::make('title')
                    ->default(null),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('location_type')
                    ->default(null),
                TextInput::make('general_area')
                    ->default(null),
                TextInput::make('location_name')
                    ->default(null),
                TextInput::make('latitude')
                    ->numeric()
                    ->default(null),
                TextInput::make('longitude')
                    ->numeric()
                    ->default(null),
                DatePicker::make('date'),
                TextInput::make('time_type')
                    ->default(null),
                TextInput::make('specific_time')
                    ->default(null),
                TextInput::make('duration_hours')
                    ->numeric()
                    ->default(null),
                TextInput::make('min_age')
                    ->required()
                    ->numeric()
                    ->default(18),
                TextInput::make('max_age')
                    ->required()
                    ->numeric()
                    ->default(65),
                TextInput::make('privacy')
                    ->required()
                    ->default('open'),
                TextInput::make('max_capacity')
                    ->required()
                    ->numeric()
                    ->default(20),
                TextInput::make('join_approval')
                    ->required()
                    ->default('instant'),
                Textarea::make('tags')
                    ->default(null)
                    ->columnSpanFull(),
                Toggle::make('is_host_verified')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('upcoming'),
            ]);
    }
}
