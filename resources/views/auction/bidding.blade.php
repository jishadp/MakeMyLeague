@extends('layouts.app')

@section('title', 'Auction Bidding - CricBid')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow mb-6 transition-all duration-300 hover:shadow-md">
            <div class="px-4 py-4 sm:px-6 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Bidding Auction - {{ $league->name }}</h1>
                        <p class="text-gray-600">Place bids on players through competitive bidding</p>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 w-full sm:w-auto">
                        <a href="{{ route('auction.manual', $league->slug) }}" 
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 w-full sm:w-auto text-center">
                            Switch to Manual Auction
                        </a>
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
            <!-- Available Players Section (Same as Manual Auction) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow transition-all duration-300 hover:shadow-md">
                    <div class="px-4 py-4 sm:px-6 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Available Players</h2>
                                <p class="text-sm text-gray-600">Select players to auction by bidding</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 sm:p-6">
                        <!-- Search Bar -->
                        <div class="mb-4">
                            <input type="text" id="playerSearch" placeholder="Search players by name or mobile..." 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>

                        <!-- Players List -->
                        <div class="space-y-3" id="playersList">
                            @forelse($availablePlayers as $key => $player)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-all duration-200 hover:shadow-sm player-card animate-fade-in {{ $key >= 3 ? 'hidden' : '' }}" 
                                 data-player-id="{{ $player->user->id }}"
                                 data-player-name="{{ $player->user->name }}"
                                 data-base-price="{{ $player->base_price }}"
                                 data-league-player-id="{{ $player->id }}">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                    <div class="flex items-center space-x-3 w-full">
                                        <div class="flex-1">
                                            <h3 class="font-medium text-gray-900">{{ $player->user->name }}</h3>
                                            <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600 mt-1">
                                                <span>📱 {{ $player->user->mobile }}</span>
                                                <span>💰 Base Price: ₹{{ number_format($player->base_price) }}</span>
                                                @if($player->retention)
                                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">Retention</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <button onclick="selectPlayerForBidding('{{ $player->id }}', '{{ $player->user->name }}', '{{ $player->base_price }}')"
                                            class="auction-btn bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200 w-full sm:w-auto relative overflow-hidden">
                                        <span class="relative z-10">Start Bidding</span>
                                        <span class="absolute inset-0 bg-green-500 transform scale-x-0 origin-left transition-transform duration-300 bid-btn-pulse"></span>
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8 text-gray-500">
                                <p>No available players for auction</p>
                            </div>
                            @endforelse
                        </div>

                        @if(count($availablePlayers) > 3)
                        <div class="mt-4 text-center" id="showMoreContainer">
                            <button id="showMoreBtn" class="text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
                                Show More Players ({{ count($availablePlayers) - 3 }} remaining)
                            </button>
                        </div>
                        @endif

                        <!-- Pagination -->
                        @if($availablePlayers->hasPages())
                        <div class="mt-6 overflow-x-auto">
                            {{ $availablePlayers->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bidding Section -->
            <div class="lg:col-span-1" id="biddingSection">
                <div class="bg-white rounded-lg shadow transition-all duration-300 hover:shadow-md mb-6">
                    <div class="px-4 py-4 sm:px-6 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg font-semibold text-gray-900">Player Bidding</h2>
                            <button id="backToPlayersBtn" class="lg:hidden bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded-lg text-sm transition-colors duration-200">
                                Back to Players
                            </button>
                        </div>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div id="noBiddingPlayer" class="text-center py-4 text-gray-500">
                            <p>Select a player to start bidding</p>
                        </div>

                        <div id="biddingCard" class="hidden">
                            <!-- Selected Player Info -->
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-4 mb-4 text-white shadow-lg transform transition-all duration-300">
                                <h3 class="text-xl font-bold mb-2" id="selectedPlayerName"></h3>
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-blue-100 text-sm" id="selectedPlayerBasePrice"></p>
                                    </div>
                                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                                        <p class="text-xs text-blue-100">Current Bid</p>
                                        <p class="text-xl font-bold" id="currentBidAmount"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Team Selection (for non-team users) -->
                            <div class="mb-4">
                                <label for="league_team_id" class="block text-sm font-medium text-gray-700 mb-2">Select Team</label>
                                <select name="league_team_id" id="league_team_id" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                    <option value="">Choose a team...</option>
                                    @foreach($leagueTeams as $leagueTeam)
                                    <option value="{{ $leagueTeam->id }}" data-wallet="{{ $leagueTeam->wallet_balance }}">
                                        {{ $leagueTeam->team->name }} (₹{{ number_format($leagueTeam->wallet_balance) }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Bidding Actions -->
                            <div class="mb-4">
                                <input type="hidden" id="selectedLeaguePlayerId">
                                
                                <!-- Current Bid Control -->
                                <div class="border border-gray-200 rounded-lg p-3 mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Bid Amount</label>
                                    <div class="flex items-center">
                                        <input type="number" id="bidAmount" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <div class="flex ml-2">
                                            <button id="incrementBidBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-l-lg transition-colors duration-200">
                                                +100
                                            </button>
                                            <button id="incrementBidBtn500" class="bg-blue-700 hover:bg-blue-800 text-white px-3 py-2 transition-colors duration-200">
                                                +500
                                            </button>
                                            <button id="incrementBidBtn1000" class="bg-blue-800 hover:bg-blue-900 text-white px-3 py-2 rounded-r-lg transition-colors duration-200">
                                                +1000
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Quick Bid Buttons -->
                                <div class="grid grid-cols-3 gap-2 mb-3">
                                    <button class="quick-bid-btn bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-2 rounded-lg transition-colors duration-200" data-amount="5000">
                                        ₹5,000
                                    </button>
                                    <button class="quick-bid-btn bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-2 rounded-lg transition-colors duration-200" data-amount="10000">
                                        ₹10,000
                                    </button>
                                    <button class="quick-bid-btn bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-2 rounded-lg transition-colors duration-200" data-amount="25000">
                                        ₹25,000
                                    </button>
                                </div>
                                
                                <!-- Custom Bid Input -->
                                <div class="border border-gray-200 rounded-lg p-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Custom Bid Amount</label>
                                    <div class="flex items-center">
                                        <input type="number" id="customBidAmount" step="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <button id="setCustomBidBtn" class="ml-2 bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 rounded-lg transition-colors duration-200">
                                            Set
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Place Bid Button -->
                            <button id="placeBidBtn" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg transition-colors duration-200 text-lg font-medium mb-3 relative overflow-hidden">
                                <span class="relative z-10">Place Bid</span>
                                <span class="absolute inset-0 bg-green-500 transform scale-x-0 origin-left transition-transform duration-300" id="bidPulse"></span>
                            </button>
                            
                            <!-- Admin Controls -->
                            <div class="flex space-x-2 mt-4 pt-4 border-t border-gray-200">
                                <button id="acceptBidBtn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg transition-colors duration-200 text-sm">
                                    Sell to Highest
                                </button>
                                <button id="skipPlayerBtn" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors duration-200 text-sm">
                                    Skip Player
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Bids -->
                <div class="bg-white rounded-lg shadow transition-all duration-300 hover:shadow-md">
                    <div class="px-4 py-4 sm:px-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Bids</h2>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div id="biddingHistory" class="space-y-3 max-h-80 overflow-y-auto">
                            <div class="text-center py-4 text-gray-500">
                                <p>No bids yet</p>
                            </div>
                        </div>
                        
                        <!-- Auto-refresh toggle -->
                        <div class="mt-4 pt-4 border-t">
                            <label class="flex items-center">
                                <input type="checkbox" id="autoRefresh" checked class="mr-2">
                                <span class="text-sm text-gray-600">Auto-refresh bids (every 5s)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Message Container -->
<div id="messageContainer" class="fixed top-4 right-4 z-50"></div>

<script>
let autoRefreshInterval;
let bidButtonLocked = false;
let currentSelectedPlayerId = null;
let currentBidAmount = 0;
let baseBidAmount = 0;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('autoRefresh').checked) {
        startAutoRefresh();
    }
    
    // "Show More" button functionality
    if (document.getElementById('showMoreBtn')) {
        document.getElementById('showMoreBtn').addEventListener('click', function() {
            const hiddenPlayers = document.querySelectorAll('.player-card.hidden');
            hiddenPlayers.forEach(player => {
                player.classList.remove('hidden');
                player.style.display = 'block';
            });
            document.getElementById('showMoreContainer').style.display = 'none';
        });
    }
    
    // Player search functionality
    document.getElementById('playerSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const playerCards = document.querySelectorAll('.player-card');
        const showMoreBtn = document.getElementById('showMoreBtn');
        const showMoreContainer = document.getElementById('showMoreContainer');
        
        // If search is active, show all matching players
        if (searchTerm.length > 0) {
            playerCards.forEach(card => {
                const playerName = card.getAttribute('data-player-name').toLowerCase();
                const showMatch = playerName.includes(searchTerm);
                card.style.display = showMatch ? 'block' : 'none';
                if (showMatch) {
                    card.classList.remove('hidden');
                }
            });
            // Hide "Show More" button during search
            if (showMoreContainer) showMoreContainer.style.display = 'none';
        } else {
            // If search is cleared, go back to showing only first 3
            playerCards.forEach((card, index) => {
                if (index < 3) {
                    card.style.display = 'block';
                    card.classList.remove('hidden');
                } else {
                    card.style.display = 'none';
                    card.classList.add('hidden');
                }
            });
            // Show "Show More" button again
            if (showMoreContainer) showMoreContainer.style.display = 'block';
        }
    });

    // Increment bid button
    document.getElementById('incrementBidBtn').addEventListener('click', function() {
        if (bidButtonLocked) return;
        lockBidButton();
        incrementBid(100);
    });
    
    // Increment bid by 500
    document.getElementById('incrementBidBtn500').addEventListener('click', function() {
        if (bidButtonLocked) return;
        lockBidButton();
        incrementBid(500);
    });
    
    // Increment bid by 1000
    document.getElementById('incrementBidBtn1000').addEventListener('click', function() {
        if (bidButtonLocked) return;
        lockBidButton();
        incrementBid(1000);
    });
    
    // Quick bid buttons
    document.querySelectorAll('.quick-bid-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (bidButtonLocked) return;
            lockBidButton();
            
            const amount = parseInt(this.getAttribute('data-amount'));
            if (amount >= baseBidAmount) {
                document.getElementById('bidAmount').value = amount;
                document.getElementById('customBidAmount').value = amount;
                currentBidAmount = amount;
                // Visual feedback
                this.classList.add('bg-blue-300');
                setTimeout(() => {
                    this.classList.remove('bg-blue-300');
                }, 500);
            } else {
                showMessage('Quick bid amount must be at least the base price', 'error');
            }
        });
    });
    
    // Set custom bid button
    document.getElementById('setCustomBidBtn').addEventListener('click', function() {
        if (bidButtonLocked) return;
        
        lockBidButton();
        
        const customAmount = parseFloat(document.getElementById('customBidAmount').value);
        
        if (isNaN(customAmount) || customAmount < baseBidAmount) {
            showMessage('Custom bid must be at least the base price', 'error');
            return;
        }
        
        // Set the bid amount
        document.getElementById('bidAmount').value = customAmount;
        currentBidAmount = customAmount;
    });
    
    // Place bid button
    document.getElementById('placeBidBtn').addEventListener('click', function() {
        if (bidButtonLocked) return;
        
        lockBidButton();
        
        const leaguePlayerId = currentSelectedPlayerId;
        const leagueTeamId = document.getElementById('league_team_id').value;
        const amount = parseFloat(document.getElementById('bidAmount').value);
        
        if (!leaguePlayerId) {
            showMessage('No player selected', 'error');
            unlockBidButton();
            return;
        }
        
        if (!leagueTeamId) {
            showMessage('Please select a team', 'error');
            unlockBidButton();
            return;
        }
        
        if (isNaN(amount) || amount < baseBidAmount) {
            showMessage('Bid amount must be at least the base price', 'error');
            unlockBidButton();
            return;
        }
        
        // Visual feedback
        const bidPulse = document.getElementById('bidPulse');
        bidPulse.classList.add('scale-x-100');
        setTimeout(() => {
            bidPulse.classList.remove('scale-x-100');
        }, 1000);
        
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
    
    // Auto-refresh toggle
    document.getElementById('autoRefresh').addEventListener('change', function() {
        if (this.checked) {
            startAutoRefresh();
        } else {
            stopAutoRefresh();
        }
    });
});

// Mobile: Back to players button
if (document.getElementById('backToPlayersBtn')) {
    document.getElementById('backToPlayersBtn').addEventListener('click', function() {
        showPlayerList();
        
        // On mobile, scroll back to players section
        if (window.innerWidth < 1024) {
            setTimeout(() => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, 100);
        }
    });
}

// Helper function to increment bid by specific amount
function incrementBid(amount) {
    const bidAmountInput = document.getElementById('bidAmount');
    let currentAmount = parseFloat(bidAmountInput.value);
    
    if (isNaN(currentAmount)) {
        currentAmount = baseBidAmount;
    }
    
    const newAmount = currentAmount + amount;
    bidAmountInput.value = newAmount;
    document.getElementById('customBidAmount').value = newAmount;
    currentBidAmount = newAmount;
}

// Select player for bidding
function selectPlayerForBidding(playerId, playerName, basePrice) {
    // Animate the button
    const buttons = document.querySelectorAll('.auction-btn');
    buttons.forEach(btn => {
        const pulse = btn.querySelector('.bid-btn-pulse');
        if (pulse) pulse.classList.remove('scale-x-100');
    });
    
    const clickedButton = event.currentTarget;
    const pulse = clickedButton.querySelector('.bid-btn-pulse');
    if (pulse) {
        pulse.classList.add('scale-x-100');
        setTimeout(() => {
            pulse.classList.remove('scale-x-100');
        }, 1000);
    }
    
    // Set selected player
    currentSelectedPlayerId = playerId;
    document.getElementById('selectedLeaguePlayerId').value = playerId;
    document.getElementById('selectedPlayerName').textContent = playerName;
    document.getElementById('selectedPlayerBasePrice').textContent = 'Base Price: ₹' + parseFloat(basePrice).toLocaleString();
    
    // Set bid amounts
    baseBidAmount = parseFloat(basePrice);
    currentBidAmount = baseBidAmount;
    document.getElementById('bidAmount').value = baseBidAmount;
    document.getElementById('customBidAmount').value = baseBidAmount;
    document.getElementById('currentBidAmount').textContent = '₹' + baseBidAmount.toLocaleString();
    
    // Show bidding card, hide placeholder
    document.getElementById('noBiddingPlayer').classList.add('hidden');
    document.getElementById('biddingCard').classList.remove('hidden');
    
    // Add a highlight animation to the selected player card
    const playerCardWrapper = document.getElementById('biddingCard');
    playerCardWrapper.classList.add('animate-pulse');
    setTimeout(() => {
        playerCardWrapper.classList.remove('animate-pulse');
    }, 500);
    
    // Fetch current bids for this player
    fetchCurrentBids(playerId);
    
    // On mobile, scroll to bidding section
    if (window.innerWidth < 1024) {
        const biddingSection = document.getElementById('biddingSection');
        if (biddingSection) {
            setTimeout(() => {
                biddingSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
        
        // Try to hide player list on mobile for better UI (safely)
        try {
            const playersList = document.getElementById('playersList');
            if (playersList) {
                playersList.classList.add('lg:block', 'hidden');
            }
        } catch (e) {
            console.log('Note: Could not hide player list on mobile', e);
        }
    }
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
        unlockBidButton();
    })
    .catch(error => {
        console.error('Error placing bid:', error);
        showMessage('An error occurred while placing the bid', 'error');
        unlockBidButton();
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
        updateCurrentBid(bids);
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

// Update current bid display
function updateCurrentBid(bids) {
    const currentBidAmount = document.getElementById('currentBidAmount');
    
    if (!bids || bids.length === 0) {
        currentBidAmount.textContent = '₹' + baseBidAmount.toLocaleString();
        document.getElementById('bidAmount').value = baseBidAmount;
        document.getElementById('customBidAmount').value = baseBidAmount;
        return;
    }
    
    const highestBid = bids[0];
    const newBidAmount = parseFloat(highestBid.amount) + 100; // Next bid is current highest + 100
    
    // Animate if the amount has changed
    const currentAmountText = currentBidAmount.textContent;
    const newAmountText = '₹' + parseFloat(highestBid.amount).toLocaleString();
    
    if (currentAmountText !== newAmountText) {
        currentBidAmount.classList.add('animate-pulse');
        setTimeout(() => {
            currentBidAmount.classList.remove('animate-pulse');
        }, 500);
    }
    
    currentBidAmount.textContent = newAmountText;
    document.getElementById('bidAmount').value = newBidAmount;
    document.getElementById('customBidAmount').value = newBidAmount;
}

// Auto-refresh functionality
function startAutoRefresh() {
    stopAutoRefresh(); // Clear any existing interval
    autoRefreshInterval = setInterval(() => {
        if (currentSelectedPlayerId) {
            fetchCurrentBids(currentSelectedPlayerId);
        }
    }, 5000); // Refresh every 5 seconds
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
}

// Lock bid button for 2 seconds
function lockBidButton() {
    bidButtonLocked = true;
    
    // Disable increment buttons
    document.getElementById('incrementBidBtn').classList.add('bg-gray-400');
    document.getElementById('incrementBidBtn').classList.remove('bg-blue-600', 'hover:bg-blue-700');
    
    document.getElementById('incrementBidBtn500').classList.add('bg-gray-400');
    document.getElementById('incrementBidBtn500').classList.remove('bg-blue-700', 'hover:bg-blue-800');
    
    document.getElementById('incrementBidBtn1000').classList.add('bg-gray-400');
    document.getElementById('incrementBidBtn1000').classList.remove('bg-blue-800', 'hover:bg-blue-900');
    
    // Disable quick bid buttons
    document.querySelectorAll('.quick-bid-btn').forEach(btn => {
        btn.classList.add('bg-gray-200', 'text-gray-500');
        btn.classList.remove('bg-blue-100', 'hover:bg-blue-200', 'text-blue-800');
    });
    
    // Disable custom bid button
    document.getElementById('setCustomBidBtn').classList.add('bg-gray-400');
    document.getElementById('setCustomBidBtn').classList.remove('bg-purple-600', 'hover:bg-purple-700');
    
    // Disable place bid button
    document.getElementById('placeBidBtn').classList.add('bg-gray-400');
    document.getElementById('placeBidBtn').classList.remove('bg-green-600', 'hover:bg-green-700');
    
    // Re-enable after 300 milliseconds
    setTimeout(() => {
        unlockBidButton();
    }, 300);
}

// Unlock bid button
function unlockBidButton() {
    bidButtonLocked = false;
    
    // Re-enable increment buttons
    document.getElementById('incrementBidBtn').classList.remove('bg-gray-400');
    document.getElementById('incrementBidBtn').classList.add('bg-blue-600', 'hover:bg-blue-700');
    
    document.getElementById('incrementBidBtn500').classList.remove('bg-gray-400');
    document.getElementById('incrementBidBtn500').classList.add('bg-blue-700', 'hover:bg-blue-800');
    
    document.getElementById('incrementBidBtn1000').classList.remove('bg-gray-400');
    document.getElementById('incrementBidBtn1000').classList.add('bg-blue-800', 'hover:bg-blue-900');
    
    // Re-enable quick bid buttons
    document.querySelectorAll('.quick-bid-btn').forEach(btn => {
        btn.classList.remove('bg-gray-200', 'text-gray-500');
        btn.classList.add('bg-blue-100', 'hover:bg-blue-200', 'text-blue-800');
    });
    
    // Re-enable custom bid button
    document.getElementById('setCustomBidBtn').classList.remove('bg-gray-400');
    document.getElementById('setCustomBidBtn').classList.add('bg-purple-600', 'hover:bg-purple-700');
    
    // Re-enable place bid button
    document.getElementById('placeBidBtn').classList.remove('bg-gray-400');
    document.getElementById('placeBidBtn').classList.add('bg-green-600', 'hover:bg-green-700');
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
    messageDiv.className = `${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg mb-4 animate-slide-in flex items-center`;
    
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
    
    // Animate out after 4 seconds
    setTimeout(() => {
        messageDiv.classList.remove('animate-slide-in');
        messageDiv.classList.add('animate-slide-out');
        
        setTimeout(() => {
            messageDiv.remove();
        }, 300);
    }, 4000);
}

// Add CSRF token to page head if not present
if (!document.querySelector('meta[name="csrf-token"]')) {
    const csrfMeta = document.createElement('meta');
    csrfMeta.name = 'csrf-token';
    csrfMeta.content = '{{ csrf_token() }}';
    document.head.appendChild(csrfMeta);
}
    // Show player list function - used when returning to the list from bidding
    function showPlayerList() {
        try {
            const playersList = document.getElementById('playersList');
            if (playersList) {
                // First remove any mobile-specific classes we may have added
                playersList.classList.remove('lg:block', 'hidden');
                
                // Ensure any hidden players are shown based on search status
                const searchTerm = document.getElementById('playerSearch').value.toLowerCase();
                if (!searchTerm) {
                    // If not searching, respect the "show more" logic
                    const playerCards = document.querySelectorAll('.player-card');
                    playerCards.forEach((card, index) => {
                        if (index < 3) {
                            card.classList.remove('hidden');
                        }
                    });
                    // Show the "show more" button if it exists
                    const showMoreContainer = document.getElementById('showMoreContainer');
                    if (showMoreContainer) {
                        showMoreContainer.style.display = '';
                    }
                } else {
                    // If searching, show all matching players
                    const playerCards = document.querySelectorAll('.player-card');
                    playerCards.forEach(card => {
                        card.classList.remove('hidden');
                    });
                }
            }
        } catch (e) {
            console.log('Note: Could not show player list', e);
        }
    }
    
    // Hide player list function - can be called safely
    function hidePlayerList() {
        try {
            const playersList = document.getElementById('playersList');
            if (playersList) {
                playersList.classList.add('lg:block', 'hidden');
            }
        } catch (e) {
            console.log('Note: Could not hide player list', e);
        }
    }
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
@keyframes slide-in {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}
@keyframes slide-out {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
}
.animate-slide-out {
    animation: slide-out 0.3s ease-out;
}
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
.animate-pulse {
    animation: pulse 0.5s ease-in-out;
}
@keyframes highlight {
    0% { background-color: rgba(167, 243, 208, 0.5); }
    100% { background-color: transparent; }
}
.animate-highlight {
    animation: highlight 1.5s ease-out;
}
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}
.animate-shake {
    animation: shake 0.3s ease-in-out;
}
@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-5px); }
    100% { transform: translateY(0px); }
}
.animate-float {
    animation: float 3s ease-in-out infinite;
}
</style>
@endsection
