<div class="navigation-buttons mt-3">
    <div class="max-w-md mx-auto px-4 sm:px-6 py-0">
        <div class="flex justify-end items-center space-x-2">

            <!-- Logout Button (Floating) -->
            <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                @csrf
                <button type="submit" 
                       class="flex items-center justify-center w-10 h-10 rounded-full 
                              bg-gradient-to-r from-[#4a90e2] to-[#87ceeb]
                              text-white hover:text-white/90
                              shadow-md hover:shadow-lg transition-all duration-300 
                              active:scale-90">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V7.414l-5-5H3zm6.293 11.293a1 1 0 001.414 1.414l4-4a1 1 0 000-1.414l-4-4a1 1 0 00-1.414 1.414L11.586 9H5a1 1 0 000 2h6.586l-2.293 2.293z" clip-rule="evenodd" />
                    </svg>
                </button>
            </form>

            <!-- Menu Button -->
            <a href="#" 
               class="flex items-center justify-center w-10 h-10 rounded-full 
                      bg-gradient-to-r from-[#4a90e2] to-[#87ceeb]
                      text-white hover:text-white/90
                      shadow-md hover:shadow-lg transition-all duration-300 
                      active:scale-90">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                </svg>
            </a>

        </div>
    </div>
</div>
