<section id="features" class="relative py-24 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 overflow-hidden">
    <!-- Background decoration -->
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%236366f1" fill-opacity="0.03"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
    
    <div class="relative max-w-7xl mx-auto">
        <div class="text-center mb-20">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-cyan-100 to-blue-100 text-cyan-800 text-sm font-medium mb-6">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Powerful Features
            </div>
            <h2 class="text-4xl sm:text-5xl lg:text-6xl font-bold bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 bg-clip-text text-transparent mb-6 tracking-tight">
                Everything You Need for
                <span class="block">Professional Cricket Leagues</span>
            </h2>
            <p class="text-xl sm:text-2xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
                From live auctions to real-time statistics — manage every aspect of your cricket league with our comprehensive platform
            </p>
        </div>
        
        <!-- Statistics Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16 grid-mobile">
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
        <div class="flex justify-center mb-16">
            <!-- Real-time Auctions -->
            <div class="group relative p-8 rounded-3xl bg-white/40 backdrop-blur-xl border border-white/20 hover:border-white/30 transition-all duration-500 ease-out transform hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-500/10 max-w-md card-mobile">
                <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/5 to-blue-500/5 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative z-10">
                    <div class="w-20 h-20 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Real-time Auctions</h3>
                    <p class="text-slate-600 text-lg mb-6 leading-relaxed">Conduct live IPL-style player auctions with real-time bidding, instant notifications, and automated bid tracking</p>
                    <div class="inline-flex items-center text-cyan-600 font-semibold text-sm group-hover:text-cyan-700 transition-colors">
                        Experience Auctions
                        <svg class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </div>
                </div>
            </div>

        </div>

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