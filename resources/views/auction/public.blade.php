@extends('layouts.app')

@section('title', 'Auction Results - ' . $league->name)

@section('head')
    <!-- Auto-refresh the page every 30 seconds -->
    <meta http-equiv="refresh" content="30">
@endsection

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Back Link -->
    <div class="mb-6">
        <a href="{{ route('leagues.show', $league) }}" class="text-indigo-600 hover:text-indigo-700 font-semibold flex items-center gap-1">← Back to League</a>
    </div>
    
    <!-- Header -->
    <div class="mb-10 text-center">
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3">{{ $league->name }} Auction</h1>
        <p class="text-gray-600 text-lg">Live auction results and team standings</p>
        <div class="mt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
            <button onclick="window.location.reload()" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh Now
            </button>
            <div class="text-sm text-gray-600">
                Auto-refreshes in <span id="countdown">30</span> seconds
            </div>
        </div>
    </div>
    
    <script>
        // Set up countdown timer
        let countdown = 30;
        const countdownElement = document.getElementById('countdown');
        
        setInterval(function() {
            countdown--;
            if (countdown <= 0) {
                countdown = 30;
            }
            countdownElement.textContent = countdown;
        }, 1000);
    </script>
    
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white rounded-xl border border-gray-200 shadow-md p-6 text-center">
            <div class="text-3xl font-bold text-indigo-600 mb-2">{{ $stats['sold'] }}</div>
            <div class="text-gray-700">Players Sold</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-md p-6 text-center">
            <div class="text-3xl font-bold text-indigo-600 mb-2">{{ $stats['unsold'] + $stats['skip'] }}</div>
            <div class="text-gray-700">Players Available</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-md p-6 text-center">
            <div class="text-3xl font-bold text-indigo-600 mb-2">₹{{ number_format($stats['total_spent']) }}</div>
            <div class="text-gray-700">Total Spent</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-md p-6 text-center">
            <div class="text-3xl font-bold text-indigo-600 mb-2">{{ $leagueTeams->count() }}</div>
            <div class="text-gray-700">Teams</div>
        </div>
    </div>
    
    <!-- Last Updated -->
    <div class="text-center text-sm text-gray-500 mb-8">
        Last updated: {{ now()->format('M d, Y H:i:s') }}
    </div>
    
    <!-- League Teams Section -->
    <div class="mb-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b border-gray-200">Team Standings</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($leagueTeams as $team)
                <div class="bg-white rounded-xl border border-gray-200 shadow-md hover:shadow-lg transition-shadow duration-300 p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-xl font-semibold text-gray-900">{{ $team->pivot->name }}</h3>
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium">
                            {{ $league->leaguePlayers()->wherePivot('league_team_id', $team->pivot->id)->wherePivot('auction_status', 'sold')->count() }} Players
                        </span>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Purse Balance:</span>
                            <span class="font-medium text-green-600">₹{{ number_format($team->pivot->purse_balance) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Spent:</span>
                            <span class="text-gray-900">₹{{ number_format($team->pivot->initial_purse_balance - $team->pivot->purse_balance) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Remaining Slots:</span>
                            <span class="text-gray-900">{{ $league->min_players_needed - $league->leaguePlayers()->wherePivot('league_team_id', $team->pivot->id)->wherePivot('auction_status', 'sold')->count() }}</span>
                        </div>
                    </div>
                    
                    <!-- Top Players -->
                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Top Buys:</h4>
                        @php
                            $topPlayers = $league->leaguePlayers()
                                ->wherePivot('league_team_id', $team->pivot->id)
                                ->wherePivot('auction_status', 'sold')
                                ->orderByPivot('bid_amount', 'desc')
                                ->take(2)
                                ->get();
                        @endphp
                        
                        @if($topPlayers->count() > 0)
                            <div class="space-y-2">
                                @foreach($topPlayers as $player)
                                    <div class="flex justify-between items-center text-sm">
                                        <span>{{ $player->name }}</span>
                                        <span class="font-medium text-indigo-600">₹{{ number_format($player->pivot->bid_amount) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic">No players purchased yet</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
    <!-- Recent Auction Results -->
    <div>
        <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b border-gray-200">Recent Auction Results</h2>
        
        @if($recentSoldPlayers->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($recentSoldPlayers as $player)
                    @php
                        $team = $leagueTeams->firstWhere('pivot.id', $player->pivot->league_team_id);
                    @endphp
                    <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-4">
                            <h3 class="text-white font-semibold truncate">{{ $player->name }}</h3>
                            <p class="text-indigo-100 text-sm">{{ $player->position }}</p>
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-gray-600 text-sm">Sold To:</span>
                                <span class="font-medium text-gray-900">{{ $team ? $team->pivot->name : 'Unknown Team' }}</span>
                            </div>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-gray-600 text-sm">Bid Amount:</span>
                                <span class="font-bold text-green-600">₹{{ number_format($player->pivot->bid_amount) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs text-gray-500">
                                <span>{{ $player->pivot->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-8 text-center">
                <a href="{{ route('leagues.show', $league) }}" class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-md">
                    View League Details
                </a>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
                <p class="text-yellow-800 text-lg">No players have been sold in the auction yet.</p>
                <p class="text-yellow-700 mt-2">Check back soon for the latest auction results!</p>
            </div>
        @endif
    </div>
</div>
@endsection
