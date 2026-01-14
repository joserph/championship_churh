<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),

                TextInput::make('points')
                    ->numeric()
                    ->required()
                    ->minValue(0),

                Toggle::make('active')->default(true),
            ]);
    }
}
