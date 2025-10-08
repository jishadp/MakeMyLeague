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
</style>
@endsection

@section('content')
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
                        <div class="text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-lg">Waiting for next player...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Balances -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Team Balances</h2>
                    <div class="space-y-3" id="teamBalances">
                        @foreach($league->leagueTeams as $leagueTeam)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                    {{ substr($leagueTeam->team->name, 0, 1) }}
                                </div>
                                <span class="font-medium text-gray-900">{{ $leagueTeam->team->name }}</span>
                            </div>
                            <span class="font-semibold text-green-600">₹{{ number_format($leagueTeam->wallet_balance, 0) }}</span>
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
    var pusher = new Pusher('a1afbae37f05666fb5b6', { cluster: 'ap2' });
    var channel = pusher.subscribe('auctions');
    
    // Auto-refresh every 5 seconds
    setInterval(function() {
        updateLastUpdated();
        refreshTeamBalances();
    }, 5000);
    
    // Listen for new player started
    channel.bind('new-player-started', function(data) {
        if (data.league.id === {{ $league->id }}) {
            updateCurrentPlayer(data);
        }
    });
    
    // Listen for new bids
    channel.bind('new-player-bid-call', function(data) {
        if (data.league_team.league_id === {{ $league->id }}) {
            addRecentBid(data);
            updateTeamBalances();
        }
    });
    
    function updateCurrentPlayer(data) {
        const playerCard = document.getElementById('currentPlayerCard');
        playerCard.innerHTML = `
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-left">
                        <h3 class="text-2xl font-bold">${data.player.name}</h3>
                        <p class="text-indigo-100">${data.player.position.name}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-indigo-100">Base Price</p>
                        <p class="text-3xl font-bold">₹${data.league_player.base_price}</p>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-indigo-100">Current Bid: <span class="font-bold" id="currentBid">₹${data.league_player.base_price}</span></span>
                    <span class="text-indigo-100">Leading: <span class="font-bold" id="leadingTeam">Awaiting bids...</span></span>
                </div>
            </div>
        `;
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
                    <span class="text-xl font-bold text-green-600">₹${data.new_bid.amount}</span>
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
            currentBidElement.textContent = `₹${data.new_bid.amount}`;
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
        // This would typically make an AJAX call to get updated balances
        // For now, we'll just update the timestamp
        updateLastUpdated();
    }
});
</script>
@endsection
