<?php

namespace App\Filament\Widgets;

use App\Models\TeamWeekScore;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Pagination\CursorPaginator;

class WeeklyResultsTable extends TableWidget
{
    protected static ?string $heading = 'Ranking por Semana';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getQuery())
            ->columns([

                TextColumn::make('week.name')
                    ->label('Semana'),

                TextColumn::make('position')
                    ->label('#')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        1 => 'success',
                        2 => 'warning',
                        3 => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('team.name')
                    ->label('Equipo')
                    ->weight(fn ($record) =>
                        $record->position === 1 ? 'bold' : 'normal'
                    ),

                TextColumn::make('total_points')
                    ->label('Puntos')
                    ->badge()
                    ->color('primary'),

            ])
            ->defaultSort('championship_week_id', 'desc')
            ->groups([
                'week.name',
            ]);
    }

    protected function getQuery(): Builder
    {
        return TeamWeekScore::query()
            ->with(['team', 'week'])
            ->orderByDesc('championship_week_id')
            ->orderByDesc('total_points');
    }

    public function getTableRecords(): Collection | Paginator | CursorPaginator
    {
        $records = parent::getTableRecords();

        if ($records instanceof Collection) {

            $grouped = $records->groupBy('championship_week_id');

            $grouped->each(function ($teams) {

                $position = 1;

                foreach ($teams as $team) {
                    $team->position = $position;
                    $position++;
                }

            });
        }

        return $records;
    }
}