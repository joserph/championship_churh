<?php

namespace App\Filament\Resources\Players\Tables;

use App\Models\Activity;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PlayersTable
{
    public static function configure(Table $table): Table
    {
        $activities = Activity::where('active', true)->get();
        $week = now()->weekOfYear;

        $columns = [
            TextColumn::make('name')
                ->searchable()
                ->sortable()
                ->weight('medium'),
            
            // REPLACE: TextColumn for age with birthdate column
            TextColumn::make('birthdate')
                ->label('Birthdate')
                ->date('d/m/Y')
                ->sortable()
                ->searchable(),
            
            // ADD: Age column (calculated)
            TextColumn::make('age')
                ->label('Age')
                ->sortable(['birthdate']) // Sort by birthdate
                ->getStateUsing(fn ($record): int => $record->age)
                ->alignCenter(),
            
            TextColumn::make('team.name')
                ->label('Team')
                ->badge()
                ->color(fn ($record) => $record->team->color ?? 'gray'),
            
            IconColumn::make('active')
                ->boolean()
                ->label('Active')
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle'),
            TextColumn::make('total_points')
                ->label('Points')
                ->numeric()
                ->sortable()
                ->color('success')
                ->weight('bold'),

            TextColumn::make('team.championship.name')
                ->label('Championship'),
            ];
            // Dynamic activity checkboxes
            /*foreach ($activities as $activity) {
                $columns[] = CheckboxColumn::make("activity_{$activity->id}")
                    ->label($activity->name . " ({$activity->points})")
                    ->getStateUsing(function ($record) use ($activity, $week) {

                        return $record->activities()
                            ->wherePivot('activity_id', $activity->id)
                            ->wherePivot('championship_week_id', $week)
                            ->value('completed') ?? false;

                    })
                    ->updateStateUsing(function ($state, $record) use ($activity, $week) {

                        $record->activities()->syncWithoutDetaching([
                            $activity->id => [
                                'week' => $week,
                                'completed' => $state,
                            ],
                        ]);

                        // optional: recalc team points later
                        // recalcTeamPoints($record->team_id, $week);
                    });
            }*/
            // Points column
            /*$columns[] = TextColumn::make('weekly_points')
                ->label('Points (week)')
                ->getStateUsing(fn ($record) => $record->pointsForWeek($week));*/

        

        return $table
            ->columns($columns)
            ->filters([
                SelectFilter::make('team')->relationship('team', 'name'),
                SelectFilter::make('championship')
                    ->relationship('team.championship', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
