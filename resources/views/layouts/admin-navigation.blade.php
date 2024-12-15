<nav class="bg-nfl-secondary">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
           <div class="flex items-center">
                <div class="shrink-0 text-white font-bold text-2xl">
                    Admin Dashboard
                </div>

            <!-- Navigation Links -->
            <div class="hidden sm:ml-6 md:block">
                <div class="flex space-x-4">
                    <a href="{{ route('admin.games.index') }}" class="rounded-md px-3 py-2 font-medium {{ request()->routeIs('admin.games.*') ? 'bg-blue-200 text-gray-700' : 'text-gray-300 hover:bg-gray-200 hover:text-gray-700' }}">
                    Games
                    </a>
                    <a href="{{ route('admin.player-stats.index') }}" class="rounded-md px-3 py-2 font-medium {{ request()->routeIs('admin.player-stats.*') ? 'bg-blue-200 text-gray-700' : 'text-gray-300 hover:bg-gray-200 hover:text-gray-700' }}">
                    Player Stats
                    </a>
                    <a href="{{ route('admin.players.index') }}" class="rounded-md px-3 py-2 font-medium {{ request()->routeIs('admin.players.*') ? 'bg-blue-200 text-gray-700' : 'text-gray-300 hover:bg-gray-200 hover:text-gray-700' }}">
                    Players
                    </a>
                    <a href="{{ route('admin.teams.index') }}" class="rounded-md px-3 py-2 font-medium {{ request()->routeIs('admin.teams.*') ? 'bg-blue-200 text-gray-700' : 'text-gray-300 hover:bg-gray-200 hover:text-gray-700' }}">
                    Teams
                    </a>
                    <a href="{{ route('dashboard') }}" class="rounded-md px-3 py-2 font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-200 text-gray-700' : 'text-gray-300 hover:bg-gray-200 hover:text-gray-700' }}">
                    Main Site
                    </a>
                </div>
            </div>
        </div>
        <!-- User Dropdown -->
        <div class="hidden sm:ml-6 md:block">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center text font-medium text-white hover:text-gray-200 focus:outline-none transition duration-150 ease-in-out">
                        <div>{{ Auth::user()->name }}</div>
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.25 8.28a.75.75 0 01-.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Account Management 
                        <div class="block px-4 py-2 text-sm text-nfl-primary">
                            {{ __('Manage Account') }}
                        </div>
                        -->
                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Logout') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>