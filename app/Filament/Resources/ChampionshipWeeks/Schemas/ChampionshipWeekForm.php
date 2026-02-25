<?php

namespace App\Filament\Resources\ChampionshipWeeks\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ChampionshipWeekForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Campeonato')
                    ->description('Informacion semana de campeonato')
                    ->schema([
                        Select::make('championship_id')
                            ->label('Campeonato')
                            ->relationship('championship', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('week_number')
                            ->required()
                            ->numeric(),
                        DatePicker::make('start_date'),
                        DatePicker::make('end_date'),
                        TextInput::make('special_games_count')
                            ->label('Cantidad de Juegos')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->reactive(),
                        Toggle::make('active')
                            ->required(),
                    ])->columns(2),
                Section::make('Juegos Especiales')
                    ->schema([
                        Repeater::make('specialGames')
                            ->relationship()
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->label('Nombre del juego'),

                                TextInput::make('points')
                                    ->numeric()
                                    ->default(50)
                                    ->required()
                                    ->label('Puntos'),

                                TextInput::make('game_number')
                                    ->numeric()
                                    ->required()
                                    ->label('Número del juego'),
                            ])
                            ->maxItems(fn ($get) => $get('special_games_count'))
                            ->visible(fn ($get) => (int) $get('special_games_count') > 0)
                            ->label('Juegos Especiales')
                            ->columns(2)
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ((int) $state === 0) {
                                    $set('specialGames', []);
                                }
                            })
                    ]),
            ]);
    }
}
