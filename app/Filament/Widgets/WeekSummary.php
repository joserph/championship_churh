<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Activity;
use App\Models\Team;
use App\Models\PlayerActivityWeek;
use App\Models\SpecialGameResult;
use Livewire\Attributes\On;

class WeekSummary extends Widget
{
    protected string $view = 'filament.widgets.week-summary';

    public ?int $weekId = null;

    #[On('weekChanged')]
    public function setWeek($weekId)
    {
        $this->weekId = $weekId;
    }

    public function getData(): array
    {
        if (!$this->weekId) {
            return [
                'teams' => [],
                'matrix' => [],
                'totals' => [],
                'winner' => null,
            ];
        }

        $activities = Activity::where('active', true)->get();
        //dd($activities);
        $teams = Team::with('players')->get();

        $activityPoints = PlayerActivityWeek::query()
            ->where('championship_week_id', $this->weekId)
            ->with(['activity', 'player.team'])
            ->get();

        $specialPoints = SpecialGameResult::query()
            ->whereHas(
                'specialGame',
                fn ($q) =>
                $q->where('championship_week_id', $this->weekId)
            )
            ->with('team', 'specialGame')
            ->get();

        $matrix = [];

        foreach ($activities as $activity) {
            foreach ($teams as $team) {

                $points = $activityPoints
                    ->where('activity_id', $activity->id)
                    ->where('player.team_id', $team->id)
                    ->sum('points_earned');

                $matrix[$activity->name][$team->id] = $points;
            }
        }

        foreach ($specialPoints as $result) {

            $matrix['Juegos'][$result->team_id] =
                ($matrix['Juegos'][$result->team_id] ?? 0)
                + $result->points_earned;
        }

        $totals = [];

        foreach ($teams as $team) {

            $totals[$team->id] =
                collect($matrix)
                    ->sum(fn ($row) => $row[$team->id] ?? 0);
        }

        $winner = collect($totals)
            ->sortDesc()
            ->keys()
            ->first();

        return [
            'teams' => $teams,
            'matrix' => $matrix,
            'totals' => $totals,
            'winner' => $winner
        ];
    }
}