<header class="bg-white shadow-sm sticky top-0 z-50">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <!-- Logo / Brand -->
            <div class="flex items-center">
                <a href="/" class="flex items-center space-x-2">
                    <span class="text-2xl font-bold text-gray-900 tracking-tight">League Manager</span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('teams.index') }}" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors">Teams</a>
                <a href="{{ route('players.index') }}" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors">Players</a>
                <a href="{{ route('grounds.index') }}" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors">Grounds</a>
                <a href="#features" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors">Features</a>
            </div>

            <!-- Actions -->
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors">Dashboard</a>
                    <a href="{{ route('logout') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                        Sign Out
                    </a>
                @else
                    <a href="{{ route('login')}}" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors">Sign In</a>
                    <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                        Get Started
                    </a>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button id="mobile-menu-toggle" type="button" class="text-gray-600 hover:text-indigo-600 focus:outline-none" aria-label="Toggle menu">
                    <svg id="icon-open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 block" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg id="icon-close" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden mt-2 space-y-2 bg-white rounded-lg shadow-lg p-4">
            <a href="{{ route('teams.index') }}" class="block text-gray-700 hover:text-indigo-600 font-medium">Teams</a>
            <a href="{{ route('players.index') }}" class="block text-gray-700 hover:text-indigo-600 font-medium">Players</a>
            <a href="{{ route('grounds.index') }}" class="block text-gray-700 hover:text-indigo-600 font-medium">Grounds</a>
            <a href="#features" class="block text-gray-700 hover:text-indigo-600 font-medium">Features</a>
            <hr class="my-2">
            @auth
                <a href="{{ route('dashboard') }}" class="block text-gray-700 hover:text-indigo-600 font-medium">Dashboard</a>
                <a href="{{ route('logout') }}" class="block bg-indigo-600 text-white text-center px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                    Sign Out
                </a>
            @else
                <a href="{{ route('login') }}" class="block text-gray-700 hover:text-indigo-600 font-medium">Sign In</a>
                <a href="{{ route('login') }}" class="block bg-indigo-600 text-white text-center px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                    Get Started
                </a>
            @endauth
        </div>
    </nav>
</header>

<script>
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const iconOpen = document.getElementById('icon-open');
    const iconClose = document.getElementById('icon-close');

    menuToggle.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
        iconOpen.classList.toggle('hidden');
        iconClose.classList.toggle('hidden');
    });
</script>
