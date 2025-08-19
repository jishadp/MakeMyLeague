@extends('layouts.app')

@section('title', 'Auction - CricBid')

@section('styles')
<style>
    /* Mobile-specific improvements */
    @media (max-width: 640px) {
        .mobile-input {
            font-size: 16px; /* Prevents zoom on iOS */
        }
        
        .mobile-button {
            min-height: 44px; /* Better touch targets */
        }
        
        .mobile-card {
            margin-bottom: 1rem;
        }
        
        .mobile-text {
            font-size: 14px;
        }
        
        .mobile-text-sm {
            font-size: 12px;
        }
    }
    
    /* Improved focus states for better accessibility */
    .focus-ring:focus {
        outline: 2px solid #4a90e2;
        outline-offset: 2px;
    }
    
    /* Better touch targets for mobile */
    .touch-target {
        min-height: 44px;
        min-width: 44px;
    }

    /* Enhanced Theme with Better Visual Appeal */
    .auction-bg {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%, #f8fafc 100%);
        position: relative;
    }

    .auction-bg::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 20% 80%, rgba(74, 144, 226, 0.03) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(107, 182, 255, 0.03) 0%, transparent 50%),
            radial-gradient(circle at 40% 40%, rgba(135, 206, 235, 0.02) 0%, transparent 50%);
        pointer-events: none;
    }

    .glacier-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 
            0 4px 6px -1px rgba(0, 0, 0, 0.1),
            0 2px 4px -1px rgba(0, 0, 0, 0.06),
            0 0 0 1px rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .glacier-card:hover {
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 
            0 10px 15px -3px rgba(0, 0, 0, 0.1),
            0 4px 6px -2px rgba(0, 0, 0, 0.05),
            0 0 0 1px rgba(255, 255, 255, 0.1);
        transform: translateY(-1px);
    }

    .glacier-gradient {
        background: linear-gradient(135deg, 
            rgba(74, 144, 226, 0.9) 0%, 
            rgba(107, 182, 255, 0.85) 50%, 
            rgba(135, 206, 235, 0.8) 100%);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(74, 144, 226, 0.2);
        box-shadow: 
            0 4px 6px -1px rgba(74, 144, 226, 0.2),
            0 2px 4px -1px rgba(74, 144, 226, 0.1);
    }

    .glacier-border {
        border-color: rgba(226, 232, 240, 0.8);
    }

    .glacier-text-primary {
        color: #1e293b;
    }

    .glacier-text-secondary {
        color: #475569;
    }

    /* Enhanced table styling */
    .table-header {
        background: linear-gradient(135deg, rgba(248, 250, 252, 0.9), rgba(241, 245, 249, 0.9));
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(226, 232, 240, 0.8);
    }

    .table-row {
        background: rgba(255, 255, 255, 0.7);
        transition: all 0.2s ease;
    }

    .table-row:hover {
        background: rgba(248, 250, 252, 0.9);
        transform: scale(1.001);
    }

    /* Enhanced button styling */
    .btn-primary {
        background: linear-gradient(135deg, #4a90e2, #6bb6ff);
        border: 1px solid rgba(74, 144, 226, 0.2);
        box-shadow: 0 2px 4px rgba(74, 144, 226, 0.2);
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #3b82f6, #60a5fa);
        box-shadow: 0 4px 8px rgba(74, 144, 226, 0.3);
        transform: translateY(-1px);
    }

    /* Enhanced input styling */
    .glass-input {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(226, 232, 240, 0.8);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .glass-input:focus {
        background: rgba(255, 255, 255, 0.95);
        border-color: rgba(74, 144, 226, 0.5);
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
    }

    /* Enhanced badge styling */
    .badge-primary {
        background: linear-gradient(135deg, rgba(74, 144, 226, 0.1), rgba(107, 182, 255, 0.1));
        border: 1px solid rgba(74, 144, 226, 0.2);
        color: #1e40af;
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }

    .badge-success {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(16, 185, 129, 0.1));
        border: 1px solid rgba(34, 197, 94, 0.2);
        color: #15803d;
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }

    .badge-warning {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(251, 191, 36, 0.1));
        border: 1px solid rgba(245, 158, 11, 0.2);
        color: #a16207;
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }

    .badge-purple {
        background: linear-gradient(135deg, rgba(147, 51, 234, 0.1), rgba(168, 85, 247, 0.1));
        border: 1px solid rgba(147, 51, 234, 0.2);
        color: #7c3aed;
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }
</style>
@endsection

@section('content')
<div class="min-h-screen auction-bg py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Header -->
        <div class="glacier-card mb-6">
            <div class="px-4 py-4 sm:px-6 border-b glacier-border">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold glacier-text-primary">Auction - {{ $league->name }}</h1>
                        <p class="text-gray-600">Place bids on players through competitive bidding</p>
                    </div>
                </div>
            </div>
            
            <!-- Auction Statistics -->
            <div class="px-4 py-4 sm:px-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="glacier-card p-3 border-0">
                        <div class="flex items-center">
                            <div class="bg-green-100 rounded-full p-2 mr-3">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-green-600 font-medium">Total Players</p>
                                <p class="text-lg font-bold text-green-800">{{ $auctionStats['total_players'] }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="glacier-card p-3 border-0">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-full p-2 mr-3">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-blue-600 font-medium">Available</p>
                                <p class="text-lg font-bold text-blue-800">{{ $auctionStats['available_players'] }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="glacier-card p-3 border-0">
                        <div class="flex items-center">
                            <div class="bg-purple-100 rounded-full p-2 mr-3">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-purple-600 font-medium">Sold</p>
                                <p class="text-lg font-bold text-purple-800">{{ $auctionStats['sold_players'] }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="glacier-card p-3 border-0">
                        <div class="flex items-center">
                            <div class="bg-yellow-100 rounded-full p-2 mr-3">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-yellow-600 font-medium">Revenue</p>
                                <p class="text-lg font-bold text-yellow-800">₹{{ number_format($auctionStats['total_revenue']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Progress</span>
                        <span>{{ $auctionStats['completion_percentage'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-green-500 to-blue-500 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ $auctionStats['completion_percentage'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Players Section - Full Row -->
        <div class="mb-8" id="availablePlayersSection">
                @include('auction.partials.available-players')
            </div>
            
        <!-- Player Bidding Section - Centered -->
        <div class="flex justify-center mb-8" id="biddingSection">
            <div class="w-full max-w-2xl">
                @include('auction.partials.player-bidding')
            </div>
            </div>
            
        <!-- Recent and Highest Bids Table - Full Width -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Recent Bids -->
            <div class="glacier-card">
                <div class="px-4 py-4 sm:px-6 border-b glacier-border">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold glacier-text-primary">Recent Bids</h2>
                        <div class="badge-primary px-3 py-1 rounded-full text-sm font-medium">
                            <span id="recentBidCount">0</span> Recent
                        </div>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <div id="recentBidsTable" class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="table-header">
                                <tr>
                                    <th class="text-left py-3 px-4 font-medium glacier-text-secondary">Player</th>
                                    <th class="text-left py-3 px-4 font-medium glacier-text-secondary">Team</th>
                                    <th class="text-left py-3 px-4 font-medium glacier-text-secondary">Amount</th>
                                    <th class="text-left py-3 px-4 font-medium glacier-text-secondary">Time</th>
                                </tr>
                            </thead>
                            <tbody id="recentBidsTableBody">
                                <tr class="table-row">
                                    <td colspan="4" class="text-center py-8 text-gray-500">
                                        <div class="bg-gray-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                        </div>
                                        <p>No recent bids</p>
                                        <p class="text-sm text-gray-400 mt-1">Recent bids will appear here</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Highest Bids -->
            <div class="glacier-card">
                <div class="px-4 py-4 sm:px-6 border-b glacier-border">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold glacier-text-primary">Highest Bids</h2>
                        <div class="badge-purple px-3 py-1 rounded-full text-sm font-medium">
                            {{ $soldPlayers->count() }} Sold
                        </div>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <div id="highestBidsTable" class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="table-header">
                                <tr>
                                    <th class="text-left py-3 px-4 font-medium glacier-text-secondary">Player</th>
                                    <th class="text-left py-3 px-4 font-medium glacier-text-secondary">Team</th>
                                    <th class="text-left py-3 px-4 font-medium glacier-text-secondary">Sold Price</th>
                                    <th class="text-left py-3 px-4 font-medium glacier-text-secondary">Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($soldPlayers as $player)
                                <tr class="table-row border-b border-gray-100">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-full overflow-hidden bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center shadow-lg">
                                                <img src="{{ asset('images/defaultplayer.jpeg') }}" 
                                                     alt="{{ $player->user->name }}" 
                                                     class="w-full h-full object-cover"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="w-full h-full flex items-center justify-center text-white font-bold text-xs" style="display: none;">
                                                    {{ strtoupper(substr($player->user->name, 0, 2)) }}
                                                </div>
                                            </div>
                                            <div>
                                                <p class="font-medium glacier-text-primary text-sm">{{ $player->user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $player->user->position->name ?? 'Unknown' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="text-sm glacier-text-primary">{{ $player->leagueTeam->team->name ?? 'Unknown Team' }}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="font-bold text-purple-600">₹{{ number_format($player->bid_price) }}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="font-medium {{ ($player->bid_price - $player->base_price) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            ₹{{ number_format($player->bid_price - $player->base_price) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr class="table-row">
                                    <td colspan="4" class="text-center py-8 text-gray-500">
                                        <div class="bg-gray-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <p>No players sold yet</p>
                                        <p class="text-sm text-gray-400 mt-1">Sold players will appear here</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Teams Table - Full Width -->
        <div class="glacier-card">
            @include('auction.partials.teams-table')
        </div>
    </div>
</div>

<!-- Message Container -->
<div id="messageContainer" class="fixed top-4 right-4 z-50"></div>

<script>
let currentSelectedPlayerId = null;
let baseBidAmount = 0;
let currentHighestBid = 0;
let auctionStatus = 'ready'; // ready, active, paused

// Make currentHighestBid globally accessible
window.currentHighestBid = 0;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    initializePlayerSearch();
    initializeBiddingControls();
    initializeMobileView();
    restoreSelectedPlayer();
    initializeTeamsTable();
});

// Restore selected player from localStorage
function restoreSelectedPlayer() {
    const savedPlayer = localStorage.getItem('selectedPlayer');
    if (savedPlayer) {
        try {
            const playerData = JSON.parse(savedPlayer);
            const { playerId, playerName, basePrice, playerRole, timestamp } = playerData;
            
            // Check if the saved data is not too old (24 hours)
            const isDataFresh = (Date.now() - timestamp) < (24 * 60 * 60 * 1000);
            
            if (!isDataFresh) {
                localStorage.removeItem('selectedPlayer');
                return;
            }
            
            // Check if the player still exists and is available
            const playerCard = document.querySelector(`[data-league-player-id="${playerId}"]`);
            if (playerCard) {
                // Check if player is still available (not sold or removed)
                const isPlayerAvailable = checkPlayerAvailability(playerId);
                
                if (isPlayerAvailable) {
                    // Restore the player selection
                    selectPlayerForBidding(playerId, playerName, basePrice, playerRole);
                    
                    // Restore recent bids for this player
                    const savedBids = localStorage.getItem('recentBids');
                    if (savedBids) {
                        try {
                            const bidsData = JSON.parse(savedBids);
                            if (bidsData.playerId === playerId) {
                                const timeDiff = Date.now() - bidsData.timestamp;
                                if (timeDiff < 3600000) { // 1 hour
                                    updateBiddingHistory(bidsData.bids);
                                            if (bidsData.bids && bidsData.bids.length > 0) {
                                        currentHighestBid = parseFloat(bidsData.bids[0].amount);
                                        window.currentHighestBid = currentHighestBid;
                                        document.getElementById('currentBidAmount').textContent = '₹' + currentHighestBid.toLocaleString();
                                        document.getElementById('nextMinimumBid').textContent = '₹' + getNextMinimumBid().toLocaleString();
                                        generateQuickBidButtons();
                                    }
                                }
                            }
                        } catch (error) {
                            console.error('Error parsing saved bids data:', error);
                        }
                    }
                    
                    showMessage(`Restored bidding for ${playerName}`, 'info');
                } else {
                    // Player is no longer available, clear from storage
                    localStorage.removeItem('selectedPlayer');
                    clearRecentBids();
                    showMessage(`${playerName} is no longer available for bidding`, 'warning');
                    
                    // Show available players since no player is selected
                    showAvailablePlayers();
                }
            } else {
                // Player no longer exists, clear from storage
                localStorage.removeItem('selectedPlayer');
            }
        } catch (error) {
            console.error('Error restoring selected player:', error);
            localStorage.removeItem('selectedPlayer');
        }
    }
}

// Check if a player is still available for bidding
function checkPlayerAvailability(playerId) {
    const playerCard = document.querySelector(`[data-league-player-id="${playerId}"]`);
    if (!playerCard) {
        return false;
    }
    
    // Check if the player card is visible (not filtered out)
    const isVisible = playerCard.style.display !== 'none';
    if (!isVisible) {
        return false;
    }
    
    // Check if the player has a "Start Bidding" button (indicating they're available)
    const startBiddingBtn = playerCard.querySelector('button[onclick*="selectPlayerForBidding"]');
    return startBiddingBtn !== null;
}

// Save selected player to localStorage
function saveSelectedPlayer(playerId, playerName, basePrice, playerRole) {
    const playerData = {
        playerId: playerId,
        playerName: playerName,
        basePrice: basePrice,
        playerRole: playerRole,
        timestamp: Date.now()
    };
    localStorage.setItem('selectedPlayer', JSON.stringify(playerData));
}

// Clear selected player from localStorage
function clearSelectedPlayer() {
    localStorage.removeItem('selectedPlayer');
    clearRecentBids();
}

// Save recent bids to localStorage
function saveRecentBids(bids) {
    if (bids && bids.length > 0) {
        const bidsData = {
            bids: bids,
            timestamp: Date.now(),
            playerId: currentSelectedPlayerId
        };
        localStorage.setItem('recentBids', JSON.stringify(bidsData));
    }
}

// Load recent bids from localStorage
function loadRecentBids() {
    const savedBids = localStorage.getItem('recentBids');
    if (savedBids) {
        try {
            const bidsData = JSON.parse(savedBids);
            
            // Check if the bids are for the current player
            if (bidsData.playerId === currentSelectedPlayerId) {
                // Check if data is not too old (1 hour)
                const isDataFresh = (Date.now() - bidsData.timestamp) < (60 * 60 * 1000);
                
                if (isDataFresh) {
                    return bidsData.bids;
                }
            }
        } catch (error) {
            console.error('Error loading recent bids:', error);
        }
    }
    return null;
}

// Clear recent bids from localStorage
function clearRecentBids() {
    localStorage.removeItem('recentBids');
}

// Initialize mobile view behavior
function initializeMobileView() {
    // Check if we're on mobile
    const isMobile = window.innerWidth < 1024; // lg breakpoint
    
    if (isMobile) {
        // On mobile, show available players only if no player is selected
        if (!currentSelectedPlayerId) {
            document.getElementById('biddingSection').classList.add('hidden');
            document.getElementById('availablePlayersSection').classList.remove('hidden');
        } else {
            document.getElementById('availablePlayersSection').classList.add('hidden');
            document.getElementById('biddingSection').classList.remove('hidden');
        }
    }
}

// Show available players section
function showAvailablePlayers() {
    const isMobile = window.innerWidth < 1024;
    
    if (isMobile) {
        document.getElementById('biddingSection').classList.add('hidden');
        document.getElementById('availablePlayersSection').classList.remove('hidden');
    } else {
        // On desktop, show both sections
        document.getElementById('availablePlayersSection').classList.remove('hidden');
        document.getElementById('biddingSection').classList.remove('hidden');
    }
    
    // Remove mobile back button if it exists
    const backButton = document.getElementById('mobileBackBtn');
    if (backButton) {
        backButton.remove();
    }
}

// Hide available players section (when player is selected)
function hideAvailablePlayers() {
    const isMobile = window.innerWidth < 1024;
    
    if (isMobile) {
        document.getElementById('availablePlayersSection').classList.add('hidden');
        document.getElementById('biddingSection').classList.remove('hidden');
    }
}











// Initialize player search and filtering
function initializePlayerSearch() {
    // Player search functionality
    document.getElementById('playerSearch').addEventListener('input', function() {
        filterPlayers();
    });
    
    // Role filter
    document.getElementById('roleFilter').addEventListener('change', function() {
        filterPlayers();
    });
    
    // Price filter
    document.getElementById('priceFilter').addEventListener('change', function() {
        filterPlayers();
    });
}

// Initialize bidding controls
function initializeBiddingControls() {
    // Place bid button - default behavior
    document.getElementById('placeBidBtn').addEventListener('click', function() {
        const leaguePlayerId = currentSelectedPlayerId;
        const amount = parseFloat(document.getElementById('bidAmount').value);
        
        if (!leaguePlayerId) {
            showMessage('No player selected', 'error');
            return;
        }
        
        if (isNaN(amount) || amount <= 0) {
            showMessage('Please enter a valid bid amount', 'error');
            return;
        }
        
        const nextMinimumBid = getNextMinimumBid();
        console.log('DEBUG: Bid validation - Amount:', amount, 'Next Minimum:', nextMinimumBid, 'Current Highest:', currentHighestBid);
        if (amount < nextMinimumBid) {
            showMessage(`Bid amount must be at least ₹${nextMinimumBid.toLocaleString()}`, 'error');
            return;
        }
        
        // For testing: Always use random team selection (TEST/DEBUG feature)
        console.log('DEBUG: Using random team selection for testing');
        
        // Store the current bid amount before random team selection
        const currentBidAmount = parseFloat(document.getElementById('bidAmount').value);
        
        selectRandomTeam();
        
        // Wait a moment for team selection to complete
        setTimeout(() => {
            if (window.selectedTeamId) {
                console.log('DEBUG: Random team selected:', window.selectedTeamName);
                
                // Restore the original bid amount if it was valid
                if (currentBidAmount && currentBidAmount > 0) {
                    document.getElementById('bidAmount').value = currentBidAmount;
                }
                
                placeBidWithSelectedTeam(window.selectedTeamId, window.selectedTeamName);
            } else {
                showMessage('No teams available with sufficient funds', 'error');
            }
        }, 100);
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
    
    // Update button text when bid amount changes
    document.getElementById('bidAmount').addEventListener('input', function() {
        const amount = parseFloat(this.value) || 0;
        const placeBidBtn = document.getElementById('placeBidBtn');
        
        // Update button with bid amount as background
        placeBidBtn.innerHTML = `
            <div class="relative w-full h-full flex items-center justify-center">
                <div class="absolute inset-0 bg-green-500 opacity-20 rounded-lg"></div>
                <div class="relative z-10 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    <span class="font-medium">Place Bid</span>
                    <span class="ml-2 text-sm opacity-75">₹${amount.toLocaleString()}</span>
                </div>
            </div>
        `;
    });
}

// Filter players based on search and filters
function filterPlayers() {
    const searchTerm = document.getElementById('playerSearch').value.toLowerCase();
    const roleFilter = document.getElementById('roleFilter').value;
    const priceFilter = document.getElementById('priceFilter').value;
    const playerCards = document.querySelectorAll('.player-card');
    
    playerCards.forEach(card => {
        const playerName = card.getAttribute('data-player-name').toLowerCase();
        const playerRole = card.getAttribute('data-player-role');
        const playerMobile = card.getAttribute('data-player-mobile').toLowerCase();
        const basePrice = parseFloat(card.getAttribute('data-base-price'));
        
        let showMatch = true;
        
        // Search filter
        if (searchTerm && !playerName.includes(searchTerm) && !playerMobile.includes(searchTerm)) {
            showMatch = false;
        }
        
        // Role filter
        if (roleFilter && playerRole !== roleFilter) {
            showMatch = false;
        }
        
        // Price filter
        if (priceFilter) {
            const [min, max] = priceFilter.split('-').map(p => p === '+' ? Infinity : parseFloat(p));
            if (basePrice < min || (max !== Infinity && basePrice > max)) {
                showMatch = false;
            }
        }
        
        card.style.display = showMatch ? 'block' : 'none';
    });
}

// Select random team for testing (for quick bid buttons)
function selectRandomTeam() {
    console.log('DEBUG: Selecting random team...');
    
    // Get all team cards from the teams table (these are already filtered by current league)
    const teamCards = document.querySelectorAll('#teamsTableBody .team-row');
    console.log('DEBUG: Found', teamCards.length, 'team cards in current league');
    
    if (teamCards.length === 0) {
        showMessage('No teams found in the current league', 'error');
        return;
    }
    
    const availableTeams = Array.from(teamCards).filter(card => {
        const walletBalance = parseFloat(card.getAttribute('data-wallet'));
        const nextMinimumBid = getNextMinimumBid();
        const canBid = walletBalance >= nextMinimumBid;
        const teamName = card.querySelector('.text-sm.font-medium.glacier-text-primary').textContent;
        console.log('DEBUG: Team', teamName, 'wallet:', walletBalance, 'min bid:', nextMinimumBid, 'can bid:', canBid);
        return canBid;
    });
    
    console.log('DEBUG: Available teams for bidding in current league:', availableTeams.length);
    
    if (availableTeams.length > 0) {
        const randomIndex = Math.floor(Math.random() * availableTeams.length);
        const randomTeam = availableTeams[randomIndex];
        const teamId = randomTeam.getAttribute('data-team-id');
        const teamName = randomTeam.querySelector('.text-sm.font-medium.glacier-text-primary').textContent;
        
        console.log('DEBUG: Selected random team:', teamName, 'with ID:', teamId);
        
        // Select the random team
        selectTeamForBidding(teamId, teamName);
        // Remove notification for random team selection
        // showMessage('Random team selected: ' + teamName, 'info');
    } else {
        console.log('DEBUG: No teams available with sufficient funds in current league');
        showMessage('No teams in the current league have sufficient funds to bid', 'warning');
    }
}

// Select random team for testing (new function for the button)
function selectRandomTeamForTesting() {
    console.log('DEBUG: Selecting random team for testing...');
    
    // Get current league ID
    const currentLeagueId = {{ $league->id }};
    console.log('DEBUG: Current league ID:', currentLeagueId);
    
    // Get all team cards from the teams table (these are already filtered by current league)
    const teamCards = document.querySelectorAll('#teamsTableBody .team-row');
    console.log('DEBUG: Found', teamCards.length, 'team cards in current league');
    
    if (teamCards.length === 0) {
        showMessage('No teams found in the current league', 'error');
        return;
    }
    
    const availableTeams = Array.from(teamCards).filter(card => {
        const walletBalance = parseFloat(card.getAttribute('data-wallet'));
        const nextMinimumBid = getNextMinimumBid();
        const canBid = walletBalance >= nextMinimumBid;
        const teamName = card.querySelector('.text-sm.font-medium.glacier-text-primary').textContent;
        console.log('DEBUG: Team', teamName, 'wallet:', walletBalance, 'min bid:', nextMinimumBid, 'can bid:', canBid);
        return canBid;
    });
    
    console.log('DEBUG: Available teams for bidding in current league:', availableTeams.length);
    
    if (availableTeams.length > 0) {
        const randomIndex = Math.floor(Math.random() * availableTeams.length);
        const randomTeam = availableTeams[randomIndex];
        const teamId = randomTeam.getAttribute('data-team-id');
        const teamName = randomTeam.querySelector('.text-sm.font-medium.glacier-text-primary').textContent;
        
        console.log('DEBUG: Selected random team for testing:', teamName, 'with ID:', teamId);
        
        // Select the random team
        selectTeamForBidding(teamId, teamName);
        showMessage(`🎲 Random team selected: ${teamName}`, 'info');
    } else {
        console.log('DEBUG: No teams available with sufficient funds in current league');
        showMessage('No teams in the current league have sufficient funds to bid', 'warning');
    }
}

// Update team wallet information (now handled by team selection)
function updateTeamWalletInfo() {
    // This function is no longer needed as team selection is handled differently
    // Keeping for compatibility but it's now a no-op
}

// Get next minimum bid based on increment structure
function getNextMinimumBid() {
    const currentBid = window.currentHighestBid || currentHighestBid || baseBidAmount;
    
    // Use league settings for bid increments
    const league = @json($league);
    let nextBid;
    
    if (league.bid_increment_type === 'custom') {
        nextBid = currentBid + (league.custom_bid_increment || 10);
    } else {
        // Predefined structure
        if (currentBid <= 100) nextBid = currentBid + 5;
        else if (currentBid <= 500) nextBid = currentBid + 10;
        else if (currentBid <= 1000) nextBid = currentBid + 25;
        else nextBid = currentBid + 50;
    }
    
    console.log('DEBUG: getNextMinimumBid - Current:', currentBid, 'Next:', nextBid, 'Structure:', league.bid_increment_type);
    return nextBid;
}

// Generate quick bid buttons with direct bid placement
function generateQuickBidButtons() {
    const container = document.getElementById('quickBidButtons');
    const nextBid = getNextMinimumBid();
    
    // Check if team is selected
    if (!window.selectedTeamId) {
        container.innerHTML = `
            <div class="col-span-3 text-center py-4 text-gray-500">
                <p class="text-sm">Please select a team first to place bids</p>
            </div>
        `;
        return;
    }
    
    // Calculate bid amounts: 1x, 5x, and 10x of base price
    const basePrice = baseBidAmount || 0;
    const bidAmounts = [
        nextBid, // First bid (minimum required)
        basePrice * 5, // 5x of base price
        basePrice * 10 // 10x of base price
    ];
    
    // Filter out amounts that are less than next minimum bid
    const validBidAmounts = bidAmounts.filter(amount => amount >= nextBid);
    
    // If we don't have 3 valid amounts, add some increments
    while (validBidAmounts.length < 3) {
        const lastAmount = validBidAmounts[validBidAmounts.length - 1] || nextBid;
        validBidAmounts.push(lastAmount + (basePrice || 10));
    }
    
    // Take only the first 3 amounts
    const finalBidAmounts = validBidAmounts.slice(0, 3);
    
    // Check team balance for each bid amount
    const teamCard = document.querySelector(`[data-team-id="${window.selectedTeamId}"]`);
    const teamBalance = teamCard ? parseFloat(teamCard.getAttribute('data-wallet')) : 0;
    
    // Define button colors and labels
    const buttonConfigs = [
        { amount: finalBidAmounts[0], color: 'bg-green-600 hover:bg-green-700', label: 'Bid', disabled: finalBidAmounts[0] > teamBalance },
        { amount: finalBidAmounts[1], color: 'bg-orange-600 hover:bg-orange-700', label: 'Bid', disabled: finalBidAmounts[1] > teamBalance },
        { amount: finalBidAmounts[2], color: 'bg-red-600 hover:bg-red-700', label: 'Bid', disabled: finalBidAmounts[2] > teamBalance }
    ];
    
    let buttonsHTML = '';
    buttonConfigs.forEach((config, index) => {
        const disabledClass = config.disabled ? 'opacity-50 cursor-not-allowed' : '';
        const disabledAttr = config.disabled ? 'disabled' : '';
        const onClick = config.disabled ? '' : `onclick="placeQuickBid(${config.amount})"`;
        
        buttonsHTML += `
            <button ${onClick} ${disabledAttr}
                    class="${config.color} text-white px-4 py-3 rounded-lg text-sm font-medium transition-colors duration-200 mobile-button flex flex-col items-center justify-center ${disabledClass}">
                <span class="font-bold">${config.label}</span>
                <span class="text-xs opacity-90">₹${config.amount.toLocaleString()}</span>
                ${config.disabled ? '<span class="text-xs text-red-200 mt-1">Insufficient Balance</span>' : ''}
            </button>
        `;
    });
    
    container.innerHTML = buttonsHTML;
}

// Place quick bid directly
function placeQuickBid(amount) {
    if (!currentSelectedPlayerId) {
        showMessage('No player selected', 'error');
        return;
    }
    
    if (!window.selectedTeamId) {
        showMessage('Please select a team first', 'error');
        return;
    }
    
    const nextMinimumBid = getNextMinimumBid();
    if (amount < nextMinimumBid) {
        showMessage(`Bid amount must be at least ₹${nextMinimumBid.toLocaleString()}`, 'error');
        return;
    }
    
    // Check if team has sufficient balance
    const teamCard = document.querySelector(`[data-team-id="${window.selectedTeamId}"]`);
    if (teamCard) {
        const currentBalance = parseFloat(teamCard.getAttribute('data-wallet'));
        if (currentBalance < amount) {
            showMessage(`Team has insufficient balance. Required: ₹${amount.toLocaleString()}, Available: ₹${currentBalance.toLocaleString()}`, 'error');
            return;
        }
    }
    
    // Place the bid directly
    placeBid(currentSelectedPlayerId, window.selectedTeamId, amount);
}

// Set bid amount
function setBidAmount(amount) {
    document.getElementById('bidAmount').value = amount;
    
    // Update button text if team is selected
    if (window.selectedTeamId && window.selectedTeamName) {
        const placeBidBtn = document.getElementById('placeBidBtn');
        placeBidBtn.innerHTML = `
            <div class="relative w-full h-full flex items-center justify-center">
                <div class="absolute inset-0 bg-green-500 opacity-20 rounded-lg"></div>
                <div class="relative z-10 flex items-center">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
                    <span class="font-medium">Place Bid</span>
                    <span class="ml-2 text-sm opacity-75">₹${amount.toLocaleString()}</span>
                </div>
            </div>
        `;
    }
    
    console.log('DEBUG: Bid amount set to ₹' + amount.toLocaleString());
}



// Select player for bidding (updated)
function selectPlayerForBidding(playerId, playerName, basePrice, playerRole) {
    // Set selected player
    currentSelectedPlayerId = playerId;
    document.getElementById('selectedLeaguePlayerId').value = playerId;
    document.getElementById('selectedPlayerName').textContent = playerName;
    document.getElementById('selectedPlayerBasePrice').textContent = '₹' + parseFloat(basePrice).toLocaleString();
    document.getElementById('playerRole').textContent = playerRole;
    
    // Update player image and initials
    const playerImage = document.getElementById('selectedPlayerImage');
    const playerInitials = document.getElementById('selectedPlayerInitials');
    
    if (playerImage) {
        playerImage.src = '/images/defaultplayer.jpeg';
        playerImage.style.display = 'block';
    }
    
    if (playerInitials) {
        playerInitials.textContent = playerName.substring(0, 2).toUpperCase();
        playerInitials.style.display = 'none';
    }
    
    // Set bid amounts
    baseBidAmount = parseFloat(basePrice);
    currentHighestBid = baseBidAmount;
    window.currentHighestBid = currentHighestBid;
    document.getElementById('currentBidAmount').textContent = '₹' + currentHighestBid.toLocaleString();
    document.getElementById('nextMinimumBid').textContent = '₹' + getNextMinimumBid().toLocaleString();
    
    // Generate quick bid buttons
    generateQuickBidButtons();
    
    // Show bidding card, hide placeholder
    document.getElementById('noBiddingPlayer').classList.add('hidden');
    document.getElementById('biddingCard').classList.remove('hidden');
    
    // Update bidding status
    document.getElementById('biddingStatus').textContent = 'Active';
    document.getElementById('biddingStatus').className = 'bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium';
    
    // Handle mobile view
    hideAvailablePlayers();
    
    // Add a back button for mobile
    const isMobile = window.innerWidth < 1024;
    if (isMobile) {
        addMobileBackButton();
    }
    
    // Save selected player to localStorage
    saveSelectedPlayer(playerId, playerName, basePrice, playerRole);
    
    // Reset team selection for new player
    resetTeamSelection();
    
    // Initialize team bid limits
    updateTeamBidLimits();
    
    // Fetch current bids for this player
    fetchCurrentBids(playerId);
}

// Place a bid (updated)
function placeBid(leaguePlayerId, leagueTeamId, amount) {
    console.log('DEBUG: Attempting to place bid:', {
        leaguePlayerId: leaguePlayerId,
        leagueTeamId: leagueTeamId,
        amount: amount
    });
    
    // Check if team has sufficient balance before placing bid
    const teamCard = document.querySelector(`[data-team-id="${leagueTeamId}"]`);
    if (teamCard) {
        const currentBalance = parseFloat(teamCard.getAttribute('data-wallet'));
        if (currentBalance < amount) {
            showMessage(`Team has insufficient balance. Required: ₹${amount.toLocaleString()}, Available: ₹${currentBalance.toLocaleString()}`, 'error');
            return;
        }
    }
    
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
        console.log('DEBUG: Bid response received:', data);
        
        if (data.success) {
            showMessage(`Bid placed successfully: ₹${amount.toLocaleString()}`, 'success');
            currentHighestBid = amount;
            window.currentHighestBid = amount;
            document.getElementById('currentBidAmount').textContent = '₹' + amount.toLocaleString();
            document.getElementById('nextMinimumBid').textContent = '₹' + getNextMinimumBid().toLocaleString();
            
            // Update team information
            updateTeamLastBid(leagueTeamId, amount);
            updateTeamWalletAfterBid(leagueTeamId, amount);
            
            // Update selected team balance display
            if (window.selectedTeamId === leagueTeamId) {
                const newBalance = parseFloat(teamCard.getAttribute('data-wallet')) - amount;
                document.getElementById('selectedTeamBalance').textContent = '₹' + newBalance.toLocaleString();
            }
            
            // Regenerate quick bid buttons with updated amounts
            generateQuickBidButtons();
            
            fetchCurrentBids(leaguePlayerId);
        } else {
            console.log('DEBUG: Bid failed:', data.error);
            showMessage(data.error, 'error');
        }
    })
    .catch(error => {
        console.error('Error placing bid:', error);
        showMessage('An error occurred while placing the bid', 'error');
    });
}

// Update team wallet after bid
function updateTeamWalletAfterBid(teamId, bidAmount) {
    const teamCard = document.querySelector(`[data-team-id="${teamId}"]`);
    if (teamCard) {
        const currentWallet = parseFloat(teamCard.getAttribute('data-wallet'));
        const newWallet = currentWallet - bidAmount;
        
        // Update the data attribute
        teamCard.setAttribute('data-wallet', newWallet);
        
        // Update display - find wallet balance element (first span in text-gray-600)
        const walletElements = teamCard.querySelectorAll('.text-gray-600 span');
        if (walletElements.length > 0) {
            walletElements[0].textContent = `₹${newWallet.toLocaleString()}`;
        }
        
        // Update team bid limit display
        const teamBidLimit = teamCard.querySelector('#teamBidLimit_' + teamId);
        if (teamBidLimit) {
            teamBidLimit.textContent = `Max Bid: ₹${newWallet.toLocaleString()}`;
        }
        
        // Update team can bid display
        const teamCanBid = teamCard.querySelector('#teamCanBid_' + teamId);
        if (teamCanBid) {
            teamCanBid.textContent = `₹${newWallet.toLocaleString()}`;
        }
        
        // Update team bid limits
        updateTeamBidLimits();
        
        console.log('DEBUG: Updated team wallet for team', teamId, 'from', currentWallet, 'to', newWallet);
    }
}

// Check minimum players requirement
function checkMinimumPlayersRequirement() {
    const league = @json($league);
    const maxTeamPlayers = league.max_team_players;
    
    // Get all teams and their current player counts
    const teamCards = document.querySelectorAll('.team-card');
    let teamsBelowMinimum = [];
    
    teamCards.forEach(card => {
        const teamId = card.getAttribute('data-team-id');
        const playerCount = parseInt(card.querySelector('.text-sm.glacier-text-primary').textContent);
        
        if (playerCount < maxTeamPlayers) {
            const teamName = card.querySelector('h3').textContent;
            teamsBelowMinimum.push({
                teamId: teamId,
                teamName: teamName,
                currentPlayers: playerCount,
                requiredPlayers: maxTeamPlayers
            });
        }
    });
    
    return teamsBelowMinimum;
}

// Accept the highest bid (updated)
function acceptBid(leaguePlayerId) {
    // Check minimum players requirement before accepting bid
    const teamsBelowMinimum = checkMinimumPlayersRequirement();
    const league = @json($league);
    
    if (teamsBelowMinimum.length > 0) {
        let warningMessage = `Warning: Some teams don't have the minimum required players (${league.max_team_players}):\n`;
        teamsBelowMinimum.forEach(team => {
            warningMessage += `- ${team.teamName}: ${team.currentPlayers}/${team.requiredPlayers} players\n`;
        });
        warningMessage += '\nDo you still want to accept this bid?';
        
        if (!confirm(warningMessage)) {
            return;
        }
    }
    
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
            
            // Clear selected player from localStorage
            clearSelectedPlayer();
            
            // Reset selected player
            currentSelectedPlayerId = null;
            
            // Reset team selection and player selection
            resetTeamSelection();
            currentSelectedPlayerId = null;
            
            // Show available players after successful sale
            showAvailablePlayers();
            
            // Reset bidding card
            document.getElementById('noBiddingPlayer').classList.remove('hidden');
            document.getElementById('biddingCard').classList.add('hidden');
            
            // Show success message for a bit longer before refresh
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        } else {
            showMessage(data.error, 'error');
        }
    })
    .catch(error => {
        console.error('Error accepting bid:', error);
        showMessage('An error occurred while accepting the bid', 'error');
    });
}

// Skip player (updated)
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
            
            // Clear selected player from localStorage
            clearSelectedPlayer();
            
            // Reset selected player
            currentSelectedPlayerId = null;
            
            // Reset team selection and player selection
            resetTeamSelection();
            currentSelectedPlayerId = null;
            
            // Show available players after skipping
            showAvailablePlayers();
            
            // Reset bidding card
            document.getElementById('noBiddingPlayer').classList.remove('hidden');
            document.getElementById('biddingCard').classList.add('hidden');
            
            // Show success message for a bit longer before refresh
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        } else {
            showMessage(data.error, 'error');
        }
    })
    .catch(error => {
        console.error('Error skipping player:', error);
        showMessage('An error occurred while skipping the player', 'error');
    });
}

// Fetch current bids for a player (updated)
function fetchCurrentBids(leaguePlayerId) {
    if (!leaguePlayerId) return;
    
    // First, try to load recent bids from localStorage for immediate display
    const savedBids = loadRecentBids();
    if (savedBids) {
        updateBiddingHistory(savedBids);
        if (savedBids && savedBids.length > 0) {
            currentHighestBid = parseFloat(savedBids[0].amount);
            window.currentHighestBid = currentHighestBid;
            document.getElementById('currentBidAmount').textContent = '₹' + currentHighestBid.toLocaleString();
            document.getElementById('nextMinimumBid').textContent = '₹' + getNextMinimumBid().toLocaleString();
            generateQuickBidButtons();
            
            // Update team last bid information
            savedBids.forEach(bid => {
                updateTeamLastBid(bid.league_team_id, bid.amount);
            });
        }
        
        // Update team bid limits after loading bids
        updateTeamBidLimits();
    }
    
    // Then fetch fresh data from server
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
        // Save bids to localStorage for persistence
        saveRecentBids(bids);
        
        if (bids && bids.length > 0) {
            currentHighestBid = parseFloat(bids[0].amount);
            window.currentHighestBid = currentHighestBid;
            document.getElementById('currentBidAmount').textContent = '₹' + currentHighestBid.toLocaleString();
            document.getElementById('nextMinimumBid').textContent = '₹' + getNextMinimumBid().toLocaleString();
            generateQuickBidButtons();
            
            // Update team last bid information
            bids.forEach(bid => {
                updateTeamLastBid(bid.league_team_id, bid.amount);
            });
        }
        
        // Update team bid limits after loading bids
        updateTeamBidLimits();
    })
    .catch(error => {
        console.error('Error fetching bids:', error);
    });
}

// Update bidding history display (updated for table structure)
function updateBiddingHistory(bids) {
    const recentBidsTableBody = document.getElementById('recentBidsTableBody');
    const recentBidCount = document.getElementById('recentBidCount');
    
    if (!bids || bids.length === 0) {
        recentBidsTableBody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-8 text-gray-500">
                <div class="bg-gray-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                    <p>No recent bids</p>
                    <p class="text-sm text-gray-400 mt-1">Recent bids will appear here</p>
                </td>
            </tr>
        `;
        recentBidCount.textContent = '0';
        
        // Hide last bid team info
        document.getElementById('lastBidTeamInfo').style.display = 'none';
        return;
    }
    
    let tableRows = '';
    bids.forEach((bid, index) => {
        const isHighest = index === 0;
        const bidTime = new Date(bid.created_at).toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: false 
        });
        
        tableRows += `
            <tr class="border-b border-gray-100 hover:bg-gray-50/70 transition-colors ${isHighest ? 'bg-green-50/50' : ''}">
                <td class="py-3 px-4">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-full w-8 h-8 flex items-center justify-center text-white text-xs font-bold">
                            ${bid.league_team.team.name.substring(0, 2).toUpperCase()}
                        </div>
                        <div>
                            <p class="font-medium glacier-text-primary text-sm">${bid.league_team.team.name}</p>
                            <p class="text-xs text-gray-500">Team</p>
                        </div>
                    </div>
                </td>
                <td class="py-3 px-4">
                    <span class="text-sm glacier-text-primary">${bid.league_team.team.name}</span>
                </td>
                <td class="py-3 px-4">
                    <span class="font-bold ${isHighest ? 'text-green-600' : 'text-gray-900'}">₹${parseFloat(bid.amount).toLocaleString()}</span>
                    ${isHighest ? '<span class="text-xs text-green-600 font-medium ml-1">(Highest)</span>' : ''}
                </td>
                <td class="py-3 px-4">
                    <span class="text-sm text-gray-600">${bidTime}</span>
                </td>
            </tr>
        `;
        
        // Update last bid team info if this is the highest bid
        if (isHighest) {
            updateLastBidTeamInfo(bid);
        }
    });
    
    recentBidsTableBody.innerHTML = tableRows;
    recentBidCount.textContent = bids.length;
    
    // Save recent bids to localStorage
    saveRecentBids(bids);
    
    // Update team bid limits and status
    updateTeamBidLimits();
}

// Update last bid team information
function updateLastBidTeamInfo(bid) {
    const lastBidTeamInfo = document.getElementById('lastBidTeamInfo');
    const lastBidTeamAvatar = document.getElementById('lastBidTeamAvatar');
    const lastBidTeamName = document.getElementById('lastBidTeamName');
    const lastBidTeamBalance = document.getElementById('lastBidTeamBalance');
    
    lastBidTeamAvatar.textContent = bid.league_team.team.name.substring(0, 2).toUpperCase();
    lastBidTeamName.textContent = bid.league_team.team.name;
    lastBidTeamBalance.textContent = '₹' + parseFloat(bid.league_team.wallet_balance).toLocaleString();
    
    lastBidTeamInfo.style.display = 'block';
}

// Update team bid limits and status
function updateTeamBidLimits() {
    const teamCards = document.querySelectorAll('.team-card');
    const nextMinimumBid = getNextMinimumBid();
    
    teamCards.forEach(card => {
        const teamId = card.getAttribute('data-team-id');
        const walletBalance = parseFloat(card.getAttribute('data-wallet'));
        
        // Get current highest bid (use global variable)
        const currentBid = window.currentHighestBid || currentHighestBid || baseBidAmount;
        
        // Calculate what this team can bid
        const canBidUpTo = walletBalance;
        const maxBidForCurrentPlayer = Math.min(walletBalance, canBidUpTo);
        
        // Update team bid limit display
        const teamBidLimit = document.getElementById(`teamBidLimit_${teamId}`);
        const teamCanBid = document.getElementById(`teamCanBid_${teamId}`);
        const teamStatus = document.getElementById(`teamStatus_${teamId}`);
        const teamNextMinBid = document.getElementById(`teamNextMinBid_${teamId}`);
        const teamSelectBtn = document.getElementById(`teamSelectBtn_${teamId}`);
        
        if (teamBidLimit) {
            teamBidLimit.textContent = `Max Bid: ₹${maxBidForCurrentPlayer.toLocaleString()}`;
        }
        
        if (teamCanBid) {
            teamCanBid.textContent = `₹${maxBidForCurrentPlayer.toLocaleString()}`;
        }
        
        if (teamNextMinBid) {
            teamNextMinBid.textContent = `₹${nextMinimumBid.toLocaleString()}`;
        }
        
        if (teamStatus) {
            if (maxBidForCurrentPlayer >= nextMinimumBid) {
                teamStatus.textContent = 'Can Bid';
                teamStatus.className = 'text-xs text-green-600 font-medium';
                
                // Highlight team that can bid
                card.classList.add('border-green-300', 'bg-green-50');
                if (teamSelectBtn) {
                    teamSelectBtn.className = 'w-full bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 team-select-btn';
                    teamSelectBtn.textContent = 'Bid Now';
                }
            } else {
                teamStatus.textContent = 'Insufficient Funds';
                teamStatus.className = 'text-xs text-red-600 font-medium';
                
                // Remove highlight
                card.classList.remove('border-green-300', 'bg-green-50');
                if (teamSelectBtn) {
                    teamSelectBtn.className = 'w-full bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 team-select-btn';
                    teamSelectBtn.textContent = 'Insufficient Funds';
                }
            }
        }
    });
}

// Select team for bidding
function selectTeamForBidding(teamId, teamName) {
    const teamCard = document.querySelector(`[data-team-id="${teamId}"]`);
    const walletBalance = parseFloat(teamCard.getAttribute('data-wallet'));
    const nextMinimumBid = getNextMinimumBid();
    
    if (walletBalance >= nextMinimumBid) {
        // Store selected team info for bidding
        window.selectedTeamId = teamId;
        window.selectedTeamName = teamName;
        
        // Update selected team display
        document.getElementById('selectedTeamName').textContent = teamName;
        document.getElementById('selectedTeamBalance').textContent = '₹' + walletBalance.toLocaleString();
        
        showMessage(`Team ${teamName} selected for bidding`, 'success');
        
        // Highlight the selected team
        document.querySelectorAll('.team-card').forEach(card => {
            card.classList.remove('border-blue-500', 'bg-blue-50');
        });
        teamCard.classList.add('border-blue-500', 'bg-blue-50');
        
        // Regenerate quick bid buttons with new team context
        generateQuickBidButtons();
        
        console.log('DEBUG: Team selected for bidding:', teamName, 'with balance: ₹' + walletBalance.toLocaleString());
    } else {
        showMessage(`Team ${teamName} has insufficient funds to bid`, 'error');
    }
}

// Place bid with selected team
function placeBidWithSelectedTeam(teamId, teamName) {
    const leaguePlayerId = currentSelectedPlayerId;
    const amount = parseFloat(document.getElementById('bidAmount').value);
    
    if (!leaguePlayerId) {
        showMessage('No player selected', 'error');
        return;
    }
    
    if (isNaN(amount) || amount <= 0) {
        showMessage('Please enter a valid bid amount', 'error');
        return;
    }
    
    const nextMinimumBid = getNextMinimumBid();
    console.log('DEBUG: Bid validation - Amount:', amount, 'Next Minimum:', nextMinimumBid, 'Current Highest:', currentHighestBid);
    if (amount < nextMinimumBid) {
        showMessage(`Bid amount must be at least ₹${nextMinimumBid.toLocaleString()}`, 'error');
        return;
    }
    
    // For testing: Always use random team selection (TEST/DEBUG feature)
    console.log('DEBUG: Using random team selection for testing');
    
    // Store the current bid amount before random team selection
    const currentBidAmount = parseFloat(document.getElementById('bidAmount').value);
    
    selectRandomTeam();
    
    // Wait a moment for team selection to complete, then place bid
    setTimeout(() => {
        if (window.selectedTeamId) {
            const finalTeamId = window.selectedTeamId;
            const finalTeamName = window.selectedTeamName;
            
            // Restore the original bid amount if it was valid
            if (currentBidAmount && currentBidAmount > 0) {
                document.getElementById('bidAmount').value = currentBidAmount;
            }
            
            // Update button text with bid value
            const placeBidBtn = document.getElementById('placeBidBtn');
            placeBidBtn.innerHTML = `
                <div class="relative w-full h-full flex items-center justify-center">
                    <div class="absolute inset-0 bg-green-500 opacity-20 rounded-lg"></div>
                    <div class="relative z-10 flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <span class="font-medium">Place Bid</span>
                        <span class="ml-2 text-sm opacity-75">₹${amount.toLocaleString()}</span>
                    </div>
                </div>
            `;
            
            console.log('DEBUG: Placing bid of ₹' + amount.toLocaleString() + ' for random team: ' + finalTeamName);
            
            // Send the bid
            placeBid(leaguePlayerId, finalTeamId, amount);
        } else {
            showMessage('No teams available with sufficient funds', 'error');
        }
    }, 100);
}

// Reset team selection
function resetTeamSelection() {
    // Clear selected team info
    window.selectedTeamId = null;
    window.selectedTeamName = null;
    
    // Clear selected team display
    document.getElementById('selectedTeamName').textContent = 'No team selected';
    document.getElementById('selectedTeamBalance').textContent = '₹0';
    
    console.log('DEBUG: Team selection reset');
    
    // Remove team highlighting
    document.querySelectorAll('.team-card').forEach(card => {
        card.classList.remove('border-blue-500', 'bg-blue-50');
    });
    
    // Regenerate quick bid buttons (they will be disabled without team selection)
    generateQuickBidButtons();
}

// Calculate team's remaining budget after current bids
function calculateTeamRemainingBudget(teamId) {
    // This would need to be calculated based on all current active bids for this team
    // For now, we'll use a simplified calculation
    const teamCard = document.querySelector(`[data-team-id="${teamId}"]`);
    if (teamCard) {
        const walletBalance = parseFloat(teamCard.getAttribute('data-wallet'));
        return walletBalance;
    }
    return 0;
}

// Update team's effective bid limit
function updateTeamEffectiveBidLimit(teamId) {
    const remainingBudget = calculateTeamRemainingBudget(teamId);
    const nextMinimumBid = getNextMinimumBid();
    
    const teamCanBid = document.getElementById(`teamCanBid_${teamId}`);
    const teamStatus = document.getElementById(`teamStatus_${teamId}`);
    
    if (teamCanBid) {
        teamCanBid.textContent = `₹${remainingBudget.toLocaleString()}`;
    }
    
    if (teamStatus) {
        if (remainingBudget >= nextMinimumBid) {
            teamStatus.textContent = 'Can Bid';
            teamStatus.className = 'text-xs text-green-600 font-medium';
        } else {
            teamStatus.textContent = 'Insufficient Funds';
            teamStatus.className = 'text-xs text-red-600 font-medium';
        }
    }
}

// Update team last bid information
function updateTeamLastBid(teamId, bidAmount) {
    const teamLastBid = document.getElementById(`teamLastBid_${teamId}`);
    if (teamLastBid) {
        teamLastBid.textContent = bidAmount ? `₹${parseFloat(bidAmount).toLocaleString()}` : 'None';
    }
}

// Add mobile back button
function addMobileBackButton() {
    const biddingSection = document.getElementById('biddingSection');
    
    // Check if back button already exists
    if (document.getElementById('mobileBackBtn')) {
        return;
    }
    
    // Create back button
    const backButton = document.createElement('button');
    backButton.id = 'mobileBackBtn';
    backButton.className = 'lg:hidden fixed top-4 left-4 z-50 bg-white border border-gray-300 rounded-full p-2 shadow-lg hover:bg-gray-50 transition-colors';
    backButton.innerHTML = `
        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
    `;
    
    backButton.addEventListener('click', function() {
        // Show available players
        showAvailablePlayers();
        
        // Reset bidding card
        document.getElementById('noBiddingPlayer').classList.remove('hidden');
        document.getElementById('biddingCard').classList.add('hidden');
        
        // Reset selected player
        currentSelectedPlayerId = null;
        
        // Reset team selection
        resetTeamSelection();
        
        // Clear selected player from localStorage
        clearSelectedPlayer();
    });
    
    document.body.appendChild(backButton);
}

// View player details (placeholder function)
function viewPlayerDetails(playerId) {
    showMessage('Player details feature coming soon!', 'info');
}

// Show message (updated)
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

// Handle window resize for mobile responsiveness
window.addEventListener('resize', function() {
    const isMobile = window.innerWidth < 1024;
    
    if (!isMobile) {
        // On desktop, show both sections
        document.getElementById('availablePlayersSection').classList.remove('hidden');
        document.getElementById('biddingSection').classList.remove('hidden');
        
        // Remove mobile back button if it exists
        const backButton = document.getElementById('mobileBackBtn');
        if (backButton) {
            backButton.remove();
        }
    }
});

// Handle page visibility changes (when user switches tabs)
document.addEventListener('visibilitychange', function() {
    if (!document.hidden && currentSelectedPlayerId) {
        // User returned to the page, refresh the current player's bid status
        fetchCurrentBids(currentSelectedPlayerId);
        
        // Check if the selected player is still available
        const isPlayerAvailable = checkPlayerAvailability(currentSelectedPlayerId);
        if (!isPlayerAvailable) {
            // Player is no longer available, clear selection
            clearSelectedPlayer();
            currentSelectedPlayerId = null;
            
            // Reset bidding card
            document.getElementById('noBiddingPlayer').classList.remove('hidden');
            document.getElementById('biddingCard').classList.add('hidden');
            
            // Show available players since no player is selected
            showAvailablePlayers();
            
            showMessage('Selected player is no longer available', 'warning');
        }
    }
});

// Handle page refresh to ensure state is saved
window.addEventListener('beforeunload', function() {
    // The selected player is already saved in localStorage
    // This ensures the state persists across page refreshes
});

// Add CSRF token to page head if not present
if (!document.querySelector('meta[name="csrf-token"]')) {
    const csrfMeta = document.createElement('meta');
    csrfMeta.name = 'csrf-token';
    csrfMeta.content = '{{ csrf_token() }}';
    document.head.appendChild(csrfMeta);
}

// Teams Table Functions
function initializeTeamsTable() {
    // Update team balances and stats
    updateTeamsTable();
}

function updateTeamsTable() {
    // This function will be called after each bid to update team balances
    // For now, we'll just ensure the table is properly initialized
    console.log('Teams table initialized');
}

function updateTeamBalance(teamId, newBalance, bidAmount) {
    // Update wallet balance
    const walletElement = document.getElementById(`teamWallet_${teamId}`);
    if (walletElement) {
        walletElement.textContent = `₹${parseInt(newBalance).toLocaleString()}`;
    }
    
    // Update total spent
    const spentElement = document.getElementById(`teamSpent_${teamId}`);
    if (spentElement) {
        const currentSpent = parseInt(spentElement.textContent.replace(/[^\d]/g, ''));
        const newSpent = currentSpent + parseInt(bidAmount);
        spentElement.textContent = `₹${newSpent.toLocaleString()}`;
    }
    
    // Update last bid info
    const lastBidElement = document.getElementById(`teamLastBid_${teamId}`);
    const lastBidAmountElement = document.getElementById(`teamLastBidAmount_${teamId}`);
    if (lastBidElement && lastBidAmountElement) {
        const now = new Date();
        lastBidElement.innerHTML = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + ', ' + 
                                 now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
        lastBidAmountElement.textContent = `₹${parseInt(bidAmount).toLocaleString()}`;
    }
    
    // Update summary row
    updateSummaryRow();
}

function updateSummaryRow() {
    // Calculate totals from all teams
    let totalWallet = 0;
    let totalSpent = 0;
    let teamCount = 0;
    
    document.querySelectorAll('.team-row').forEach(row => {
        const teamId = row.getAttribute('data-team-id');
        const walletElement = document.getElementById(`teamWallet_${teamId}`);
        const spentElement = document.getElementById(`teamSpent_${teamId}`);
        
        if (walletElement && spentElement) {
            const wallet = parseInt(walletElement.textContent.replace(/[^\d]/g, ''));
            const spent = parseInt(spentElement.textContent.replace(/[^\d]/g, ''));
            
            totalWallet += wallet;
            totalSpent += spent;
            teamCount++;
        }
    });
    
    // Update summary display (if summary elements exist)
    const avgBalance = teamCount > 0 ? Math.round(totalWallet / teamCount) : 0;
    
    // You can add summary update logic here if needed
    console.log(`Updated summary: ${teamCount} teams, ₹${totalWallet.toLocaleString()} total wallet, ₹${totalSpent.toLocaleString()} total spent`);
}

// Function to refresh teams table data from server
function refreshTeamsTable() {
    // This would make an AJAX call to get updated team data
    // For now, we'll just log that it should be refreshed
    console.log('Teams table should be refreshed from server');
}
</script>
@endsection
