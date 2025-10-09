<!-- How It Works Section -->
<section class="relative py-24 px-4 sm:px-6 lg:px-8 bg-white overflow-hidden">
    <!-- Background decoration -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 via-indigo-50/30 to-purple-50/50"></div>
    <div class="absolute top-20 right-20 w-72 h-72 bg-gradient-to-r from-blue-200/20 to-purple-200/20 rounded-full blur-3xl"></div>
    <div class="absolute bottom-20 left-20 w-96 h-96 bg-gradient-to-r from-cyan-200/20 to-blue-200/20 rounded-full blur-3xl"></div>
    
    <div class="relative max-w-7xl mx-auto">
        <div class="text-center mb-20">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 text-sm font-medium mb-6">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Simple Process
            </div>
            <h2 class="text-4xl sm:text-5xl lg:text-6xl font-bold bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 bg-clip-text text-transparent mb-6 tracking-tight">
                How It Works
            </h2>
            <p class="text-xl sm:text-2xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
                Get your cricket league up and running in just three simple steps
            </p>
        </div>

        <!-- Steps -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative grid-mobile">
            <!-- Connection lines for desktop -->
            <div class="hidden md:block absolute top-24 left-1/3 right-1/3 h-0.5 bg-gradient-to-r from-blue-300 to-purple-300 transform -translate-y-1/2"></div>
            
            <!-- Step 1 -->
            <div class="relative group">
                <div class="flex flex-col items-center text-center">
                    <!-- Step number -->
                    <div class="relative mb-8">
                        <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-2xl group-hover:scale-110 transition-transform duration-300">
                            1
                        </div>
                        <!-- Floating elements -->
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300 animate-pulse"></div>
                    </div>
                    
                    <!-- Icon -->
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    
                    <!-- Content -->
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Create Your League</h3>
                    <p class="text-slate-600 text-lg leading-relaxed mb-6">
                        Set up your cricket league with custom rules, team limits, and tournament format. Choose from various game types and configure your league settings.
                    </p>
                    
                    <!-- Action button -->
                    @auth
                        <a href="{{ route('leagues.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-blue-500/25">
                            Create League
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-blue-500/25">
                            Register Now
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Step 2 -->
            <div class="relative group">
                <div class="flex flex-col items-center text-center">
                    <!-- Step number -->
                    <div class="relative mb-8">
                        <div class="w-24 h-24 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-2xl group-hover:scale-110 transition-transform duration-300">
                            2
                        </div>
                        <!-- Floating elements -->
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-gradient-to-br from-pink-400 to-purple-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300 animate-pulse"></div>
                    </div>
                    
                    <!-- Icon -->
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-100 to-pink-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    
                    <!-- Content -->
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Add Teams & Players</h3>
                    <p class="text-slate-600 text-lg leading-relaxed mb-6">
                        Register teams and players for your league. Conduct live auctions to distribute players fairly among teams with real-time bidding.
                    </p>
                    
                    <!-- Action button -->
                    <a href="{{ route('teams.index') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-600 text-white font-semibold rounded-xl hover:from-purple-600 hover:to-pink-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-purple-500/25">
                        Browse Teams
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="relative group">
                <div class="flex flex-col items-center text-center">
                    <!-- Step number -->
                    <div class="relative mb-8">
                        <div class="w-24 h-24 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-2xl group-hover:scale-110 transition-transform duration-300">
                            3
                        </div>
                        <!-- Floating elements -->
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-gradient-to-br from-emerald-400 to-green-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300 animate-pulse"></div>
                    </div>
                    
                    <!-- Icon -->
                    <div class="w-16 h-16 bg-gradient-to-br from-green-100 to-emerald-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    
                    <!-- Content -->
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Run Matches</h3>
                    <p class="text-slate-600 text-lg leading-relaxed mb-6">
                        Generate fixtures, track live scores, update player statistics, and award points. See everything update in real-time across all devices.
                    </p>
                    
                    <!-- Action button -->
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl hover:from-green-600 hover:to-emerald-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-green-500/25">
                            View Dashboard
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl hover:from-green-600 hover:to-emerald-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-green-500/25">
                            Start Now
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Bottom CTA -->
        <div class="text-center mt-20">
            <div class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-slate-100 to-blue-100 rounded-full text-slate-700 font-medium mb-6">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Ready to get started?
            </div>
            <h3 class="text-3xl font-bold text-slate-900 mb-6">Join thousands of cricket enthusiasts</h3>
            @auth
                <a href="{{ route('leagues.create') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-semibold rounded-2xl text-lg hover:from-blue-700 hover:to-indigo-800 transform hover:scale-105 transition-all duration-300 shadow-xl hover:shadow-blue-500/25">
                    Create Your First League
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            @else
                <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-semibold rounded-2xl text-lg hover:from-blue-700 hover:to-indigo-800 transform hover:scale-105 transition-all duration-300 shadow-xl hover:shadow-blue-500/25">
                    Start Free Trial
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            @endauth
        </div>
    </div>
</section>
