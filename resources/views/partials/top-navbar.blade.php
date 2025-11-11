<header class="sticky top-0 left-0 w-full z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-2">
        <div class="nav-container flex justify-between items-center 
                    bg-gradient-to-r from-[#4a90e2]/85 via-[#6bb6ff]/85 to-[#87ceeb]/85
                    backdrop-blur-lg border border-white/20 rounded-2xl
                    shadow-[0_4px_20px_rgba(0,0,0,0.15)] px-4 py-2 relative">
            
            <!-- Logo / Brand -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white drop-shadow" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-xl font-bold text-white drop-shadow tracking-tight hidden sm:inline-block">{{config('app.name')}}</span>
                </a>
            </div>

            <!-- Header Search (persistent) -->
            <div class="hidden md:flex flex-1 px-4">
                <form action="{{ route('dashboard') }}" method="GET" class="w-full relative">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search leagues by name, game, or location..."
                        class="w-full px-4 py-2 pr-12 rounded-xl border-2 border-white/30 bg-white/20 text-white placeholder-white/70 focus:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/40"
                        aria-label="Search leagues"
                    >
                    @if(request('role'))
                        <input type="hidden" name="role" value="{{ request('role') }}">
                    @endif
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-white p-2 rounded-md hover:bg-white/10" aria-label="Search">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- User Name -->
            <div class="flex items-center space-x-3">
                @auth
                    <!-- Role Switcher -->
                    <form action="{{ route('dashboard') }}" method="GET" class="hidden sm:block">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        <label for="role" class="sr-only">Viewing as</label>
                        <select
                            id="role"
                            name="role"
                            class="px-3 py-2 rounded-lg bg-white/20 text-white border border-white/30 focus:bg-white/30 focus:outline-none"
                            onchange="this.form.submit()"
                            aria-label="Viewing as role"
                        >
                            @php $role = request('role', 'player'); @endphp
                            <option value="player" {{ $role==='player' ? 'selected' : '' }}>Player</option>
                            <option value="team" {{ $role==='team' ? 'selected' : '' }}>Team Owner</option>
                            <option value="organizer" {{ $role==='organizer' ? 'selected' : '' }}>Organizer</option>
                        </select>
                    </form>
                    <div class="flex items-center space-x-2">
                        <span class="text-white font-medium drop-shadow">{{ Auth::user()->name }}</span>
                    </div>
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
