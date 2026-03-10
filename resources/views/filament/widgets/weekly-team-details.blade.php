@php

$activities = $record->team
    ->players
    ->flatMap->playerActivityWeeks
    ->where('championship_week_id', $record->championship_week_id)
    ->groupBy(fn ($item) => $item->activity?->name ?? 'Actividad')
    ->map(fn ($items) => $items->sum('points_earned'));

$specialGames = $record->week
    ->specialGames
    ->map(function ($game) use ($record) {

        $winner = $game->results
            ->firstWhere('team_id', $record->team_id);

        return [
            'name' => $game->name,
            'points' => $winner?->points_earned ?? 0,
        ];
    });

@endphp


<div class="text-sm space-y-4">

    <div>
        <strong>Actividades</strong>

        <ul class="space-y-1">
            @foreach ($activities as $name => $points)
                <li class="flex justify-between">
                    <span>{{ $name }}</span>
                    <span class="font-semibold">{{ $points }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <div>
        <strong>Juegos Especiales</strong>

        <ul class="space-y-1">
            @foreach ($specialGames as $game)
                <li class="flex justify-between">
                    <span>{{ $game['name'] }}</span>
                    <span class="font-semibold">{{ $game['points'] }}</span>
                </li>
            @endforeach
        </ul>
    </div>

</div>