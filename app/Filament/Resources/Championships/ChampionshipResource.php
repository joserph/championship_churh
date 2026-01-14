<?php

namespace App\Filament\Resources\Championships;

use App\Filament\Resources\Championships\Pages\CreateChampionship;
use App\Filament\Resources\Championships\Pages\EditChampionship;
use App\Filament\Resources\Championships\Pages\ListChampionships;
use App\Filament\Resources\Championships\Schemas\ChampionshipForm;
use App\Filament\Resources\Championships\Tables\ChampionshipsTable;
use App\Models\Championship;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ChampionshipResource extends Resource
{
    protected static ?string $model = Championship::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ChampionshipForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChampionshipsTable::configure($table);
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
            'index' => ListChampionships::route('/'),
            'create' => CreateChampionship::route('/create'),
            'edit' => EditChampionship::route('/{record}/edit'),
        ];
    }
}
