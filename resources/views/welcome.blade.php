<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{config('app.name')}} - Organize Your Sports Leagues</title>
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


    @include('web.header')

    @include('web.hero')

    @include('web.features')
    
    @include('web.grounds')

    @include('web.teams')

    {{-- @include('web.players') --}}

    @include('web.testimonials')

    @include('web.footer')

    <!-- JavaScript -->
    <script src="{{ asset('js/main.js') }}?v={{ time() }}" defer></script>
</body>
</html>
