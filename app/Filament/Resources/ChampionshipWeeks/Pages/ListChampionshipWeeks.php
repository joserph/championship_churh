<?php

namespace App\Filament\Resources\ChampionshipWeeks\Pages;

use App\Filament\Resources\ChampionshipWeeks\ChampionshipWeekResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChampionshipWeeks extends ListRecords
{
    protected static string $resource = ChampionshipWeekResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
