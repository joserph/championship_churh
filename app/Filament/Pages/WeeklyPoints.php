<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\WeekSummary;
use App\Models\Activity;
use App\Models\ChampionshipWeek;
use App\Models\Player;
use App\Models\PlayerActivityWeek;
use App\Models\SpecialGameResult;
use App\Models\Team;
use App\Models\TeamWeekScore;
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
use App\Filament\Widgets\TeamPointsOverview;
use Livewire\Attributes\On;
use App\Services\ChampionshipStandingsService;

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
                    'championship_weeks' => ChampionshipWeek::where('active', true)->get(),
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
                    $this->dispatch('refreshTeamStats');
                    unset($this->teamSubtotals);
                    $this->syncTeamWeekScores();
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
        $this->syncTeamWeekScores();
        $this->dispatch('weekChanged', weekId: $this->weekId);
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
        $this->dispatch('refreshTeamStats');
        $this->syncTeamWeekScores();

        // $this->calculateTeamSubtotals();
    }
    
    #[Computed()]
    public function teamSubtotals(): array
    {
        if (! $this->weekId) {
            return [];
        }
        // ✅ obtener championship_id seguro
        $championshipId = ChampionshipWeek::query()
            ->whereKey($this->weekId)
            ->value('championship_id');

        if (! $championshipId) {
            return [];
        }

        $teams = Team::query()
            ->where('championship_id', $championshipId)
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

    protected function getHeaderWidgets(): array
    {
        return [
            TeamPointsOverview::make([
                'totals' => $this->teamSubtotals,
                // 'teams' => $this->teams,
            ]),
            WeekSummary::make(['weekId' => $this->weekId])
        ];
    }

    // protected function getFooterWidgets(): array
    // {
    //     return [
    //         WeekSummary::make([
    //             'weekId' => $this->weekId,
    //         ]),
    //     ];
    // }

    #[On('refreshTeamStats')]
    public function refresh(): void
    {
        // fuerza rerender del widget
    }

    protected function syncTeamWeekScores(): void
    {
        if (! $this->weekId) {
            return;
        }

        TeamWeekScore::upsert(
            collect($this->teamSubtotals)
                ->map(fn ($points, $teamId) => [
                    'championship_week_id' => $this->weekId,
                    'team_id' => $teamId,
                    'total_points' => $points,
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
                ->values()
                ->toArray(),

            ['championship_week_id', 'team_id'],
            ['total_points', 'updated_at']
        );

        ChampionshipStandingsService::rebuild(
            $this->week->championship_id
        );
    }

}
