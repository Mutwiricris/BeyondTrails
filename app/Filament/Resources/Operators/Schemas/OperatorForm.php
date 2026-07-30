<?php

namespace App\Filament\Resources\Operators\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OperatorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('tagline')
                    ->default(null),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('logo_url')
                    ->url()
                    ->default(null),
                FileUpload::make('cover_image_url')
                    ->image(),
                Textarea::make('gallery')
                    ->required()
                    ->default('[]')
                    ->columnSpanFull(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->default(null),
                TextInput::make('phone')
                    ->tel()
                    ->default(null),
                TextInput::make('website')
                    ->url()
                    ->default(null),
                TextInput::make('address')
                    ->default(null),
                TextInput::make('city')
                    ->default(null),
                TextInput::make('country')
                    ->required()
                    ->default('Kenya'),
                TextInput::make('business_type')
                    ->default(null),
                Textarea::make('specializations')
                    ->required()
                    ->default('[]')
                    ->columnSpanFull(),
                Textarea::make('certifications')
                    ->required()
                    ->default('[]')
                    ->columnSpanFull(),
                Textarea::make('services')
                    ->required()
                    ->default('[]')
                    ->columnSpanFull(),
                Textarea::make('languages')
                    ->required()
                    ->default('["English","Swahili"]')
                    ->columnSpanFull(),
                Textarea::make('operating_hours')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('social_links')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('cancellation_policy')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('payment_terms')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('safety_measures')
                    ->default(null)
                    ->columnSpanFull(),
                Toggle::make('is_verified')
                    ->required(),
                TextInput::make('verification_badge')
                    ->default(null),
                TextInput::make('license_number')
                    ->default(null),
                TextInput::make('rating')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('review_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_bookings')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('tours_offered')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('accommodations_offered')
                    ->required()
                    ->numeric()
                    ->default(0),
                DatePicker::make('member_since'),
                Toggle::make('is_active')
                    ->required(),
                Toggle::make('is_featured')
                    ->required(),
            ]);
    }
}
