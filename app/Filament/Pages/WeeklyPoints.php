<?php

namespace App\Filament\Pages;

use App\Models\Activity;
use App\Models\ChampionshipWeek;
use App\Models\Player;
use App\Models\PlayerActivityWeek;
use App\Models\SpecialGameResult;
use App\Models\Team;
use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;

class WeeklyPoints extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;
    protected string $view = 'filament.pages.weekly-points';
    public ?int $weekId = null;
    public array $data = [];
    protected ?Collection $activities = null;
    public ?ChampionshipWeek $week = null;
    public array $specialWinners = [];
    public $teams = [];
    // public array $teamSubtotals = [];


    public function table(Table $table): Table
    {
        
        return $table
            ->query(
                Player::query()
                    ->with([
                        'team',
                        'playerActivityWeeks' => function ($q) {
                            if ($this->weekId) {
                                $q->where('championship_week_id', $this->weekId);
                            }
                        }
                    ])
            )
            ->header(
                fn () => view('filament.components.special-games', [
                    'week' => $this->week,
                    'teams' => $this->teams,
                    'championship_weeks' => ChampionshipWeek::all(),
                ])
            )
            ->columns($this->getDynamicColumns())
            ->groups([
                Group::make('team.name')
                    ->label('Equipo')
                    ->collapsible()
                    ->getTitleFromRecordUsing(function ($record) {
                        $subtotal =
                            $this->teamSubtotals[$record->team_id] ?? 0;

                        return "{$record->team->name} (Subtotal: {$subtotal})";
                    }),
            ])
            ->defaultGroup('team.name')
            ->paginated(false);
        
    }

    protected function getDynamicColumns(): array
    {
        $columns = [
            TextColumn::make('team.name')
                ->label('Equipo')
                ->sortable(),

            TextColumn::make('name')
                ->label('Jugador')
                ->sortable(),
        ];

        $activities = Activity::where('active', true)->get();

        foreach ($activities as $activity) {

            $columns[] = CheckboxColumn::make("activity_{$activity->id}")
                ->label($activity->name)

                ->getStateUsing(function ($record) use ($activity) {

                    if (!$this->weekId) return false;

                    return $record->playerActivityWeeks
                        ->where('activity_id', $activity->id)
                        ->isNotEmpty();
                })
                ->updateStateUsing(function ($record, $state) use ($activity) {

                    if (!$this->weekId) return;

                    if ($state) {

                        PlayerActivityWeek::updateOrCreate(
                            [
                                'championship_week_id' => $this->weekId,
                                'player_id' => $record->id,
                                'activity_id' => $activity->id,
                            ],
                            [
                                'points_earned' => $activity->points,
                            ]
                        );

                    } else {

                        PlayerActivityWeek::where([
                            'championship_week_id' => $this->weekId,
                            'player_id' => $record->id,
                            'activity_id' => $activity->id,
                        ])->delete();
                    }
                    // $this->calculateTeamSubtotals();
                    $this->dispatch('$refresh');
                });
        }

        $columns[] = TextColumn::make('total_points')
            ->label('Total puntos')
            ->state(function ($record) {
                if (!$this->weekId) return 0;
                return $record->playerActivityWeeks->sum('points_earned');
            })
            ->badge()
            ->color('success');

        return $columns;
    }

    public function updatedWeekId($value): void
    {
        $this->week = ChampionshipWeek::with('specialGames')
        ->find($value);

        $this->teams = Team::where(
            'championship_id',
            $this->week->championship_id
        )->get();

        $this->loadWeekData();
        $this->syncSpecialWinners();
        // $this->calculateTeamSubtotals();
    }

    protected function loadWeekData(): void
    {
        if (! $this->weekId) {
            return;
        }

        $this->week = ChampionshipWeek::query()
            ->with([
                'specialGames.results', // 🔥 clave
            ])
            ->find($this->weekId);

        $this->teams = Team::where(
            'championship_id',
            $this->week->championship_id
        )->get();
    }

    protected function syncSpecialWinners(): void
    {
        $this->specialWinners =
            $this->week
                ->specialGames
                ->mapWithKeys(fn ($game) => [
                    $game->id =>
                        $game->results->first()?->team_id
                ])
                ->toArray();
    }

    public function updatedSpecialWinners($teamId, $gameId)
    {
        if (! $teamId) return;
        $gameId = (int) $gameId;
        $game = $this->week
            ->specialGames
            ->firstWhere('id', (int) $gameId);

        if (! $game) {
            return; // seguridad extra
        }
        SpecialGameResult::updateOrCreate(
            ['special_game_id' => $gameId],
            [
                'team_id' => $teamId,
                'points_earned' => $game->points,
            ]
        );
        unset($this->teamSubtotals);

        // $this->calculateTeamSubtotals();
    }
    
    #[Computed()]
    public function teamSubtotals(): array
    {
        if (! $this->weekId) {
            return [];
        }

        $teams = Team::query()
            ->where('championship_id', $this->week->championship_id)
            ->with([
                'players.playerActivityWeeks' => fn ($q)
                    => $q->where('championship_week_id', $this->weekId),
            ])
            ->get();

        // 🔥 juegos especiales en UNA sola query
        $specialPoints = SpecialGameResult::query()
            ->whereHas(
                'specialGame',
                fn ($q) => $q->where(
                    'championship_week_id',
                    $this->weekId
                )
            )
            ->get()
            ->groupBy('team_id')
            ->map(fn ($items) => $items->sum('points_earned'));

        return $teams
            ->mapWithKeys(function ($team) use ($specialPoints) {

                $playerPoints = $team->players
                    ->flatMap->playerActivityWeeks
                    ->sum('points_earned');

                return [
                    $team->id =>
                        $playerPoints +
                        ($specialPoints[$team->id] ?? 0),
                ];
            })
            ->toArray();
    }

}
