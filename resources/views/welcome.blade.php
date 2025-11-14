<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{config('app.name')}} - Professional Cricket League Management Platform</title>
    <meta name="description" content="Create tournaments, host live auctions, manage players & teams — all in one place. Professional cricket league management made simple with {{config('app.name')}}.">
    <meta name="keywords" content="cricket league management, live auctions, player statistics, team management, tournament organization, IPL style auctions">
    <meta name="author" content="{{config('app.name')}}">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{config('app.name')}} - Professional Cricket League Management">
    <meta property="og:description" content="Create tournaments, host live auctions, manage players & teams — all in one place. Professional cricket league management made simple.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ config('app.url') }}">
    <meta property="og:image" content="{{ asset('images/hero.jpg') }}">
    <meta property="og:site_name" content="{{config('app.name')}}">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{config('app.name')}} - Professional Cricket League Management">
    <meta name="twitter:description" content="Create tournaments, host live auctions, manage players & teams — all in one place.">
    <meta name="twitter:image" content="{{ asset('images/hero.jpg') }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Preload Critical Resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/jquery.min.js') }}" defer></script>
    
    <!-- Theme CSS Files -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}?v={{ time() }}">
    <link rel="stylesheet" id="theme-css" href="{{ asset('css/glacier-blue-theme.css') }}?v={{ time() }}">
    
    <!-- Performance Optimization -->
    <style>
        /* Critical CSS for above-the-fold content */
        .hero-gradient {
            background: linear-gradient(135deg, #1e1b4b 0%, #581c87 50%, #be185d 100%);
        }
        .glass-morphism {
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Enhanced Mobile optimizations */
        @media (max-width: 768px) {
            .hero-gradient {
                min-height: 100vh;
                padding: 2rem 1rem;
            }
            .text-responsive {
                font-size: clamp(1.5rem, 4vw, 3rem);
                line-height: 1.2;
            }
            .container-responsive {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            /* Enhanced mobile typography */
            h1 { font-size: clamp(2rem, 8vw, 4rem) !important; }
            h2 { font-size: clamp(1.5rem, 6vw, 3rem) !important; }
            h3 { font-size: clamp(1.25rem, 5vw, 2rem) !important; }
            
            /* Better mobile spacing */
            .py-24 { padding-top: 4rem !important; padding-bottom: 4rem !important; }
            .py-20 { padding-top: 3rem !important; padding-bottom: 3rem !important; }
            .py-16 { padding-top: 2.5rem !important; padding-bottom: 2.5rem !important; }
            
            /* Mobile-optimized cards */
            .card-mobile {
                padding: 1.5rem !important;
                margin-bottom: 1.5rem;
            }
            
            /* Better mobile grid layouts */
            .grid-mobile {
                gap: 1rem !important;
            }
            
            /* Enhanced touch targets */
            button, a {
                min-height: 44px;
                min-width: 44px;
            }
            
            /* Better mobile forms */
            input, select, textarea {
                font-size: 16px !important; /* Prevents zoom on iOS */
                padding: 0.875rem !important;
            }
            
            /* Mobile navigation improvements */
            .nav-mobile {
                padding: 1rem !important;
            }
        }
        
        /* Extra small devices */
        @media (max-width: 480px) {
            .container-responsive {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            
            .py-24 { padding-top: 3rem !important; padding-bottom: 3rem !important; }
            .py-20 { padding-top: 2.5rem !important; padding-bottom: 2.5rem !important; }
            
            h1 { font-size: clamp(1.75rem, 7vw, 3.5rem) !important; }
            h2 { font-size: clamp(1.25rem, 5.5vw, 2.5rem) !important; }
            
            /* Larger buttons for small screens */
            .btn-mobile {
                padding: 1rem 2rem !important;
                font-size: 1.125rem !important;
                width: 100% !important;
                margin-bottom: 0.75rem !important;
            }
        }
        
        /* Reduce motion for accessibility */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
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
    <script src="{{ asset('js/landing.js') }}?v={{ time() }}" defer></script>
</body>
</html>
