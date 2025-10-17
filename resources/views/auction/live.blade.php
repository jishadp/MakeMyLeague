@extends('layouts.app')

@section('title', 'Live Auction - ' . $league->name)

@section('styles')
<link rel="stylesheet" href="{{ asset('css/auction.css') }}">
<style>
    .live-indicator {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .team-balance {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .bid-card {
        transition: all 0.3s ease;
    }
    .bid-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    /* Glassmorphism Card Styles */
    .card-container {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .card__content {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    
    .stat-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    /* Blob Animation */
    @keyframes blob {
        0% {
            transform: translate(0px, 0px) scale(1);
        }
        33% {
            transform: translate(30px, -50px) scale(1.1);
        }
        66% {
            transform: translate(-20px, 20px) scale(0.9);
        }
        100% {
            transform: translate(0px, 0px) scale(1);
        }
    }
    
    .animate-blob {
        animation: blob 7s infinite;
    }
    
    .blob {
        filter: blur(40px);
    }
</style>
@endsection

@section('content')
<input type="hidden" id="league-id" value="{{ $league->id }}">
<input type="hidden" id="league-slug" value="{{ $league->slug }}">
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex items-center space-x-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $league->name }}</h1>
                            <p class="text-gray-600">{{ $league->game->name }} League</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-red-500 rounded-full live-indicator"></div>
                            <span class="text-red-600 font-semibold">LIVE</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Last Updated</p>
                            <p class="text-sm font-semibold text-gray-900" id="lastUpdated">{{ now()->format('H:i:s') }}</p>
                        </div>
                        <a href="{{ route('auctions.index') }}" 
                           class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                            Back to Auctions
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Player Being Auctioned -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Current Player Card -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Current Player</h2>
                    <div id="currentPlayerCard" class="text-center py-8">
                        @if(isset($currentPlayer) && $currentPlayer)
                            <!-- Glassmorphism Card Style -->
                            <div class="card-container relative rounded-3xl overflow-hidden shadow-2xl">
                                <!-- Animated Background Blobs -->
                                <div class="absolute inset-0 overflow-hidden rounded-3xl">
                                    <div class="blob absolute -left-12 -top-24 w-32 h-32 sm:w-48 sm:h-48 bg-orange-400 rounded-full opacity-60 animate-blob"></div>
                                    <div class="blob absolute right-8 sm:right-24 -top-6 w-32 h-32 sm:w-48 sm:h-48 bg-purple-500 rounded-full opacity-60 animate-blob" style="animation-delay: 1s;"></div>
                                    <div class="blob absolute -left-10 top-32 sm:top-24 w-32 h-32 sm:w-48 sm:h-48 bg-pink-500 rounded-full opacity-60 animate-blob" style="animation-delay: 2s;"></div>
                                    <div class="blob absolute right-8 sm:right-24 bottom-8 sm:top-44 w-32 h-32 sm:w-48 sm:h-48 bg-blue-500 rounded-full opacity-60 animate-blob" style="animation-delay: 3s;"></div>
                                </div>

                                <!-- Glassmorphism Content -->
                                <div class="card__content relative z-10 p-6 sm:p-8 lg:p-10">
                                    <!-- Player Header -->
                                    <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-6 sm:space-y-0 sm:space-x-8 mb-8">
                                        <div class="relative flex-shrink-0">
                                            <div class="w-24 h-24 sm:w-28 sm:h-28 lg:w-32 lg:h-32 rounded-3xl overflow-hidden bg-white bg-opacity-20 flex items-center justify-center ring-4 ring-blue-200 ring-opacity-50 shadow-2xl">
                                                @if($currentPlayer->player && $currentPlayer->player->photo)
                                                    <img src="{{ asset('storage/' . $currentPlayer->player->photo) }}"
                                                         alt="{{ $currentPlayer->player->name }}"
                                                         class="w-full h-full object-cover rounded-3xl"
                                                         onerror="handleImageError(this);">
                                                @else
                                                    <img src="{{ asset('images/defaultplayer.jpeg') }}"
                                                         alt="Player"
                                                         class="w-full h-full object-cover rounded-3xl"
                                                         onerror="handleImageError(this);">
                                                @endif
                                                <!-- Fallback avatar when image fails -->
                                                <div class="w-full h-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl hidden rounded-3xl">
                                                    {{ strtoupper(substr($currentPlayer->player->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg">
                                                <svg class="w-4 h-4 text-yellow-800" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="text-center sm:text-left flex-grow">
                                            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-3 text-gray-800 playerName">
                                                {{ $currentPlayer->player->name }}
                                            </h2>
                                            <div class="flex flex-wrap gap-2 justify-center sm:justify-start mb-4">
                                                <span class="bg-blue-500 bg-opacity-20 backdrop-blur-sm px-4 py-2 rounded-2xl text-sm font-semibold text-blue-700 border border-blue-300 position">
                                                    @if($currentPlayer->player->primaryGameRole && $currentPlayer->player->primaryGameRole->gamePosition)
                                                        {{ $currentPlayer->player->primaryGameRole->gamePosition->name }}
                                                    @elseif($currentPlayer->player->position)
                                                        {{ $currentPlayer->player->position->name }}
                                                    @else
                                                        Player
                                                    @endif
                                                </span>
                                                <span class="bg-green-500 bg-opacity-20 backdrop-blur-sm px-4 py-2 rounded-2xl text-sm font-semibold text-green-700 border border-green-300">
                                                    Base Price ₹<span class="basePrice">{{ $currentPlayer->base_price ?? 0 }}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Current Bid Row -->
                                    <div class="stat-card bg-white bg-opacity-80 backdrop-blur-sm rounded-2xl p-4 sm:p-6 border border-blue-200 shadow-lg mb-6">
                                        <!-- Current Bid (Main Highlight) -->
                                        <div class="text-center mb-4">
                                            <p class="text-gray-600 text-xs sm:text-sm font-medium mb-2 bidStatus">
                                                @if(isset($currentHighestBid) && $currentHighestBid)
                                                    Current Bid
                                                @else
                                                    Base Price
                                                @endif
                                            </p>
                                            <p class="font-bold text-2xl sm:text-3xl lg:text-4xl text-blue-600 mb-2">₹ <span class="currentBid">
                                                {{ isset($currentHighestBid) && $currentHighestBid ? $currentHighestBid->amount : $currentPlayer->base_price }}
                                            </span></p>
                                            <p class="text-gray-600 text-sm font-medium bidTeam">
                                                @if(isset($currentHighestBid) && $currentHighestBid && $currentHighestBid->leagueTeam && $currentHighestBid->leagueTeam->team)
                                                    Leading: {{ $currentHighestBid->leagueTeam->team->name }}
                                                @else
                                                    Awaiting new bids..
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-lg">Waiting for next player...</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Team Cards with Players -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        Teams ({{ count($teams) }})
                    </h2>
                    <div class="space-y-4" id="teamsContainer">
                        @foreach($teams as $team)
                        <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                            <!-- Team Header -->
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-3 text-white">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center font-bold backdrop-blur-sm">
                                            {{ strtoupper(substr($team->team->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <h3 class="font-bold">{{ $team->team->name }}</h3>
                                            <p class="text-xs text-blue-100">{{ $team->league_players_count }} Players</p>
                                            @if($team->teamAuctioneer && $team->teamAuctioneer->auctioneer)
                                                <p class="text-xs text-blue-200">
                                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    Auctioneer: {{ $team->teamAuctioneer->auctioneer->name }}
                                                </p>
                                            @elseif($team->auctioneer)
                                                <p class="text-xs text-blue-200">
                                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    Auctioneer: {{ $team->auctioneer->name }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold">₹{{ number_format($team->wallet_balance) }}</p>
                                        <p class="text-xs text-blue-100">Balance</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Players List -->
                            @if($team->leaguePlayers && $team->leaguePlayers->count() > 0)
                            <div class="p-2 bg-gray-50 max-h-48 overflow-y-auto">
                                <div class="space-y-1">
                                    @foreach($team->leaguePlayers as $index => $leaguePlayer)
                                    <div class="flex items-center justify-between p-2 bg-white rounded text-xs">
                                        <div class="flex items-center space-x-2">
                                            <div class="flex items-center justify-center w-5 h-5 bg-blue-600 text-white rounded-full text-xs font-bold flex-shrink-0">
                                                {{ $index + 1 }}
                                            </div>
                                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                                {{ strtoupper(substr($leaguePlayer->player->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $leaguePlayer->player->name }}</p>
                                                <p class="text-xs text-gray-500">
                                                    @if($leaguePlayer->player->primaryGameRole && $leaguePlayer->player->primaryGameRole->gamePosition)
                                                        {{ $leaguePlayer->player->primaryGameRole->gamePosition->name }}
                                                    @elseif($leaguePlayer->player->position)
                                                        {{ $leaguePlayer->player->position->name }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            @if($leaguePlayer->status === 'retained')
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    ⭐ Retained
                                                </span>
                                            @else
                                                <p class="text-xs font-bold text-green-600">₹{{ number_format($leaguePlayer->bid_price ?? 0) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @else
                            <div class="p-3 text-center text-gray-500 text-xs">
                                No players yet
                            </div>
                            @endif

                            <!-- Team Stats Footer -->
                            <div class="bg-gray-100 px-3 py-1.5 flex items-center justify-between text-xs text-gray-600">
                                <span>Spent: <strong class="text-gray-900">₹{{ number_format($team->leaguePlayers->sum('bid_price') ?? 0) }}</strong></span>
                                <span>Avg: <strong class="text-gray-900">₹{{ $team->leaguePlayers->count() > 0 ? number_format($team->leaguePlayers->avg('bid_price') ?? 0) : 0 }}</strong></span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Bids -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Recent Bids</h2>
            <div id="recentBids" class="space-y-3">
                <div class="text-center text-gray-500 py-4">
                    <p>No recent bids yet...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Pusher for live updates
    var pusher = new Pusher('b62b3b015a81d2d28278', { 
        cluster: 'ap2',
        forceTLS: true,
        enabledTransports: ['ws', 'wss']
    });
    
    // Subscribe to the general auctions channel
    var channel = pusher.subscribe('auctions');
    
    // Subscribe to league-specific channel
    const leagueId = document.getElementById('league-id').value;
    const leagueSlug = document.getElementById('league-slug').value;
    var leagueChannel = pusher.subscribe(`auctions.league.${leagueId}`);
    console.log(`Subscribed to league channel: auctions.league.${leagueId}`);
    
    // Use a more efficient approach to update content instead of full page refresh
    console.log('🔄 Live view: Auto-updating specific content (every 8 seconds)');
    let autoRefreshInterval = setInterval(function() {
        console.log('🔄 Updating specific components...');
        updateLastUpdated();
        fetchRecentBids();
        refreshTeamBalances();
    }, 8000);
    
    // Listen to sold/unsold events to update data
    channel.bind('player-sold', function(data) {
        console.log('✅ Player sold - updating components');
        // Refresh all components without page reload
        fetchRecentBids();
        refreshTeamBalances();
        updateLastUpdated();
        
        // Show user notification
        const notificationElement = document.createElement('div');
        notificationElement.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
        notificationElement.textContent = `Player sold to ${data.team.team.name}!`;
        document.body.appendChild(notificationElement);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notificationElement.remove();
        }, 3000);
    });
    
    channel.bind('player-unsold', function(data) {
        console.log('❌ Player unsold - updating components');
        // Refresh all components without page reload
        fetchRecentBids();
        refreshTeamBalances();
        updateLastUpdated();
        
        // Show user notification
        const notificationElement = document.createElement('div');
        notificationElement.className = 'fixed top-4 right-4 bg-yellow-500 text-white px-4 py-2 rounded shadow-lg z-50';
        notificationElement.textContent = `Player marked as unsold!`;
        document.body.appendChild(notificationElement);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notificationElement.remove();
        }, 3000);
    });
    
    leagueChannel.bind('player-sold', function(data) {
        console.log('✅ Player sold (league channel) - updating components');
        // Refresh all components without page reload
        fetchRecentBids();
        refreshTeamBalances();
        updateLastUpdated();
        
        // Show user notification
        const notificationElement = document.createElement('div');
        notificationElement.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
        notificationElement.textContent = `Player sold to ${data.team.team.name}!`;
        document.body.appendChild(notificationElement);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notificationElement.remove();
        }, 3000);
    });
    
    leagueChannel.bind('player-unsold', function(data) {
        console.log('❌ Player unsold (league channel) - updating components');
        // Refresh all components without page reload
        fetchRecentBids();
        refreshTeamBalances();
        updateLastUpdated();
        
        // Show user notification
        const notificationElement = document.createElement('div');
        notificationElement.className = 'fixed top-4 right-4 bg-yellow-500 text-white px-4 py-2 rounded shadow-lg z-50';
        notificationElement.textContent = `Player marked as unsold!`;
        document.body.appendChild(notificationElement);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notificationElement.remove();
        }, 3000);
    });
    
    // Listen for new player started on both channels
    channel.bind('new-player-started', function(data) {
        if (data.league.id === {{ $league->id }}) {
            updateCurrentPlayer(data);
        }
    });
    
    leagueChannel.bind('new-player-started', function(data) {
        updateCurrentPlayer(data);
    });
    
    // Listen for new bids on both channels
    channel.bind('new-player-bid-call', function(data) {
        if (data.league_team.league_id === {{ $league->id }}) {
            addRecentBid(data);
            updateTeamBalances();
            updateCurrentBid(data);
        }
    });
    
    leagueChannel.bind('new-player-bid-call', function(data) {
        addRecentBid(data);
        updateTeamBalances();
        updateCurrentBid(data);
    });
    
    function updateCurrentPlayer(data) {
        const playerCard = document.getElementById('currentPlayerCard');
        
        // Get position name from primary game role or fallback to position
        let positionName = 'Player';
        if (data.player.primary_game_role && data.player.primary_game_role.game_position) {
            positionName = data.player.primary_game_role.game_position.name;
        } else if (data.player.position) {
            positionName = data.player.position.name;
        }
        
        const playerInitial = data.player.name ? data.player.name.charAt(0).toUpperCase() : 'P';
        
        playerCard.innerHTML = `
            <!-- Glassmorphism Card Style -->
            <div class="card-container relative rounded-3xl overflow-hidden shadow-2xl">
                <!-- Animated Background Blobs -->
                <div class="absolute inset-0 overflow-hidden rounded-3xl">
                    <div class="blob absolute -left-12 -top-24 w-32 h-32 sm:w-48 sm:h-48 bg-orange-400 rounded-full opacity-60 animate-blob"></div>
                    <div class="blob absolute right-8 sm:right-24 -top-6 w-32 h-32 sm:w-48 sm:h-48 bg-purple-500 rounded-full opacity-60 animate-blob" style="animation-delay: 1s;"></div>
                    <div class="blob absolute -left-10 top-32 sm:top-24 w-32 h-32 sm:w-48 sm:h-48 bg-pink-500 rounded-full opacity-60 animate-blob" style="animation-delay: 2s;"></div>
                    <div class="blob absolute right-8 sm:right-24 bottom-8 sm:top-44 w-32 h-32 sm:w-48 sm:h-48 bg-blue-500 rounded-full opacity-60 animate-blob" style="animation-delay: 3s;"></div>
                </div>

                <!-- Glassmorphism Content -->
                <div class="card__content relative z-10 p-6 sm:p-8 lg:p-10">
                    <!-- Player Header -->
                    <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-6 sm:space-y-0 sm:space-x-8 mb-8">
                        <div class="relative flex-shrink-0">
                            <div class="w-24 h-24 sm:w-28 sm:h-28 lg:w-32 lg:h-32 rounded-3xl overflow-hidden bg-white bg-opacity-20 flex items-center justify-center ring-4 ring-blue-200 ring-opacity-50 shadow-2xl">
                                <img src="${data.player.photo || '/images/defaultplayer.jpeg'}"
                                     alt="${data.player.name}"
                                     class="w-full h-full object-cover rounded-3xl"
                                     onerror="handleImageError(this);">
                                <!-- Fallback avatar when image fails -->
                                <div class="w-full h-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl hidden rounded-3xl">
                                    ${playerInitial}
                                </div>
                            </div>
                            <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg">
                                <svg class="w-4 h-4 text-yellow-800" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="text-center sm:text-left flex-grow">
                            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-3 text-gray-800 playerName">
                                ${data.player.name}
                            </h2>
                            <div class="flex flex-wrap gap-2 justify-center sm:justify-start mb-4">
                                <span class="bg-blue-500 bg-opacity-20 backdrop-blur-sm px-4 py-2 rounded-2xl text-sm font-semibold text-blue-700 border border-blue-300 position">
                                    ${positionName}
                                </span>
                                <span class="bg-green-500 bg-opacity-20 backdrop-blur-sm px-4 py-2 rounded-2xl text-sm font-semibold text-green-700 border border-green-300">
                                    Base Price ₹<span class="basePrice">${data.league_player.base_price}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Current Bid Row -->
                    <div class="stat-card bg-white bg-opacity-80 backdrop-blur-sm rounded-2xl p-4 sm:p-6 border border-blue-200 shadow-lg mb-6">
                        <!-- Current Bid (Main Highlight) -->
                        <div class="text-center mb-4">
                            <p class="text-gray-600 text-xs sm:text-sm font-medium mb-2 bidStatus">Base Price</p>
                            <p class="font-bold text-2xl sm:text-3xl lg:text-4xl text-blue-600 mb-2">₹ <span class="currentBid">${data.league_player.base_price}</span></p>
                            <p class="text-gray-600 text-sm font-medium bidTeam">Awaiting new bids..</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Add image error handler function
    function handleImageError(img) {
        if (img && img.nextElementSibling) {
            img.style.display = 'none';
            img.nextElementSibling.style.display = 'flex';
        }
    }
    
    function addRecentBid(data) {
        const recentBids = document.getElementById('recentBids');
        const bidElement = document.createElement('div');
        bidElement.className = 'bid-card bg-gray-50 rounded-lg p-4 border-l-4 border-green-500';
        bidElement.innerHTML = `
            <div class="flex justify-between items-center">
                <div>
                    <span class="font-semibold text-gray-900">${data.league_team.team.name}</span>
                    <span class="text-gray-600 ml-2">placed a bid</span>
                </div>
                <div class="text-right">
                    <span class="text-xl font-bold text-green-600">₹${data.new_bid}</span>
                    <p class="text-xs text-gray-500">${new Date().toLocaleTimeString()}</p>
                </div>
            </div>
        `;
        
        // Add to top of recent bids
        if (recentBids.firstChild && recentBids.firstChild.classList.contains('text-center')) {
            recentBids.removeChild(recentBids.firstChild);
        }
        recentBids.insertBefore(bidElement, recentBids.firstChild);
        
        // Keep only last 10 bids
        while (recentBids.children.length > 10) {
            recentBids.removeChild(recentBids.lastChild);
        }
        
        // Update current bid display
        const currentBidElement = document.getElementById('currentBid');
        const leadingTeamElement = document.getElementById('leadingTeam');
        if (currentBidElement) {
            currentBidElement.textContent = `₹${data.new_bid}`;
        }
        if (leadingTeamElement) {
            leadingTeamElement.textContent = data.league_team.team.name;
        }
    }
    
    function updateLastUpdated() {
        const lastUpdated = document.getElementById('lastUpdated');
        lastUpdated.textContent = new Date().toLocaleTimeString();
    }
    
    function refreshTeamBalances() {
        // Get the league slug from the hidden input
        const leagueSlug = document.getElementById('league-slug').value;
        
        // Make an AJAX call to get updated balances
        fetch(`/api/auctions/league/${leagueSlug}/team-balances`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.teams) {
                    // Update team balances in the UI
                    const teamsContainer = document.getElementById('teamsContainer');
                    if (teamsContainer) {
                        const teamCards = teamsContainer.querySelectorAll('.border');
                        data.teams.forEach(team => {
                            // Find team card by team name
                            teamCards.forEach(card => {
                                const teamNameElement = card.querySelector('h3');
                                if (teamNameElement && teamNameElement.textContent === team.name) {
                                    // Update balance
                                    const balanceElement = card.querySelector('.text-lg.font-bold');
                                    if (balanceElement) {
                                        balanceElement.textContent = `₹${numberFormat(team.wallet_balance)}`;
                                    }
                                    
                                    // Update player count
                                    const playerCountElement = card.querySelector('.text-xs.text-blue-100');
                                    if (playerCountElement) {
                                        playerCountElement.textContent = `${team.players_count} Players`;
                                    }
                                }
                            });
                        });
                    }
                }
                
                // Update timestamp regardless of API success
                updateLastUpdated();
            })
            .catch(error => {
                console.error('Error fetching team balances:', error);
                updateLastUpdated(); // Still update timestamp on error
            });
    }
    
    // Helper function to format numbers with commas
    function numberFormat(number) {
        return new Intl.NumberFormat('en-IN').format(number);
    }
    
    function fetchRecentBids() {
        // Get the league slug from the hidden input
        const leagueSlug = document.getElementById('league-slug').value;
        
        // Fetch recent bids via AJAX instead of refreshing the entire page
        fetch(`/api/auctions/league/${leagueSlug}/recent-bids`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.bids && data.bids.length > 0) {
                    const recentBids = document.getElementById('recentBids');
                    
                    // Clear existing content if we have bids
                    recentBids.innerHTML = '';
                    
                    // Add each bid to the container
                    data.bids.forEach(bid => {
                        const bidElement = document.createElement('div');
                        bidElement.className = 'bid-card bg-gray-50 rounded-lg p-4 border-l-4 border-green-500';
                        bidElement.innerHTML = `
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="font-semibold text-gray-900">${bid.league_team.team.name}</span>
                                    <span class="text-gray-600 ml-2">placed a bid</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xl font-bold text-green-600">₹${bid.amount}</span>
                                    <p class="text-xs text-gray-500">${new Date(bid.created_at).toLocaleTimeString()}</p>
                                </div>
                            </div>
                        `;
                        recentBids.appendChild(bidElement);
                    });
                } else if (!data.bids || data.bids.length === 0) {
                    // If no bids, show the empty state
                    const recentBids = document.getElementById('recentBids');
                    recentBids.innerHTML = `
                        <div class="text-center text-gray-500 py-4">
                            <p>No recent bids yet...</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching recent bids:', error);
            });
    }
});
</script>
@endsection
