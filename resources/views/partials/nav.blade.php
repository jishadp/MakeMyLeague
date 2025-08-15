<header class="bg-white shadow-sm sticky top-0 z-50">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Desktop Navigation - Full Width -->
        <div class="hidden md:flex justify-between items-center h-16">
            <!-- Logo / Brand -->
            <div class="flex items-center">
                <a href="/" class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-2xl font-bold text-gray-900 tracking-tight">League Manager</span>
                </a>
            </div>

            <!-- Nav Links -->
            <div class="flex items-center space-x-8">
                <a href="/" class="text-gray-700 hover:text-primary font-medium transition-colors">Home</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-primary font-medium transition-colors">Dashboard</a>
                    <a href="{{ route('teams.index') }}" class="text-gray-700 hover:text-primary font-medium transition-colors">Teams</a>
                    <a href="{{ route('players.index') }}" class="text-gray-700 hover:text-primary font-medium transition-colors">Players</a>
                    <a href="{{ route('auctions.index') }}" class="text-gray-700 hover:text-primary font-medium transition-colors">Auctions</a>
                @else
                    <a href="#about" class="text-gray-700 hover:text-primary font-medium transition-colors">About Us</a>
                    <a href="#features" class="text-gray-700 hover:text-primary font-medium transition-colors">Features</a>
                @endauth
            </div>

            <!-- League Selector -->
            @auth
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <select id="league-selector" class="block w-48 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md bg-gray-50">
                        <option value="">Select League</option>
                        @foreach($navLeagues as $league)
                            <option value="{{ $league->id }}" {{ $defaultLeague && $defaultLeague->id == $league->id ? 'selected' : '' }}>
                                {{ $league->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endauth

            <!-- User Actions -->
            <div class="flex items-center space-x-4">
                @auth
                    <span class="text-gray-700 font-medium">{{ Auth::user()->name }}</span>
                    <a href="{{ route('logout') }}" class="text-gray-700 hover:text-primary font-medium transition-colors">Logout</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-primary font-medium transition-colors">Login</a>
                    <a href="#registration" class="bg-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                        Get Started
                    </a>
                @endauth
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div class="flex md:hidden justify-between items-center h-16">
            <!-- Logo / Brand -->
            <div class="flex items-center">
                <a href="/" class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-2xl font-bold text-gray-900 tracking-tight">League Manager</span>
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <div class="flex items-center">
                <button id="mobile-menu-toggle" type="button" class="text-gray-600 hover:text-primary focus:outline-none" aria-label="Toggle menu">
                    <div class="hamburger-menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>
            </div>
        </div>
    </nav>
</header>

<!-- Mobile Sidebar Overlay -->
<div id="mobile-sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 md:hidden hidden transition-opacity duration-300 opacity-0"></div>

<!-- Mobile Sidebar -->
<div id="mobile-sidebar" class="fixed top-0 left-0 h-full w-64 bg-white shadow-xl z-50 md:hidden transform -translate-x-full transition-transform duration-300 overflow-y-auto max-h-screen pb-24">
    <div class="flex flex-col h-full">
        <!-- Sidebar Header -->
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-gradient-to-br from-primary to-indigo-700 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                    </svg>
                </div>
                <span class="text-lg font-bold">Cricket League</span>
            </div>
            <button id="close-sidebar" class="p-1 rounded-full hover:bg-gray-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- League Selector (Mobile) -->
        @auth
        <div class="px-4 pt-4">
            <label for="mobile-league-selector" class="block text-sm font-medium text-gray-700 mb-1">Select League</label>
            <select id="mobile-league-selector" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md bg-gray-50">
                <option value="">Select League</option>
                @foreach($navLeagues as $league)
                    <option value="{{ $league->id }}" {{ $defaultLeague && $defaultLeague->id == $league->id ? 'selected' : '' }}>
                        {{ $league->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @endauth

        <!-- Sidebar Content -->
        <div class="p-4 space-y-1 flex-grow">
            <!-- Dashboard Link -->
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors group">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                </div>
                <span class="font-medium">Dashboard</span>
            </a>

            <!-- Teams Link -->
            <a href="{{ route('teams.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors group">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                    </svg>
                </div>
                <span class="font-medium">Teams</span>
            </a>

            <!-- Players Link -->
            <a href="{{ route('players.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors group">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                </div>
                <span class="font-medium">Players</span>
            </a>

            <!-- Auctions Link -->
            <a href="{{ route('auctions.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors group">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                    </svg>
                </div>
                <span class="font-medium">Auctions</span>
            </a>
        </div>
        
        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-gray-200 pb-24 md:pb-4">
            @auth
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="font-medium text-sm text-gray-700">{{ Auth::user()->name }}</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center space-x-2 bg-red-50 hover:bg-red-100 text-red-600 p-3 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V7.414l-5-5H3zm7 5a1 1 0 10-2 0v6a1 1 0 102 0V8zm5 4a1 1 0 10-2 0v2a1 1 0 102 0v-2z" clip-rule="evenodd" />
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            @else
                <div class="space-y-3">
                    <a href="{{ route('login') }}" class="block w-full text-center bg-white border border-primary text-primary hover:bg-primary/5 p-3 rounded-lg font-medium transition-colors">Login</a>
                    <a href="{{ route('register') }}" class="block w-full text-center bg-primary text-white hover:bg-primary/90 p-3 rounded-lg font-medium transition-colors">Register</a>
                </div>
            @endauth
        </div>
    </div>
</div>

<style>
    /* Hamburger Menu Animation */
    .hamburger-menu {
        width: 30px;
        height: 20px;
        position: relative;
        cursor: pointer;
    }
    
    .hamburger-menu span {
        display: block;
        position: absolute;
        height: 3px;
        width: 100%;
        background: #4f46e5;
        border-radius: 3px;
        opacity: 1;
        left: 0;
        transform: rotate(0deg);
        transition: .25s ease-in-out;
    }
    
    .hamburger-menu span:nth-child(1) {
        top: 0px;
    }
    
    .hamburger-menu span:nth-child(2) {
        top: 8px;
    }
    
    .hamburger-menu span:nth-child(3) {
        top: 16px;
    }
    
    .open .hamburger-menu span:nth-child(1) {
        top: 8px;
        transform: rotate(135deg);
    }
    
    .open .hamburger-menu span:nth-child(2) {
        opacity: 0;
        left: -60px;
    }
    
    .open .hamburger-menu span:nth-child(3) {
        top: 8px;
        transform: rotate(-135deg);
    }
    
    /* Mobile sidebar animation */
    #mobile-sidebar {
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }
    
    /* Ensure the logout button is always visible on mobile */
    @media (max-width: 768px) {
        #mobile-sidebar {
            padding-bottom: 100px; /* Extra space to ensure logout is visible */
        }
        
        #mobile-sidebar form button {
            position: relative;
            z-index: 60; /* Higher than sidebar but lower than bottom nav */
            margin-bottom: 28px; /* Extra margin to push above bottom nav */
        }
    }
    
    /* Responsive adjustments for desktop navigation */
    @media (min-width: 768px) {
        .nav-links-container {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.getElementById('mobile-menu-toggle');
        const sidebar = document.getElementById('mobile-sidebar');
        const overlay = document.getElementById('mobile-sidebar-overlay');
        const closeButton = document.getElementById('close-sidebar');
        
        function openSidebar() {
            menuToggle.classList.add('open');
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
            }, 10);
            document.body.classList.add('overflow-hidden');
        }
        
        function closeSidebar() {
            menuToggle.classList.remove('open');
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('opacity-0');
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300);
            document.body.classList.remove('overflow-hidden');
        }
        
        menuToggle.addEventListener('click', function() {
            if(sidebar.classList.contains('-translate-x-full')) {
                openSidebar();
            } else {
                closeSidebar();
            }
        });
        
        closeButton.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);
        
        // Close sidebar when escape key is pressed
        document.addEventListener('keydown', function(e) {
            if(e.key === 'Escape') {
                closeSidebar();
            }
        });
        
        // Handle league selection
        const desktopLeagueSelector = document.getElementById('league-selector');
        const mobileLeagueSelector = document.getElementById('mobile-league-selector');
        
        function handleLeagueChange(selectElement) {
            if (!selectElement) return;
            
            selectElement.addEventListener('change', function() {
                const selectedLeagueId = this.value;
                if (selectedLeagueId) {
                    window.location.href = `/leagues/${selectedLeagueId}`;
                }
            });
        }
        
        handleLeagueChange(desktopLeagueSelector);
        handleLeagueChange(mobileLeagueSelector);
    });
</script>
