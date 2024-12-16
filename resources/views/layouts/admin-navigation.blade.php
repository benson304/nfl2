<nav class="bg-nfl-secondary" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center">
                <div class="shrink-0 text-white font-bold text-2xl">
                    <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:ml-6 md:block">
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.games.index') }}" class="rounded-md px-3 py-2 font-medium {{ request()->routeIs('admin.games.*') ? 'bg-red-200 text-gray-700' : 'text-gray-100 hover:bg-gray-200 hover:text-gray-700' }}">
                            Games
                        </a>
                        <a href="{{ route('admin.player-stats.index') }}" class="rounded-md px-3 py-2 font-medium {{ request()->routeIs('admin.player-stats.*') ? 'bg-red-200 text-gray-700' : 'text-gray-100 hover:bg-gray-200 hover:text-gray-700' }}">
                            Player Stats
                        </a>
                        <a href="{{ route('admin.players.index') }}" class="rounded-md px-3 py-2 font-medium {{ request()->routeIs('admin.players.*') ? 'bg-red-200 text-gray-700' : 'text-gray-100 hover:bg-gray-200 hover:text-gray-700' }}">
                            Players
                        </a>
                        <a href="{{ route('admin.teams.index') }}" class="rounded-md px-3 py-2 font-medium {{ request()->routeIs('admin.teams.*') ? 'bg-red-200 text-gray-700' : 'text-gray-100 hover:bg-gray-200 hover:text-gray-700' }}">
                            Teams
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
                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link href="{{ route('logout') }}"
                                             onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Logout') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('dashboard')">
                                {{ __('User Dashboard') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            <div class="-mr-2 flex md:hidden">
                <!-- Mobile menu button -->
                <button type="button" class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-100 hover:bg-red-200 hover:text-gray-600  focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="open = !open" aria-controls="mobile-menu" aria-expanded="false">
                    <span class="absolute -inset-0.5"></span>
                    <span class="sr-only">Open main menu</span>
                    <!--
                      Icon when menu is closed.

                      Menu open: "hidden", Menu closed: "block"
                    -->
                    <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <!--
                      Icon when menu is open.

                      Menu open: "block", Menu closed: "hidden"
                    -->
                    <svg class="hidden size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile menu, show/hide based on menu state. -->
    <div class="md:hidden" id="mobile-menu" x-show="open">
        <div class="space-y-1 px-2 pb-3 pt-2">
            <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
            <a href="{{ route('admin.games.index') }}" class="block rounded-md px-3 py-2 text-base font-medium {{ Request::routeIs('dashboard') ? 'bg-red-200 text-gray-700' : 'text-gray-100 hover:bg-gray-200 hover:text-gray-700' }}">Games</a>
            <a href="{{ route('admin.player-stats.index') }}" class="block rounded-md px-3 py-2 text-base font-medium {{ Request::routeIs('entries.*') ? 'bg-red-200 text-gray-700' : 'text-gray-100 hover:bg-gray-200 hover:text-gray-700' }}">Player Stats</a>
            <a href="{{ route('admin.players.index') }}" class="block rounded-md px-3 py-2 text-base font-medium {{ Request::routeIs('standings.*') ? 'bg-red-200 text-gray-700' : 'text-gray-100 hover:bg-gray-200 hover:text-gray-700' }}">Players</a>
            <a href="{{ route('admin.teams.index') }}" class="block rounded-md px-3 py-2 text-base font-medium {{ Request::routeIs('transactions.*') ? 'bg-red-200 text-gray-700' : 'text-gray-100 hover:bg-gray-200 hover:text-gray-700' }}">Teams</a>
        </div>
        <div class="border-t border-gray-700 pb-3 pt-4">
            <div class="flex items-center px-5">
                <div class="">
                    <div class="text-base font-medium text-white">{{ Auth::user()->name }}</div>
                    <div class="text-sm font-medium text-gray-400">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1 px-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{route('logout')}}" onclick="event.preventDefault(); this.closest('form').submit();" class="block rounded-md px-3 py-2 text-base font-medium text-gray-100 hover:bg-gray-200 hover:text-gray-700">{{ __('Sign Out') }}</a>
                </form>
                <a href="{{route('dashboard')}}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-100 hover:bg-gray-200 hover:text-gray-700">{{ __('User Dashboard') }}</a>

            </div>
        </div>
    </div>
</nav>
