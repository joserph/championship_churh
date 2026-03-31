<x-filament-widgets::widget>
    <x-filament::section>
    @php
    $data = $this->getData();
    //dd($data);
    $teams = $data['teams'];
    $matrix = $data['matrix'];
    $totals = $data['totals'];
    $winner = $data['winner'];
    @endphp

    <table class="w-full text-sm border rounded-lg overflow-hidden">
        
        <thead class="bg-gray-100">
            <tr>

                <th class="p-2 text-left">
                    Actividad
                </th>

                @foreach($teams as $team)

                <th class="p-2 text-center
                    {{ $team->id === $winner ? 'bg-yellow-100 font-bold' : '' }}">

                    {{ $team->name }}

                </th>

                @endforeach

            </tr>
        </thead>

        <tbody>

            @foreach($matrix as $activity => $row)

            <tr class="border-t">

                <td class="p-2 font-medium">
                    {{ $activity }}
                </td>

                @foreach($teams as $team)

                <td class="p-2 text-center">

                    {{ $row[$team->id] ?? 0 }}

                </td>

                @endforeach

            </tr>

            @endforeach

            <tr class="border-t bg-gray-50 font-bold">

                <td class="p-2">
                    TOTAL
                </td>

                @foreach($teams as $team)

                <td class="p-2 text-center
                    {{ $team->id === $winner ? 'text-green-600 text-lg' : '' }}">

                    {{ $totals[$team->id] ?? 0 }}

                </td>

                @endforeach

            </tr>

        </tbody>

    </table>

    </x-filament::section>
</x-filament-widgets::widget>
