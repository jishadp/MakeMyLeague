<header class="bg-white shadow-lg sticky top-0 z-50 border-b border-gray-100">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            
            <!-- Logo / Brand -->
            <div class="flex items-center">
                <a href="/" class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Cricket League</span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('teams.index') }}" class="text-gray-700 hover:text-indigo-600 font-medium transition-all duration-200 hover:scale-105">Teams</a>
                <a href="{{ route('players.index') }}" class="text-gray-700 hover:text-indigo-600 font-medium transition-all duration-200 hover:scale-105">Players</a>
                <a href="{{ route('auctions.index') }}" class="text-gray-700 hover:text-indigo-600 font-medium transition-all duration-200 hover:scale-105 relative group">
                    Auctions
                    <span class="absolute -top-1 -right-2 px-1.5 py-0.5 bg-gradient-to-r from-pink-500 to-purple-500 text-white text-xs rounded-full group-hover:scale-110 transition-transform">Live</span>
                </a>
                @auth
                    <a href="{{ route('leagues.index') }}" class="text-gray-700 hover:text-indigo-600 font-medium transition-all duration-200 hover:scale-105">My Leagues</a>
                @endauth
                <a href="/#features" class="text-gray-700 hover:text-indigo-600 font-medium transition-all duration-200 hover:scale-105">Features</a>
            </div>

            <!-- Actions -->
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    <div class="flex items-center space-x-3">
                        @php
                            $userRoles = Auth::user()->roles;
                            $primaryRole = $userRoles->first();
                        @endphp
                        @if($primaryRole)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 border border-indigo-200">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    @switch($primaryRole->name)
                                        @case('organizer')
                                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                            @break
                                        @case('owner')
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                            @break
                                        @case('player')
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                            @break
                                        @default
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                    @endswitch
                                </svg>
                                {{ ucfirst($primaryRole->name) }}
                            </span>
                        @endif
                        <span class="text-gray-600">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-indigo-600 font-medium transition-colors">Logout</button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-600 font-medium transition-colors">Login</a>
                    <a href="{{ route('register') }}" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2 rounded-full font-medium hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                        Register
                    </a>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button id="mobile-menu-toggle" type="button" class="text-gray-600 hover:text-indigo-600 focus:outline-none p-2 rounded-lg hover:bg-gray-100 transition-colors" aria-label="Toggle menu">
                    <svg id="icon-open" class="h-6 w-6 block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg id="icon-close" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

    </nav>
</header>

<!-- Mobile Sidebar Overlay -->
<div id="mobile-sidebar-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"></div>

<!-- Mobile Sidebar -->
<div id="mobile-sidebar" class="fixed top-0 left-0 h-full w-80 bg-white shadow-xl transform -translate-x-full transition-transform duration-300 z-50 md:hidden">
    <div class="flex items-center justify-between p-4 border-b">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-lg font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Cricket League</span>
        </div>
        <button id="close-sidebar" class="p-2 rounded-lg hover:bg-gray-100">
            <svg class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    
    <div class="p-4 space-y-2">
        <a href="{{ route('teams.index') }}" class="flex items-center space-x-3 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 font-medium py-3 px-4 rounded-lg transition-all">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Teams</span>
        </a>
        
        <a href="{{ route('players.index') }}" class="flex items-center space-x-3 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 font-medium py-3 px-4 rounded-lg transition-all">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span>Players</span>
        </a>
        
        <a href="{{ route('auctions.index') }}" class="flex items-center justify-between text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 font-medium py-3 px-4 rounded-lg transition-all">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z" />
                </svg>
                <span>Auctions</span>
            </div>
            <span class="px-1.5 py-0.5 bg-gradient-to-r from-pink-500 to-purple-500 text-white text-xs rounded-full">Live</span>
        </a>
        
        @auth
            <a href="{{ route('leagues.index') }}" class="flex items-center space-x-3 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 font-medium py-3 px-4 rounded-lg transition-all">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                </svg>
                <span>My Leagues</span>
            </a>
        @endauth
        
        <hr class="my-4 border-gray-200">
        
        @auth
            <div class="px-4 py-2">
                <p class="text-sm text-gray-500">Signed in as</p>
                <p class="font-medium text-gray-900">{{ Auth::user()->name }}</p>
            </div>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center space-x-3 w-full text-gray-700 hover:text-red-600 hover:bg-red-50 font-medium py-3 px-4 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="flex items-center space-x-3 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 font-medium py-3 px-4 rounded-lg transition-all">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Login</span>
            </a>
            
            <a href="{{ route('register') }}" class="flex items-center justify-center space-x-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium py-3 px-4 rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-md mt-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                </svg>
                <span>Register</span>
            </a>
        @endauth
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.getElementById('mobile-menu-toggle');
        const sidebar = document.getElementById('mobile-sidebar');
        const overlay = document.getElementById('mobile-sidebar-overlay');
        const closeSidebar = document.getElementById('close-sidebar');
        const iconOpen = document.getElementById('icon-open');
        const iconClose = document.getElementById('icon-close');

        function openSidebar() {
            if (sidebar) sidebar.classList.remove('-translate-x-full');
            if (overlay) overlay.classList.remove('hidden');
            if (iconOpen) iconOpen.classList.add('hidden');
            if (iconClose) iconClose.classList.remove('hidden');
        }

        function closeSidebarFn() {
            if (sidebar) sidebar.classList.add('-translate-x-full');
            if (overlay) overlay.classList.add('hidden');
            if (iconOpen) iconOpen.classList.remove('hidden');
            if (iconClose) iconClose.classList.add('hidden');
        }

        if (menuToggle) {
            menuToggle.addEventListener('click', () => {
                if (sidebar && sidebar.classList.contains('-translate-x-full')) {
                    openSidebar();
                } else {
                    closeSidebarFn();
                }
            });
        }

        if (closeSidebar) {
            closeSidebar.addEventListener('click', closeSidebarFn);
        }

        if (overlay) {
            overlay.addEventListener('click', closeSidebarFn);
        }
    });
</script>