@extends('layouts.app')

@section('title', 'Auction Bidding - CricBid')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Auction Bidding - {{ $league->name }}</h1>
                        <p class="text-gray-600">Place bids on players through competitive bidding</p>
                    </div>
                    <div class="flex space-x-4">
                        <a href="{{ route('auction.manual', $league->slug) }}" 
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Switch to Manual Auction
                        </a>
                        @if($userTeam)
                        <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg">
                            <strong>{{ $userTeam->team->name }}</strong> - ₹{{ number_format($userTeam->wallet_balance) }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($currentPlayer)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Current Player Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Current Player Up for Auction</h2>
                    </div>
                    <div class="p-6">
                        <!-- Player Info Card -->
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-6 text-white mb-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-2xl font-bold mb-2">{{ $currentPlayer->user->name }}</h3>
                                    <div class="space-y-1">
                                        <p class="text-blue-100">📱 {{ $currentPlayer->user->mobile }}</p>
                                        <p class="text-blue-100">💰 Base Price: ₹{{ number_format($currentPlayer->base_price) }}</p>
                                        @if($currentPlayer->retention)
                                            <span class="bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-sm font-medium">
                                                Retention Player
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                                        <p class="text-sm text-blue-100">Current Highest Bid</p>
                                        <p class="text-2xl font-bold" id="currentHighestBid">
                                            ₹{{ $currentBids->first() ? number_format($currentBids->first()->amount) : number_format($currentPlayer->base_price) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bidding Actions -->
                        @if($userTeam)
                        <div class="mb-6">
                            <form id="biddingForm" class="flex space-x-4">
                                @csrf
                                <input type="hidden" name="league_player_id" value="{{ $currentPlayer->id }}">
                                <input type="hidden" name="league_team_id" value="{{ $userTeam->id }}">
                                
                                <div class="flex-1">
                                    <input type="number" name="amount" id="bidAmount" 
                                           min="{{ $currentBids->first() ? $currentBids->first()->amount + 100 : $currentPlayer->base_price }}"
                                           step="100" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg"
                                           placeholder="Enter your bid amount">
                                </div>
                                <button type="submit" 
                                        class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg text-lg font-medium transition-colors">
                                    Place Bid
                                </button>
                            </form>
                            <p class="text-sm text-gray-500 mt-2">
                                Minimum bid: ₹{{ number_format($currentBids->first() ? $currentBids->first()->amount + 100 : $currentPlayer->base_price) }}
                            </p>
                        </div>
                        @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                            <p class="text-yellow-800">You need to be associated with a team to place bids.</p>
                        </div>
                        @endif

                        <!-- Admin Controls -->
                        <div class="border-t pt-6">
                            <div class="flex space-x-4">
                                <button onclick="acceptHighestBid()" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                                    Sell to Highest Bidder
                                </button>
                                <button onclick="skipPlayer()" 
                                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors">
                                    Skip Player (No Sale)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bidding History Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Current Bids</h2>
                    </div>
                    <div class="p-6">
                        <div id="biddingHistory" class="space-y-3 max-h-96 overflow-y-auto">
                            @forelse($currentBids as $index => $bid)
                            <div class="border border-gray-200 rounded-lg p-3 {{ $index === 0 ? 'bg-green-50 border-green-300' : '' }}">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $bid->leagueTeam->team->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $bid->created_at->format('H:i:s') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-lg {{ $index === 0 ? 'text-green-600' : 'text-gray-900' }}">
                                            ₹{{ number_format($bid->amount) }}
                                        </p>
                                        @if($index === 0)
                                            <p class="text-xs text-green-600">Highest</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8 text-gray-500">
                                <p>No bids yet</p>
                                <p class="text-sm">Starting at ₹{{ number_format($currentPlayer->base_price) }}</p>
                            </div>
                            @endforelse
                        </div>

                        <!-- Auto-refresh toggle -->
                        <div class="mt-4 pt-4 border-t">
                            <label class="flex items-center">
                                <input type="checkbox" id="autoRefresh" checked class="mr-2">
                                <span class="text-sm text-gray-600">Auto-refresh bids</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Teams Wallet Status -->
                <div class="bg-white rounded-lg shadow mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Team Wallets</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-2">
                            @foreach($leagueTeams as $team)
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-gray-700">{{ $team->team->name }}</span>
                                <span class="text-sm font-medium">₹{{ number_format($team->wallet_balance) }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <div class="text-gray-400 mb-4">
                <svg class="mx-auto h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">No Players Available</h2>
            <p class="text-gray-600">All players have been sold or there are no available players for auction at the moment.</p>
        </div>
        @endif
    </div>
</div>

<!-- Success/Error Messages -->
<div id="messageContainer" class="fixed top-4 right-4 z-50"></div>

<script>
let autoRefreshInterval;

// Initialize auto-refresh
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('autoRefresh').checked) {
        startAutoRefresh();
    }
});

// Auto-refresh toggle
document.getElementById('autoRefresh')?.addEventListener('change', function() {
    if (this.checked) {
        startAutoRefresh();
    } else {
        stopAutoRefresh();
    }
});

function startAutoRefresh() {
    autoRefreshInterval = setInterval(refreshBids, 5000); // Refresh every 5 seconds
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
}

// Bidding form submission
document.getElementById('biddingForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("auction.place-bid", $league->slug) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            refreshBids();
            // Clear the form
            document.getElementById('bidAmount').value = '';
        } else {
            showMessage(data.error, 'error');
        }
    })
    .catch(error => {
        showMessage('An error occurred while placing the bid', 'error');
        console.error('Error:', error);
    });
});

// Refresh bids
function refreshBids() {
    @if($currentPlayer)
    fetch(`{{ route("auction.current-bids", [$league->slug, $currentPlayer->id]) }}`)
        .then(response => response.json())
        .then(bids => {
            updateBiddingHistory(bids);
            updateMinimumBid(bids);
        })
        .catch(error => {
            console.error('Error refreshing bids:', error);
        });
    @endif
}

// Update bidding history
function updateBiddingHistory(bids) {
    const historyContainer = document.getElementById('biddingHistory');
    if (bids.length === 0) {
        historyContainer.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <p>No bids yet</p>
                <p class="text-sm">Starting at ₹{{ $currentPlayer ? number_format($currentPlayer->base_price) : '0' }}</p>
            </div>
        `;
        document.getElementById('currentHighestBid').textContent = '₹{{ $currentPlayer ? number_format($currentPlayer->base_price) : '0' }}';
        return;
    }

    let historyHTML = '';
    bids.forEach((bid, index) => {
        historyHTML += `
            <div class="border border-gray-200 rounded-lg p-3 ${index === 0 ? 'bg-green-50 border-green-300' : ''}">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-medium text-gray-900">${bid.league_team.team.name}</p>
                        <p class="text-sm text-gray-600">${new Date(bid.created_at).toLocaleTimeString()}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-lg ${index === 0 ? 'text-green-600' : 'text-gray-900'}">
                            ₹${parseFloat(bid.amount).toLocaleString()}
                        </p>
                        ${index === 0 ? '<p class="text-xs text-green-600">Highest</p>' : ''}
                    </div>
                </div>
            </div>
        `;
    });

    historyContainer.innerHTML = historyHTML;
    
    // Update highest bid display
    if (bids.length > 0) {
        document.getElementById('currentHighestBid').textContent = '₹' + parseFloat(bids[0].amount).toLocaleString();
    }
}

// Update minimum bid amount
function updateMinimumBid(bids) {
    const bidAmountInput = document.getElementById('bidAmount');
    if (bidAmountInput) {
        const minBid = bids.length > 0 ? bids[0].amount + 100 : {{ $currentPlayer ? $currentPlayer->base_price : 0 }};
        bidAmountInput.min = minBid;
        bidAmountInput.placeholder = `Minimum: ₹${parseFloat(minBid).toLocaleString()}`;
    }
}

// Accept highest bid
function acceptHighestBid() {
    if (!confirm('Are you sure you want to sell this player to the highest bidder?')) {
        return;
    }

    fetch('{{ route("auction.accept-bid", $league->slug) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            league_player_id: {{ $currentPlayer ? $currentPlayer->id : 'null' }}
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showMessage(data.error, 'error');
        }
    })
    .catch(error => {
        showMessage('An error occurred while accepting the bid', 'error');
        console.error('Error:', error);
    });
}

// Skip player
function skipPlayer() {
    if (!confirm('Are you sure you want to skip this player? They will be marked as unsold.')) {
        return;
    }
    
    // Implementation for skipping player (you'll need to create this route and method)
    showMessage('Feature coming soon', 'info');
}

// Show message
function showMessage(message, type) {
    const container = document.getElementById('messageContainer');
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg mb-4`;
    messageDiv.textContent = message;
    
    container.appendChild(messageDiv);
    
    setTimeout(() => {
        messageDiv.remove();
    }, 5000);
}

// Add CSRF token to page head if not present
if (!document.querySelector('meta[name="csrf-token"]')) {
    const csrfMeta = document.createElement('meta');
    csrfMeta.name = 'csrf-token';
    csrfMeta.content = '{{ csrf_token() }}';
    document.head.appendChild(csrfMeta);
}
</script>
@endsection
