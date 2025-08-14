<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="League Manager - Register today to manage leagues, teams, and players seamlessly">
    <meta name="keywords" content="league manager, sports management, team management, player registration">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'League Manager - Register Today')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    
    <!-- Custom CSS -->
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        
        @media (prefers-reduced-motion) {
            .animate-fade-in {
                animation: none;
                opacity: 1;
            }
        }
    </style>

    @yield('styles')
</head>
<body class="bg-gray-50 antialiased min-h-screen flex flex-col">
    @include('partials.nav')
    
    <main class="flex-grow">
        @yield('content')
    </main>
    
    @include('partials.footer')
    
    @yield('scripts')
</body>
</html>
