@extends('layouts.app')

@section('title', config('app.name').' - Dashboard')

@section('content')

<!-- Professional Hero Section -->
<section class="relative bg-gradient-to-br from-gray-50 via-white to-gray-100 overflow-hidden border-b-2 border-gray-200"> 
    <!-- Animated Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0zNiAxOGMzLjMxNCAwIDYgMi42ODYgNiA2cy0yLjY4NiA2LTYgNi02LTIuNjg2LTYtNiAyLjY4Ni02IDYtNnoiIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLXdpZHRoPSIyIi8+PC9nPjwvc3ZnPg==')] animate-pulse"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 items-center">
            <div class="space-y-6">
                <div class="inline-flex items-center px-4 py-2 bg-blue-50 border-2 border-blue-200 rounded-full shadow-sm">
                    <span class="w-2 h-2 bg-blue-600 rounded-full animate-pulse mr-2"></span>
                    <span class="text-blue-900 text-sm font-bold">Live Dashboard</span>
                </div>
                
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-gray-900 tracking-tight">
                    Welcome,
                    <span class="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 bg-clip-text text-transparent">
                        {{ auth()->user()->name }}
                    </span>
                    </h1>
                
                <p class="text-base sm:text-lg text-gray-700 leading-relaxed font-medium">
                    Your sports hub. Track leagues & dominate.
                </p>

                <!-- Quick Action Buttons -->
                <div class="flex flex-wrap gap-3 pt-4">
                    <a href="#available-leagues" class="px-4 sm:px-6 py-2.5 bg-blue-600 hover:bg-blue-700 rounded-lg font-bold text-sm sm:text-base text-white shadow-lg hover:shadow-xl transition-all">
                        Join League
                    </a>
                    @if($liveAuctions->isNotEmpty())
                    <a href="{{ route('auctions.live', $liveAuctions->first()) }}" class="px-4 sm:px-6 py-2.5 bg-red-600 hover:bg-red-700 rounded-lg font-bold text-sm sm:text-base text-white shadow-lg hover:shadow-xl transition-all">
                        <span class="inline-flex items-center">
                            <span class="w-2 h-2 bg-white rounded-full animate-pulse mr-2"></span>
                            <span class="hidden sm:inline">Watch Live Auction</span>
                            <span class="sm:hidden">Live Auction</span>
                        </span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Stats Card - Tabbed Interface -->
            <div class="stats-crypto-card">
                <div class="card-header">
                    <div class="card-logo">MakeMyLeague</div>
                    <div class="card-open">STATS</div>
                </div>

                <!-- Tab Switcher -->
                <div class="crypto-switch">
                    <input type="radio" name="stat-tab" id="leagues" checked>
                    <label for="leagues">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                        </svg>
                        <span class="hidden sm:inline">Leagues</span>
                    </label>

                    <input type="radio" name="stat-tab" id="teams">
                    <label for="teams">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                        <span class="hidden sm:inline">Teams</span>
                    </label>

                    <input type="radio" name="stat-tab" id="players">
                    <label for="players">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        <span class="hidden sm:inline">Players</span>
                    </label>

                    <div class="slider"></div>
                </div>

                <!-- Stats Content -->
                <div class="price-infos">
                    <!-- Leagues Stats -->
                    <div class="price-info leagues">
                        <div class="stat-value stat-leagues">{{ $quickStats['active_leagues'] }}</div>
                        <div class="stat-label">Active Leagues</div>
                        <div class="stats">
                            <span class="text-gray-400 text-sm">Total in platform</span>
                            <span class="change change-purple">⚡ Live Now</span>
                        </div>
                    </div>

                    <!-- Teams Stats -->
                    <div class="price-info teams">
                        <div class="stat-value stat-teams">{{ number_format($quickStats['total_teams']) }}</div>
                        <div class="stat-label">Total Teams</div>
                        <div class="stats">
                            <span class="text-gray-400 text-sm">Registered teams</span>
                            <span class="change change-indigo">+{{ $quickStats['total_teams'] }}</span>
                        </div>
                    </div>

                    <!-- Players Stats -->
                    <div class="price-info players">
                        <div class="stat-value stat-players">{{ number_format($quickStats['players_registered']) }}</div>
                        <div class="stat-label">Total Players</div>
                        <div class="stats">
                            <span class="text-gray-400 text-sm">Registered users</span>
                            <span class="change change-pink">+{{ $quickStats['players_registered'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Chart Visualization -->
                <div class="chart">
                    <!-- Leagues Chart -->
                    <svg id="chart-leagues" viewBox="0 0 320 90" preserveAspectRatio="none">
                        <path d="M0,70 Q80,20 160,45 T320,30" />
                    </svg>
                    
                    <!-- Teams Chart -->
                    <svg id="chart-teams" viewBox="0 0 320 90" preserveAspectRatio="none">
                        <path d="M0,60 Q80,30 160,50 T320,25" />
                    </svg>
                    
                    <!-- Players Chart -->
                    <svg id="chart-players" viewBox="0 0 320 90" preserveAspectRatio="none">
                        <path d="M0,65 Q80,35 160,40 T320,20" />
                    </svg>
                </div>
            </div>
            </div>
        </div>
    </section>

<!-- Live Auctions Alert Banner -->
@if($liveAuctions->isNotEmpty())
<section class="bg-white border-b-4 border-red-500 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center space-x-3">
                <div class="relative flex items-center justify-center">
                    <div class="w-3 h-3 bg-red-600 rounded-full animate-ping absolute"></div>
                    <div class="w-3 h-3 bg-red-600 rounded-full"></div>
                </div>
                <span class="text-gray-900 font-black text-lg uppercase tracking-wide">🔴 LIVE AUCTION IN PROGRESS</span>
                <span class="hidden sm:inline text-gray-700 font-semibold">{{ $liveAuctions->first()->name }}</span>
            </div>
            <a href="{{ route('auctions.live', $liveAuctions->first()) }}" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold shadow-lg hover:shadow-xl transition-all">
                Watch Now →
            </a>
        </div>
    </div>
</section>
@endif

<!-- Available Leagues to Join (PRIMARY NEED) -->
<section id="available-leagues" class="py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-gray-50 to-white">
        <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-6">
                <div>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 mb-2">
                    <span class="hidden sm:inline">Available Leagues</span><span class="sm:hidden">Leagues</span>
                    <span class="bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">to Join</span>
                </h2>
                <p class="text-sm sm:text-base text-gray-600">Register now and showcase your skills</p>
                </div>
            <a href="{{ route('leagues.index') }}" class="hidden sm:inline-flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-lg hover:shadow-xl transition-all">
                View All
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            </div>

        <!-- Search Bar -->
        <div class="mb-6">
            <form method="GET" action="{{ route('dashboard') }}" class="relative">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search leagues by name, game, or location..." 
                       class="w-full px-4 sm:px-6 py-3 sm:py-4 pr-12 sm:pr-14 rounded-xl sm:rounded-2xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition-all text-sm sm:text-base">
                <button type="submit" class="absolute right-3 sm:right-4 top-1/2 transform -translate-y-1/2 bg-blue-600 hover:bg-blue-700 text-white p-2 sm:p-2.5 rounded-lg shadow-lg hover:shadow-xl transition-all">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                </button>
            </form>
            @if(request('search'))
            <div class="mt-3 flex items-center justify-between">
                <p class="text-sm text-gray-600">
                    Showing results for: <span class="font-semibold text-blue-600">"{{ request('search') }}"</span>
                </p>
                <a href="{{ route('dashboard') }}" class="text-sm text-red-600 hover:text-red-700 font-medium">
                    Clear Search
                </a>
            </div>
            @endif
        </div>

        @if($availableLeagues->isEmpty())
        <div class="bg-white rounded-3xl shadow-xl p-12 text-center border-2 border-dashed border-gray-200">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">No Available Leagues</h3>
            <p class="text-gray-600 mb-6">Check back soon for new league opportunities!</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($availableLeagues as $league)
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-blue-200 hover:-translate-y-2">
                <!-- League Header -->
                <div class="relative h-48 overflow-hidden bg-gradient-to-br from-blue-600 via-blue-500 to-blue-700">
                                @if($league->banner)
                        <img src="{{ Storage::url($league->banner) }}" alt="{{ $league->name }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                                @endif
                                
                                <!-- Status Badge -->
                                <div class="absolute top-4 left-4">
                        <span class="inline-flex items-center px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full shadow-lg">
                            OPEN FOR REGISTRATION
                                    </span>
                                </div>
                                
                    <!-- League Logo/Name -->
                                    <div class="absolute bottom-4 left-4 right-4">
                                        <div class="flex items-center space-x-3">
                                            @if($league->logo)
                                <img src="{{ Storage::url($league->logo) }}" alt="{{ $league->name }}" class="w-14 h-14 rounded-xl border-2 border-white shadow-lg">
                            @else
                                <div class="w-14 h-14 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center border-2 border-white">
                                    <span class="text-white font-black text-2xl">{{ substr($league->name, 0, 1) }}</span>
                                </div>
                                            @endif
                                            <div>
                                <h3 class="text-xl font-black text-white drop-shadow-lg">{{ $league->name }}</h3>
                                <p class="text-sm text-white/90">{{ $league->game->name }}</p>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            
                <!-- League Details -->
                            <div class="p-6">
                                <!-- Quick Stats -->
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="text-center p-3 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                            <div class="text-xl font-black text-blue-600">{{ $league->leagueTeams->count() }}/{{ $league->max_teams }}</div>
                            <div class="text-xs text-gray-600 font-medium">Teams</div>
                                    </div>
                        <div class="text-center p-3 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                            <div class="text-xl font-black text-blue-700">{{ $league->leaguePlayers->count() }}</div>
                            <div class="text-xs text-gray-600 font-medium">Players</div>
                        </div>
                        <div class="text-center p-3 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                            <div class="text-xl font-black text-blue-800">{{ $league->season }}</div>
                            <div class="text-xs text-gray-600 font-medium">Season</div>
                                    </div>
                                </div>
                                
                    <!-- Info -->
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                        </svg>
                            <span class="font-medium">{{ $league->start_date->format('M d') }} - {{ $league->end_date->format('M d, Y') }}</span>
                                    </div>
                        @if($league->localBody)
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $league->localBody->name }}, {{ $league->localBody->district->name }}</span>
                                    </div>
                                @endif
                    </div>

                    <!-- Prize Pool -->
                    @if($league->winner_prize || $league->runner_prize)
                    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-xl p-4 mb-4">
                        <div class="flex items-center justify-between text-white">
                            <div>
                                <div class="text-xs font-bold mb-1">PRIZE POOL</div>
                                <div class="text-2xl font-black">₹{{ number_format(($league->winner_prize + $league->runner_prize)/1000, 0) }}K</div>
                                        </div>
                            <div class="text-right">
                                <div class="text-xs opacity-90">Winner: ₹{{ number_format($league->winner_prize/1000, 0) }}K</div>
                                <div class="text-xs opacity-90">Runner: ₹{{ number_format($league->runner_prize/1000, 0) }}K</div>
                                                </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Action Button -->
                    <div class="flex gap-2">
                        <a href="{{ route('leagues.show', $league) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2.5 px-4 rounded-lg font-bold shadow-lg hover:shadow-xl transition-all">
                            Register Now →
                        </a>
                        <a href="{{ route('leagues.show', $league) }}" class="px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                    </div>
                            </div>
                        </div>
                    @endforeach
                </div>
        
        <!-- Pagination -->
        @if($availableLeagues->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $availableLeagues->links() }}
                </div>
        @endif
            @endif
        </div>
    </section>

<!-- Auction Leaderboard Section -->
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 mb-2">
                    <span class="hidden sm:inline">Auction</span> <span class="bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">Leaderboard</span>
                </h2>
                <p class="text-sm sm:text-base text-gray-600">Top 5 valued players across all leagues</p>
            </div>
            </div>

        @if($auctionLeaderboard->isEmpty())
        <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-3xl p-12 text-center">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">No Auction Data Yet</h3>
            <p class="text-gray-600">Leaderboard will appear after player auctions</p>
                </div>
            @else
        <div class="bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b-2 border-gray-200">
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">#</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Player</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Position</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden md:table-cell">Team</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hidden lg:table-cell">League</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($auctionLeaderboard->take(5) as $index => $player)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-3 sm:px-6 py-4">
                                <div class="flex items-center">
                                    @if($index < 3)
                                        <div class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full {{ $index === 0 ? 'bg-yellow-400' : ($index === 1 ? 'bg-gray-300' : 'bg-orange-400') }}">
                                            <span class="text-lg sm:text-xl font-black text-white">{{ $index + 1 }}</span>
                                        </div>
                                @else
                                        <span class="text-lg sm:text-xl font-bold text-gray-900">{{ $index + 1 }}</span>
                                            @endif
                                </div>
                            </td>
                            <td class="px-3 sm:px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-bold mr-2 sm:mr-3 shadow-md text-xs sm:text-base">
                                        {{ strtoupper(substr($player->user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 text-sm sm:text-base">{{ $player->user->name }}</div>
                                        <div class="text-xs sm:text-sm text-gray-600 hidden sm:block">{{ $player->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 sm:px-6 py-4">
                                <span class="px-2 sm:px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs sm:text-sm font-medium">
                                    {{ $player->user->position->name ?? 'No Role' }}
                                </span>
                            </td>
                            <td class="px-3 sm:px-6 py-4 hidden md:table-cell">
                                <span class="font-medium text-gray-900">{{ $player->leagueTeam->team->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-3 sm:px-6 py-4 hidden lg:table-cell">
                                <span class="text-gray-700">{{ $player->league->name }}</span>
                            </td>
                            <td class="px-3 sm:px-6 py-4 text-right">
                                <!-- Mobile: Full number -->
                                <div class="sm:hidden text-lg font-black bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                                    ₹{{ number_format($player->bid_price, 0) }}
                                </div>
                                <!-- Desktop: With K -->
                                <div class="hidden sm:block text-2xl font-black bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                                    ₹{{ number_format($player->bid_price/1000, 1) }}K
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                                        </div>
                                    </div>
                                @endif
    </div>
</section>

<!-- User's Auction History & Stats -->
@if($auctionHistory->isNotEmpty())
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 mb-8">
            <span class="hidden sm:inline">Your</span> <span class="bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">Auction Stats</span>
        </h2>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white border-2 border-blue-200 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all">
                <div class="text-sm font-bold mb-2 text-gray-600 uppercase tracking-wide">Total Earnings</div>
                <div class="text-4xl font-black mb-2 text-blue-600">₹{{ number_format($auctionStats['total_value']/1000, 1) }}K</div>
                <div class="text-xs text-gray-600">Across {{ $auctionStats['times_sold'] }} auctions</div>
                                </div>
                                
            <div class="bg-white border-2 border-green-200 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all">
                <div class="text-sm font-bold mb-2 text-gray-600 uppercase tracking-wide">Highest Bid</div>
                <div class="text-4xl font-black mb-2 text-green-600">₹{{ number_format($auctionStats['highest_bid']/1000, 1) }}K</div>
                <div class="text-xs text-gray-600">Your top auction price</div>
            </div>
            
            <div class="bg-white border-2 border-purple-200 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all">
                <div class="text-sm font-bold mb-2 text-gray-600 uppercase tracking-wide">Average Price</div>
                <div class="text-4xl font-black mb-2 text-purple-600">₹{{ number_format($auctionStats['average_bid']/1000, 1) }}K</div>
                <div class="text-xs text-gray-600">Per auction</div>
            </div>
            
            <div class="bg-white border-2 border-orange-200 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all">
                <div class="text-sm font-bold mb-2 text-gray-600 uppercase tracking-wide">Times Sold</div>
                <div class="text-4xl font-black mb-2 text-orange-600">{{ $auctionStats['times_sold'] }}</div>
                <div class="text-xs text-gray-600">Successful auctions</div>
            </div>
        </div>

        <!-- Auction History Table -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900">Your Auction History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">League</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Team</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Game</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Sold Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($auctionHistory as $auction)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $auction->league->name }}</div>
                                <div class="text-sm text-gray-500">{{ $auction->league->localBody->district->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-900">
                                {{ $auction->leagueTeam->team->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                    {{ $auction->league->game->name }}
                                        </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-xl font-black text-green-600">
                                    ₹{{ number_format($auction->bid_price, 0) }}
                                    </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
                                @endif
                                
<!-- Upcoming Matches -->
@if($upcomingMatches->isNotEmpty())
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-8">
                                            <div>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 mb-2">
                    <span class="hidden sm:inline">Upcoming</span> <span class="bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">Matches</span>
                </h2>
                <p class="text-sm sm:text-base text-gray-600">Your scheduled fixtures</p>
                                            </div>
                                        </div>

        <div class="space-y-4">
            @foreach($upcomingMatches as $match)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-blue-700 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                            VS
                        </div>
                        <div>
                            <div class="font-black text-gray-900 text-lg">
                                {{ $match->homeTeam->team->name }} vs {{ $match->awayTeam->team->name }}
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ $match->league->name }} • {{ $match->match_type }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-6">
                        <div class="text-right">
                            <div class="text-sm text-gray-600">Match Date</div>
                            <div class="font-bold text-gray-900">{{ $match->match_date->format('M d, Y') }}</div>
                            <div class="text-sm text-gray-600">{{ $match->match_time ? $match->match_time : 'TBD' }}</div>
                        </div>
                        
                        @if($match->venue)
                        <div class="text-right">
                            <div class="text-sm text-gray-600">Venue</div>
                            <div class="font-bold text-gray-900">{{ $match->venue }}</div>
                                    </div>
                                @endif
                            </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Recent Match Results -->
@if($recentResults->isNotEmpty())
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-white">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 mb-8">
            <span class="hidden sm:inline">Recent</span> <span class="bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">Results</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($recentResults as $result)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3">
                    <div class="text-white text-sm font-medium">{{ $result->league->name }}</div>
                </div>
                            <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-1 text-center">
                            <div class="font-bold text-gray-900 mb-2">{{ $result->homeTeam->team->name }}</div>
                            <div class="text-4xl font-black text-gray-900">{{ $result->home_score ?? '-' }}</div>
                                    </div>
                        
                        <div class="px-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center font-black text-gray-400">
                                VS
                                    </div>
                                </div>
                                
                        <div class="flex-1 text-center">
                            <div class="font-bold text-gray-900 mb-2">{{ $result->awayTeam->team->name }}</div>
                            <div class="text-4xl font-black text-gray-900">{{ $result->away_score ?? '-' }}</div>
                                    </div>
                                </div>
                                
                    <div class="text-center pt-4 border-t border-gray-200">
                        <div class="text-sm text-gray-600">{{ $result->match_date->format('M d, Y') }}</div>
                        @if($result->venue)
                            <div class="text-sm text-gray-500">{{ $result->venue }}</div>
                        @endif
                                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
                                @endif
                                
<!-- My League Participations -->
@if($userLeagueParticipations->isNotEmpty())
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-white">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 mb-8">
            <span class="hidden sm:inline">My</span> <span class="bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">Leagues</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($userLeagueParticipations as $participation)
            <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="relative h-32 bg-gradient-to-br from-blue-600 via-blue-500 to-blue-700">
                    @if($participation->league->banner)
                        <img src="{{ Storage::url($participation->league->banner) }}" alt="{{ $participation->league->name }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    @endif
                    
                    <div class="absolute bottom-3 left-3 right-3">
                        <div class="font-black text-white text-lg">{{ $participation->league->name }}</div>
                        <div class="text-sm text-white/90">{{ $participation->league->game->name }}</div>
                                        </div>

                    <div class="absolute top-3 right-3">
                        <span class="px-3 py-1 {{ $participation->status === 'sold' ? 'bg-green-500' : ($participation->status === 'available' ? 'bg-blue-500' : 'bg-yellow-500') }} text-white text-xs font-bold rounded-full">
                            {{ ucfirst($participation->status) }}
                        </span>
                                                </div>
                </div>

                <div class="p-6">
                    @if($participation->leagueTeam)
                    <div class="mb-4">
                        <div class="text-sm text-gray-600 mb-1">Your Team</div>
                        <div class="font-bold text-gray-900">{{ $participation->leagueTeam->team->name }}</div>
                                                </div>
                                            @endif

                    @if($participation->bid_price)
                    <div class="mb-4">
                        <div class="text-sm text-gray-600 mb-1">Sold For</div>
                        <div class="text-2xl font-black text-green-600">₹{{ number_format($participation->bid_price, 0) }}</div>
                                    </div>
                                @endif
                                
                    <a href="{{ route('leagues.show', $participation->league) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2.5 px-4 rounded-lg font-bold shadow-lg hover:shadow-xl transition-all">
                        View League →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                    </div>
</section>
                @endif

<!-- Trending Leagues -->
@if($trendingLeagues->isNotEmpty())
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 mb-8">
            <span class="bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">Trending</span> <span class="hidden sm:inline">Leagues</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($trendingLeagues as $league)
            <div class="group bg-white border-2 border-gray-200 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center border-2 border-blue-300">
                        @if($league->logo)
                            <img src="{{ Storage::url($league->logo) }}" alt="{{ $league->name }}" class="w-12 h-12 rounded-lg">
                        @else
                            <span class="text-blue-600 font-black text-2xl">{{ substr($league->name, 0, 1) }}</span>
            @endif
                    </div>
                </div>

                <h3 class="text-2xl font-black mb-2 text-gray-900">{{ $league->name }}</h3>
                <p class="text-gray-600 mb-4 font-medium">{{ $league->game->name }}</p>

                <div class="flex items-center justify-between mb-6">
                    <div class="text-center">
                        <div class="text-2xl font-black text-blue-600">{{ $league->league_teams_count }}</div>
                        <div class="text-xs text-gray-600 font-semibold">Teams</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-black text-green-600">{{ $league->league_players_count }}</div>
                        <div class="text-xs text-gray-600 font-semibold">Players</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-black text-purple-600">{{ $league->season }}</div>
                        <div class="text-xs text-gray-600 font-semibold">Season</div>
                    </div>
                </div>

                <a href="{{ route('leagues.show', $league) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2.5 px-4 rounded-lg font-bold shadow-lg hover:shadow-xl transition-all block">
                    View Details →
                </a>
            </div>
            @endforeach
        </div>
        </div>
    </section>
    @endif

<!-- Owned Teams (if team owner) -->
@if($userOwnedTeams->isNotEmpty())
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-8">
                <div>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 mb-2">
                    <span class="hidden sm:inline">Your</span> <span class="bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">Teams</span>
                </h2>
                <p class="text-sm sm:text-base text-gray-600">Teams you own and manage</p>
                </div>
            <a href="{{ route('teams.create') }}" class="hidden sm:inline-flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-lg hover:shadow-xl transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                Create Team
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($userOwnedTeams as $team)
            <div class="bg-white border-2 border-gray-200 rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="relative h-40 bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700">
                        @if($team->banner)
                        <img src="{{ Storage::url($team->banner) }}" alt="{{ $team->name }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                        @endif
                        
                            <div class="absolute bottom-4 left-4 right-4">
                                <div class="flex items-center space-x-3">
                                    @if($team->logo)
                                <img src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }}" class="w-14 h-14 rounded-xl border-2 border-white shadow-lg">
                            @else
                                <div class="w-14 h-14 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center border-2 border-white">
                                    <span class="text-white font-black text-2xl">{{ substr($team->name, 0, 1) }}</span>
                                </div>
                                    @endif
                                    <div>
                                <div class="font-black text-white text-lg">{{ $team->name }}</div>
                                <div class="text-sm text-white/90">{{ $team->localBody->name }}</div>
                                    </div>
                                </div>
                            </div>

                    <div class="absolute top-4 right-4">
                        <span class="px-3 py-1 bg-blue-500 text-white text-xs font-bold rounded-full">Owner</span>
                    </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center p-3 bg-blue-50 border border-blue-200 rounded-xl">
                            <div class="text-2xl font-black text-blue-600">{{ $team->leagueTeams->count() }}</div>
                                <div class="text-xs text-gray-600 font-semibold">Leagues</div>
                            </div>
                            <div class="text-center p-3 bg-green-50 border border-green-200 rounded-xl">
                            <div class="text-2xl font-black text-green-600">{{ $team->leagueTeams->sum(function($lt) { return $lt->leaguePlayers->count(); }) }}</div>
                                <div class="text-xs text-gray-600 font-semibold">Players</div>
                            </div>
                        </div>
                        
                    <a href="{{ route('teams.show', $team) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2.5 px-4 rounded-lg font-bold shadow-lg hover:shadow-xl transition-all block">
                        Manage Team →
                        </a>
                                </div>
                </div>
            @endforeach
            </div>
        </div>
    </section>
                            @endif

<!-- Organized Leagues (if organizer) -->
@if($organizedLeagues->isNotEmpty())
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-8">
                <div>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 mb-2">
                    <span class="hidden sm:inline">Leagues You</span> <span class="bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">Organize</span>
                </h2>
                <p class="text-sm sm:text-base text-gray-600">Manage your organized leagues</p>
                            </div>
            <a href="{{ route('leagues.create') }}" class="hidden sm:inline-flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-lg hover:shadow-xl transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                Create League
                </a>
                            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($organizedLeagues as $league)
            <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="relative h-40 bg-gradient-to-br from-blue-600 via-blue-500 to-blue-700">
                    @if($league->banner)
                        <img src="{{ Storage::url($league->banner) }}" alt="{{ $league->name }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    @endif
                    
                    <div class="absolute bottom-4 left-4 right-4">
                        <div class="font-black text-white text-lg">{{ $league->name }}</div>
                        <div class="text-sm text-white/90">{{ $league->game->name }}</div>
                        </div>
                        
                        <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 {{ $league->status === 'active' ? 'bg-green-500' : ($league->status === 'pending' ? 'bg-yellow-500' : 'bg-gray-500') }} text-white text-xs font-bold rounded-full">
                            {{ ucfirst($league->status) }}
                            </span>
                    </div>
                </div>
                    
                    <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center p-3 bg-blue-50 border border-blue-200 rounded-xl">
                            <div class="text-2xl font-black text-blue-600">{{ $league->league_teams_count }}</div>
                            <div class="text-xs text-gray-600 font-semibold">Teams</div>
                    </div>
                        <div class="text-center p-3 bg-purple-50 border border-purple-200 rounded-xl">
                            <div class="text-2xl font-black text-purple-600">{{ $league->league_players_count }}</div>
                            <div class="text-xs text-gray-600 font-semibold">Players</div>
                            </div>
                        </div>
                        
                    <a href="{{ route('leagues.show', $league) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2.5 px-4 rounded-lg font-bold shadow-lg hover:shadow-xl transition-all block">
                        Manage League →
                    </a>
                </div>
                </div>
            @endforeach
            </div>
        </div>
    </section>
    @endif

<!-- Player Profile Summary -->
                @if($playerInfo)
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-7xl mx-auto">
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 mb-8">
            ⭐ <span class="hidden sm:inline">Your</span> <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Player Profile</span>
        </h2>

        <div class="bg-white border-2 border-gray-200 rounded-3xl overflow-hidden shadow-xl">
                    <div class="grid grid-cols-1 lg:grid-cols-3">
                <!-- Profile Card -->
                <div class="p-8 bg-gradient-to-br from-blue-500 to-blue-600">
                    <div class="text-center">
                        <div class="w-32 h-32 rounded-full overflow-hidden mx-auto mb-4 border-4 border-white shadow-2xl">
                                    @if($playerInfo->photo)
                                <img src="{{ asset('storage/' . $playerInfo->photo) }}" alt="{{ $playerInfo->name }}" class="w-full h-full object-cover">
                                    @else
                                <img src="{{ asset('images/defaultplayer.jpeg') }}" alt="{{ $playerInfo->name }}" class="w-full h-full object-cover">
                                    @endif
                </div>
                        <h3 class="text-2xl font-black text-white mb-2">{{ $playerInfo->name }}</h3>
                        <p class="text-blue-100 mb-4">{{ $playerInfo->position->name ?? 'No Game Role' }}</p>
                        
                        <div class="flex justify-center gap-2 mb-6">
                            <span class="px-3 py-1 bg-white/20 text-white text-xs font-bold rounded-full">Player</span>
                            <span class="px-3 py-1 bg-white/20 text-white text-xs font-bold rounded-full">{{ $playerInfo->position->name ?? 'No Role' }}</span>
                        </div>

                        <a href="{{ route('players.edit', $playerInfo) }}" class="inline-flex items-center px-6 py-2.5 bg-white hover:bg-gray-100 text-blue-600 rounded-lg font-bold shadow-lg hover:shadow-xl transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                            Edit Profile
                </a>
                                </div>
            </div>

                <!-- Stats -->
                <div class="lg:col-span-2 p-8 space-y-6 bg-gray-50">
                                    <div>
                        <h4 class="text-xl font-bold text-gray-900 mb-4">Player Statistics</h4>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-center p-4 bg-white rounded-xl border-2 border-blue-200 shadow-sm">
                                <div class="text-3xl font-black text-blue-600 mb-1">{{ $playerInfo->stats['leagues_joined'] }}</div>
                                <div class="text-sm text-gray-600 font-semibold">Leagues Joined</div>
                                    </div>
                            <div class="text-center p-4 bg-white rounded-xl border-2 border-green-200 shadow-sm">
                                <div class="text-3xl font-black text-green-600 mb-1">{{ $playerInfo->stats['leagues_active'] }}</div>
                                <div class="text-sm text-gray-600 font-semibold">Active Leagues</div>
                                </div>
                            <div class="text-center p-4 bg-white rounded-xl border-2 border-purple-200 shadow-sm">
                                <div class="text-3xl font-black text-purple-600 mb-1">{{ $playerInfo->stats['teams_played_for'] }}</div>
                                <div class="text-sm text-gray-600 font-semibold">Teams Played</div>
                            </div>
                            </div>
                        </div>
                        
                                    <div>
                        <h4 class="text-xl font-bold text-gray-900 mb-4">Profile Information</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between p-3 bg-white rounded-xl border border-gray-200">
                                <span class="text-gray-600 font-semibold">Email:</span>
                                <span class="text-gray-900 font-medium">{{ $playerInfo->email }}</span>
                                    </div>
                            <div class="flex justify-between p-3 bg-white rounded-xl border border-gray-200">
                                <span class="text-gray-600 font-semibold">Mobile:</span>
                                <span class="text-gray-900 font-medium">{{ $playerInfo->mobile }}</span>
                                </div>
                            <div class="flex justify-between p-3 bg-white rounded-xl border border-gray-200">
                                <span class="text-gray-600 font-semibold">Location:</span>
                                <span class="text-gray-900 font-medium">{{ $playerInfo->localBody->name ?? 'Not specified' }}</span>
                            </div>
                            <div class="flex justify-between p-3 bg-white rounded-xl border border-gray-200">
                                <span class="text-gray-600 font-semibold">Member Since:</span>
                                <span class="text-gray-900 font-medium">{{ $playerInfo->created_at->format('M Y') }}</span>
                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
                        @endif
                        
<!-- My Joined Leagues - Tabbed Interface -->
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-white">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 mb-8">
            <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Joined Leagues</span>
        </h2>

        <!-- Tabs Navigation -->
        <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-200 overflow-hidden">
            <div class="flex border-b-2 border-gray-200 overflow-x-auto">
                <button onclick="switchTab('organizer')" id="tab-organizer" class="tab-btn flex-1 min-w-[120px] px-4 sm:px-6 py-4 font-bold text-sm sm:text-base transition-all border-b-4 border-blue-600 text-blue-600 bg-blue-50">
                    <div class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                </svg>
                        <span>Organizer</span>
                            </div>
                </button>
                <button onclick="switchTab('team')" id="tab-team" class="tab-btn flex-1 min-w-[120px] px-4 sm:px-6 py-4 font-bold text-sm sm:text-base transition-all border-b-4 border-transparent text-gray-500 hover:bg-purple-50">
                    <div class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                </svg>
                        <span>Team Owner</span>
                            </div>
                </button>
                <button onclick="switchTab('player')" id="tab-player" class="tab-btn flex-1 min-w-[120px] px-4 sm:px-6 py-4 font-bold text-sm sm:text-base transition-all border-b-4 border-transparent text-gray-500 hover:bg-green-50">
                    <div class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        <span>Player</span>
                    </div>
                </button>
                        </div>
                        
            <!-- Tab Content: Organizer -->
            <div id="content-organizer" class="tab-content p-6">
                @if($organizedLeagues->isEmpty())
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Organized Leagues</h3>
                    <p class="text-gray-600 mb-6">You haven't organized any leagues yet.</p>
                    <a href="{{ route('leagues.create') }}" class="inline-flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-lg hover:shadow-xl transition-all">
                        Create Your First League
                        </a>
                    </div>
                @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($organizedLeagues as $league)
                    <div class="bg-white rounded-xl p-6 border-2 border-blue-200 hover:shadow-lg transition-all">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-black text-gray-900 mb-1">{{ $league->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $league->game->name }}</p>
                </div>
                            <span class="px-3 py-1 {{ $league->status === 'active' ? 'bg-green-500' : ($league->status === 'pending' ? 'bg-yellow-500' : 'bg-gray-500') }} text-white text-xs font-bold rounded-full">
                                {{ ucfirst($league->status) }}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="text-center bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <div class="text-xl font-black text-blue-600">{{ $league->league_teams_count }}</div>
                                <div class="text-xs text-gray-600 font-semibold">Teams</div>
                            </div>
                            <div class="text-center bg-purple-50 border border-purple-200 rounded-lg p-3">
                                <div class="text-xl font-black text-purple-600">{{ $league->league_players_count }}</div>
                                <div class="text-xs text-gray-600 font-semibold">Players</div>
                            </div>
                            <div class="text-center bg-green-50 border border-green-200 rounded-lg p-3">
                                <div class="text-xl font-black text-green-600">{{ $league->season }}</div>
                                <div class="text-xs text-gray-600 font-semibold">Season</div>
                            </div>
                        </div>

                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex items-center text-gray-700">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                                <span class="font-medium">{{ $league->start_date->format('M d') }} - {{ $league->end_date->format('M d, Y') }}</span>
                    </div>
                            @if($league->localBody)
                            <div class="flex items-center text-gray-700">
                                <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                                <span class="font-medium">{{ $league->localBody->name }}</span>
                            </div>
                            @endif
                        </div>

                        <a href="{{ route('leagues.show', $league) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2.5 rounded-lg font-bold shadow-lg hover:shadow-xl transition-all">
                            Manage League →
                    </a>
                </div>
                    @endforeach
            </div>
                @endif
        </div>

            <!-- Tab Content: Team Owner -->
            <div id="content-team" class="tab-content hidden p-6">
                @if($userOwnedTeams->isEmpty())
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Teams Owned</h3>
                    <p class="text-gray-600 mb-6">You don't own any teams yet.</p>
                    <a href="{{ route('teams.create') }}" class="inline-flex items-center px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold shadow-lg hover:shadow-xl transition-all">
                        Create Your First Team
                    </a>
                </div>
                @else
                <div class="space-y-4">
                    @foreach($userOwnedTeams as $team)
                    <div class="bg-white rounded-xl p-6 border-2 border-blue-200 shadow-sm hover:shadow-md transition-all">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-center space-x-4">
                                @if($team->logo)
                                    <img src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }}" class="w-16 h-16 rounded-xl object-cover border-2 border-blue-300">
                                @else
                                    <div class="w-16 h-16 rounded-xl bg-blue-600 flex items-center justify-center text-white font-black text-2xl border-2 border-blue-300">
                                        {{ substr($team->name, 0, 1) }}
                                    </div>
                @endif
                                
                                <div>
                                    <h3 class="text-xl font-black text-gray-900">{{ $team->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $team->localBody->name }}</p>
                                </div>
            </div>

                            <div class="flex flex-wrap gap-3">
                                <div class="text-center bg-white rounded-lg px-4 py-2">
                                    <div class="text-2xl font-black text-blue-600">{{ $team->leagueTeams->count() }}</div>
                                    <div class="text-xs text-gray-600">Leagues</div>
                                    </div>
                                <div class="text-center bg-white rounded-lg px-4 py-2">
                                    <div class="text-2xl font-black text-cyan-600">{{ $team->leagueTeams->sum(function($lt) { return $lt->leaguePlayers->count(); }) }}</div>
                                    <div class="text-xs text-gray-600">Total Players</div>
                                </div>
                                </div>
                                </div>

                        @if($team->leagueTeams->isNotEmpty())
                        <div class="mt-4 pt-4 border-t-2 border-gray-200">
                            <h4 class="text-sm font-bold text-gray-900 mb-3">Participating Leagues:</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($team->leagueTeams as $leagueTeam)
                                <div class="bg-gray-50 rounded-lg p-3 border-2 border-gray-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-bold text-gray-900">{{ $leagueTeam->league->name }}</span>
                                        <span class="px-2 py-1 bg-blue-500 text-white text-xs font-bold rounded">
                                            {{ $leagueTeam->leaguePlayers->count() }} Players
                                        </span>
                            </div>
                                    <div class="text-xs text-gray-700 font-semibold">
                                        Wallet: ₹{{ number_format($leagueTeam->wallet_balance, 0) }}
                            </div>
                        </div>
                                @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="mt-4">
                            <a href="{{ route('teams.show', $team) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-lg hover:shadow-xl transition-all">
                                Manage Team →
                                    </a>
                                </div>
                    </div>
                    @endforeach
                </div>
                @endif
                            </div>

            <!-- Tab Content: Player -->
            <div id="content-player" class="tab-content hidden p-6">
                @if($userLeagueParticipations->isEmpty())
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Player Participations</h3>
                    <p class="text-gray-600 mb-6">You haven't joined any leagues as a player yet.</p>
                    <a href="#available-leagues" class="inline-flex items-center px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold shadow-lg hover:shadow-xl transition-all">
                        Browse Available Leagues
                                </a>
                            </div>
                @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($userLeagueParticipations as $participation)
                    <div class="bg-white rounded-xl p-6 border-2 border-green-200 hover:shadow-lg transition-all">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-black text-gray-900 mb-1">{{ $participation->league->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $participation->league->game->name }}</p>
                        </div>
                            <span class="px-3 py-1 {{ $participation->status === 'sold' ? 'bg-green-500' : ($participation->status === 'available' ? 'bg-blue-500' : 'bg-yellow-500') }} text-white text-xs font-bold rounded-full">
                                {{ ucfirst($participation->status) }}
                            </span>
                    </div>

                        @if($participation->leagueTeam)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                            <div class="text-xs text-gray-600 mb-1 font-semibold">Your Team</div>
                            <div class="font-bold text-gray-900">{{ $participation->leagueTeam->team->name }}</div>
                        </div>
                        @endif

                        @if($participation->bid_price)
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-300 rounded-lg p-3 mb-4">
                            <div class="text-xs mb-1 text-gray-600 font-semibold">Sold Price</div>
                            <div class="text-2xl font-black text-green-600">₹{{ number_format($participation->bid_price, 0) }}</div>
                    </div>
                @endif

                        <div class="space-y-2 mb-4 text-sm">
                            @if($participation->base_price)
                            <div class="flex justify-between text-gray-700">
                                <span>Base Price:</span>
                                <span class="font-bold">₹{{ number_format($participation->base_price, 0) }}</span>
            </div>
                            @endif
                            <div class="flex justify-between text-gray-700">
                                <span>Joined:</span>
                                <span class="font-bold">{{ $participation->created_at->format('M d, Y') }}</span>
        </div>
                    </div>

                        <a href="{{ route('leagues.show', $participation->league) }}" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center py-2.5 rounded-lg font-bold shadow-lg hover:shadow-xl transition-all">
                            View League →
                        </a>
                    </div>
                    @endforeach
                </div>
                @endif
                </div>
            </div>
        </div>
    </section>

<script>
function switchTab(tab) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active styles from all tabs
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-blue-600', 'border-purple-600', 'border-green-600', 'text-blue-600', 'text-purple-600', 'text-green-600', 'bg-blue-50', 'bg-purple-50', 'bg-green-50');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tab).classList.remove('hidden');
    
    // Add active styles to selected tab
    const activeTab = document.getElementById('tab-' + tab);
    if (tab === 'organizer') {
        activeTab.classList.add('border-blue-600', 'text-blue-600', 'bg-blue-50');
    } else if (tab === 'team') {
        activeTab.classList.add('border-purple-600', 'text-purple-600', 'bg-purple-50');
    } else {
        activeTab.classList.add('border-green-600', 'text-green-600', 'bg-green-50');
    }
    activeTab.classList.remove('border-transparent', 'text-gray-500');
}
</script>

    <style>
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes slideIn { from { transform: translateX(-100%); } to { transform: translateX(0); } }
@keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
@keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }

/* Crypto Card Styles */
.stats-crypto-card {
    max-width: 100%;
    width: 100%;
    padding: 18px;
    border-radius: 30px;
    background: #ffffff;
    color: #1f2937;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(0, 0, 0, 0.05);
    border: 2px solid #e5e7eb;
}

@media (min-width: 640px) {
    .stats-crypto-card {
        max-width: 400px;
        padding: 25px;
        border-radius: 35px;
    }
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}

.card-logo {
    font-weight: bold;
    font-size: 1em;
    background: linear-gradient(to right, #6366f1, #a855f7);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

@media (min-width: 640px) {
    .card-logo {
        font-size: 1.2em;
    }
}

.card-open {
    font-size: 0.75em;
    color: #9ca3af;
    cursor: pointer;
    transition: all 0.3s ease;
}

@media (min-width: 640px) {
    .card-open {
        font-size: 0.85em;
    }
}

.card-open:hover {
    color: #6366f1;
}

.crypto-switch {
    position: relative;
    display: flex;
    background: #f3f4f6;
    border-radius: 20px;
    padding: 4px;
    gap: 4px;
    margin-bottom: 16px;
    user-select: none;
    border: 2px solid #d1d5db;
}

@media (min-width: 640px) {
    .crypto-switch {
        gap: 8px;
    }
}

.crypto-switch input {
    display: none;
}

.crypto-switch label {
    flex: 1;
    text-align: center;
    padding: 8px 4px;
    border-radius: 16px;
    cursor: pointer;
    color: #9ca3af;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    transition: all 0.3s;
    font-size: 0.75em;
    z-index: 2;
    position: relative;
}

@media (min-width: 640px) {
    .crypto-switch label {
        padding: 10px 0;
        gap: 6px;
        font-size: 0.9em;
    }
}

.crypto-switch input:checked + label {
    color: #ffffff;
}

.crypto-switch input:hover + label {
    color: #6b7280;
}

.crypto-switch .slider {
    position: absolute;
    top: 4px;
    bottom: 4px;
    width: calc((100% - 16px) / 3);
    background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
    backdrop-filter: blur(1px);
    border: 1px solid rgba(99, 102, 241, 0.3);
    border-radius: 16px;
    z-index: 1;
    transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    box-shadow:
        0 4px 12px rgba(99, 102, 241, 0.3),
        inset 0 -2px 4px rgba(255, 255, 255, 0.5);
}

.crypto-switch input:hover + label {
    color: #ffffff;
}

#leagues:checked ~ .slider {
    transform: translateX(0%);
}

#teams:checked ~ .slider {
    transform: translateX(108%);
}

#players:checked ~ .slider {
    transform: translateX(216%);
}

.price-infos {
    margin-bottom: 16px;
}

.price-info {
    display: none;
}

.price-info .stat-value {
    font-size: 2em;
    font-weight: bold;
    margin-bottom: 6px;
}

@media (min-width: 640px) {
    .price-info .stat-value {
        font-size: 2.5em;
    }
}

/* Color-coded stat values */
.stat-leagues {
    background: linear-gradient(to right, #a855f7, #d946ef);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-teams {
    background: linear-gradient(to right, #6366f1, #8b5cf6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-players {
    background: linear-gradient(to right, #ec4899, #f472b6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.price-info .stat-label {
    font-size: 0.85em;
    color: #6b7280;
    margin-bottom: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

@media (min-width: 640px) {
    .price-info .stat-label {
        font-size: 0.95em;
    }
}

.price-info .stats {
    display: flex;
    justify-content: space-between;
    font-size: 0.8em;
    color: #4b5563;
}

@media (min-width: 640px) {
    .price-info .stats {
        font-size: 0.9em;
    }
}

.price-info .change {
    font-weight: bold;
}

.change-purple {
    color: #a855f7;
}

.change-indigo {
    color: #6366f1;
}

.change-pink {
    color: #ec4899;
}

.chart {
    height: 70px;
    background: #f9fafb;
    border-radius: 12px;
    position: relative;
    overflow: hidden;
    width: 100%;
    border: 2px solid #e5e7eb;
}

@media (min-width: 640px) {
    .chart {
        height: 90px;
    }
}

.chart svg {
    width: 100%;
    height: 100%;
    display: none;
    transform: translateX(2px) scale(1.05);
}

.chart path {
    stroke: #a855f7;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-dasharray: 500;
    stroke-dashoffset: 500;
    animation: draw 2s forwards;
    fill: rgba(168, 85, 247, 0.2);
}

@keyframes draw {
    to {
        stroke-dashoffset: 0;
    }
}

/* Show chart based on selected tab */
.stats-crypto-card:has(#leagues:checked) .chart #chart-leagues {
    display: block;
}

.stats-crypto-card:has(#leagues:checked) .chart path {
    stroke: #a855f7;
    fill: rgba(168, 85, 247, 0.2);
}

.stats-crypto-card:has(#teams:checked) .chart #chart-teams {
    display: block;
}

.stats-crypto-card:has(#teams:checked) .chart path {
    stroke: #6366f1;
    fill: rgba(99, 102, 241, 0.2);
}

.stats-crypto-card:has(#players:checked) .chart #chart-players {
    display: block;
}

.stats-crypto-card:has(#players:checked) .chart path {
    stroke: #ec4899;
    fill: rgba(236, 72, 153, 0.2);
}

/* Show price info based on selected tab */
.stats-crypto-card:has(#leagues:checked) .price-info.leagues {
    display: block;
}

.stats-crypto-card:has(#teams:checked) .price-info.teams {
    display: block;
}

.stats-crypto-card:has(#players:checked) .price-info.players {
    display: block;
}

/* Futuristic 3D Animations */
@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

@keyframes shimmer-fast {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

@keyframes pulse-glow {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.9; }
}

@keyframes pulse-slow {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.1); }
}

@keyframes grid-move {
    0% { transform: translateY(0); }
    100% { transform: translateY(20px); }
}

@keyframes rotate-y {
    0% { transform: rotateY(0deg); }
    100% { transform: rotateY(360deg); }
}

.animate-fadeIn { animation: fadeIn 0.6s ease-in-out; }
.animate-fadeInUp { animation: fadeInUp 0.6s ease-in-out; }
.animate-slideIn { animation: slideIn 0.6s ease-in-out; }
.animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
.animate-bounce { animation: bounce 1s infinite; }
.animate-shimmer { animation: shimmer 3s ease-in-out infinite; }
.animate-shimmer-fast { animation: shimmer-fast 2s ease-in-out infinite; }
.animate-float { animation: float 3s ease-in-out infinite; }
.animate-pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }
.animate-pulse-slow { animation: pulse-slow 4s ease-in-out infinite; }
.animate-grid-move { animation: grid-move 2s linear infinite; }
.animation-delay-1000 { animation-delay: 1s; }

/* Custom Scrollbar */
::-webkit-scrollbar { width: 10px; height: 10px; }
::-webkit-scrollbar-track { background: #f3f4f6; border-radius: 10px; }
::-webkit-scrollbar-thumb { background: linear-gradient(to bottom, #3b82f6, #2563eb); border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background: linear-gradient(to bottom, #2563eb, #1d4ed8); }

/* Glassmorphism Effect */
.backdrop-blur-md { backdrop-filter: blur(12px); }
.backdrop-blur-xl { backdrop-filter: blur(24px); }

/* Tab Transition */
.tab-content { animation: fadeIn 0.3s ease-in-out; }

/* Responsive 3D Effects - Reduced on mobile for performance */
@media (prefers-reduced-motion: reduce) {
    .animate-shimmer,
    .animate-shimmer-fast,
    .animate-float,
    .animate-pulse-glow,
    .animate-pulse-slow,
    .animate-grid-move {
        animation: none;
    }
}
    </style>

@endsection
