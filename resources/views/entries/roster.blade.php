<x-app-layout>

<script>
    document.addEventListener('livewire:initialized', function () {
        Livewire.on('showDialog', (data) => {
            window.dispatchEvent(new CustomEvent('showdialog', { detail: data[0] }));
        });

        Livewire.on('playerUpdated', () => {
            location.reload();
        });
    });
</script>
<x-slot name="header">
        <h2 class="text-2xl font-semibold leading-tight text-nfl-primary">
            {{ $entry->entry_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<!-- Top Row Summary Cards -->
<div class="grid grid-cols-3 gap-6 mb-8">
    <!-- Total Points Card -->
    <div class="px-4 py-6 bg-nfl-background border rounded-lg shadow flex items-center justify-between">
        <h3 class="text-lg font-medium">Total Points</h3>
        <p class="mt-2 text-2xl font-bold">{{ $totalPoints }}</p>
    </div>

    <!-- Changes Remaining Card -->
    <div class="px-4 py-6 bg-nfl-background border rounded-lg shadow flex items-center justify-between">
        <h3 class="text-lg font-medium">Changes Remaining</h3>
        <p class="mt-2 text-2xl font-bold">{{ $entry->getChangesRemaining() }} / 2</p>
    </div>

    <!-- Players Active Card -->
    <div class="px-4 py-6 bg-nfl-background border rounded-lg shadow flex items-center justify-between">
        <h3 class="text-lg font-medium">Players Active</h3>
        <p class="mt-2 text-2xl font-bold">{{ $playersActive->count() }} / 8</p>
    </div>
</div>

<!-- Full Width Points by Position Card -->
<div class="mb-8 hidden">
    <div class="px-6 py-4 bg-nfl-background border rounded-lg shadow">
        <h3 class="text-lg font-medium mb-4">Points by Position</h3>
        <div class="grid grid-cols-2 gap-8">
            <div class="space-y-4">
                <div class="flex gap-4">
                    <span class="font-medium w-12">QB</span>
                    <span class="font-bold">{{ $pointsByPosition['QB'] ?? 0 }}</span>
                </div>
                <div class="flex gap-4">
                    <span class="font-medium w-12">WR</span>
                    <span class="font-bold">{{ $pointsByPosition['WR'] ?? 0 }}</span>
                </div>
                <div class="flex gap-4">
                    <span class="font-medium w-12">RB</span>
                    <span class="font-bold">{{ $pointsByPosition['RB'] ?? 0 }}</span>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex gap-4">
                    <span class="font-medium w-12">FLEX</span>
                    <span class="font-bold">{{ $pointsByPosition['FLEX'] ?? 0 }}</span>
                </div>
                <div class="flex gap-4">
                    <span class="font-medium w-12">TE</span>
                    <span class="font-bold">{{ $pointsByPosition['TE'] ?? 0 }}</span>
                </div>
                <div class="flex gap-4">
                    <span class="font-medium w-12">Total</span>
                    <span class="font-bold">{{ $totalPoints }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

            <!-- Flash Messages -->
            <div class="mb-4">
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        </div>
            <!-- Roster Table -->
            <div class="p-6 bg-white border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2">Position</th>
                                <th class="px-4 py-2">Player Name</th>
                                <th class="px-4 py-2">Wildcard Points</th>
                                <th class="px-4 py-2">Divisional Points</th>
                                <th class="px-4 py-2">Conference Points</th>
                    <th class="px-4 py-2">Super Bowl Points</th>
                    <th class="px-4 py-2">Total Points</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach($entry->players->sortBy(function($player) {
                $positionOrder = [
                    'QB' => 1,
                    'WR1' => 2,
                    'WR2' => 3,
                    'WR3' => 4,
                    'RB1' => 5,
                    'RB2' => 6,
                    'TE' => 7,
                    'FLEX' => 8
                ];
                return $positionOrder[$player->pivot->roster_position] ?? 999;
            })->sortBy(function($player) {
                return is_null($player->pivot->removed_at) ?0 :1;
            }) as $player)

                    <tr class="text-center {{!is_null($player->pivot->removed_at)?' bg-red-200':(!$playersActive->contains($player->id)?'bg-gray-200':'')}}" x-data="{ showDropdown: false, loading: false }">
                        <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $player->pivot->roster_position }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $player->name }} ({{ $player->team->name }})
                            @if(!is_null($player->pivot->removed_at))
                                <span class="block text-xs">Added: {{$transactions->firstWhere('dropped_player_id',$player->id)->addedPlayer->name}} ({{$transactions->firstWhere('dropped_player_id',$player->id)->created_at->format('m/d/Y g:i a')}})</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $entry->getPlayerPoints($player->id,'Wild Card') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ is_null($player->pivot->removed_at) ? $entry->getPlayerPoints($player->id,'Divisional'):'' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ is_null($player->pivot->removed_at) ? number_format($player->getPoints('Conference'), 1):''  }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ is_null($player->pivot->removed_at) ? number_format($player->getPoints('Super Bowl'), 1):''  }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ is_null($player->pivot->removed_at) ? number_format($player->pivot->total_points, 1):''  }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center relative">
                            @if(is_null($player->pivot->removed_at)&&!in_array($player->id, $lockedPlayers->pluck('id')->toArray()))
                                @if($changesRemaining<=0)
                                    <button type="button" disabled
                                            class="px-3 py-1 cursor-not-allowed text-sm text-white bg-blue-300 rounded">
                                        Change Player
                                    </button>
                                @else
                                <div>
                                    <livewire:player-selector
                                        :entry="$entry"
                                        :currentPlayerId="$player->id"
                                        :rosterPosition="$player->pivot->roster_position"
                                        :key="'player-'.$player->id" />
                                </div>

                                @endif
                                    @if($player->pivot->roster_position=='FLEX'&&is_null($player->pivot->removed_at) && !in_array($player->id, $lockedPlayers->pluck('id')->toArray()))
                                        <div>
                                            <livewire:position-swapper
                                                :entry="$entry"
                                                :currentPlayerId="$player->id"
                                                :rosterPosition="$player->pivot->roster_position"
                                                :key="'swap-'.$player->id" />
                                        </div>
                                    @endif
                            @elseif(is_null($player->pivot->removed_at))
                                <span class="text-red-500">Locked</span>
                            @else
                                @if($transactions->firstWhere('dropped_player_id',$player->id)->revoke===true)
                                <form method="POST" action="{{ route('entries.revert-player', $entry) }}">
                                    <input type="hidden" id="transaction_id" name="transaction_id" value="{{$transactions->firstWhere('dropped_player_id',$player->id)->id}}" />
                                    @csrf

                                    <button type="submit"
                                            class="px-3 py-1 cursor-pointer text-sm text-white bg-red-600 rounded">
                                        Cancel Change
                                    </button>
                                </form>
                                    @endif

                            @endif

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
<!-- After current roster table -->
@if(isset($historicalPlayers) && $historicalPlayers->count() > 0)
    <div class="mt-8 hidden">
        <h3 class="text-xl font-semibold mb-4">Previously Rostered Players</h3>
        <div class="p-6 bg-white border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-100">
                        <th class="px-4 py-2">Position</th>
                        <th class="px-4 py-2">Player Name</th>
                        <th class="px-4 py-2">Total Points</th>
                        <th class="px-4 py-2">Removed Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historicalPlayers as $player)
                        <tr class="text-center border-b border-gray-200">
                            <td class="px-6 py-4">{{ $player['roster_position'] }}</td>
                            <td class="px-6 py-4">{{ $player['name'] }}</td>
                            <td class="px-6 py-4">{{ number_format($player['total_points'], 1) }}</td>
                            <td class="px-6 py-4">{{ Carbon\Carbon::parse($player['removed_at'])->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
</x-app-layout>
