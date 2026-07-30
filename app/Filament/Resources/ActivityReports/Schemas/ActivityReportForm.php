<?php

namespace App\Filament\Resources\ActivityReports\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ActivityReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('activity_id')
                    ->required(),
                TextInput::make('reporter_id')
                    ->required()
                    ->numeric(),
                TextInput::make('reason')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
            ]);
    }
}
