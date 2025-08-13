<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>League Manager - Organize Your Sports Leagues</title>
    <meta name="description" content="Create, manage, and track your sports leagues with ease. Built with Laravel for modern league management.">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
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
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body class="bg-white text-gray-900 antialiased">
    @include('web.header')

    @include('web.hero')

    @include('web.features')
    
    @include('web.grounds')

    @include('web.testimonials')

    @include('web.footer')
</body>
</html>
