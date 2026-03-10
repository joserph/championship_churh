<?php

namespace App\Filament\Widgets;

use App\Models\ChampionshipStanding;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;

class ChampionshipTable extends TableWidget
{
    protected static ?string $heading = 'Tabla General';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ChampionshipStanding::query()
                    ->with('team')
                    ->orderBy('position')
            )
            ->columns([
                TextColumn::make('position')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('team.name')
                    ->label('Equipo')
                    ->searchable(),

                TextColumn::make('played_weeks')
                    ->label('Semanas'),

                TextColumn::make('total_points')
                    ->label('Puntos')
                    ->sortable(),

                TextColumn::make('points_difference')
                    ->label('Avg'),
            ]);
    }
}