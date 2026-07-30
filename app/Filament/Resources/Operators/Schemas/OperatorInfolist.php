<?php

namespace App\Filament\Resources\Operators\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OperatorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('slug'),
                TextEntry::make('name'),
                TextEntry::make('tagline')
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('logo_url')
                    ->placeholder('-'),
                ImageEntry::make('cover_image_url')
                    ->placeholder('-'),
                TextEntry::make('gallery')
                    ->columnSpanFull(),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('website')
                    ->placeholder('-'),
                TextEntry::make('address')
                    ->placeholder('-'),
                TextEntry::make('city')
                    ->placeholder('-'),
                TextEntry::make('country'),
                TextEntry::make('business_type')
                    ->placeholder('-'),
                TextEntry::make('specializations')
                    ->columnSpanFull(),
                TextEntry::make('certifications')
                    ->columnSpanFull(),
                TextEntry::make('services')
                    ->columnSpanFull(),
                TextEntry::make('languages')
                    ->columnSpanFull(),
                TextEntry::make('operating_hours')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('social_links')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('cancellation_policy')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('payment_terms')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('safety_measures')
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_verified')
                    ->boolean(),
                TextEntry::make('verification_badge')
                    ->placeholder('-'),
                TextEntry::make('license_number')
                    ->placeholder('-'),
                TextEntry::make('rating')
                    ->numeric(),
                TextEntry::make('review_count')
                    ->numeric(),
                TextEntry::make('total_bookings')
                    ->numeric(),
                TextEntry::make('tours_offered')
                    ->numeric(),
                TextEntry::make('accommodations_offered')
                    ->numeric(),
                TextEntry::make('member_since')
                    ->date()
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                IconEntry::make('is_featured')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
