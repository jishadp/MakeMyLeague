<!-- Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden transition-opacity duration-200 opacity-0 backdrop-blur-sm"></div>

<!-- Sidebar Menu -->
<div id="sidebar" class="fixed top-0 left-0 h-full w-80 bg-gradient-to-b from-[#4a90e2] to-[#87ceeb] shadow-xl z-50 transform -translate-x-full transition-transform duration-200 ease-out overflow-y-auto max-h-screen pb-24 rounded-r-3xl">
    <div class="flex flex-col h-full">
        <!-- Sidebar Header -->
        <div class="p-6 border-b border-white/20 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                    </svg>
                </div>
                <span class="text-xl font-bold text-white">Cricket League</span>
            </div>
            <button id="close-sidebar" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- League Selector (Mobile) -->
        @auth
        <div class="px-6 pt-6">
            <label for="mobile-league-selector" class="block text-sm font-medium text-white/80 mb-2">Select League</label>
            <select id="mobile-league-selector" class="block w-full pl-4 pr-10 py-3 text-white border-white/20 bg-white/10 focus:outline-none focus:ring-white/30 focus:border-white/30 rounded-xl shadow-inner backdrop-blur-lg text-sm">
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
            <!-- Home Link for Everyone -->
            <a href="/" class="flex items-center space-x-3 p-3 rounded-lg text-white/90 hover:bg-white/10 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 drop-shadow" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                <span class="font-medium">Home</span>
            </a>

            @auth
                <!-- Dashboard Link -->
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 p-3 rounded-lg text-white/90 hover:bg-white/10 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 drop-shadow" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" />
                        <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" />
                    </svg>
                    <span class="font-medium">Dashboard</span>
                </a>

                <!-- Leagues Link -->
                <a href="{{ route('leagues.index') }}" class="flex items-center space-x-3 p-3 rounded-lg text-white/90 hover:bg-white/10 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 drop-shadow" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z" />
                        <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z" />
                    </svg>
                    <span class="font-medium">Leagues</span>
                </a>

                <!-- Teams Link -->
                <a href="{{ route('teams.index') }}" class="flex items-center space-x-3 p-3 rounded-lg text-white/90 hover:bg-white/10 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 drop-shadow" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                    </svg>
                    <span class="font-medium">Teams</span>
                </a>

                <!-- Players Link -->
                <a href="{{ route('players.index') }}" class="flex items-center space-x-3 p-3 rounded-lg text-white/90 hover:bg-white/10 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 drop-shadow" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">Players</span>
                </a>

                <!-- Auctions Link -->
                <a href="{{ route('auctions.index') }}" class="flex items-center space-x-3 p-3 rounded-lg text-white/90 hover:bg-white/10 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 drop-shadow" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">Auctions</span>
                </a>

                <!-- Profile Link -->
                <a href="{{ route('profile.show') }}" class="flex items-center space-x-3 p-3 rounded-lg text-white/90 hover:bg-white/10 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 drop-shadow" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">Profile</span>
                </a>
            @else
                <!-- About Link -->
                <a href="#about" class="flex items-center space-x-3 p-3 rounded-lg text-white/90 hover:bg-white/10 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 drop-shadow" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">About Us</span>
                </a>

                <!-- Features Link -->
                <a href="#features" class="flex items-center space-x-3 p-3 rounded-lg text-white/90 hover:bg-white/10 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 drop-shadow" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                    </svg>
                    <span class="font-medium">Features</span>
                </a>

                <!-- Register Link -->
                <a href="#registration" class="flex items-center space-x-3 p-3 rounded-lg text-white/90 hover:bg-white/10 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 drop-shadow" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                    </svg>
                    <span class="font-medium">Register</span>
                </a>
            @endauth
        </div>
        
        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-white/20">
            @auth
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-medium text-white">{{ auth()->user()->name }}</p>
                        <p class="text-sm text-white/70">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                
                <!-- Logout Button -->
                <a href="{{ route('logout') }}" class="w-full flex items-center justify-center space-x-2 p-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Logout</span>
                </a>
            @else
                <div class="flex flex-col space-y-2">
                    <a href="{{ route('login') }}" class="w-full flex items-center justify-center space-x-2 p-2 bg-white text-[#4a90e2] hover:bg-white/90 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span>Login</span>
                    </a>
                    <a href="{{ route('register') }}" class="w-full flex items-center justify-center space-x-2 p-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <span>Register</span>
                    </a>
                </div>
            @endauth
        </div>
    </div>
</div>
