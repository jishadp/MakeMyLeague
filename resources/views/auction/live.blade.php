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
<div class="min-h-screen bg-gray-50 py-6 sm:py-8 lg:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 lg:space-y-8">
        <!-- Header -->
        <div class="bg-white/90 backdrop-blur rounded-3xl shadow-xl ring-1 ring-gray-100">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100/70">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $league->name }}</h1>
                            <p class="text-sm sm:text-base text-gray-600">{{ $league->game->name }} League</p>
                        </div>
                        <div class="flex items-center gap-2 text-sm font-semibold">
                            <div class="w-3 h-3 bg-red-500 rounded-full live-indicator"></div>
                            <span class="text-red-600">LIVE</span>
                        </div>
                    </div>
                    <div class="flex w-full flex-col gap-4 sm:flex-row sm:items-center sm:justify-end">
                        <div class="text-left sm:text-right">
                            <p class="text-xs uppercase tracking-wide text-gray-500">Last Updated</p>
                            <p class="text-base font-semibold text-gray-900" id="lastUpdated">{{ now()->format('H:i:s') }}</p>
                            <p class="text-xs text-gray-400" id="autoRefreshNote">Auto refreshes every 5s</p>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:justify-end w-full sm:w-auto">
                            @if(isset($liveViewers))
                                <div class="flex items-center justify-between sm:justify-start gap-2 px-4 py-2 rounded-2xl bg-slate-100 text-slate-700 text-sm font-semibold shadow-sm w-full sm:w-auto">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>{{ $liveViewers }} watching</span>
                                </div>
                            @endif
                            <button
                                type="button"
                                id="refreshButton"
                                aria-label="Refresh auction data"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-gray-700 font-semibold hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all w-full sm:w-auto">
                                <svg id="refreshIcon" class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m0 0a8 8 0 111.387 8.457L4 15m.582-6H9"/>
                                </svg>
                                <span class="text-sm">Refresh</span>
                            </button>
                            <a href="{{ route('auctions.index') }}" 
                               class="inline-flex items-center justify-center bg-indigo-600 text-white px-4 py-2 rounded-xl hover:bg-indigo-700 transition-colors text-center w-full sm:w-auto">
                                Back to Auctions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Player Being Auctioned -->
        <div class="grid grid-cols-1 gap-6 lg:gap-8 lg:grid-cols-12">
            <!-- Current Player Card -->
            <div class="space-y-4 lg:col-span-7 xl:col-span-8">
                <div class="bg-white/90 backdrop-blur rounded-3xl shadow-xl ring-1 ring-gray-100 p-4 sm:p-6 lg:p-8">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-4">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Current Player</h2>
                        <p class="text-sm text-gray-500">Live roster updates refresh in real-time</p>
                    </div>
                    <div id="currentPlayerCard" class="text-center py-6 sm:py-8">
                        @if(isset($currentPlayer) && $currentPlayer)
                            @php
                                $leadingBudget = null;
                                if ($currentHighestBid) {
                                    $leadingBudget = $teams->firstWhere('id', $currentHighestBid->league_team_id);
                                }
                            @endphp
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
                                <div class="card__content relative z-10 flex flex-col gap-8 p-6 sm:p-8 lg:p-10">
                                    <!-- Player Header -->
                                    <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:gap-8">
                                        <div class="relative flex-shrink-0 mx-auto sm:mx-0">
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
                                            @if($currentPlayer->retention)
                                                <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg" title="Retained player" aria-hidden="true">
                                                    <svg class="w-4 h-4 text-yellow-800" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-center sm:text-left flex-grow w-full">
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
                                                @if($currentPlayer->retention)
                                                    <span class="bg-yellow-500 bg-opacity-10 backdrop-blur-sm px-4 py-2 rounded-2xl text-sm font-semibold text-yellow-700 border border-yellow-300 flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                        Retained Player
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Current Bid Row -->
                                    <div class="stat-card bg-white bg-opacity-80 backdrop-blur-sm rounded-2xl p-4 sm:p-6 border border-blue-200 shadow-lg">
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
                                        @if($leadingBudget)
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 text-center text-sm text-gray-700">
                                                <div class="rounded-xl bg-white/80 px-3 py-2">
                                                    <p class="text-xs uppercase text-gray-500">Players Needed</p>
                                                    <p class="font-semibold">{{ $leadingBudget->players_needed }}</p>
                                                </div>
                                                <div class="rounded-xl bg-white/80 px-3 py-2">
                                                    <p class="text-xs uppercase text-gray-500">Reserve Funds</p>
                                                    <p class="font-semibold">₹{{ number_format($leadingBudget->reserve_amount) }}</p>
                                                </div>
                                                <div class="rounded-xl bg-white/80 px-3 py-2">
                                                    <p class="text-xs uppercase text-gray-500">Max Bid This Player</p>
                                                    <p class="font-semibold text-emerald-600">₹{{ number_format($leadingBudget->max_bid_cap) }}</p>
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-xs text-gray-400 text-center">Bid to unlock team budget info for this player.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-gray-500 flex flex-col items-center justify-center gap-3 py-12">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-lg font-semibold">Waiting for next player...</p>
                                <p class="text-sm text-gray-400">Stay tuned, the auctioneer is preparing the next card.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Team Cards with Players -->
            <div class="space-y-4 lg:col-span-5 xl:col-span-4">
                <div class="bg-white/90 backdrop-blur rounded-3xl shadow-xl ring-1 ring-gray-100 p-4 sm:p-6 flex flex-col gap-4">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">
                            Teams ({{ count($teams) }})
                        </h2>
                        <p class="text-sm text-gray-500">Tap a card on mobile to inspect full squad stats, balances, and bids.</p>
                    </div>
                    <div class="space-y-4" id="teamsContainer">
                        @foreach($teams as $team)
                        <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white/80 hover:shadow-xl transition-shadow duration-300">
                            <!-- Team Header -->
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-4 text-white">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center font-bold backdrop-blur-sm">
                                            {{ strtoupper(substr($team->team->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <h3 class="font-bold">{{ $team->team->name }}</h3>
                                            <p class="text-xs text-blue-100">{{ $team->leaguePlayers->count() }} Players</p>
                                            @php
                                                $retainedPlayers = $team->leaguePlayers->where('retention', true);
                                                $boughtPlayers = $team->leaguePlayers->where('retention', false)->where('status', 'sold');
                                            @endphp
                                            <div class="flex flex-wrap gap-1 mt-1 text-[10px] font-semibold">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-yellow-500/20 text-yellow-100 border border-yellow-400/40">
                                                    <svg class="w-3 h-3 text-yellow-200" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                    Retained {{ $team->retained_players_count ?? $retainedPlayers->count() }}
                                                </span>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-100 border border-emerald-400/40">
                                                    <svg class="w-3 h-3 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18l-1.5 13.5a3 3 0 01-2.985 2.7H7.485A3 3 0 014.5 16.5L3 3zm6 6h6m-5 4h4m-6-8V3m6 2V3"></path>
                                                    </svg>
                                                    Bought {{ $team->sold_players_count ?? $boughtPlayers->count() }}
                                                </span>
                                            </div>
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
                                    <div class="text-right space-y-1">
                                        <div>
                                            <p class="text-lg font-bold">₹{{ number_format($team->display_wallet ?? $team->wallet_balance) }}</p>
                                            <p class="text-xs text-blue-100">Balance</p>
                                        </div>
                                        <p class="text-[11px] text-blue-100">Needs {{ $team->players_needed }} • Reserve ₹{{ number_format($team->reserve_amount) }}</p>
                                        <p class="text-[11px] text-emerald-200">Max bid now ₹{{ number_format($team->max_bid_cap) }}</p>
                                    </div>
                                </div>
                                <button type="button" data-team-toggle aria-controls="teamRoster-{{ $team->id }}" aria-expanded="false" class="mt-4 inline-flex items-center justify-between gap-3 rounded-xl border border-white/30 bg-white/10 px-3 py-2 text-sm font-semibold text-white/90 transition-colors lg:hidden w-full">
                                    <span data-toggle-label>View Squad</span>
                                    <svg class="w-4 h-4 transition-transform duration-200" data-toggle-icon fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>

                            <div id="teamRoster-{{ $team->id }}" class="team-roster hidden lg:block">
                                <!-- Players List -->
                                @if($team->leaguePlayers && $team->leaguePlayers->count() > 0)
                                <div class="p-3 sm:p-4 bg-gray-50 space-y-4">
                                    @if($retainedPlayers->isNotEmpty())
                                        <div>
                                            <p class="text-xs font-semibold text-yellow-700 uppercase mb-1 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                Retained Players
                                            </p>
                                            <div class="space-y-1.5">
                                                @foreach($retainedPlayers as $playerIndex => $leaguePlayer)
                                                    <div class="flex items-center justify-between p-2 bg-white rounded-lg text-xs">
                                                        <div class="flex items-center space-x-2">
                                                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white text-xs font-bold">
                                                                {{ strtoupper(substr($leaguePlayer->player->name, 0, 1)) }}
                                                            </div>
                                                            <div>
                                                                <p class="font-medium text-gray-900">{{ $leaguePlayer->player->name }}</p>
                                                                <p class="text-[11px] text-gray-500">
                                                                    @if($leaguePlayer->player->primaryGameRole && $leaguePlayer->player->primaryGameRole->gamePosition)
                                                                        {{ $leaguePlayer->player->primaryGameRole->gamePosition->name }}
                                                                @elseif($leaguePlayer->player->position)
                                                                        {{ $leaguePlayer->player->position->name }}
                                                                    @else
                                                                        Player
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <span class="text-[11px] font-semibold text-yellow-600">Retained</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    @if($boughtPlayers->isNotEmpty())
                                        <div>
                                            <p class="text-xs font-semibold text-emerald-700 uppercase mb-1 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18l-1.5 13.5a3 3 0 01-2.985 2.7H7.485A3 3 0 014.5 16.5L3 3zm6 6h6m-5 4h4m-6-8V3m6 2V3"></path>
                                                </svg>
                                                Auction Wins
                                            </p>
                                            <div class="space-y-1.5">
                                                @foreach($boughtPlayers as $leaguePlayer)
                                                    <div class="flex items-center justify-between p-2 bg-white rounded-lg text-xs">
                                                        <div class="flex items-center space-x-2">
                                                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                                                {{ strtoupper(substr($leaguePlayer->player->name, 0, 1)) }}
                                                            </div>
                                                            <div>
                                                                <p class="font-medium text-gray-900">{{ $leaguePlayer->player->name }}</p>
                                                                <p class="text-[11px] text-gray-500">
                                                                    @if($leaguePlayer->player->primaryGameRole && $leaguePlayer->player->primaryGameRole->gamePosition)
                                                                        {{ $leaguePlayer->player->primaryGameRole->gamePosition->name }}
                                                                @elseif($leaguePlayer->player->position)
                                                                        {{ $leaguePlayer->player->position->name }}
                                                                    @else
                                                                        Player
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <p class="text-xs font-bold text-green-600">₹{{ number_format($leaguePlayer->bid_price ?? 0) }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @else
                                <div class="p-4 text-center text-gray-500 bg-gray-50 text-sm">
                                    <p>No players yet</p>
                                </div>
                                @endif

                                <!-- Team Stats Footer -->
                                <div class="bg-gray-100 px-3 py-2 flex flex-col sm:flex-row sm:items-center sm:justify-between text-xs text-gray-600 gap-1 text-center sm:text-left">
                                    <span>Spent: <strong class="text-gray-900">₹{{ number_format($team->leaguePlayers->sum('bid_price') ?? 0) }}</strong></span>
                                    <span>Avg: <strong class="text-gray-900">₹{{ $team->leaguePlayers->count() > 0 ? number_format($team->leaguePlayers->avg('bid_price') ?? 0) : 0 }}</strong></span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Bids -->
        <div class="bg-white/90 backdrop-blur rounded-3xl shadow-xl ring-1 ring-gray-100 p-4 sm:p-6">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-4">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Recent Bids</h2>
                <p class="text-sm text-gray-500">Live feed of the latest 10 bids across the league.</p>
            </div>
            <div id="recentBids" class="space-y-3 max-h-[28rem] overflow-y-auto pr-1" aria-live="polite">
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
    
    const refreshButton = document.getElementById('refreshButton');
    const refreshIcon = document.getElementById('refreshIcon');
    const autoRefreshRateMs = 5000;
    let refreshInFlight = false;
    const teamToggleButtons = document.querySelectorAll('[data-team-toggle]');

    teamToggleButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('aria-controls');
            const rosterPanel = document.getElementById(targetId);
            if (!rosterPanel) {
                return;
            }

            const willShow = rosterPanel.classList.contains('hidden');
            rosterPanel.classList.toggle('hidden');
            button.setAttribute('aria-expanded', willShow ? 'true' : 'false');

            const label = button.querySelector('[data-toggle-label]');
            if (label) {
                label.textContent = willShow ? 'Hide Squad' : 'View Squad';
            }

            const icon = button.querySelector('[data-toggle-icon]');
            if (icon) {
                icon.classList.toggle('rotate-180', willShow);
            }
        });
    });

    function performSoftRefresh(source = 'auto') {
        if (refreshInFlight && source === 'auto') {
            return;
        }

        refreshInFlight = true;

        if (refreshButton && source === 'manual') {
            refreshButton.classList.add('opacity-70', 'cursor-wait');
            refreshButton.setAttribute('aria-disabled', 'true');
        }
        if (refreshIcon && source === 'manual') {
            refreshIcon.classList.add('animate-spin');
        }

        Promise.allSettled([
            Promise.resolve(updateLastUpdated()),
            fetchRecentBids(),
            refreshTeamBalances()
        ]).finally(() => {
            refreshInFlight = false;
            if (refreshButton) {
                refreshButton.classList.remove('opacity-70', 'cursor-wait');
                refreshButton.removeAttribute('aria-disabled');
            }
            if (refreshIcon) {
                refreshIcon.classList.remove('animate-spin');
            }
        });
    }

    if (refreshButton) {
        refreshButton.addEventListener('click', () => performSoftRefresh('manual'));
    }
    
    // Use a more efficient approach to update content instead of full page refresh
    console.log('🔄 Live view: Auto-refreshing page every 5 seconds');
    setInterval(function() {
        if (!document.hidden) {
            window.location.reload();
        }
    }, autoRefreshRateMs);
    
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
        const isRetained = Boolean(
            data.league_player &&
            (data.league_player.retention === true ||
            data.league_player.retention === 1 ||
            data.league_player.retention === '1')
        );
        const retainedStar = isRetained
            ? `<div class="absolute -bottom-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg" title="Retained player" aria-hidden="true">
                    <svg class="w-4 h-4 text-yellow-800" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
               </div>`
            : '';
        const retainedBadge = isRetained
            ? `<span class="bg-yellow-500 bg-opacity-10 backdrop-blur-sm px-4 py-2 rounded-2xl text-sm font-semibold text-yellow-700 border border-yellow-300 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    Retained Player
               </span>`
            : '';
        
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
                            ${retainedStar}
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
                                ${retainedBadge}
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
        bidElement.className = 'bid-card border border-gray-100 bg-white/80 rounded-2xl p-4 sm:p-5 shadow-sm';
        bidElement.innerHTML = `
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600">
                    <span class="font-semibold text-gray-900">${data.league_team.team.name}</span>
                    <span class="text-gray-500">placed a bid</span>
                </div>
                <div class="text-left sm:text-right">
                    <span class="block text-xl font-bold text-emerald-600">₹${data.new_bid}</span>
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
        if (lastUpdated) {
            lastUpdated.textContent = new Date().toLocaleTimeString();
        }
    }
    
    function refreshTeamBalances() {
        // Get the league slug from the hidden input
        const leagueSlug = document.getElementById('league-slug').value;
        
        // Make an AJAX call to get updated balances
        return fetch(`/api/auctions/league/${leagueSlug}/team-balances`)
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
        return fetch(`/api/auctions/league/${leagueSlug}/recent-bids`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.bids && data.bids.length > 0) {
                    const recentBids = document.getElementById('recentBids');
                    
                    // Clear existing content if we have bids
                    recentBids.innerHTML = '';
                    
                    // Add each bid to the container
                    data.bids.forEach(bid => {
                        const bidElement = document.createElement('div');
                        bidElement.className = 'bid-card border border-gray-100 bg-white/80 rounded-2xl p-4 sm:p-5 shadow-sm';
                        bidElement.innerHTML = `
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600">
                                    <span class="font-semibold text-gray-900">${bid.league_team.team.name}</span>
                                    <span class="text-gray-500">placed a bid</span>
                                </div>
                                <div class="text-left sm:text-right">
                                    <span class="block text-xl font-bold text-emerald-600">₹${bid.amount}</span>
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
                        <div class="text-center text-gray-500 py-4 text-sm">
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
