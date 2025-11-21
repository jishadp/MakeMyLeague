<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
          content="{{ config('app.name') }} - Live auction broadcast view">
    <meta name="keywords" content="{{ config('app.name') }}, auction, live broadcast">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Live Broadcast')</title>

    <link rel="preload" href="{{ asset('css/main.css') }}?v={{ time() }}" as="style">
    <link rel="preload" href="{{ asset('js/main.js') }}?v={{ time() }}" as="script">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>

    <link rel="stylesheet" href="{{ asset('css/main.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/glacier-blue-theme.css') }}?v={{ time() }}">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
          crossorigin="anonymous"
          referrerpolicy="no-referrer" />

    @include('partials.cdn-links')
    @livewireStyles
    @yield('styles')
</head>

<body class="bg-slate-950 text-white antialiased min-h-screen flex flex-col" id="broadcast-body">
    <main class="flex-grow min-h-screen" id="broadcast-shell">
        @yield('content')
    </main>

    <div id="loading-overlay" class="loading-overlay">
        <div class="loader"></div>
    </div>

    <script src="{{ asset('js/main.js') }}?v={{ time() }}" defer></script>
    <script src="{{ asset('js/scripts.js') }}?v={{ time() }}" defer></script>
    @livewireScripts
    @yield('scripts')
</body>

</html>
