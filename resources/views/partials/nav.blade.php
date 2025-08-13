<header class="bg-white shadow-sm sticky top-0 z-50">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo / Brand -->
            <div class="flex items-center">
                <a href="/" class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-2xl font-bold text-gray-900 tracking-tight">League Manager</span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="/" class="text-gray-700 hover:text-primary font-medium transition-colors">Home</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-primary font-medium transition-colors">Dashboard</a>
                @else
                    <a href="#about" class="text-gray-700 hover:text-primary font-medium transition-colors">About Us</a>
                    <a href="#features" class="text-gray-700 hover:text-primary font-medium transition-colors">Features</a>
                @endauth
            </div>

            <!-- Actions -->
            <div class="hidden md:flex items-center space-x-4">
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

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button id="mobile-menu-toggle" type="button" class="text-gray-600 hover:text-primary focus:outline-none" aria-label="Toggle menu">
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
            <a href="/" class="block text-gray-700 hover:text-primary font-medium">Home</a>
            @auth
                <a href="{{ route('dashboard') }}" class="block text-gray-700 hover:text-primary font-medium">Dashboard</a>
                <hr class="my-2">
                <span class="block text-gray-700 font-medium">{{ Auth::user()->name }}</span>
                <a href="{{ route('logout') }}" class="block text-gray-700 hover:text-primary font-medium">Logout</a>
            @else
                <a href="#about" class="block text-gray-700 hover:text-primary font-medium">About Us</a>
                <a href="#features" class="block text-gray-700 hover:text-primary font-medium">Features</a>
                <hr class="my-2">
                <a href="{{ route('login') }}" class="block text-gray-700 hover:text-primary font-medium">Login</a>
                <a href="#registration" class="block bg-primary text-white text-center px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                    Get Started
                </a>
            @endauth
        </div>
    </nav>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const iconOpen = document.getElementById('icon-open');
        const iconClose = document.getElementById('icon-close');

        menuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
            iconOpen.classList.toggle('hidden');
            iconClose.classList.toggle('hidden');
        });
    });
</script>
