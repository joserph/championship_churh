<?php

namespace App\Filament\Resources\Teams\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TeamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('championship.name')
                ->badge()
                    ->searchable()
                    ->sortable(),
                // Color column
                ColorColumn::make('color')
                    ->label('')
                    ->width(20),

                // Name with color badge
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->description)
                    ->weight('bold'),

                // Youth count
                // TextColumn::make('players_count')->counts('players')->label('Players'),
                // TextColumn::make('youths_count')
                //     ->counts('youths')
                //     ->label('Youths')
                //     ->sortable()
                //     ->color('primary'),

                // Total points
                TextColumn::make('total_points')
                    ->label('Points')
                    ->numeric()
                    ->sortable()
                    ->color('success'),

                // Active status
                IconColumn::make('active')
                    ->boolean()
                    ->label('Active')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                SelectFilter::make('championship')
                    ->relationship('championship', 'name'),
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
