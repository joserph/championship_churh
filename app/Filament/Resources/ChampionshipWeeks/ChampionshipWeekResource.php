<?php

namespace App\Filament\Resources\ChampionshipWeeks;

use App\Filament\Resources\ChampionshipWeeks\Pages\CreateChampionshipWeek;
use App\Filament\Resources\ChampionshipWeeks\Pages\EditChampionshipWeek;
use App\Filament\Resources\ChampionshipWeeks\Pages\ListChampionshipWeeks;
use App\Filament\Resources\ChampionshipWeeks\Schemas\ChampionshipWeekForm;
use App\Filament\Resources\ChampionshipWeeks\Tables\ChampionshipWeeksTable;
use App\Models\ChampionshipWeek;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ChampionshipWeekResource extends Resource
{
    protected static ?string $model = ChampionshipWeek::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'week_number';

    public static function form(Schema $schema): Schema
    {
        return ChampionshipWeekForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChampionshipWeeksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChampionshipWeeks::route('/'),
            'create' => CreateChampionshipWeek::route('/create'),
            'edit' => EditChampionshipWeek::route('/{record}/edit'),
        ];
    }
}
