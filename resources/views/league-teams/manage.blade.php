@extends('layouts.app')

@section('title', 'Manage Teams - ' . $league->name)

@section('content')

<!-- Hero Section -->
<section class="bg-gradient-to-br from-indigo-600 to-purple-600 py-8 px-4 sm:px-6 lg:px-8 text-white animate-fadeIn">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-4 drop-shadow">
                    {{ Auth::user()->isOrganizerForLeague($league->id) || Auth::user()->isAdmin() ? 'Manage Teams' : 'My Squad' }}
                </h1>
                <p class="text-lg sm:text-xl text-white/90">
                    {{ $league->name }} - {{ Auth::user()->isOrganizerForLeague($league->id) || Auth::user()->isAdmin() ? 'View and manage all teams and their players after auction' : 'View and manage your team squad with retention players' }}
                </p>
            </div>
            <a href="{{ route('leagues.show', $league) }}" 
               class="inline-flex items-center px-6 py-3 bg-white/20 hover:bg-white/30 text-white rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl backdrop-blur-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to League
            </a>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">


        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-4 lg:p-6 hover:shadow-xl transition-all duration-300 animate-fadeInUp">
                <div class="text-center">
                    <div class="w-12 h-12 lg:w-16 lg:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-3 lg:mb-4">
                        <svg class="h-6 w-6 lg:h-8 lg:w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-1">{{ $totalTeams }}</h3>
                    <p class="text-xs lg:text-sm text-gray-600 font-medium">Total Teams</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-4 lg:p-6 hover:shadow-xl transition-all duration-300 animate-fadeInUp">
                <div class="text-center">
                    <div class="w-12 h-12 lg:w-16 lg:h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-3 lg:mb-4">
                        <svg class="h-6 w-6 lg:h-8 lg:w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-1">{{ $totalSoldPlayers }}</h3>
                    <p class="text-xs lg:text-sm text-gray-600 font-medium">Sold Players</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-4 lg:p-6 hover:shadow-xl transition-all duration-300 animate-fadeInUp">
                <div class="text-center">
                    <div class="w-12 h-12 lg:w-16 lg:h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-3 lg:mb-4">
                        <svg class="h-6 w-6 lg:h-8 lg:w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-1">{{ $totalRetentionPlayers }}</h3>
                    <p class="text-xs lg:text-sm text-gray-600 font-medium">Retention Players</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-4 lg:p-6 hover:shadow-xl transition-all duration-300 animate-fadeInUp">
                <div class="text-center">
                    <div class="w-12 h-12 lg:w-16 lg:h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-3 lg:mb-4">
                        <svg class="h-6 w-6 lg:h-8 lg:w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-1">{{ $retentionLimit }}</h3>
                    <p class="text-xs lg:text-sm text-gray-600 font-medium">Retention Limit</p>
                </div>
            </div>
        </div>

        <!-- Teams Grid -->
        @if($teamsByTeam->count() > 1)
            <!-- Multiple Teams View -->
            <div class="space-y-8">
                @foreach($teamsByTeam as $teamId => $leagueTeamsForTeam)
                    @php
                        $team = $leagueTeamsForTeam->first()->team;
                        $totalPlayersForTeam = $leagueTeamsForTeam->sum(function($lt) { return $lt->leaguePlayers->count(); });
                        $totalRetentionForTeam = $leagueTeamsForTeam->sum(function($lt) { return $lt->leaguePlayers->where('retention', true)->count(); });
                        $totalSpentForTeam = $leagueTeamsForTeam->sum(function($lt) { return $lt->leaguePlayers->sum('bid_price'); });
                    @endphp
                    
                    <!-- Team Card -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl hover:scale-[1.01] transition-all duration-300 animate-fadeInUp">
                        <!-- Hero Image Section -->
                        <div class="relative h-32 sm:h-40 md:h-48 overflow-hidden">
                            <div class="w-full h-full bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 flex items-center justify-center">
                                <div class="text-center text-white px-4">
                                    @if($team->logo)
                                        <img src="{{ asset('storage/' . $team->logo) }}" 
                                             alt="{{ $team->name }} Logo" 
                                             class="w-12 h-12 sm:w-16 sm:h-16 md:w-20 md:h-20 rounded-full object-cover border-2 sm:border-4 border-white/30 mx-auto mb-2 sm:mb-4">
                                    @else
                                        <div class="w-12 h-12 sm:w-16 sm:h-16 md:w-20 md:h-20 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-2 sm:mb-4">
                                            <span class="text-white font-bold text-lg sm:text-2xl md:text-3xl">{{ substr($team->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <h3 class="text-lg sm:text-2xl md:text-3xl font-bold drop-shadow-lg truncate">{{ $team->name }}</h3>
                                    <p class="text-xs sm:text-sm opacity-90 drop-shadow truncate">
                                        @if($team->owners->count() > 0)
                                            {{ $team->owners->first()->name }}
                                        @else
                                            No Owner
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Wallet Balance Badge -->
                            <div class="absolute top-2 right-2 sm:top-4 sm:right-4">
                                <div class="bg-white/90 backdrop-blur-sm rounded-lg sm:rounded-xl px-2 py-1 sm:px-3 sm:py-2 text-center shadow-lg">
                                    <div class="text-xs sm:text-sm font-bold text-green-600">₹{{ number_format($leagueTeamsForTeam->first()->wallet_balance, 0) }}</div>
                                    <div class="text-xs text-gray-600 hidden sm:block">Balance</div>
                                </div>
                            </div>
                            
                            <!-- Auctioneer Badge -->
                            @if($leagueTeamsForTeam->first()->auctioneer)
                                <div class="absolute bottom-2 left-2 right-2 sm:bottom-4 sm:left-4 sm:right-4">
                                    <div class="bg-white/90 backdrop-blur-sm rounded-lg sm:rounded-xl px-2 py-1 sm:px-3 sm:py-2 shadow-lg">
                                        <div class="flex items-center">
                                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <span class="text-xs sm:text-sm font-medium text-gray-700 truncate">Auctioneer: {{ $leagueTeamsForTeam->first()->auctioneer->name }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Content Section -->
                        <div class="p-3 sm:p-4 md:p-6">
                            <!-- Quick Stats -->
                            <div class="grid grid-cols-3 gap-2 sm:gap-4 mb-4 sm:mb-6">
                                <div class="text-center p-2 sm:p-3 md:p-4 bg-gray-50 rounded-lg sm:rounded-xl">
                                    <div class="text-lg sm:text-xl md:text-2xl font-bold text-indigo-600">{{ $totalPlayersForTeam }}</div>
                                    <div class="text-xs text-gray-600">Players</div>
                                </div>
                                <div class="text-center p-2 sm:p-3 md:p-4 bg-gray-50 rounded-lg sm:rounded-xl">
                                    <div class="text-lg sm:text-xl md:text-2xl font-bold text-purple-600">{{ $totalRetentionForTeam }}</div>
                                    <div class="text-xs text-gray-600">Retention</div>
                                </div>
                                <div class="text-center p-2 sm:p-3 md:p-4 bg-gray-50 rounded-lg sm:rounded-xl">
                                    <div class="text-lg sm:text-xl md:text-2xl font-bold text-green-600">₹{{ number_format($totalSpentForTeam/1000, 0) }}K</div>
                                    <div class="text-xs text-gray-600">Spent</div>
                                </div>
                            </div>

                            <!-- Players List -->
                            <div class="mb-4 sm:mb-6">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3 sm:mb-4 gap-3">
                                    <h4 class="text-base sm:text-lg font-semibold text-gray-900 flex items-center">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                        Squad Players
                                    </h4>
                                    @php
                                        // Always show button if there are any available players
                                        $hasAvailableRetentionPlayers = $availableRetentionPlayers->count() > 0;
                                        $firstLeagueTeamId = $leagueTeamsForTeam->first()->id;
                                        
                                    @endphp
                                    @if($hasAvailableRetentionPlayers)
                                        <button onclick="openAddRetentionModal({{ $firstLeagueTeamId }}, '{{ $team->name }}')" 
                                                class="inline-flex items-center px-3 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Add Retention
                                        </button>
                                    @endif
                                </div>
                                @if($totalPlayersForTeam > 0)
                                    <div class="space-y-2 sm:space-y-3">
                                        @foreach($leagueTeamsForTeam as $leagueTeam)
                                            @foreach($leagueTeam->leaguePlayers as $player)
                                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-3 sm:p-4 bg-white border border-gray-200 rounded-lg sm:rounded-xl hover:shadow-md transition-all duration-200 {{ $player->retention ? 'ring-2 ring-purple-200 bg-purple-50' : '' }} gap-3 sm:gap-0">
                                                    <div class="flex items-center">
                                                        @if($player->player->image)
                                                            <img src="{{ asset('storage/' . $player->player->image) }}" 
                                                                 alt="{{ $player->player->name }}" 
                                                                 class="h-10 w-10 sm:h-12 sm:w-12 rounded-full object-cover border-2 border-gray-200">
                                                        @else
                                                            <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-gradient-to-br from-gray-400 to-gray-500 flex items-center justify-center border-2 border-gray-200">
                                                                <span class="text-white font-semibold text-sm sm:text-base">{{ substr($player->player->name, 0, 1) }}</span>
                                                            </div>
                                                        @endif
                                                        <div class="ml-3 sm:ml-4">
                                                            <p class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $player->player->name }}</p>
                                                            <p class="text-xs sm:text-sm text-gray-600">{{ $player->player->position->name }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 gap-2 sm:gap-0">
                                                        @if(!$player->retention)
                                                            <div class="text-left sm:text-right">
                                                                <p class="font-bold text-sm sm:text-base lg:text-lg text-gray-900">₹{{ number_format($player->bid_price, 0) }}</p>
                                                            </div>
                                                        @endif
                                                        <div class="flex items-center space-x-1 sm:space-x-2">
                                                            @if($player->retention)
                                                                <span class="inline-flex items-center px-2 py-1 sm:px-3 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                    </svg>
                                                                    <span class="hidden sm:inline">Retention</span>
                                                                    <span class="sm:hidden">R</span>
                                                                </span>
                                                            @endif
                                                            <button onclick="toggleRetention({{ $player->id }}, {{ $player->retention ? 'true' : 'false' }})"
                                                                    class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1.5 rounded-lg text-xs font-medium transition-all duration-200 {{ $player->retention ? 'bg-red-100 text-red-700 hover:bg-red-200 border border-red-200' : 'bg-purple-100 text-purple-700 hover:bg-purple-200 border border-purple-200' }}">
                                                                @if($player->retention)
                                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                    <span class="hidden sm:inline">Remove</span>
                                                                    <span class="sm:hidden">×</span>
                                                                @else
                                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                    </svg>
                                                                    <span class="hidden sm:inline">Mark Retention</span>
                                                                    <span class="sm:hidden">+</span>
                                                                @endif
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-12 text-gray-500">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No players in squad</h3>
                                        <p class="text-gray-600">Players will appear here after auction</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Single Team View (Dashboard Style) -->
            <div class="space-y-8">
                @foreach($leagueTeams as $leagueTeam)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl hover:scale-[1.01] transition-all duration-300 animate-fadeInUp">
                        <!-- Hero Image Section -->
                        <div class="relative h-48 overflow-hidden">
                            <div class="w-full h-full bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 flex items-center justify-center">
                                <div class="text-center text-white">
                                    @if($leagueTeam->team->logo)
                                        <img src="{{ asset('storage/' . $leagueTeam->team->logo) }}" 
                                             alt="{{ $leagueTeam->team->name }} Logo" 
                                             class="w-20 h-20 rounded-full object-cover border-4 border-white/30 mx-auto mb-4">
                                    @else
                                        <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-4">
                                            <span class="text-white font-bold text-3xl">{{ substr($leagueTeam->team->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <h3 class="text-3xl font-bold drop-shadow-lg">{{ $leagueTeam->team->name }}</h3>
                                    <p class="text-sm opacity-90 drop-shadow">
                                        @if($leagueTeam->team->owners->count() > 0)
                                            {{ $leagueTeam->team->owners->first()->name }}
                                        @else
                                            No Owner
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Wallet Balance Badge -->
                            <div class="absolute top-4 right-4">
                                <div class="bg-white/90 backdrop-blur-sm rounded-xl px-3 py-2 text-center shadow-lg">
                                    <div class="text-sm font-bold text-green-600">₹{{ number_format($leagueTeam->wallet_balance, 0) }}</div>
                                    <div class="text-xs text-gray-600">Balance</div>
                                </div>
                            </div>
                            
                            <!-- Auctioneer Badge -->
                            @if($leagueTeam->auctioneer)
                                <div class="absolute bottom-4 left-4 right-4">
                                    <div class="bg-white/90 backdrop-blur-sm rounded-xl px-3 py-2 shadow-lg">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-700">Auctioneer: {{ $leagueTeam->auctioneer->name }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Content Section -->
                        <div class="p-6">
                            <!-- Quick Stats -->
                            <div class="grid grid-cols-3 gap-4 mb-6">
                                <div class="text-center p-4 bg-gray-50 rounded-xl">
                                    <div class="text-2xl font-bold text-indigo-600">{{ $leagueTeam->leaguePlayers->count() }}</div>
                                    <div class="text-xs text-gray-600">Players</div>
                                </div>
                                <div class="text-center p-4 bg-gray-50 rounded-xl">
                                    <div class="text-2xl font-bold text-purple-600">{{ $leagueTeam->leaguePlayers->where('retention', true)->count() }}</div>
                                    <div class="text-xs text-gray-600">Retention</div>
                                </div>
                                <div class="text-center p-4 bg-gray-50 rounded-xl">
                                    <div class="text-2xl font-bold text-green-600">₹{{ number_format($leagueTeam->leaguePlayers->sum('bid_price')/1000, 0) }}K</div>
                                    <div class="text-xs text-gray-600">Spent</div>
                                </div>
                            </div>

                            <!-- Players List -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                        Squad Players
                                    </h4>
                                    @php
                                        // Always show button if there are any available players
                                        $showAddButton = $availableRetentionPlayers->count() > 0;
                                    @endphp
                                    @if($showAddButton)
                                        <button onclick="openAddRetentionModal({{ $leagueTeam->id }}, '{{ $leagueTeam->team->name }}')" 
                                                class="inline-flex items-center px-3 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Add Retention
                                        </button>
                                    @endif
                                </div>
                                @if($leagueTeam->leaguePlayers->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($leagueTeam->leaguePlayers as $player)
                                            <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-xl hover:shadow-md transition-all duration-200 {{ $player->retention ? 'ring-2 ring-purple-200 bg-purple-50' : '' }}">
                                                <div class="flex items-center">
                                                    @if($player->player->image)
                                                        <img src="{{ asset('storage/' . $player->player->image) }}" 
                                                             alt="{{ $player->player->name }}" 
                                                             class="h-12 w-12 rounded-full object-cover border-2 border-gray-200">
                                                    @else
                                                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-gray-400 to-gray-500 flex items-center justify-center border-2 border-gray-200">
                                                            <span class="text-white font-semibold">{{ substr($player->player->name, 0, 1) }}</span>
                                                        </div>
                                                    @endif
                                                    <div class="ml-4">
                                                        <p class="font-semibold text-gray-900">{{ $player->player->name }}</p>
                                                        <p class="text-sm text-gray-600">{{ $player->player->position->name }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-3">
                                                    <div class="text-right">
                                                        <p class="font-bold text-lg text-gray-900">₹{{ number_format($player->bid_price, 0) }}</p>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        @if($player->retention)
                                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                                Retention
                                                            </span>
                                                        @endif
                                                        <button onclick="toggleRetention({{ $player->id }}, {{ $player->retention ? 'true' : 'false' }})"
                                                                class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 {{ $player->retention ? 'bg-red-100 text-red-700 hover:bg-red-200 border border-red-200' : 'bg-purple-100 text-purple-700 hover:bg-purple-200 border border-purple-200' }}">
                                                            @if($player->retention)
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                                Remove
                                                            @else
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                                Mark Retention
                                                            @endif
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-12 text-gray-500">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No players in squad</h3>
                                        <p class="text-gray-600">Players will appear here after auction</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if($leagueTeams->count() === 0)
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center animate-fadeInUp">
                <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">No teams found</h3>
                <p class="text-gray-600 mb-8">There are no teams registered in this league yet.</p>
                <a href="{{ route('league-teams.create', $league) }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Team to League
                </a>
            </div>
        @endif

    </div>
</section>

<!-- Add Retention Player Modal -->
<div id="addRetentionModal" class="fixed inset-0 bg-white bg-opacity-90 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center p-2 sm:p-4">
    <div class="white-glass-card relative w-full max-w-2xl mx-auto p-4 sm:p-6 md:p-8 animate-fadeInUp">
        <!-- Close Button -->
        <button onclick="closeAddRetentionModal()" class="absolute top-2 right-2 sm:top-4 sm:right-4 text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center mb-4 shadow-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2" id="addRetentionModalTitle">Add Retention Player</h3>
            <p class="text-sm text-gray-600" id="addRetentionInfo"></p>
        </div>
        
        <!-- Player Selection Section -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-3">Search Available Players</label>
            <div class="relative">
                <input type="text" id="playerRetentionSearch" placeholder="Type to search players..." 
                       class="white-glass-input w-full px-4 py-3 rounded-xl border-0 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-200 text-gray-800 placeholder-gray-500">
                <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            <div id="retentionSearchResults" class="mt-3 max-h-64 overflow-y-auto white-glass-card hidden">
                <!-- Search results will be populated here -->
            </div>
        </div>
        
        <!-- Selected Player Display -->
        <div id="selectedPlayerDisplay" class="mb-6 hidden">
            <label class="block text-sm font-semibold text-gray-700 mb-3">Selected Player</label>
            <div id="selectedPlayerInfo" class="white-glass-card p-4 rounded-xl">
                <!-- Selected player info will be displayed here -->
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex space-x-4">
            <button onclick="closeAddRetentionModal()" 
                    class="flex-1 white-glass-button px-6 py-3 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition-all duration-200">
                Cancel
            </button>
            <button id="addRetentionButton" onclick="confirmAddRetention()" 
                    class="flex-1 white-glass-button px-6 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-purple-700 font-semibold hover:from-purple-700 hover:to-purple-800 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg" disabled>
                <span class="flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add as Retention Player
                </span>
            </button>
        </div>
    </div>
</div>

<script>
function toggleRetention(playerId, currentRetention) {
    if (!confirm(currentRetention ? 'Are you sure you want to remove this player from retention?' : 'Are you sure you want to mark this player as retention?')) {
        return;
    }

    fetch(`/leagues/{{ $league->slug }}/players/${playerId}/toggle-retention`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification(data.message, 'success');
            // Reload the page to update the UI
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'Failed to update retention status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while updating retention status', 'error');
    });
}

// Add Retention Player Modal Functions
let currentLeagueTeamId = null;
let selectedRetentionPlayerId = null;
let selectedRetentionPlayerData = null;

function openAddRetentionModal(leagueTeamId, teamName) {
    currentLeagueTeamId = leagueTeamId;
    
    document.getElementById('addRetentionModalTitle').textContent = 'Add Retention Player';
    document.getElementById('addRetentionInfo').textContent = `Add retention player to team: ${teamName}`;
    
    // Reset search input
    const searchInput = document.getElementById('playerRetentionSearch');
    searchInput.value = '';
    searchInput.classList.remove('bg-purple-50', 'border-purple-300');
    
    // Hide search results and selected player display
    document.getElementById('retentionSearchResults').classList.add('hidden');
    document.getElementById('selectedPlayerDisplay').classList.add('hidden');
    document.getElementById('addRetentionButton').disabled = true;
    selectedRetentionPlayerId = null;
    selectedRetentionPlayerData = null;
    
    // Show modal
    document.getElementById('addRetentionModal').classList.remove('hidden');
}

function closeAddRetentionModal() {
    document.getElementById('addRetentionModal').classList.add('hidden');
    currentLeagueTeamId = null;
    selectedRetentionPlayerId = null;
    selectedRetentionPlayerData = null;
}

function confirmAddRetention() {
    if (!selectedRetentionPlayerId) {
        alert('Please select a player first.');
        return;
    }
    
    if (!confirm('Are you sure you want to add this player as a retention player?')) {
        return;
    }
    
    const addButton = document.getElementById('addRetentionButton');
    const originalText = addButton.innerHTML;
    
    addButton.disabled = true;
    addButton.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Adding...';
    
    fetch(`/leagues/{{ $league->slug }}/players/add-retention`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            league_player_id: selectedRetentionPlayerId,
            league_team_id: currentLeagueTeamId
        })
    })
    .then(response => {
        // Debug logging removed for production
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeAddRetentionModal();
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'Failed to add retention player', 'error');
            addButton.disabled = false;
            addButton.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while adding retention player', 'error');
        addButton.disabled = false;
        addButton.innerHTML = originalText;
    });
}

// Search functionality for retention players
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('playerRetentionSearch');
    const searchResults = document.getElementById('retentionSearchResults');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            if (query.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }
            
            // Filter available players based on search query
            const availablePlayers = @json($availableRetentionPlayers);
            const filteredPlayers = availablePlayers.filter(player => {
                const playerName = player.player?.name || '';
                const positionName = player.player?.position?.name || '';
                return playerName.toLowerCase().includes(query.toLowerCase()) ||
                       positionName.toLowerCase().includes(query.toLowerCase());
            });
            
            displayRetentionSearchResults(filteredPlayers);
        });
    }
});

function displayRetentionSearchResults(players) {
    const searchResults = document.getElementById('retentionSearchResults');
    
    if (players.length === 0) {
        searchResults.innerHTML = '<div class="p-4 text-center text-gray-500">No players found</div>';
    } else {
        searchResults.innerHTML = players.map(player => {
            const playerName = player.player?.name || 'Unknown Player';
            const positionName = player.player?.position?.name || 'No Position';
            const teamStatus = player.league_team_id ? 'Assigned' : 'Unassigned';
            
            return `
                <div class="p-4 border-b border-gray-200 hover:bg-purple-50 cursor-pointer transition-all duration-200 hover:shadow-sm" 
                     onclick="selectRetentionPlayer(${player.id}, '${playerName}', '${positionName}', '${player.league_team_id || 'Unassigned'}')">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center mr-4 shadow-md">
                                <span class="text-white font-semibold text-lg">${playerName.charAt(0)}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-lg">${playerName}</p>
                                <p class="text-sm text-gray-600">${positionName}</p>
                                <p class="text-xs text-purple-600 font-medium">${teamStatus}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }
    
    searchResults.classList.remove('hidden');
}

function selectRetentionPlayer(playerId, playerName, positionName, teamStatus) {
    // Find the full player data
    const availablePlayers = @json($availableRetentionPlayers);
    const playerData = availablePlayers.find(p => p.id === playerId);
    
    if (!playerData) {
        console.error('Player data not found for ID:', playerId);
        return;
    }
    
    selectedRetentionPlayerId = playerId;
    selectedRetentionPlayerData = playerData;
    
    // Update search input
    const searchInput = document.getElementById('playerRetentionSearch');
    searchInput.value = `${playerName} - ${positionName}`;
    searchInput.classList.add('bg-purple-50', 'border-purple-300');
    
    // Hide search results
    document.getElementById('retentionSearchResults').classList.add('hidden');
    
    // Show selected player display
    displaySelectedPlayer(playerData);
    
    // Enable add button
    document.getElementById('addRetentionButton').disabled = false;
}

function displaySelectedPlayer(playerData) {
    const selectedPlayerDisplay = document.getElementById('selectedPlayerDisplay');
    const selectedPlayerInfo = document.getElementById('selectedPlayerInfo');
    
    const playerName = playerData.player?.name || 'Unknown Player';
    const positionName = playerData.player?.position?.name || 'No Position';
    const teamStatus = playerData.league_team_id ? 'Currently Assigned' : 'Unassigned Player';
    const statusClass = playerData.league_team_id ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
    
    selectedPlayerInfo.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center mr-4 shadow-lg">
                    <span class="text-white font-bold text-xl">${playerName.charAt(0)}</span>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-lg">${playerName}</h4>
                    <p class="text-gray-600">${positionName}</p>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${statusClass}">
                        ${teamStatus}
                    </span>
                </div>
            </div>
            <button onclick="clearSelection()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    selectedPlayerDisplay.classList.remove('hidden');
}

function clearSelection() {
    selectedRetentionPlayerId = null;
    selectedRetentionPlayerData = null;
    
    // Reset search input
    const searchInput = document.getElementById('playerRetentionSearch');
    searchInput.value = '';
    searchInput.classList.remove('bg-purple-50', 'border-purple-300');
    
    // Hide selected player display
    document.getElementById('selectedPlayerDisplay').classList.add('hidden');
    
    // Disable add button
    document.getElementById('addRetentionButton').disabled = true;
}

// Close modal when clicking outside
document.getElementById('addRetentionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddRetentionModal();
    }
});

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
        type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success' 
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                }
            </svg>
            <span class="font-medium">${message}</span>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>

<style>
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fadeIn { animation: fadeIn 0.5s ease-in-out; }
.animate-fadeInUp { animation: fadeInUp 0.4s ease-in-out; }

/* White Glassmorphism Styles */
.white-glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.1),
        0 4px 16px rgba(0, 0, 0, 0.05),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
    border-radius: 20px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.white-glass-card:hover {
    background: rgba(255, 255, 255, 0.98);
    box-shadow: 
        0 16px 48px rgba(0, 0, 0, 0.15),
        0 8px 24px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
    transform: translateY(-2px);
}

.white-glass-button {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    box-shadow: 
        0 4px 16px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
    transition: all 0.3s ease;
}

.white-glass-button:hover {
    background: rgba(255, 255, 255, 0.9);
    box-shadow: 
        0 6px 20px rgba(0, 0, 0, 0.15),
        inset 0 1px 0 rgba(255, 255, 255, 1);
    transform: translateY(-1px);
}

.white-glass-input {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    box-shadow: 
        0 4px 16px rgba(0, 0, 0, 0.05),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
    transition: all 0.3s ease;
}

.white-glass-input:focus {
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 
        0 6px 20px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
    transform: translateY(-1px);
}
</style>
@endsection
