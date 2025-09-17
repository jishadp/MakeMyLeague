<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="{{ config('app.name') }} - Register today to manage leagues, teams, and players seamlessly">
    <meta name="keywords" content="{{ config('app.name') }}, sports management, team management, player registration">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name') . '- Register Today')</title>

    <!-- Preload critical CSS -->
    <link rel="preload" href="{{ asset('css/main.css') }}?v={{ time() }}" as="style">
    <link rel="preload" href="{{ asset('js/main.js') }}?v={{ time() }}" as="script">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4f46e5', // Indigo-600
                        secondary: '#6b7280' // Gray-500
                    }
                }
            }
        }
    </script>

    <!-- External CSS -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}?v={{ time() }}">

    <!-- Blue Theme CSS -->
    <link rel="stylesheet" href="{{ asset('css/glacier-blue-theme.css') }}?v={{ time() }}">

    <!-- Include CDN Links for Select2 and Bootstrap Datepicker -->
    @include('partials.cdn-links')

    @yield('styles')
</head>

<body class="bg-gray-50 antialiased min-h-screen flex flex-col glacier-blue-theme" id="app-body">
    @include('partials.nav')

    <main class="flex-grow pb-28" id="top">

        @yield('content')

        @auth
        @if(request()->route()->getName() !="auction.index")
            @include('partials.bottom-navigation-buttons')
            @endif
        @endauth
    </main>

    @include('partials.footer')

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="loading-overlay">
        <div class="loader"></div>
    </div>

    <!-- External JavaScript -->
    <script src="{{ asset('js/main.js') }}?v={{ time() }}" defer></script>
    <script src="{{ asset('js/scripts.js') }}?v={{ time() }}" defer></script>

    @yield('scripts')
</body>

</html>
