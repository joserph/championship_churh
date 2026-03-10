<?php

namespace App\Filament\Widgets;

use App\Models\Team;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\Reactive;
use App\Filament\Pages\WeeklyPoints;

class TeamPointsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '1s';
    #[Reactive]
    // public array $teams = [];
    public array $totals = [];
    protected function getStats(): array
    {
        if (empty($this->totals)) {
            return [
                Stat::make('Cargando...', '...')
            ];
        }

        $teams = Team::whereIn(
            'id',
            array_keys($this->totals)
        )->get();

        return $teams->map(fn ($team) =>
            Stat::make(
                $team->name,
                $this->totals[$team->id] ?? 0
            )
            ->description('Puntos')
            ->icon('heroicon-o-trophy')
        )->toArray();
    }

    protected function getPollingInterval(): ?string
    {
        return $this->totals ? '2s' : null;
    }

}