<section id="features" class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4 tracking-tight">
                Everything You Need for Cricket Leagues
            </h2>
            <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto">
                Manage cricket teams, track player stats, and organize tournaments with ease
            </p>
        </div>
        
        <!-- Statistics Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            <div class="text-center">
                <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="text-4xl font-bold text-indigo-600 mb-2" data-count="{{ $stats['leagues'] ?? 0 }}">0</div>
                <div class="text-lg font-medium text-gray-900">Active Leagues</div>
                <p class="text-sm text-gray-500 mt-1">Cricket tournaments organized</p>
            </div>
            
            <div class="text-center">
                <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="text-4xl font-bold text-indigo-600 mb-2" data-count="{{ $stats['teams'] ?? 0 }}">0</div>
                <div class="text-lg font-medium text-gray-900">Registered Teams</div>
                <p class="text-sm text-gray-500 mt-1">Teams competing in leagues</p>
            </div>
            
            <div class="text-center">
                <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="text-4xl font-bold text-indigo-600 mb-2" data-count="{{ $stats['players'] ?? 0 }}">0</div>
                <div class="text-lg font-medium text-gray-900">Active Players</div>
                <p class="text-sm text-gray-500 mt-1">Cricket players in the system</p>
            </div>
        </div>
        
        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <a href="{{ route('dashboard') }}" class="group relative text-center p-6 rounded-xl bg-white shadow-md hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-1 cursor-pointer">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6 transition-colors duration-300 group-hover:bg-indigo-200">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">League Management</h3>
                <p class="text-gray-500 text-base mb-4">Create and manage cricket leagues with customizable tournament formats and rules</p>
                <span class="inline-flex items-center text-indigo-600 font-medium text-sm group-hover:text-indigo-700">
                    Explore Leagues
                    <svg class="ml-1 w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </span>
            </a>
            
            <a href="{{ route('teams.index') }}" class="group relative text-center p-6 rounded-xl bg-white shadow-md hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-1 cursor-pointer">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6 transition-colors duration-300 group-hover:bg-indigo-200">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Team Registration</h3>
                <p class="text-gray-500 text-base mb-4">Register teams, manage rosters, and track team performance across multiple leagues</p>
                <span class="inline-flex items-center text-indigo-600 font-medium text-sm group-hover:text-indigo-700">
                    Browse Teams
                    <svg class="ml-1 w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </span>
            </a>
            
            <a href="{{ route('players.index') }}" class="group relative text-center p-6 rounded-xl bg-white shadow-md hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-1 cursor-pointer">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6 transition-colors duration-300 group-hover:bg-indigo-200">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Player Analytics</h3>
                <p class="text-gray-500 text-base mb-4">Track detailed player statistics, performance metrics, and career progression</p>
                <span class="inline-flex items-center text-indigo-600 font-medium text-sm group-hover:text-indigo-700">
                    View Players
                    <svg class="ml-1 w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </span>
            </a>
        </div>
        
        <!-- Additional Features Row for Authenticated Users -->
        @auth
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
            <a href="{{ route('auctions.index') }}" class="group relative text-center p-6 rounded-xl bg-white shadow-md hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-1 cursor-pointer">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6 transition-colors duration-300 group-hover:bg-indigo-200">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Player Auctions</h3>
                <p class="text-gray-500 text-base mb-4">View completed player auction transactions and bidding history</p>
                <span class="inline-flex items-center text-indigo-600 font-medium text-sm group-hover:text-indigo-700">
                    View Auctions
                    <svg class="ml-1 w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </span>
            </a>
            
            <div class="group relative text-center p-6 rounded-xl bg-gradient-to-br from-indigo-50 to-purple-50 shadow-md hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-1 cursor-pointer border-2 border-dashed border-indigo-200">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6 transition-colors duration-300 group-hover:bg-indigo-200">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">More Features</h3>
                <p class="text-gray-500 text-base mb-4">Additional features and tools for comprehensive league management</p>
                <span class="inline-flex items-center text-indigo-600 font-medium text-sm group-hover:text-indigo-700">
                    Coming Soon
                    <svg class="ml-1 w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </span>
            </div>
        </div>
        @endauth
    </div>
</section>

<!-- Counter Animation Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('[data-count]');
    
    const animateCounter = (counter) => {
        const target = parseInt(counter.getAttribute('data-count'));
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60fps
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                counter.textContent = target;
                clearInterval(timer);
            } else {
                counter.textContent = Math.floor(current);
            }
        }, 16);
    };
    
    // Use Intersection Observer to trigger animation when section is visible
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                animateCounter(counter);
                observer.unobserve(counter);
            }
        });
    }, { threshold: 0.5 });
    
    counters.forEach(counter => observer.observe(counter));
});
</script>