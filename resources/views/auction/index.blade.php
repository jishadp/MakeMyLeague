@extends('layouts.app')

@section('title', 'Auction - CricBid')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-4 py-4 sm:px-6 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Auction - {{ $league->name }}</h1>
                        <p class="text-gray-600">Place bids on players through competitive bidding</p>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 w-full sm:w-auto">
                        @if($userTeam)
                        <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg w-full sm:w-auto text-center">
                            <strong>{{ $userTeam->team->name }}</strong> - ₹{{ number_format($userTeam->wallet_balance) }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @include('auction.partials.available-players')
            @include('auction.partials.player-bidding')
        </div>
    </div>
</div>

<!-- Message Container -->
<div id="messageContainer" class="fixed top-4 right-4 z-50"></div>

<script>
let currentSelectedPlayerId = null;
let baseBidAmount = 0;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Player search functionality
    document.getElementById('playerSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const playerCards = document.querySelectorAll('.player-card');
        
        playerCards.forEach(card => {
            const playerName = card.getAttribute('data-player-name').toLowerCase();
            const showMatch = playerName.includes(searchTerm);
            card.style.display = showMatch ? 'block' : 'none';
        });
    });
    
    // Place bid button
    document.getElementById('placeBidBtn').addEventListener('click', function() {
        const leaguePlayerId = currentSelectedPlayerId;
        const leagueTeamId = document.getElementById('league_team_id').value;
        const amount = parseFloat(document.getElementById('bidAmount').value);
        
        if (!leaguePlayerId) {
            showMessage('No player selected', 'error');
            return;
        }
        
        if (!leagueTeamId) {
            showMessage('Please select a team', 'error');
            return;
        }
        
        if (isNaN(amount) || amount < baseBidAmount) {
            showMessage('Bid amount must be at least the base price', 'error');
            return;
        }
        
        // Send the bid
        placeBid(leaguePlayerId, leagueTeamId, amount);
    });
    
    // Accept bid button
    document.getElementById('acceptBidBtn').addEventListener('click', function() {
        if (!currentSelectedPlayerId) {
            showMessage('No player selected', 'error');
            return;
        }
        
        if (!confirm('Are you sure you want to sell this player to the highest bidder?')) {
            return;
        }
        
        acceptBid(currentSelectedPlayerId);
    });
    
    // Skip player button
    document.getElementById('skipPlayerBtn').addEventListener('click', function() {
        if (!currentSelectedPlayerId) {
            showMessage('No player selected', 'error');
            return;
        }
        
        if (!confirm('Are you sure you want to skip this player? They will be marked as unsold.')) {
            return;
        }
        
        skipPlayer(currentSelectedPlayerId);
    });
});

// Select player for bidding
function selectPlayerForBidding(playerId, playerName, basePrice) {
    // Set selected player
    currentSelectedPlayerId = playerId;
    document.getElementById('selectedLeaguePlayerId').value = playerId;
    document.getElementById('selectedPlayerName').textContent = playerName;
    document.getElementById('selectedPlayerBasePrice').textContent = 'Base Price: ₹' + parseFloat(basePrice).toLocaleString();
    
    // Set bid amounts
    baseBidAmount = parseFloat(basePrice);
    document.getElementById('bidAmount').value = baseBidAmount;
    
    // Show bidding card, hide placeholder
    document.getElementById('noBiddingPlayer').classList.add('hidden');
    document.getElementById('biddingCard').classList.remove('hidden');
    
    // Fetch current bids for this player
    fetchCurrentBids(playerId);
}

// Place a bid
function placeBid(leaguePlayerId, leagueTeamId, amount) {
    fetch('{{ route("auction.place-bid", $league->slug) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            league_player_id: leaguePlayerId,
            league_team_id: leagueTeamId,
            amount: amount
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            fetchCurrentBids(leaguePlayerId);
        } else {
            showMessage(data.error, 'error');
        }
    })
    .catch(error => {
        console.error('Error placing bid:', error);
        showMessage('An error occurred while placing the bid', 'error');
    });
}

// Accept the highest bid
function acceptBid(leaguePlayerId) {
    fetch('{{ route("auction.accept-bid", $league->slug) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            league_player_id: leaguePlayerId
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
        console.error('Error accepting bid:', error);
        showMessage('An error occurred while accepting the bid', 'error');
    });
}

// Skip player (mark as unsold)
function skipPlayer(leaguePlayerId) {
    fetch('{{ route("auction.skip-player", $league->slug) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            league_player_id: leaguePlayerId
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
        console.error('Error skipping player:', error);
        showMessage('An error occurred while skipping the player', 'error');
    });
}

// Fetch current bids for a player
function fetchCurrentBids(leaguePlayerId) {
    if (!leaguePlayerId) return;
    
    fetch('{{ route("auction.current-bids", $league->slug) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            league_player_id: leaguePlayerId
        })
    })
    .then(response => response.json())
    .then(bids => {
        updateBiddingHistory(bids);
    })
    .catch(error => {
        console.error('Error fetching bids:', error);
    });
}

// Update bidding history display
function updateBiddingHistory(bids) {
    const historyContainer = document.getElementById('biddingHistory');
    
    if (!bids || bids.length === 0) {
        historyContainer.innerHTML = `
            <div class="text-center py-4 text-gray-500">
                <p>No bids yet</p>
            </div>
        `;
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
}

// Show message
function showMessage(message, type) {
    const container = document.getElementById('messageContainer');
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
        warning: 'bg-yellow-500'
    };
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg mb-4 flex items-center`;
    
    const icon = document.createElement('span');
    icon.className = 'mr-2 text-xl';
    
    if (type === 'success') {
        icon.innerHTML = '✅';
    } else if (type === 'error') {
        icon.innerHTML = '❌';
    } else if (type === 'info') {
        icon.innerHTML = 'ℹ️';
    } else if (type === 'warning') {
        icon.innerHTML = '⚠️';
    }
    
    messageDiv.appendChild(icon);
    
    const textSpan = document.createElement('span');
    textSpan.textContent = message;
    messageDiv.appendChild(textSpan);
    
    container.appendChild(messageDiv);
    
    // Remove after 4 seconds
    setTimeout(() => {
        messageDiv.remove();
    }, 4000);
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
