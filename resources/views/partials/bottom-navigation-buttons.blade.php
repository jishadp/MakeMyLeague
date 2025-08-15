<div class="bottom-navigation-buttons fixed bottom-0 left-0 w-full z-50">
    <div class="max-w-md mx-auto px-4 sm:px-6 py-3">
        <div class="nav-container flex justify-between items-center 
                    bg-gradient-to-r from-[#89a894]/85 via-[#7ea28b]/85 to-[#5c9c85]/85
                    backdrop-blur-lg border border-white/20 rounded-full
                    shadow-[0_-4px_20px_rgba(0,0,0,0.15)] px-2 relative">

            <!-- Back Button -->
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" 
               class="nav-item flex flex-col items-center justify-center p-2
                      text-white/80 hover:text-white transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" 
                     class="h-6 w-6 drop-shadow" 
                     viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" 
                          d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" 
                          clip-rule="evenodd" />
                </svg>
                <span class="text-xs mt-1">Back</span>
            </a>

            <!-- League Button -->
            <a href="{{ route('leagues.index') }}" 
               class="nav-item flex flex-col items-center justify-center p-2
                      text-white/80 hover:text-white transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" 
                     class="h-6 w-6 drop-shadow" 
                     viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z" />
                    <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z" />
                </svg>
                <span class="text-xs mt-1">Leagues</span>
            </a>

            <!-- Home Button (Centered & Elevated) -->
            <div class="relative">
                <a href="{{ route('dashboard') }}" 
                   class="home-button absolute bottom-1 left-1/2 transform -translate-x-1/2 translate-y-0
                          flex flex-col items-center justify-center rounded-full 
                          bg-white text-[#5c9c85] w-16 h-16
                          shadow-lg hover:shadow-xl transition-all duration-300
                          active:scale-95 border-4 border-[#89a894]/30">
                    <svg xmlns="http://www.w3.org/2000/svg" 
                         class="h-7 w-7" 
                         viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    <span class="text-xs mt-1 font-medium">Home</span>
                </a>
                <div class="w-16 h-6 opacity-0">Spacer</div>
            </div>

            <!-- Teams Button -->
            <a href="{{ route('teams.index') }}" 
               class="nav-item flex flex-col items-center justify-center p-2
                      text-white/80 hover:text-white transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" 
                     class="h-6 w-6 drop-shadow" 
                     viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                </svg>
                <span class="text-xs mt-1">Teams</span>
            </a>

            <!-- Top Button -->
            <a href="#top" 
               class="nav-item flex flex-col items-center justify-center p-2
                      text-white/80 hover:text-white transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" 
                     class="h-6 w-6 drop-shadow" 
                     viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" 
                          d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" 
                          clip-rule="evenodd" />
                </svg>
                <span class="text-xs mt-1">Top</span>
            </a>
        </div>
    </div>

    <style>
        .home-button {
            top: -20px;
            z-index: 1;
        }
        
        .nav-item {
            position: relative;
            z-index: 0;
            min-width: 60px;
        }
        
        @media (max-width: 400px) {
            .nav-item span {
                font-size: 0.65rem;
            }
        }
    </style>
</div>