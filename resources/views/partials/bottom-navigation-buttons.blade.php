<div class="bottom-navigation-buttons fixed bottom-0 left-0 w-full z-50">
    <div class="max-w-md mx-auto px-4 sm:px-6 py-3">
        <div class="flex justify-between items-center 
                    bg-gradient-to-r from-[#89a894]/85 via-[#7ea28b]/85 to-[#5c9c85]/85
                    backdrop-blur-lg border border-white/20 rounded-full
                    shadow-[0_-4px_20px_rgba(0,0,0,0.15)] px-2">

            <!-- Back Button (Icon Only) -->
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" 
               class="flex items-center justify-center w-12 h-12 rounded-full 
                      text-white/80 hover:text-white
                      hover:bg-white/20 transition-all duration-300 
                      shadow-sm hover:shadow-md active:scale-90">
                <svg xmlns="http://www.w3.org/2000/svg" 
                     class="h-6 w-6 drop-shadow" 
                     viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" 
                          d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" 
                          clip-rule="evenodd" />
                </svg>
            </a>

            <!-- Home Button (Centered & Elevated) -->
            <a href="{{ route('dashboard') }}" 
               class="flex items-center justify-center w-16 h-16 rounded-full 
                      bg-white text-[#5c9c85] -mt-5
                      shadow-lg hover:shadow-xl transition-all duration-300
                      active:scale-95 border-4 border-[#89a894]/30">
                <svg xmlns="http://www.w3.org/2000/svg" 
                     class="h-7 w-7" 
                     viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
            </a>

            <!-- Top Button (Icon Only) -->
            <a href="#top" 
               class="flex items-center justify-center w-12 h-12 rounded-full 
                      text-white/80 hover:text-white
                      hover:bg-white/20 transition-all duration-300
                      shadow-sm hover:shadow-md active:scale-90">
                <svg xmlns="http://www.w3.org/2000/svg" 
                     class="h-6 w-6 drop-shadow" 
                     viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" 
                          d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" 
                          clip-rule="evenodd" />
                </svg>
            </a>

        </div>
    </div>
</div>
