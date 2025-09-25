<div class="bottom-navigation-buttons fixed bottom-0 left-0 w-full z-[100]">
    <div class="max-w-md mx-auto px-4 sm:px-6 py-3">
        <div class="nav-container flex justify-between items-center
                    bg-gradient-to-r from-[#4a90e2]/85 via-[#6bb6ff]/85 to-[#87ceeb]/85
                    backdrop-blur-lg border border-white/20 rounded-full
                    shadow-[0_-4px_20px_rgba(0,0,0,0.15)] px-2 relative">

            <!-- Dashboard Button -->
            <a href="{{ route('dashboard') }}"
               class="nav-item flex flex-col items-center justify-center p-2
                      text-white/80 hover:text-white transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-6 w-6 drop-shadow"
                     viewBox="0 0 20 20" fill="currentColor">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                </svg>
                <span class="text-xs mt-1">Dashboard</span>
            </a>

            <!-- My Leagues Button -->
            <a href="{{ route('my-leagues') }}"
               class="nav-item flex flex-col items-center justify-center p-2
                      text-white/80 hover:text-white transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-6 w-6 drop-shadow"
                     viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z" />
                    <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z" />
                </svg>
                <span class="text-xs mt-1">My Leagues</span>
            </a>

            <!-- Home Button (Centered & Elevated) -->

            <div class="relative">
                <button id="mobile-menu-toggle"
                   class="home-button absolute bottom-1 left-1/2 transform -translate-x-1/2 translate-y-0
                          flex flex-col items-center justify-center rounded-full
                          bg-white text-[#4a90e2] w-16 h-16
                          shadow-lg hover:shadow-xl transition-all duration-300
                          active:scale-95 border-4 border-[#4a90e2]/30"
                   aria-label="Toggle menu">
                    <div class="hamburger-menu-small">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>
                <div class="w-16 h-6 opacity-0">Spacer</div>
            </div>

            <!-- Profile Button -->
            <a href="{{ route('profile.show') }}"
               class="nav-item flex flex-col items-center justify-center p-2
                      text-white/80 hover:text-white transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-6 w-6 drop-shadow"
                     viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
                <span class="text-xs mt-1">Profile</span>
            </a>

            <!-- Auction Button -->
            <a href="{{ route('auctions.index') }}"
               class="nav-item flex flex-col items-center justify-center p-2
                      text-white/80 hover:text-white transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-6 w-6 drop-shadow"
                     viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                </svg>
                <span class="text-xs mt-1">Auction</span>
            </a>
        </div>
    </div>
</div>
