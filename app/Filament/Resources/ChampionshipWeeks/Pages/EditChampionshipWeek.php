<?php

namespace App\Filament\Resources\ChampionshipWeeks\Pages;

use App\Filament\Resources\ChampionshipWeeks\ChampionshipWeekResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditChampionshipWeek extends EditRecord
{
    protected static string $resource = ChampionshipWeekResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
