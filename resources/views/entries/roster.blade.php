<x-app-layout>
<!-- Dialog Component -->
<div x-data="{
    isOpen: false,
    type: '',
    message: '',
    show(data) {
        this.type = data.type;
        this.message = data.message;
        this.isOpen = true;
    },
    hide() {
        this.isOpen = false;
    }
}"
@showdialog.window="show($event.detail)"
class="relative z-50">

    <!-- Background overlay -->
    <div x-show="isOpen"
         class="fixed inset-0 bg-black/30"
         @click="hide()"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    <!-- Dialog -->
    <div x-show="isOpen"
         class="fixed inset-0 z-10 overflow-y-auto"
         @click.away="hide()">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="isOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">

                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <div class="mt-2">
                            <p x-text="message" :class="{
                                'text-red-500': type === 'error',
                                'text-green-500': type === 'success'
                            }" class="text-sm"></p>
                        </div>
                    </div>
                </div>

                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                            @click="hide()">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

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
        <p class="mt-2 text-2xl font-bold">{{ $changesRemaining }} / 2</p>
    </div>

    <!-- Players Active Card -->
    <div class="px-4 py-6 bg-nfl-background border rounded-lg shadow flex items-center justify-between">
        <h3 class="text-lg font-medium">Players Active</h3>
        <p class="mt-2 text-2xl font-bold">{{ $playersActive }} / 8</p>
    </div>
</div>

<!-- Full Width Points by Position Card -->
<div class="mb-8">
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
    <div class=" bg-white border rounded-lg shadow">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Player Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wildcard Points</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Divisional Points</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Conference Points</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Super Bowl Points</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Points</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
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
}) as $player)
                    <tr x-data="{ showDropdown: false, loading: false }">
                        <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $player->pivot->roster_position }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $player->name }} ({{ $player->team->name }})
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($player->pivot->wildcard_points, 1) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($player->pivot->divisional_points, 1) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($player->pivot->conference_points, 1) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($player->pivot->superbowl_points, 1) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($player->pivot->total_points, 1) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center relative">
                            @if(!in_array($player->id, $lockedPlayers->pluck('id')->toArray()))
                                <div>
                                    <livewire:player-selector
                                        :entry="$entry"
                                        :currentPlayerId="$player->id"
                                        :rosterPosition="$player->pivot->roster_position"
                                        :key="'player-'.$player->id" />
                                </div>
                            @else
                                <span class="text-red-500">Locked</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
<!-- After current roster table -->
@if(isset($historicalPlayers) && $historicalPlayers->count() > 0)
    <div class="mt-8">
        <h3 class="text-xl font-semibold mb-4">Previously Rostered Players</h3>
        <div class="overflow-x-auto bg-white border rounded-lg shadow">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Player Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Points</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Removed Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historicalPlayers as $player)
                        <tr class="bg-gray-50">
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
