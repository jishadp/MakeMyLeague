<div class="bottom-navigation-buttons fixed bottom-0 left-0 w-full z-[100]">
    <div class="max-w-md mx-auto px-4 pt-2 pb-[calc(0.75rem+env(safe-area-inset-bottom))]">
        @php
            $isDashboard = request()->routeIs('dashboard');
            $isMyLeagues = request()->routeIs('my-leagues');
            $isMyTeams = request()->routeIs('my-teams');
        @endphp
        <div class="nav-container flex justify-between items-center
                    bg-gradient-to-r from-[#4a90e2]/85 via-[#6bb6ff]/85 to-[#87ceeb]/85
                    backdrop-blur-lg border border-white/10 rounded-2xl sm:rounded-full
                    shadow-[0_-6px_24px_rgba(0,0,0,0.3)] px-3 py-2 relative">

            <!-- Dashboard Button -->
            <a href="{{ route('dashboard') }}"
               class="nav-item flex items-center justify-center px-2 sm:px-3 py-1.5 rounded-full transition-all duration-300 {{ $isDashboard ? 'bg-blue-600 text-white shadow' : 'text-white/80 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-6 w-6"
                     viewBox="0 0 20 20" fill="currentColor">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                </svg>
                @if($isDashboard)
                    <span class="text-sm font-semibold ml-2 text-white">Home</span>
                @endif
            </a>

            <!-- My Leagues Button -->
            <a href="{{ route('my-leagues') }}"
               class="nav-item flex items-center justify-center px-2 sm:px-3 py-1.5 rounded-full transition-all duration-300 {{ $isMyLeagues ? 'bg-blue-600 text-white shadow' : 'text-white/80 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-6 w-6"
                     viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z" />
                    <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z" />
                </svg>
                @if($isMyLeagues)
                    <span class="text-sm font-semibold ml-2 text-white">Leagues</span>
                @endif
            </a>

            <!-- Home Button (Centered & Elevated) -->

            <div class="relative">
                <button id="mobile-menu-toggle"
                   class="home-button absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-0
                          flex items-center justify-center rounded-full
                          bg-blue-600 text-white w-14 h-14 sm:w-16 sm:h-16
                          shadow-lg hover:shadow-xl transition-all duration-300
                          active:scale-95 border-4 border-blue-300/40"
                   aria-label="Toggle menu">
                    <div class="hamburger-menu-small">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>
                <div class="w-16 h-6 opacity-0">Spacer</div>
            </div>

            
            <!-- My Teams Button -->
            <a href="{{ route('my-teams') }}"
               class="nav-item flex items-center justify-center px-2 sm:px-3 py-1.5 rounded-full transition-all duration-300 {{ $isMyTeams ? 'bg-blue-600 text-white shadow' : 'text-white/80 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-6 w-6"
                     viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                </svg>
                @if($isMyTeams)
                    <span class="text-sm font-semibold ml-2 text-white">Teams</span>
                @endif
            </a>

            <!-- WhatsApp Help Button -->
            <a href="https://wa.me/918301867613"
               target="_blank"
               rel="noopener noreferrer"
               class="nav-item flex items-center justify-center px-3 py-1.5 rounded-full transition-all duration-300 text-white/90 hover:text-white"
               aria-label="Contact MakeMyLegeu Help on WhatsApp">
                <i class="fa-brands fa-whatsapp text-2xl"></i>
            </a>

        </div>
    </div>
</div>
