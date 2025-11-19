<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/main.css') }}?v={{ time() }}">
    <style>
        body {
            background: #f8fafc;
        }
        @media print {
            body {
                background: #fff;
            }
        }
    </style>
    @yield('styles')
</head>
<body class="min-h-screen bg-gray-100">
    <main class="py-6">
        @yield('content')
    </main>
</body>
</html>
