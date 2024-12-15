<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <title>NFL Playoff Pool</title>
   <style>[x-cloak] { display: none !important; }</style>
   @vite(['resources/css/app.css', 'resources/js/app.js'])
   @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-100">
   <div class="min-h-screen">
       @include('layouts.navigation')

       @if(isset($header))
           <header class="bg-white shadow">
               <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                   {{ $header }}
               </div>
           </header>
       @endif

        @if (session('status'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>

       <footer class="bg-nfl-secondary">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="text-center text-white">
                    <p>&copy; {{ date('Y') }} NFL Fantasy Playoffs. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

   @livewireScripts
</body>
</html>
