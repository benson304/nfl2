<x-app-layout>

    <!-- Flash Messages -->
    <!-- TODO: MAKE THIS BETTER AND MORE CONSISTENT THROUGHOUT -->
    @if (session('success')||session('error'))
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
    @endif
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Overview Section -->
            <x-card >
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="bg-nfl-primary border border-nfl-primary/20 p-4 rounded-lg">
                        <div class="text-lg font-semibold text-white mb-4">Total Entries</div>
                        <div class="text-3xl font-bold text-white">{{ $entriesCount }} / 4</div>
                        @if($gamesStarted==0&&$remainingEntries > 0)
                            <a href="{{ route('entries.create') }}"
                                class="inline-flex items-center justify-center px-4 py-2 bg-nfl-secondary text-white font-medium rounded-lg hover:bg-nfl-secondary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-nfl-secondary/50 transition-colors mt-2">
                                Add Entry (+{{ $remainingEntries }} remaining)
                            </a>
                        @endif
                    </div>
                    <div class="bg-nfl-secondary border border-nfl-secondary/20 p-4 rounded-lg">
                        <div class="text-lg font-semibold text-white mb-4">Best Performing Entry</div>
                        <div class="text-3xl font-bold text-white">
                            @if($entries->isNotEmpty())
                                {{ $entries->max('total_points') ?? 0 }} pts
                            @else
                                0 pts
                            @endif
                        </div>
                    </div>
                    <div class="bg-nfl-accent border border-nfl-accent/20 p-4 rounded-lg">
                        <div class="text-lg font-semibold text-white mb-4">Changes Remaining</div>
                        <div class="text-3xl font-bold text-white">
                            This does not make sense here.
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Rest of the content... -->
            <!-- Entries Section -->
            <x-card title="Entries" class="mt-6">
                @if($entries->isEmpty())
                    <div class="text-center py-8">
                        <p class="text-gray-600 mb-4">You haven't created any entries yet.</p>
                        @if($gamesStarted==0)
                        <a href="{{ route('entries.create') }}"
                        class="inline-flex items-center justify-center px-4 py-2 bg-nfl-primary text-white font-medium rounded-lg hover:bg-nfl-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-nfl-primary/50 transition-colors mt-2">
                                Create Your First Entry
                        </a>
                        @endif
                    </div>
                @else
                    <div class="grid md:grid-cols-2 gap-6">
                        @foreach($entries as $entry)
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-xl font-semibold">{{ $entry->entry_name }}</h3>
                                    <span class="text-lg font-bold">{{ $entry->total_points ?? 0 }} pts</span>
                                </div>

                                <div class="space-y-2">
                                    @foreach($entry->rosters as $roster)
                                        <div class="flex justify-between items-center">
                                            <div class="flex items-center">
                                                <span class="w-12 text-gray-600">{{ $roster->roster_position }}</span>
                                                <span>{{ $roster->player->name }}</span>
                                            </div>
                                            <span class="text-sm text-gray-600">{{ $roster->player->team->abbreviation }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-4 flex justify-between items-center">
                                    <span class="text-sm text-gray-600">
                                        Changes remaining: {{ $entry->getChangesRemaining() }}
                                    </span>
                                    <div class="inline-flex -space-x-px overflow-hidden rounded-md border bg-nfl-primary shadow-sm">
                                        <a href="{{ route('entries.roster', $entry) }}" class="inline-block px-4 py-2 text-sm font-medium text-white hover:bg-red-500 focus:relative"> Manage Roster</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>

            <!-- Recent Activity -->
            <x-card title="Recent Activity" class="mt-6">
                <div class="space-y-4">
                    @forelse($entries->flatMap->transactions->take(5) as $transaction)
                        <div class="flex justify-between items-center">
                            <div>
                                Dropped {{ $transaction->droppedPlayer->name }} for
                                {{ $transaction->addedPlayer->name }}
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ $transaction->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-600">No recent activity</p>
                    @endforelse
                </div>
            </x-card>

            <!-- Weekly Performance -->
            <x-card title="Weekly Performance" class="mt-6">
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach($entries as $entry)
                        <div class="bg-white p-4 rounded-lg shadow">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-semibold">{{ $entry->entry_name }}</h4>
                                <span class="text-lg font-bold">{{ number_format($entry->total_points, 1) }} pts</span>
                            </div>
                            <div class="space-y-2">
{{--                                @foreach($entry->weekly_points as $week => $points)--}}
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Wild Card</span>
                                        <span class="font-medium">{{ number_format($entry->getPointsByRound('Wild Card'), 1) }} pts</span>
                                    </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Divisional</span>
                                    <span class="font-medium">{{ number_format($entry->getPointsByRound('Divisional'), 1) }} pts</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Conference</span>
                                    <span class="font-medium">{{ number_format($entry->getPointsByRound('Conference'), 1) }} pts</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Super Bowl</span>
                                    <span class="font-medium">{{ number_format($entry->getPointsByRound('Super Bowl'), 1) }} pts</span>
                                </div>
{{--                                @endforeach--}}
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
