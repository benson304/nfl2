<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-nfl-primary leading-tight">
            {{ __('Game Statistics') }}
        </h2>
    </x-slot>


    <div class="max-w-7xl mx-auto">
        <!-- Game Information -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-nfl-accent border-b border-gray-200">
                <h3 class="text-lg font-semibold mb-2 text-nfl-primary">{{ $game->homeTeam->name }} vs {{ $game->awayTeam->name }}</h3>
                <p><strong>Kickoff:</strong> {{ $game->kickoff->format('l, M j, Y g:i A') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($game->status) }}</p>
                <p><strong>Round:</strong> {{ $game->round }}</p>
                <p><strong>Score:</strong> {{ $game->home_score }} - {{ $game->away_score }}</p>
            </div>
        </div>

        <!-- Player Statistics -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h4 class="text-md font-semibold mb-4 text-nfl-primary">Player Statistics</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-nfl-accent">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Player</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Team</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Passing Yards</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Passing TDs</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Interceptions</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Rushing Yards</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Rushing TDs</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Receptions</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Receiving Yards</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Receiving TDs</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">2PT Conversions</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Fumbles Lost</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Fumble TDs</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Points</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($game->playerStats as $stat)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stat->player->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stat->player->team->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">{{ $stat->passing_yards }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">{{ $stat->passing_tds }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">{{ $stat->interceptions }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">{{ $stat->rushing_yards }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">{{ $stat->rushing_tds }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">{{ $stat->receptions }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">{{ $stat->receiving_yards }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">{{ $stat->receiving_tds }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">{{ $stat->two_point_conversions }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">{{ $stat->fumbles_lost }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">{{ $stat->offensive_fumble_return_td }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 font-semibold">{{ $stat->calculatePoints() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No player statistics available for this game.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Optional: Add Export Button or Additional Features -->
    </div>
</x-admin-layout>
