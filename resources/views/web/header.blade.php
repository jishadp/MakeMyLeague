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
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors">Dashboard</a>
                        <a href="{{ route('logout') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                            Sign Out
                        </a>
                    </div>
                @else
                    <a href="{{ route('login')}}" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors">Sign In</a>
                    <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                        Register
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
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600 text-sm">Role:</span>
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
                </div>
                <a href="{{ route('dashboard') }}" class="block text-gray-700 hover:text-indigo-600 font-medium">Dashboard</a>
                <a href="{{ route('logout') }}" class="block bg-indigo-600 text-white text-center px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                    Sign Out
                </a>
            @else
                <a href="{{ route('login') }}" class="block text-gray-700 hover:text-indigo-600 font-medium">Sign In</a>
                <a href="{{ route('login') }}" class="block bg-indigo-600 text-white text-center px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                    Register
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
