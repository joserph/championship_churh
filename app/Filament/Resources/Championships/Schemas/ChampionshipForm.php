<?php

namespace App\Filament\Resources\Championships\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ChampionshipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),

                DatePicker::make('start_date'),

                DatePicker::make('end_date'),

                Toggle::make('active')->default(true),
            ]);
    }
}
