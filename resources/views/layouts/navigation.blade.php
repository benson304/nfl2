<nav class="bg-nfl-primary" x-data="{ open: false }">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center">
                <div class="shrink-0 text-white font-bold text-2xl">
                    NFL Playoff Fantasy
                </div>
                <div class="hidden sm:ml-6 md:block">
                    <div class="flex space-x-4">
                        <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
                        <a href="{{ route('dashboard') }}" class="rounded-md px-3 py-2 font-medium {{ Request::routeIs('dashboard') ? 'bg-blue-200 text-gray-700' : 'text-gray-300 hover:bg-gray-200 hover:text-gray-700' }}">Dashboard</a>
                        <a href="{{ route('entries.index') }}" class="rounded-md px-3 py-2 font-medium {{ Request::routeIs('entries.*') ? 'bg-blue-200 text-gray-700' : 'text-gray-300 hover:bg-gray-200 hover:text-gray-700' }}">My Entries</a>
                        <a href="{{ route('standings.index') }}" class="rounded-md px-3 py-2 font-medium {{ Request::routeIs('standings.*') ? 'bg-blue-200 text-gray-700' : 'text-gray-300 hover:bg-gray-200 hover:text-gray-700' }}">Standings</a>
                        <a href="{{ route('transactions.index') }}" class="rounded-md px-3 py-2 font-medium {{ Request::routeIs('transactions.*') ? 'bg-blue-200 text-gray-700' : 'text-gray-300 hover:bg-gray-200 hover:text-gray-700' }}">Transactions</a>
                    </div>
                </div>
            </div>
            <div class="hidden sm:ml-6 md:block">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center text font-medium text-white hover:text-gray-200 focus:outline-none transition duration-150 ease-in-out">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <x-dropdown-link :href="route('logout')"
                         onclick="event.preventDefault();
                                this.closest('form').submit();">
            {{ __('Log Out') }}
        </x-dropdown-link>
        @if (Auth::user()->is_admin == 1)
        <x-dropdown-link :href="route('admin.dashboard')">
            {{ __('Admin Dashboard') }}
        </x-dropdown-link>
        @endif
    </form>
</x-slot>
    </x-dropdown>
@else
    <a href="{{ route('login') }}" class="text-white hover:text-gray-200 px-3 py-2">Log in</a>
    <a href="{{ route('register') }}" class="ml-4 text-white hover:text-gray-200 px-3 py-2">Register</a>
@endauth


            </div>
            <div class="-mr-2 flex md:hidden">
                <!-- Mobile menu button -->
                <button type="button" class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-blue-200 hover:text-gray-700  focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="open = !open" aria-controls="mobile-menu" aria-expanded="false">
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
            <a href="{{ route('dashboard') }}" class="block rounded-md px-3 py-2 text-base font-medium {{ Request::routeIs('dashboard') ? 'bg-blue-200 text-gray-700' : 'text-gray-300 hover:bg-gray-200 hover:text-gray-700' }}">Dashboard</a>
            <a href="{{ route('entries.index') }}" class="block rounded-md px-3 py-2 text-base font-medium {{ Request::routeIs('entries.*') ? 'bg-blue-200 text-gray-700' : 'text-gray-300 hover:bg-gray-200 hover:text-gray-700' }}">My Entries</a>
            <a href="{{ route('standings.index') }}" class="block rounded-md px-3 py-2 text-base font-medium {{ Request::routeIs('standings.*') ? 'bg-blue-200 text-gray-700' : 'text-gray-300 hover:bg-gray-200 hover:text-gray-700' }}">Standings</a>
            <a href="{{ route('transactions.index') }}" class="block rounded-md px-3 py-2 text-base font-medium {{ Request::routeIs('transactions.*') ? 'bg-blue-200 text-gray-700' : 'text-gray-300 hover:bg-gray-200 hover:text-gray-700' }}">Transactions</a>
            @if (Auth::user()->is_admin == 1)
            <a href="{{route('admin.dashboard')}}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">{{ __('Admin Dashboard') }}</a>
            @endif

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
                    <a href="{{route('logout')}}" onclick="event.preventDefault(); this.closest('form').submit();" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">{{ __('Sign Out') }}</a>
                </form>
            </div>
        </div>
    </div>
</nav>


<nav class="bg-nfl-primary hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}">
                                <span class="text-white font-bold text-xl">NFL Playoff Fantasy</span>
                            </a>
                        </div>

                        <div class="flex space-x-4">
                            @auth
                                <a href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="nav-link text-white px-3 py-2 mt-4 rounded-md text-sm font-medium">
                                    {{ __('Dashboard') }}
</a >
                                <a href="{{ route('entries.index') }}" :active="request()->routeIs('entries.*')" class="nav-link text-white px-3 py-2 mt-4 rounded-md text-sm font-medium">
                                    {{ __('My Entries') }}
                                </a>
                                <a href="{{ route('standings.index') }}" :active="request()->routeIs('standings.*')" class="nav-link text-white px-3 py-2 mt-4 rounded-md text-sm font-medium">
                                    {{ __('Standings') }}
                                </a>
                                <a href="{{ route('transactions.index') }}" :active="request()->routeIs('transactions.*')" class="nav-link text-white px-3 py-2 mt-4 rounded-md text-sm font-medium">
                                    {{ __('Transactions') }}
                                </a>
                                @if (Auth::user()->is_admin == 1)
                                <a href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.*')" class="nav-link text-white px-3 py-2 mt-4 rounded-md text-sm font-medium">
                                    {{ __('Admin Dashboard') }}
                                </a>
                            @endif
                            @endauth
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        @auth
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="flex items-center text-sm font-medium text-white hover:text-gray-200 focus:outline-none transition duration-150 ease-in-out">
                                        <div>{{ Auth::user()->name }}</div>
                                        <div class="ml-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        @else
                            <a href="{{ route('login') }}" class="text-white hover:text-gray-200 px-3 py-2">Log in</a>
                            <a href="{{ route('register') }}" class="ml-4 text-white hover:text-gray-200 px-3 py-2">Register</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
