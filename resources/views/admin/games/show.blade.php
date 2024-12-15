<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-nfl-primary leading-tight">
            {{ __('Game Details') }}
        </h2>
    </x-slot>


    <div class="max-w-7xl mx-auto">
        <!-- Game Summary -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-nfl-accent border-b text-white border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="text-2xl font-bold text-gray-100">{{ $game->homeTeam->name }}</div>
                        <div class="text-lg text-gray-100">{{ $game->home_score }}</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-100">{{ $game->awayTeam->name }}</div>
                        <div class="text-lg text-gray-100">{{ $game->away_score }}</div>
                    </div>
                </div>
                <div class="mt-4">
                    <p><strong>Kickoff:</strong> {{ $game->kickoff->format('M j, Y g:i A') }}</p>
                    <p><strong>Status:</strong>
                        <span class="inline-block px-4 rounded-full
                            @if($game->status === 'scheduled')
                                bg-nfl-secondary text-white
                            @elseif($game->status === 'in_progress')
                                bg-green-500 text-white
                            @elseif($game->status === 'finished')
                                bg-gray-300 text-gray-900
                            @endif
                        ">
                            {{ ucfirst($game->status) }}
                        </span>
                    </p>
                    <p><strong>Round:</strong> {{ $game->round }}</p>
                </div>
            </div>
        </div>

        <!-- Player Statistics -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-xl font-semibold text-nfl-primary mb-4">Player Statistics</h3>
                @foreach([$game->homeTeam, $game->awayTeam] as $team)
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-nfl-primary mb-2">{{ $team->name }}</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-nfl-accent">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Player</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Pass</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Rush</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Receive</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Other</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-white uppercase tracking-wider">Points</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($game->playerStats->where('player.team_id', $team->id) as $stat)
                                        <tr>
                                            <td class="px-4 py-2">
                                                <div class="font-medium text-gray-900">{{ $stat->player->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $stat->player->position }}</div>
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-900">
                                                {{ $stat->passing_yards }} yds<br>
                                                {{ $stat->passing_tds }} TD<br>
                                                {{ $stat->interceptions }} INT
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-900">
                                                {{ $stat->rushing_yards }} yds<br>
                                                {{ $stat->rushing_tds }} TD
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-900">
                                                {{ $stat->receptions }} rec<br>
                                                {{ $stat->receiving_yards }} yds<br>
                                                {{ $stat->receiving_tds }} TD
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-900">
                                                2PT: {{ $stat->two_point_conversions }}<br>
                                                FUM L: {{ $stat->fumbles_lost }}<br>
                                                FUM TD: {{ $stat->offensive_fumble_return_td }}
                                            </td>
                                            <td class="px-4 py-2 text-sm text-right text-gray-900 font-semibold">
                                                {{ $stat->calculatePoints() }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-2 text-sm text-gray-500 text-center">No player statistics available for this team.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-admin-layout>
