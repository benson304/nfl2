<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-nfl-primary">
            {{ __('My Entries') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-nfl-background shadow-sm sm:rounded-lg">
                <div class="p-6 bg-nfl-background border-b border-gray-200">
                    @if(auth()->user()->entries()->count() < 4)
                        <a href="{{ route('entries.create') }}" class="btn-primary">
                            Create New Entry
                        </a>
                    @endif

                    <div class="mt-6">
                        @foreach($entries as $entry)
                            <div class="p-4 mb-4 border rounded">
                                <h3 class="text-lg font-semibold">{{ $entry->entry_name }}</h3>
                                <p>Changes remaining: {{ $entry->changes_remaining }}</p>
                                <div class="mt-2">
                                  <a href="{{ route('entries.roster', $entry) }}" class="text-blue-600">
                                      View Details
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
