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
    
    <main class="flex-grow pb-28" id="top">
        
        @yield('content')
        
        @auth
            @include('partials.bottom-navigation-buttons')
        @endauth
    </main>
    
    @include('partials.footer')
    
    @yield('scripts')
    
    <script>
        // Smooth scroll for Back to Top button
        document.addEventListener('DOMContentLoaded', function() {
            const backToTopLinks = document.querySelectorAll('a[href="#top"]');
            
            backToTopLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            });
            
            // Menu Button Functionality for Desktop and Mobile
            const menuButtons = document.querySelectorAll('#mobile-menu-toggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const closeButton = document.getElementById('close-sidebar');
            
            function openSidebar() {
                if (!sidebar || !overlay) return;
                
                // Mark all menu buttons as open
                menuButtons.forEach(btn => {
                    if (btn) btn.classList.add('open');
                });
                
                // Show sidebar
                sidebar.classList.remove('-translate-x-full');
                
                // Show overlay
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    overlay.classList.remove('opacity-0');
                }, 10);
                
                // Prevent body scrolling
                document.body.classList.add('overflow-hidden');
            }
            
            function closeSidebar() {
                if (!sidebar || !overlay) return;
                
                // Mark all menu buttons as closed
                menuButtons.forEach(btn => {
                    if (btn) btn.classList.remove('open');
                });
                
                // Hide sidebar
                sidebar.classList.add('-translate-x-full');
                
                // Hide overlay
                overlay.classList.add('opacity-0');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 300);
                
                // Restore body scrolling
                document.body.classList.remove('overflow-hidden');
            }
            
            // Add click events to all menu buttons
            menuButtons.forEach(btn => {
                if (btn && sidebar) {
                    btn.addEventListener('click', function() {
                        if(sidebar.classList.contains('-translate-x-full')) {
                            openSidebar();
                        } else {
                            closeSidebar();
                        }
                    });
                }
            });
            
            // Close sidebar when escape key is pressed
            document.addEventListener('keydown', function(e) {
                if(e.key === 'Escape') {
                    closeSidebar();
                }
            });
            
            if (closeButton) {
                closeButton.addEventListener('click', closeSidebar);
            }
            
            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }
            
            // League Selector Synchronization
            const desktopLeagueSelector = document.getElementById('header-league-selector');
            const mobileLeagueSelector = document.getElementById('mobile-league-selector');
            
            function syncLeagueSelectors() {
                if (desktopLeagueSelector && mobileLeagueSelector) {
                    // Keep values in sync
                    desktopLeagueSelector.value = mobileLeagueSelector.value;
                    
                    desktopLeagueSelector.addEventListener('change', function() {
                        mobileLeagueSelector.value = this.value;
                        if (this.value) {
                            window.location.href = `/leagues/${this.value}`;
                        }
                    });
                    
                    mobileLeagueSelector.addEventListener('change', function() {
                        desktopLeagueSelector.value = this.value;
                        if (this.value) {
                            window.location.href = `/leagues/${this.value}`;
                        }
                    });
                }
            }
            
            syncLeagueSelectors();
        });
    </script>
</body>
</html>
