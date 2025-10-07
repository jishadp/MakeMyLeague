@extends('layouts.app')

@section('title', 'Auction - MakeMyLeague')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/auction.css') }}">
@endsection

@section('content')
<div class="min-h-screen auction-bg py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Header -->
        <div class="glacier-card mb-6">
            <div class="px-4 py-4 sm:px-6 border-b glacier-border">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold glacier-text-primary">Cricket League Auction</h1>
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
                                <p class="text-lg font-bold text-green-800">{{ $leaguePlayers->count()}}</p>
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
                                <p class="text-lg font-bold text-blue-800">{{ $leaguePlayers->where('status','available')->count()}}</p>
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
                                <p class="text-lg font-bold text-purple-800">{{ $leaguePlayers->where('status','sold')->count()}}</p>
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
                                <p class="text-sm text-yellow-600 font-medium">Unsold</p>
                                <p class="text-lg font-bold text-yellow-800">{{ $leaguePlayers->where('status','unsold')->count()}}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Progress</span>
                        <span>70%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-green-500 to-blue-500 h-2 rounded-full transition-all duration-300" style="width: 70%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Players Section -->
        @if(auth()->user()->isOrganizer())
        <div class="mb-8" id="availablePlayersSection">
            @include('auction.partials.available-players')
        </div>
        @endif

        <!-- Player Bidding Section -->
        <div class="flex justify-center mb-8" id="biddingSection">
            <div class="w-full max-w-2xl">
                @include('auction.partials.player-bidding')
            </div>
        </div>

        <!-- Recent and Highest Bids Table -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

            <!-- Highest Bids -->
            @livewire('highest-bids', ['leagueId' => $league->id])

            <!-- Teams Table -->
            @livewire('teams', ['leagueId' => $league->id])
        </div>
    </div>
</div>

<!-- Message Container -->
<div id="messageContainer" class="fixed top-4 right-4 z-50"></div>

@endsection

@section('scripts')
<script
  src="https://code.jquery.com/jquery-3.7.1.js"
  integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
  crossorigin="anonymous"></script>
<script src="{{ asset('js/auction.js') }}?v=4"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="{{ asset('js/pusher-main.js') }}?v=1"></script>

<script>
// Player search functionality
document.addEventListener('DOMContentLoaded', function() {
    const playerSearchInput = document.getElementById('playerSearch');
    const playerSearchResults = document.getElementById('playerSearchResults');
    const leagueId = {{ $league->id }};
    
    if (playerSearchInput) {
        let searchTimeout;
        
        playerSearchInput.addEventListener('input', function(e) {
            const query = e.target.value.trim();
            
            // Clear previous timeout
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                playerSearchResults.classList.add('hidden');
                return;
            }
            
            // Debounce search requests
            searchTimeout = setTimeout(() => {
                searchPlayers(query);
            }, 300);
        });
        
        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!playerSearchInput.contains(e.target) && !playerSearchResults.contains(e.target)) {
                playerSearchResults.classList.add('hidden');
            }
        });
    }
    
    function searchPlayers(query) {
        fetch(`/auction/search-players?query=${encodeURIComponent(query)}&league_id=${leagueId}`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.players.length > 0) {
                displaySearchResults(data.players);
            } else {
                displayNoResults();
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            displayNoResults();
        });
    }
    
    function displaySearchResults(players) {
        let html = '';
        
        players.forEach(player => {
            html += `
                <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 player-search-result" 
                     data-player-id="${player.id}" 
                     data-user-id="${player.user_id}"
                     data-player-name="${player.player_name}"
                     data-base-price="${player.base_price}"
                     data-position="${player.position}">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full overflow-hidden bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                            <img src="${player.photo}" 
                                 alt="${player.player_name}"
                                 class="w-full h-full object-cover"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-full h-full flex items-center justify-center text-white font-bold text-sm" style="display: none;">
                                ${player.player_name.substring(0, 2).toUpperCase()}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-900 truncate">${player.player_name}</div>
                            <div class="text-sm text-gray-500 truncate">
                                ${player.position} • ₹${player.base_price}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        playerSearchResults.innerHTML = html;
        playerSearchResults.classList.remove('hidden');
        
        // Add click event listeners to search results
        document.querySelectorAll('.player-search-result').forEach(result => {
            result.addEventListener('click', function() {
                const playerId = this.dataset.playerId;
                const userId = this.dataset.userId;
                const playerName = this.dataset.playerName;
                const basePrice = this.dataset.basePrice;
                const position = this.dataset.position;
                
                // Update the search input
                playerSearchInput.value = playerName;
                playerSearchResults.classList.add('hidden');
                
                // Add player to queue instead of showing all players
                addPlayerToQueue({
                    id: playerId,
                    user_id: userId,
                    player_name: playerName,
                    base_price: basePrice,
                    position: position
                });
                
                // Highlight the selected player in the main list or queue
                highlightPlayer(playerId);
                
                // Scroll to the player
                scrollToPlayer(playerId);
                
                // Debug log to check what ID we're looking for
                console.log('Looking for player with ID:', playerId);
            });
        });
    }
    
    function displayNoResults() {
        playerSearchResults.innerHTML = `
            <div class="p-4 text-center text-gray-500">
                <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <p>No players found</p>
            </div>
        `;
        playerSearchResults.classList.remove('hidden');
    }
    
    // Player queue management
    let playerQueue = [];
    
    function addPlayerToQueue(player) {
        // Check if player is already in queue
        const existingIndex = playerQueue.findIndex(p => p.id === player.id);
        if (existingIndex !== -1) {
            // Player already in queue, just highlight it
            highlightPlayer(player.id);
            return;
        }
        
        // Add to queue
        playerQueue.push(player);
        
        // Show queue section
        const queueSection = document.getElementById('playerQueueSection');
        queueSection.classList.remove('hidden');
        
        // Create queue item
        const queueContainer = document.getElementById('playerQueue');
        const queueItem = createQueueItem(player);
        queueContainer.appendChild(queueItem);
        
        // Update visible count
        updateVisibleCount();
    }
    
    function createQueueItem(player) {
        const queueItem = document.createElement('div');
        queueItem.className = 'glass-card p-4 hover:shadow-lg transition-all duration-300 player-card queue-item group';
        queueItem.setAttribute('data-player-id', player.id);
        queueItem.setAttribute('data-user-id', player.user_id);
        
        queueItem.innerHTML = `
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full overflow-hidden bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center shadow-lg">
                        <div class="w-full h-full flex items-center justify-center text-white font-bold text-sm">
                            ${player.player_name.substring(0, 2).toUpperCase()}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-lg truncate">${player.player_name}</h3>
                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                ${player.position}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-green-600">₹${player.base_price}</div>
                    <div class="text-xs text-gray-500">Base Price</div>
                </div>
            </div>
            
            <div class="flex space-x-2">
                <button onclick="startBidding('${player.user_id}', '${player.id}', '${player.player_name}', '${player.base_price}', '${player.position}')"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center group-hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    <span class="hidden sm:inline">Start Bidding</span>
                    <span class="sm:hidden">Bid</span>
                </button>
                <button onclick="removeFromQueue('${player.id}')"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        return queueItem;
    }
    
    function removeFromQueue(playerId) {
        // Remove from queue array
        playerQueue = playerQueue.filter(p => p.id !== playerId);
        
        // Remove from DOM
        const queueItem = document.querySelector(`[data-player-id="${playerId}"].queue-item`);
        if (queueItem) {
            queueItem.remove();
        }
        
        // Hide queue section if empty
        if (playerQueue.length === 0) {
            const queueSection = document.getElementById('playerQueueSection');
            queueSection.classList.add('hidden');
        }
        
        // Update visible count
        updateVisibleCount();
    }
    
    function updateVisibleCount() {
        const visibleCount = document.getElementById('visiblePlayersCount');
        if (visibleCount) {
            const mainPlayers = document.querySelectorAll('.player-card:not(.queue-item)').length;
            const queuePlayers = playerQueue.length;
            visibleCount.textContent = `${mainPlayers + queuePlayers} on this page`;
        }
    }
    
    function highlightPlayer(playerId) {
        // Remove previous highlights from both main list and queue
        document.querySelectorAll('.player-card').forEach(card => {
            card.classList.remove('ring-2', 'ring-blue-500', 'ring-opacity-50');
        });
        
        // Find and highlight the selected player (check both main list and queue)
        const playerCard = document.querySelector(`[data-player-id="${playerId}"]`);
        console.log('Looking for card with data-player-id:', playerId);
        console.log('Found card:', playerCard);
        
        if (playerCard) {
            playerCard.classList.add('ring-2', 'ring-blue-500', 'ring-opacity-50');
            console.log('Player highlighted successfully');
        } else {
            console.log('Player card not found');
        }
    }
    
    function scrollToPlayer(playerId) {
        const playerCard = document.querySelector(`[data-player-id="${playerId}"]`);
        if (playerCard) {
            playerCard.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
        }
    }
});
</script>
@endsection
