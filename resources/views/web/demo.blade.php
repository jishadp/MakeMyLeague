    <!-- Demo Section -->
<section class="relative py-24 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-50 via-white to-blue-50 overflow-hidden">
    <!-- Background decoration -->
    <div class="absolute inset-0">
        <div class="absolute top-10 right-10 w-96 h-96 bg-gradient-to-r from-blue-200/20 to-indigo-200/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-10 w-72 h-72 bg-gradient-to-r from-cyan-200/20 to-blue-200/20 rounded-full blur-3xl"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto">
        <div class="text-center mb-20">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 text-sm font-medium mb-6">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                Live Demo
            </div>
            <h2 class="text-4xl sm:text-5xl lg:text-6xl font-bold bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 bg-clip-text text-transparent mb-6 tracking-tight">
                See {{config('app.name')}} in Action
            </h2>
            <p class="text-xl sm:text-2xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
                Watch how easy it is to create leagues, manage teams, and conduct live auctions with our platform
            </p>
        </div>

        <!-- Demo Container -->
        <div class="relative max-w-6xl mx-auto">
            <!-- Video/Demo Placeholder -->
            <div class="relative group">
                <!-- Main demo container -->
                <div class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 rounded-3xl overflow-hidden shadow-2xl">
                    <!-- Browser mockup header -->
                    <div class="flex items-center px-6 py-4 bg-slate-800/50 border-b border-white/10">
                        <div class="flex space-x-2">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        </div>
                        <div class="flex-1 mx-4">
                            <div class="bg-slate-700/50 rounded-lg px-4 py-2 text-white/70 text-sm">
                                {{config('app.url')}}/dashboard
                            </div>
                        </div>
                    </div>
                    
                    <!-- Demo content -->
                    <div class="relative p-8 md:p-12">
                        <!-- Mock dashboard interface -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                            <!-- League Stats Card -->
                            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-white font-semibold text-lg">Active Leagues</h3>
                                    <div class="w-8 h-8 bg-gradient-to-r from-cyan-400 to-blue-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-3xl font-bold text-white mb-2">12</div>
                                <div class="text-white/70 text-sm">+2 this week</div>
                            </div>
                            
                            <!-- Teams Card -->
                            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-white font-semibold text-lg">Total Teams</h3>
                                    <div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-3xl font-bold text-white mb-2">48</div>
                                <div class="text-white/70 text-sm">Across all leagues</div>
                            </div>
                            
                            <!-- Players Card -->
                            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-white font-semibold text-lg">Active Players</h3>
                                    <div class="w-8 h-8 bg-gradient-to-r from-green-400 to-emerald-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-3xl font-bold text-white mb-2">320</div>
                                <div class="text-white/70 text-sm">Registered players</div>
                            </div>
                        </div>
                        
                        <!-- Live Auction Mockup -->
                        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-white font-semibold text-xl">Live Auction - Mumbai Indians vs Chennai Super Kings</h3>
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                                    <span class="text-green-400 text-sm font-medium">LIVE</span>
                                </div>
                            </div>
                            
                            <!-- Auction progress -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-white mb-2">Player #15</div>
                                    <div class="text-white/70 text-sm">Rohit Sharma</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-cyan-400 mb-2">₹8.5L</div>
                                    <div class="text-white/70 text-sm">Current Bid</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-yellow-400 mb-2">Mumbai</div>
                                    <div class="text-white/70 text-sm">Leading Bid</div>
                                </div>
                            </div>
                            
                            <!-- Bid history -->
                            <div class="mt-6 space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-white/70">Mumbai Indians</span>
                                    <span class="text-cyan-400 font-semibold">₹8.5L</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-white/70">Chennai Super Kings</span>
                                    <span class="text-white/50">₹8.2L</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-white/70">Royal Challengers</span>
                                    <span class="text-white/50">₹8.0L</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Play button overlay -->
                <div class="absolute inset-0 flex items-center justify-center bg-black/20 backdrop-blur-sm rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <button class="w-20 h-20 bg-white/20 backdrop-blur-xl border border-white/30 rounded-full flex items-center justify-center text-white hover:bg-white/30 transition-all duration-300 transform hover:scale-110">
                        <svg class="w-8 h-8 ml-1" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Feature highlights below demo -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-cyan-100 to-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Real-time Updates</h3>
                    <p class="text-slate-600">All changes sync instantly across devices and users</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-100 to-pink-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Secure Platform</h3>
                    <p class="text-slate-600">Enterprise-grade security for all your league data</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-100 to-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Easy Setup</h3>
                    <p class="text-slate-600">Get your league running in minutes, not hours</p>
                </div>
            </div>
        </div>
        
        <!-- CTA Section -->
        <div class="text-center mt-20">
            <h3 class="text-3xl font-bold text-slate-900 mb-6">Ready to experience the future of league management?</h3>
            @auth
                <a href="{{ route('leagues.create') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-semibold rounded-2xl text-lg hover:from-blue-700 hover:to-indigo-800 transform hover:scale-105 transition-all duration-300 shadow-xl hover:shadow-blue-500/25">
                    Create Your League
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            @else
                <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-semibold rounded-2xl text-lg hover:from-blue-700 hover:to-indigo-800 transform hover:scale-105 transition-all duration-300 shadow-xl hover:shadow-blue-500/25">
                    Start Your League
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            @endauth
        </div>
    </div>
</section>
