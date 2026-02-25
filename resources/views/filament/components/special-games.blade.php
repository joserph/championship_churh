{{-- <x-filament::section>
    <x-filament::input.wrapper>
        <x-filament::input.select
            wire:model.live="weekId"
        >
            <option value="">Seleccionar semana</option>
            @foreach($championship_weeks as $week)
                <option value="{{ $week->id }}">
                    Semana {{ $week->week_number }}
                </option>
            @endforeach
        </x-filament::input.select>
    </x-filament::input.wrapper>
</x-filament::section> --}}


{{-- @if($this->week && $this->week->specialGames->count())
    <div wire:key="special-games-week-{{ $week?->id ?? 'none' }}" >
        <x-filament::section>
            <x-slot name="heading">
                Juegos Especiales
            </x-slot>

            <div class="fi-ta-actions fi-align-start fi-wrapped">

                @foreach($this->week->specialGames as $game)

                    <x-filament::fieldset>
                        <x-slot name="label">
                            {{ $game->name }} ({{ $game->points }} pts)
                        </x-slot>

                        <x-filament::input.wrapper>
                            <x-filament::input.select
                                wire:model.live="specialWinners.{{ $game->id }}"
                            >
                                <option value="">
                                    Seleccionar ganador
                                </option>

                                @foreach($teams as $team)
                                    <option
                                        value="{{ $team->id }}"
                                        @selected(
                                            \App\Models\SpecialGameResult::where('special_game_id', $game->id)
                                                ->value('team_id') == $team->id
                                        )
                                    >
                                        {{ $team->name }}
                                    </option>
                                @endforeach

                            </x-filament::input.select>
                        </x-filament::input.wrapper>

                    </x-filament::fieldset>

                @endforeach

            </div>

        </x-filament::section>
    </div>
@endif --}}

{{-- LO NUEVO PARA OPTIMIZAR EL CODIGO --}}
<x-filament::section>
    <x-filament::input.wrapper>
        <x-filament::input.select
            wire:model.live="weekId"
        >
            <option value="">Seleccionar semana</option>
            @foreach($championship_weeks as $week)
                <option value="{{ $week->id }}">
                    Semana {{ $week->week_number }}
                </option>
            @endforeach
        </x-filament::input.select>
    </x-filament::input.wrapper>
</x-filament::section>

@if($this->week && $this->week->specialGames->count())
    <div wire:key="special-games-week-{{ $week?->id ?? 'none' }}" >
        <x-filament::section>
            <x-slot name="heading">
                Juegos Especiales
            </x-slot>

            <div class="fi-ta-actions fi-align-start fi-wrapped">

                @foreach($this->week->specialGames as $game)

                    <x-filament::fieldset>
                        <x-slot name="label">
                            {{ $game->name }} ({{ $game->points }} pts)
                        </x-slot>

                        <x-filament::input.wrapper>
                            <x-filament::input.select
                                wire:model.live="specialWinners.{{ $game->id }}"
                            >
                                <option value="">Seleccionar ganador</option>

                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}">
                                        {{ $team->name }}
                                    </option>
                                @endforeach

                            </x-filament::input.select>
                        </x-filament::input.wrapper>

                    </x-filament::fieldset>

                @endforeach

            </div>

        </x-filament::section>
    </div>
@endif

