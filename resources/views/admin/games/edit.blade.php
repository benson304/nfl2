<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Game') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('admin.games.update', $game) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="home_team_id" class="block text-sm font-medium text-gray-700">Home Team</label>
                        <select name="home_team_id" id="home_team_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" {{ $game->home_team_id == $team->id ? 'selected' : '' }}>
                                    {{ $team->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('home_team_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="away_team_id" class="block text-sm font-medium text-gray-700">Away Team</label>
                        <select name="away_team_id" id="away_team_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" {{ $game->away_team_id == $team->id ? 'selected' : '' }}>
                                    {{ $team->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('away_team_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="kickoff" class="block text-sm font-medium text-gray-700">Kickoff Time</label>
                        <input type="datetime-local" name="kickoff" id="kickoff"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="{{ $game->kickoff->format('Y-m-d\TH:i') }}">
                        @error('kickoff')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="round" class="block text-sm font-medium text-gray-700">Round</label>
                        <select name="round" id="round" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach(['Wild Card', 'Divisional', 'Conference', 'Super Bowl'] as $round)
                                <option value="{{ $round }}" {{ $game->round == $round ? 'selected' : '' }}>
                                    {{ $round }}
                                </option>
                            @endforeach
                        </select>
                        @error('round')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach(['scheduled', 'in_progress', 'finished'] as $status)
                                <option value="{{ $status }}" {{ $game->status == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="home_score" class="block text-sm font-medium text-gray-700">Home Score</label>
                            <input type="number" name="home_score" id="home_score"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ $game->home_score }}">
                            @error('home_score')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="away_score" class="block text-sm font-medium text-gray-700">Away Score</label>
                            <input type="number" name="away_score" id="away_score"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                value="{{ $game->away_score }}">
                            @error('away_score')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Update Game
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
