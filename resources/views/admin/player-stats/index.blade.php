<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Player Stats') }}
            </h2>
            <a href="{{ route('admin.player-stats.create') }}"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Player Stat
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="px-2 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Player</th>
                                <th class="px-2 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Game</th>
                                <th class="px-2 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Pass Yds</th>
                                <th class="px-2 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Pass TD</th>
                                <th class="px-2 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">INT</th>
                                <th class="px-2 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Rush Yds</th>
                                <th class="px-2 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Rush TD</th>
                                <th class="px-2 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Rec</th>
                                <th class="px-2 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Rec Yds</th>
                                <th class="px-2 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Rec TD</th>
                                <th class="px-2 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">2PC</th>
                                <th class="px-2 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Fum</th>
                                <th class="px-2 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Fum TD</th>
                                <th class="px-2 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($stats as $stat)
                                <tr>
                                    <td class="px-2 py-4 whitespace-nowrap border-b border-gray-200">{{ $stat->player->name }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap border-b border-gray-200">{{ $stat->game->id }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap border-b border-gray-200">{{ $stat->passing_yards }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap border-b border-gray-200">{{ $stat->passing_tds }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap border-b border-gray-200">{{ $stat->interceptions }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap border-b border-gray-200">{{ $stat->rushing_yards }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap border-b border-gray-200">{{ $stat->rushing_tds }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap border-b border-gray-200">{{ $stat->receptions }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap border-b border-gray-200">{{ $stat->receiving_yards }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap border-b border-gray-200">{{ $stat->receiving_tds }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap border-b border-gray-200">{{ $stat->two_point_conversions }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap border-b border-gray-200">{{ $stat->fumbles_lost }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap border-b border-gray-200">{{ $stat->offensive_fumble_return_td }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap text-right border-b border-gray-200">
                                        <a href="{{ route('admin.player-stats.edit', $stat) }}"
                                           class="text-blue-600 hover:text-blue-900 mr-4">Edit</a>
                                        <form action="{{ route('admin.player-stats.destroy', $stat) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Are you sure?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="px-2 py-4 text-center border-b border-gray-200">No player stats found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">
                {{ $stats->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
