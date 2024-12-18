<div class="flex items-center space-x-2">
    @if($rosterPosition === 'FLEX' || in_array($entry->players()->find($currentPlayerId)->position, ['RB', 'WR', 'TE']))
        <select wire:model="swapWithId" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <option value="">Select player to swap</option>
            @foreach($eligiblePlayers as $player)
                <option value="{{ $player->id }}">
                    {{ $player->name }} ({{ $player->pivot->roster_position }})
                </option>
            @endforeach
        </select>
        <button wire:click="swapPosition" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Swap
        </button>
    @else
        <span class="text-gray-500">Not eligible for FLEX swap</span>
    @endif
</div>
