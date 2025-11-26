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

    .budget-metrics {
        scrollbar-width: none;
    }

    .budget-metrics::-webkit-scrollbar {
        display: none;
    }

    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endsection

@section('content')
<input type="hidden" id="league-id" value="{{ $league->id }}">
<input type="hidden" id="league-slug" value="{{ $league->slug }}">
<button
    type="button"
    id="focusCurrentPlayerButton"
    aria-label="Jump to current player"
    class="fixed z-40 bottom-24 right-4 sm:bottom-6 sm:right-6 inline-flex items-center justify-center w-12 h-12 rounded-full bg-indigo-600/60 text-white shadow-lg shadow-indigo-400/40 backdrop-blur focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white hover:bg-indigo-500/70 transition-transform duration-300 motion-safe:animate-bounce">
    <span class="sr-only">Current player</span>
    <i class="fa-solid fa-stamp text-xl" aria-hidden="true"></i>
</button>
<div class="min-h-screen bg-gray-50 py-6 sm:py-8 lg:py-12 pb-32 lg:pb-24">
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
                            <p class="text-xs text-gray-400" id="autoRefreshNote">Tap the refresh icon under the current bid to reload this page.</p>
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
            @include('auction.partials.current-player-card', [
                'currentPlayer' => $currentPlayer ?? null,
                'currentHighestBid' => $currentHighestBid ?? null,
                'teams' => $teams ?? collect(),
            ])

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
                        <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white/80 hover:shadow-xl transition-shadow duration-300" data-team-card="{{ $team->id }}">
                            <!-- Team Header -->
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-4 text-white">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center font-bold backdrop-blur-sm">
                                            {{ strtoupper(substr($team->team->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <h3 class="font-bold">{{ $team->team->name }}</h3>
                                            <p class="text-xs text-blue-100"><span data-team-players>{{ $team->leaguePlayers->count() }}</span> Players</p>
                                            @php
                                                $retainedPlayers = $team->leaguePlayers->where('retention', true);
                                                $boughtPlayers = $team->leaguePlayers->where('retention', false)->where('status', 'sold');
                                            @endphp
                                            <div class="flex flex-wrap gap-1 mt-1 text-[10px] font-semibold">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-yellow-500/20 text-yellow-100 border border-yellow-400/40">
                                                    <svg class="w-3 h-3 text-yellow-200" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                    Retained <span data-team-retained>{{ $team->retained_players_count ?? $retainedPlayers->count() }}</span>
                                                </span>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-100 border border-emerald-400/40">
                                                    <svg class="w-3 h-3 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18l-1.5 13.5a3 3 0 01-2.985 2.7H7.485A3 3 0 014.5 16.5L3 3zm6 6h6m-5 4h4m-6-8V3m6 2V3"></path>
                                                    </svg>
                                                    Bought <span data-team-sold>{{ $team->sold_players_count ?? $boughtPlayers->count() }}</span>
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
                                            <p class="text-lg font-bold">&#8377; <span data-team-balance>{{ number_format($team->display_wallet ?? $team->wallet_balance) }}</span></p>
                                            <p class="text-xs text-blue-100">Balance</p>
                                        </div>
                                        <p class="text-[11px] text-blue-100">Needs <span data-team-needed>{{ $team->players_needed }}</span> | Reserve &#8377; <span data-team-reserve>{{ number_format($team->reserve_amount) }}</span></p>
                                        <p class="text-[11px] text-emerald-200">Max bid now &#8377; <span data-team-max>{{ number_format($team->max_bid_cap) }}</span></p>
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

        @php
            $liveSpotlightRecent = ($recentSoldPlayers ?? collect());
            $liveSpotlightTop = ($topSoldPlayers ?? collect());
            $liveSpotlightRecentChunks = $liveSpotlightRecent->chunk(10);
            $liveSpotlightTopChunks = $liveSpotlightTop->chunk(10);
            $hasLiveSpotlight = $liveSpotlightRecent->isNotEmpty() || $liveSpotlightTop->isNotEmpty();
        @endphp
        @if($hasLiveSpotlight)
        <div id="livePlayerSpotlight" class="bg-white/90 backdrop-blur rounded-3xl shadow-xl ring-1 ring-gray-100 p-4 sm:p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-blue-600 uppercase tracking-wide">Player Spotlight</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Sold Players</h2>
                    <p class="text-sm text-gray-500">Swipe through the latest signings and top buys.</p>
                </div>
                <div class="inline-flex rounded-full border border-gray-200 overflow-hidden w-full sm:w-auto">
                    <button type="button"
                        class="spotlight-tab-btn px-4 py-2 text-xs font-bold uppercase tracking-wide text-white bg-blue-600 shadow-md focus:outline-none flex-1 sm:flex-none"
                        data-live-spotlight-tab="recent">
                        Recent
                    </button>
                    <button type="button"
                        class="spotlight-tab-btn px-4 py-2 text-xs font-bold uppercase tracking-wide text-gray-600 bg-white focus:outline-none flex-1 sm:flex-none"
                        data-live-spotlight-tab="top">
                        Top
                    </button>
                </div>
            </div>
            <div class="relative">
                <div data-live-spotlight-panel="recent">
                    <div class="overflow-hidden">
                        @forelse($liveSpotlightRecentChunks as $index => $chunk)
                        <div class="player-spotlight-chunk {{ $index === 0 ? '' : 'hidden' }}" data-live-player-chunk data-panel="recent" data-index="{{ $index }}">
                            <div class="flex gap-3 overflow-x-auto px-3 py-4 snap-x snap-mandatory scrollbar-hide" role="list">
                                @foreach($chunk as $player)
                                @php
                                    $playerPhoto = $player->user && $player->user->photo ? Storage::url($player->user->photo) : null;
                                    $playerInitials = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($player->user->name, 0, 2));
                                    $teamFullName = optional(optional($player->leagueTeam)->team)->name ?? 'Team TBA';
                                    $teamShort = \Illuminate\Support\Str::limit($teamFullName, 18);
                                    $posterUrl = ($player->league && $player->leagueTeam) ? route('posters.show', [$player->league, $player->leagueTeam]) : null;
                                @endphp
                                <article class="live-spotlight-card flex-none w-[44%] min-w-[130px] max-w-[150px] sm:w-48 sm:max-w-[200px] snap-start snap-center bg-white border border-gray-100 rounded-2xl shadow-sm p-3 text-center relative">
                                    <div class="rounded-2xl bg-gray-50 p-2 flex justify-center">
                                        @if($playerPhoto)
                                            <img src="{{ $playerPhoto }}" alt="{{ $player->user->name }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover shadow" loading="lazy">
                                        @else
                                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 text-white font-bold text-xl sm:text-2xl flex items-center justify-center shadow">
                                                {{ $playerInitials }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mt-3 space-y-1">
                                        <p class="text-sm sm:text-base font-semibold text-gray-900 truncate">{{ $player->user->name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $teamShort }}</p>
                                        <p class="text-base sm:text-lg font-bold text-emerald-600">₹{{ number_format($player->bid_price, 0) }}</p>
                                    </div>
                                    @if($posterUrl)
                                        <a href="{{ $posterUrl }}" class="mt-2 inline-flex items-center justify-center w-full text-[11px] font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-xl py-1 transition-colors" aria-label="View {{ $teamFullName }} poster">
                                            Team Poster
                                        </a>
                                    @else
                                        <span class="mt-2 inline-flex items-center justify-center w-full text-[11px] font-semibold text-gray-400 bg-gray-100 rounded-xl py-1">
                                            Poster Unavailable
                                        </span>
                                    @endif
                                </article>
                                @endforeach
                            </div>
                        </div>
                        @empty
                        <div class="player-spotlight-empty text-center py-10 text-sm text-gray-500 border border-dashed border-gray-200 rounded-2xl">
                            No recent sales yet.
                        </div>
                        @endforelse
                    </div>
                    @if($liveSpotlightRecentChunks->count() > 1)
                    <div class="flex justify-center mt-4 gap-2" data-live-player-dots="recent">
                        @foreach($liveSpotlightRecentChunks as $index => $_)
                        <button type="button" class="player-spotlight-dot w-2.5 h-2.5 rounded-full {{ $index === 0 ? 'bg-blue-600' : 'bg-gray-300' }}" data-panel="recent" data-target-index="{{ $index }}" aria-label="Show recent batch {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="hidden" data-live-spotlight-panel="top">
                    <div class="overflow-hidden">
                        @forelse($liveSpotlightTopChunks as $index => $chunk)
                        <div class="player-spotlight-chunk {{ $index === 0 ? '' : 'hidden' }}" data-live-player-chunk data-panel="top" data-index="{{ $index }}">
                            <div class="flex gap-3 overflow-x-auto px-3 py-4 snap-x snap-mandatory scrollbar-hide" role="list">
                                @foreach($chunk as $player)
                                @php
                                    $playerPhoto = $player->user && $player->user->photo ? Storage::url($player->user->photo) : null;
                                    $playerInitials = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($player->user->name, 0, 2));
                                    $teamFullName = optional(optional($player->leagueTeam)->team)->name ?? 'Team TBA';
                                    $teamShort = \Illuminate\Support\Str::limit($teamFullName, 18);
                                    $posterUrl = ($player->league && $player->leagueTeam) ? route('posters.show', [$player->league, $player->leagueTeam]) : null;
                                @endphp
                                <article class="live-spotlight-card flex-none w-[44%] min-w-[130px] max-w-[150px] sm:w-48 sm:max-w-[200px] snap-start snap-center bg-white border border-gray-100 rounded-2xl shadow-sm p-3 text-center relative">
                                    <div class="rounded-2xl bg-gray-50 p-2 flex justify-center">
                                        @if($playerPhoto)
                                            <img src="{{ $playerPhoto }}" alt="{{ $player->user->name }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover shadow" loading="lazy">
                                        @else
                                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 text-white font-bold text-xl sm:text-2xl flex items-center justify-center shadow">
                                                {{ $playerInitials }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mt-3 space-y-1">
                                        <p class="text-sm sm:text-base font-semibold text-gray-900 truncate">{{ $player->user->name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $teamShort }}</p>
                                        <p class="text-base sm:text-lg font-bold text-emerald-600">₹{{ number_format($player->bid_price, 0) }}</p>
                                    </div>
                                    @if($posterUrl)
                                        <a href="{{ $posterUrl }}" class="mt-2 inline-flex items-center justify-center w-full text-[11px] font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-xl py-1 transition-colors" aria-label="View {{ $teamFullName }} poster">
                                            Team Poster
                                        </a>
                                    @else
                                        <span class="mt-2 inline-flex items-center justify-center w-full text-[11px] font-semibold text-gray-400 bg-gray-100 rounded-xl py-1">
                                            Poster Unavailable
                                        </span>
                                    @endif
                                </article>
                                @endforeach
                            </div>
                        </div>
                        @empty
                        <div class="player-spotlight-empty text-center py-10 text-sm text-gray-500 border border-dashed border-gray-200 rounded-2xl">
                            No top sales yet.
                        </div>
                        @endforelse
                    </div>
                    @if($liveSpotlightTopChunks->count() > 1)
                    <div class="flex justify-center mt-4 gap-2" data-live-player-dots="top">
                        @foreach($liveSpotlightTopChunks as $index => $_)
                        <button type="button" class="player-spotlight-dot w-2.5 h-2.5 rounded-full {{ $index === 0 ? 'bg-blue-600' : 'bg-gray-300' }}" data-panel="top" data-target-index="{{ $index }}" aria-label="Show top batch {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

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
    const PUSHER_KEY = '{{ config('broadcasting.connections.pusher.key') }}';
    const PUSHER_CLUSTER = '{{ config('broadcasting.connections.pusher.options.cluster') }}';
    const PUSHER_LOG_TO_CONSOLE = {{ config('app.debug') ? 'true' : 'false' }};
</script>
<script src="{{ asset('js/pusher-main.js') }}?v={{ time() + 1 }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const leagueId = document.getElementById('league-id').value;
    const leagueSlug = document.getElementById('league-slug').value;

    const refreshButton = document.getElementById('currentBidRefreshButton');
    const refreshIcon = document.getElementById('currentBidRefreshIcon');
    const teamToggleButtons = document.querySelectorAll('[data-team-toggle]');
    const focusToggleButton = document.getElementById('focusCurrentPlayerButton');

    initLiveSpotlightShowcase();

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

    if (focusToggleButton) {
        focusToggleButton.addEventListener('click', () => {
            focusToggleButton.classList.add('animate-pulse');
            focusCurrentPlayerCard();
            setTimeout(() => focusToggleButton.classList.remove('animate-pulse'), 600);
        });
    }

    function initLiveSpotlightShowcase() {
        const spotlightWrapper = document.getElementById('livePlayerSpotlight');
        if (!spotlightWrapper) {
            return;
        }

        const tabButtons = Array.from(spotlightWrapper.querySelectorAll('[data-live-spotlight-tab]'));
        const panels = Array.from(spotlightWrapper.querySelectorAll('[data-live-spotlight-panel]'));
        if (!tabButtons.length || !panels.length) {
            return;
        }

        const dotContainers = Array.from(spotlightWrapper.querySelectorAll('[data-live-player-dots]'));
        const chunkGroups = {};

        const ensureGroup = (panelKey) => {
            if (!chunkGroups[panelKey]) {
                chunkGroups[panelKey] = {
                    chunks: [],
                    dots: [],
                    active: 0,
                };
            }
            return chunkGroups[panelKey];
        };

        spotlightWrapper.querySelectorAll('[data-live-player-chunk]').forEach((chunk) => {
            const panelKey = chunk.dataset.panel || 'recent';
            ensureGroup(panelKey).chunks.push(chunk);
        });

        dotContainers.forEach((container) => {
            const panelKey = container.dataset.livePlayerDots;
            ensureGroup(panelKey).dots = Array.from(container.querySelectorAll('.player-spotlight-dot'));
        });

        const updateButtonStyles = (button, isActive) => {
            button.classList.toggle('bg-blue-600', isActive);
            button.classList.toggle('text-white', isActive);
            button.classList.toggle('shadow-md', isActive);
            button.classList.toggle('text-gray-600', !isActive);
            button.classList.toggle('bg-white', !isActive);
        };

        const setActiveChunk = (panelKey, nextIndex) => {
            const group = chunkGroups[panelKey];
            if (!group || !group.chunks.length) {
                return;
            }

            const previousChunk = group.chunks[group.active];
            if (previousChunk) {
                previousChunk.classList.add('hidden');
            }
            if (group.dots[group.active]) {
                group.dots[group.active].classList.remove('bg-blue-600');
                group.dots[group.active].classList.add('bg-gray-300');
            }

            const safeIndex = ((nextIndex % group.chunks.length) + group.chunks.length) % group.chunks.length;
            group.active = safeIndex;

            const nextChunk = group.chunks[group.active];
            if (nextChunk) {
                nextChunk.classList.remove('hidden');
            }
            if (group.dots[group.active]) {
                group.dots[group.active].classList.remove('bg-gray-300');
                group.dots[group.active].classList.add('bg-blue-600');
            }
        };

        const setActivePanel = (panelKey) => {
            panels.forEach((panel) => {
                panel.classList.toggle('hidden', panel.dataset.liveSpotlightPanel !== panelKey);
            });
            tabButtons.forEach((button) => {
                updateButtonStyles(button, button.dataset.liveSpotlightTab === panelKey);
            });
            const group = chunkGroups[panelKey];
            if (group && group.chunks.length) {
                setActiveChunk(panelKey, group.active);
            }
        };

        tabButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const panelKey = button.dataset.liveSpotlightTab;
                if (panelKey) {
                    setActivePanel(panelKey);
                }
            });
        });

        spotlightWrapper.querySelectorAll('.player-spotlight-dot').forEach((dot) => {
            dot.addEventListener('click', () => {
                const panelKey = dot.dataset.panel || 'recent';
                const target = Number(dot.dataset.targetIndex);
                if (!Number.isNaN(target)) {
                    setActivePanel(panelKey);
                    setActiveChunk(panelKey, target);
                }
            });
        });

        const defaultPanel = tabButtons.find(btn => btn.dataset.liveSpotlightTab === 'recent')
            ? 'recent'
            : (tabButtons[0]?.dataset.liveSpotlightTab || 'recent');
        setActivePanel(defaultPanel);
    }

    if (refreshButton) {
        refreshButton.addEventListener('click', () => {
            refreshButton.classList.add('opacity-70', 'cursor-wait');
            refreshButton.setAttribute('aria-disabled', 'true');
            if (refreshIcon) {
                refreshIcon.classList.add('animate-spin');
            }
            window.location.reload();
        });
    }
    
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
            refreshTeamBalances();
            updateCurrentBid(data);
        }
    });
    
    leagueChannel.bind('new-player-bid-call', function(data) {
        addRecentBid(data);
        refreshTeamBalances();
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
                <div class="card__content relative z-10 flex flex-col gap-8 p-6 sm:p-8 lg:p-10">
                    <!-- Player Header -->
                    <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:gap-8 mb-8">
                        <div class="relative flex-shrink-0 mx-auto sm:mx-0">
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
                        <div class="text-center sm:text-left flex-grow w-full">
                            <p class="text-xs sm:text-sm uppercase tracking-wide text-indigo-500 font-semibold mb-2 playerRole position">
                                ${positionName}
                            </p>
                            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-4 text-gray-800 playerName">
                                ${data.player.name}
                            </h2>
                            ${isRetained ? `<div class="flex flex-wrap gap-2 justify-center sm:justify-start mb-4">${retainedBadge}</div>` : ''}
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

        document.querySelectorAll('.basePrice').forEach(element => {
            if (element) {
                element.textContent = data.league_player.base_price;
            }
        });
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
                    <span class="block text-xl font-bold text-emerald-600">₹${Number(data.new_bid).toLocaleString('en-IN')}</span>
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
            currentBidElement.textContent = Number(data.new_bid).toLocaleString('en-IN');
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
        const leagueSlug = document.getElementById('league-slug').value;
        
        return fetch(`/api/auctions/league/${leagueSlug}/team-balances`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.teams) {
                    data.teams.forEach(updateTeamCardData);
                }
                updateLastUpdated();
            })
            .catch(error => {
                console.error('Error fetching team balances:', error);
                updateLastUpdated();
            });
    }
    
    function updateTeamCardData(team) {
        if (!team || !team.id) {
            return;
        }
        const card = document.querySelector(`[data-team-card="${team.id}"]`);
        if (!card) {
            return;
        }
        
        const setText = (selector, value, formatter) => {
            const element = card.querySelector(selector);
            if (!element || value === undefined || value === null) {
                return;
            }
            element.textContent = formatter ? formatter(value) : value;
        };
        
        setText('[data-team-balance]', team.wallet_balance, numberFormat);
        setText('[data-team-players]', team.players_count);
        setText('[data-team-needed]', team.players_needed);
        setText('[data-team-reserve]', team.reserve_amount, numberFormat);
        setText('[data-team-max]', team.max_bid_cap, numberFormat);
        setText('[data-team-retained]', team.retained_players_count);
        setText('[data-team-sold]', team.sold_players_count);
    }
    
    // Helper function to format numbers with commas
    function numberFormat(number) {
        const value = Number(number);
        if (Number.isNaN(value)) {
            return '0';
        }
        return new Intl.NumberFormat('en-IN').format(Math.max(0, value));
    }

    function focusCurrentPlayerCard() {
        const playerCardContainer = document.getElementById('currentPlayerCard');
        if (!playerCardContainer) {
            return;
        }

        requestAnimationFrame(() => {
            playerCardContainer.scrollIntoView({
                behavior: 'smooth',
                block: 'center',
                inline: 'center'
            });

            if (typeof playerCardContainer.focus === 'function') {
                try {
                    playerCardContainer.focus({ preventScroll: true });
                } catch (error) {
                    playerCardContainer.focus();
                }
            }
        });
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
