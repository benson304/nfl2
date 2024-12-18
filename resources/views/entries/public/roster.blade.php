<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $entry->entry_name }} (Owned by {{ $entry->user->name }})
            </h2>
            <div class="justify-end">
            <x-primary-button class="ml-3 bg-nfl-primary text-white hover:bg-nfl-secondary px-2 py-2 rounded" onclick="window.location.href='{{ route('standings.index') }}'">
                Back to Overall Standings
            </x-primary-button>
            <x-primary-button class="ml-3 bg-nfl-primary text-white hover:bg-nfl-secondary px-2 py-2 rounded" onclick="window.location.href='{{ route('standings.weekly', ['week' => 1])}}'">
                Back to Weekly Standings
            </x-primary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Total Points: {{ $totalPoints }}</h3>
                        <h3 class="text-lg font-semibold mb-2">Changes Remaining: {{ $entry->getChangesRemaining() }}</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2">Position</th>
                                    <th class="px-4 py-2">Player</th>
                                    <th class="px-4 py-2">Team</th>
                                    <th class="px-4 py-2">Points</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($entry->current_players as $player)
                                    <tr class="border-b">
                                        <td class="px-4 py-2">{{ $player->pivot->roster_position }}</td>
                                        <td class="px-4 py-2">{{ $player->name }}</td>
                                        <td class="px-4 py-2">{{ $player->team->name }}</td>
                                        <td class="px-4 py-2">
                                            {{ $pointsByPosition[$player->pivot->roster_position] ?? 0 }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
