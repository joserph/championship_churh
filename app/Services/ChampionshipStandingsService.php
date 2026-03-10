<?php

namespace App\Services;

use App\Models\ChampionshipStanding;
use App\Models\TeamWeekScore;
use App\Models\ChampionshipWeek;

class ChampionshipStandingsService
{
    public static function rebuild(int $championshipId): void
    {
        $scores = TeamWeekScore::query()
            ->whereHas(
                'week',
                fn ($q) =>
                    $q->where('championship_id', $championshipId)
            )
            ->get()
            ->groupBy('team_id');

        $standings = [];

        foreach ($scores as $teamId => $weeks) {

            $total = $weeks->sum('total_points');

            $standings[] = [
                'championship_id' => $championshipId,
                'team_id' => $teamId,
                'played_weeks' => $weeks->count(),
                'total_points' => $total,
                'points_difference' =>
                    $total - $weeks->avg('total_points'),
            ];
        }

        ChampionshipStanding::upsert(
            $standings,
            ['championship_id', 'team_id']
        );

        self::calculatePositions($championshipId);
    }

    protected static function calculatePositions(int $championshipId)
    {
        $teams = ChampionshipStanding::where(
            'championship_id',
            $championshipId
        )
        ->orderByDesc('total_points')
        ->orderByDesc('points_difference')
        ->get();

        foreach ($teams as $index => $team) {
            $team->update([
                'position' => $index + 1
            ]);
        }
    }
}