<?php

use App\Models\ChampionshipWeek;

if (! function_exists('activeWeekForChampionship')) {
    function activeWeekForChampionship(int $championshipId)
    {
        return ChampionshipWeek::where('championship_id', $championshipId)
            ->where('is_active', true)
            ->first();
    }
}
