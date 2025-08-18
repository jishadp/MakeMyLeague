<header class="sticky top-0 left-0 w-full z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-2">
        <div class="nav-container flex justify-between items-center 
                    bg-gradient-to-r from-[#89a894]/85 via-[#7ea28b]/85 to-[#5c9c85]/85
                    backdrop-blur-lg border border-white/20 rounded-2xl
                    shadow-[0_4px_20px_rgba(0,0,0,0.15)] px-4 py-2 relative">
            
            <!-- Logo / Brand -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white drop-shadow" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-xl font-bold text-white drop-shadow tracking-tight hidden sm:inline-block">League Manager</span>
                </a>
            </div>

            <!-- League Selector -->
            @auth
            <div class="hidden md:flex items-center">
                <div class="relative">
                    <select id="header-league-selector" class="block w-48 pl-3 pr-10 py-2 text-sm border-white/20 bg-white/20 text-white backdrop-blur-lg focus:outline-none focus:ring-white focus:border-white rounded-full shadow-inner">
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

            <!-- User Name & Theme Switcher (For All Devices) -->
            <div class="flex items-center space-x-3">
                <!-- Theme Switcher Button -->
                <button id="theme-switcher" type="button" 
                        class="flex items-center justify-center p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-full transition-all duration-300 active:scale-95 theme-switcher-btn" 
                        aria-label="Switch between Blue and Green themes"
                        title="Switch between Blue and Green themes (Ctrl+T)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 drop-shadow transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v6a2 2 0 002 2h4a2 2 0 002-2V5zM21 15a2 2 0 00-2-2h-4a2 2 0 00-2 2v2a2 2 0 002 2h4a2 2 0 002-2v-2z"/>
                    </svg>
                    <!-- Theme indicator dots -->
                    <div class="absolute -bottom-1 -right-1 flex space-x-0.5">
                        <div class="w-1.5 h-1.5 rounded-full bg-blue-400 theme-indicator blue-dot transition-opacity duration-300"></div>
                        <div class="w-1.5 h-1.5 rounded-full bg-green-400 theme-indicator green-dot transition-opacity duration-300"></div>
                    </div>
                </button>
                
                @auth
                    <span class="text-white font-medium drop-shadow">{{ Auth::user()->name }}</span>
                @endauth
            </div>

            <!-- Menu Button (For All Devices) -->
            <div class="flex items-center">
                <button id="mobile-menu-toggle" type="button" class="flex items-center justify-center p-2 text-white hover:text-white transition-all duration-300 active:scale-95" aria-label="Toggle menu">
                    <div class="hamburger-menu-top drop-shadow-md">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>
            </div>
        </div>
    </div>
</header>
