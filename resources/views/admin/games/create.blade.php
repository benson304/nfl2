<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-nfl-primary leading-tight">
            {{ __('Add New Game') }}
        </h2>
    </x-slot>


    <div class="max-w-7xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-nfl-primary border-b border-gray-200">
                <form method="POST" action="{{ route('admin.games.store') }}">
                    @csrf

                    <!-- Home Team -->
                    <div class="mb-4">
                        <label for="home_team_id" class="block text-sm font-medium text-white">Home Team</label>
                        <select name="home_team_id" id="home_team_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:white focus:ring-nfl-primary">
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" {{ old('home_team_id') == $team->id ? 'selected' : '' }}>
                                    {{ $team->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('home_team_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Away Team -->
                    <div class="mb-4">
                        <label for="away_team_id" class="block text-sm font-medium text-white">Away Team</label>
                        <select name="away_team_id" id="away_team_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:white focus:ring-nfl-primary">
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" {{ old('away_team_id') == $team->id ? 'selected' : '' }}>
                                    {{ $team->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('away_team_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kickoff Time -->
                    <div class="mb-4">
                        <label for="kickoff" class="block text-sm font-medium text-white">Kickoff Time</label>
                        <input type="datetime-local" name="kickoff" id="kickoff"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:white focus:ring-nfl-primary"
                            value="{{ old('kickoff') }}">
                        @error('kickoff')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Round -->
                    <div class="mb-4">
                        <label for="round" class="block text-sm font-medium text-white">Round</label>
                        <select name="round" id="round" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:white focus:ring-nfl-primary">
                            @foreach(['Wild Card', 'Divisional', 'Conference', 'Super Bowl'] as $round)
                                <option value="{{ $round }}" {{ old('round') == $round ? 'selected' : '' }}>
                                    {{ $round }}
                                </option>
                            @endforeach
                        </select>
                        @error('round')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-white">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:white focus:ring-nfl-primary">
                            @foreach(['scheduled', 'in_progress', 'finished'] as $status)
                                <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Scores -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="home_score" class="block text-sm font-medium text-white">Home Score</label>
                            <input type="number" name="home_score" id="home_score"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:white focus:ring-nfl-primary"
                                value="{{ old('home_score', 0) }}">
                            @error('home_score')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="away_score" class="block text-sm font-medium text-white">Away Score</label>
                            <input type="number" name="away_score" id="away_score"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:white focus:ring-nfl-primary"
                                value="{{ old('away_score', 0) }}">
                            @error('away_score')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                            <button type="submit" class="btn-secondary hover:bg-nfl-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-nfl-primary">
                            Add Game
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
