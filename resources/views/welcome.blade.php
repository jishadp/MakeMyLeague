<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>League Manager - Organize Your Sports Leagues</title>
    <meta name="description" content="Create, manage, and track your sports leagues with ease. Built with Laravel for modern league management.">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4f46e5',
                        secondary: '#6b7280'
                    }
                }
            }
        }
    </script>
    
    <!-- Theme CSS Files -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}?v={{ time() }}">
    <link rel="stylesheet" id="theme-css" href="{{ asset('css/glacier-blue-theme.css') }}?v={{ time() }}">
</head>
<body class="bg-white text-gray-900 antialiased glacier-blue-theme" id="app-body">
    <!-- Theme Switcher (Top Right Corner) -->
    <div class="fixed top-4 right-4 z-50">
        <button id="theme-switcher" type="button" 
                class="flex items-center justify-center p-3 bg-white/10 hover:bg-white/20 backdrop-blur-lg border border-white/20 text-white rounded-full transition-all duration-300 active:scale-95 shadow-lg" 
                aria-label="Switch theme"
                title="Switch theme (Ctrl+T)">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v6a2 2 0 002 2h4a2 2 0 002-2V5zM21 15a2 2 0 00-2-2h-4a2 2 0 00-2 2v2a2 2 0 002 2h4a2 2 0 002-2v-2z"/>
            </svg>
        </button>
    </div>

    @include('web.header')

    @include('web.hero')

    @include('web.features')
    
    @include('web.grounds')

    @include('web.teams')

    @include('web.players')

    @include('web.testimonials')

    @include('web.footer')

    <!-- JavaScript -->
    <script src="{{ asset('js/main.js') }}?v={{ time() }}" defer></script>
</body>
</html>
