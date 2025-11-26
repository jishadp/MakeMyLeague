@extends('layouts.app')

@section('title', 'Auction - MakeMyLeague')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/auction.css') }}">
@endsection

@section('content')
<!-- Hidden inputs for bid increment settings -->
<input type="hidden" id="bid-increment-type" value="{{ $league->bid_increment_type ?? 'predefined' }}">
<input type="hidden" id="custom-bid-increment" value="{{ $league->custom_bid_increment ?? 10 }}">
<input type="hidden" id="predefined-increments" value="{{ json_encode($league->predefined_increments ?? []) }}">
<input type="hidden" id="league-id" value="{{ $league->id }}">
<input type="hidden" id="league-slug" value="{{ $league->slug }}">
<input type="hidden" id="is-organizer-or-admin" value="{{ auth()->user()->isOrganizerForLeague($league->id) || auth()->user()->isAdmin() ? 'true' : 'false' }}">
<input type="hidden" id="user-role" value="{{ $userRole }}">
<input type="hidden" id="user-team-id" value="{{ $userTeamId ?? '' }}">
<input type="hidden" id="auction-start-url" value="{{ route('auction.start') }}">

<div class="min-h-screen auction-bg py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Header -->
        <div class="glacier-card mb-6">
            <div class="px-4 py-4 sm:px-6 border-b glacier-border">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold glacier-text-primary">Cricket League Auction</h1>
                        <div class="mt-2 flex items-center space-x-2">
                            @if($userRole === 'organizer')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                    Organizer
                                </span>
                            @elseif($userRole === 'auctioneer')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    @if($userAuctioneerAssignment)
                                        Bidding for: {{ $userAuctioneerAssignment->leagueTeam->team->name }}
                                    @else
                                        Auctioneer
                                    @endif
                                </span>
                            @elseif($userRole === 'both')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                    </svg>
                                    Organizer + Auctioneer
                                    @if($userAuctioneerAssignment)
                                        ({{ $userAuctioneerAssignment->leagueTeam->team->name }})
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        @if($league->isAuctionActive())
                            <div class="flex flex-wrap gap-2">
                                <button onclick="copyLiveLink()" 
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                    </svg>
                                    Copy Live Link
                                </button>
                                <button onclick="copyBroadcastLink()" 
                                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors font-medium inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4 4 4-4m-4-5v9"></path>
                                    </svg>
                                    Copy Broadcast
                                </button>
                                <button type="button"
                                        onclick="shareLiveToWhatsApp()"
                                        class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors font-medium inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M12.04 2.25h-.08C6.617 2.25 2.25 6.617 2.25 11.96c0 1.856.486 3.664 1.41 5.27L2.3 21.57a.75.75 0 00.95.95l4.34-1.36a10 10 0 004.45 1.05h.08c5.343 0 9.71-4.367 9.71-9.71s-4.367-9.71-9.71-9.71zm0 1.5c4.535 0 8.21 3.675 8.21 8.21s-3.675 8.21-8.21 8.21h-.07a8.5 8.5 0 01-4.03-1.02l-.28-.15-2.58.81.83-2.52-.17-.3a8.5 8.5 0 01-1.05-4.03c0-4.535 3.675-8.21 8.21-8.21h.12zm3.132 6.21c-.165-.082-.97-.48-1.12-.535-.15-.056-.26-.082-.37.082-.11.165-.425.535-.52.645-.095.11-.19.124-.355.041-.165-.082-.7-.258-1.333-.82-.493-.44-.825-.984-.922-1.149-.096-.165-.01-.255.073-.337.075-.075.165-.196.248-.295.083-.098.11-.165.165-.275.055-.11.027-.206-.014-.288-.041-.082-.37-.891-.506-1.223-.133-.32-.269-.276-.37-.281-.096-.004-.206-.005-.316-.005-.11 0-.288.041-.44.206-.15.165-.58.566-.58 1.38 0 .814.594 1.6.677 1.713.082.11 1.17 1.788 2.836 2.505.396.171.704.272.944.349.397.126.76.108 1.047.065.319-.048.97-.396 1.107-.777.137-.38.137-.706.096-.777-.04-.068-.15-.11-.316-.192z"></path>
                                    </svg>
                                    WhatsApp Live
                                </button>
                                <button type="button"
                                        onclick="shareBroadcastToWhatsApp()"
                                        class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors font-medium inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M12.04 2.25h-.08C6.617 2.25 2.25 6.617 2.25 11.96c0 1.856.486 3.664 1.41 5.27L2.3 21.57a.75.75 0 00.95.95l4.34-1.36a10 10 0 004.45 1.05h.08c5.343 0 9.71-4.367 9.71-9.71s-4.367-9.71-9.71-9.71zm0 1.5c4.535 0 8.21 3.675 8.21 8.21s-3.675 8.21-8.21 8.21h-.07a8.5 8.5 0 01-4.03-1.02l-.28-.15-2.58.81.83-2.52-.17-.3a8.5 8.5 0 01-1.05-4.03c0-4.535 3.675-8.21 8.21-8.21h.12zm3.132 6.21c-.165-.082-.97-.48-1.12-.535-.15-.056-.26-.082-.37.082-.11.165-.425.535-.52.645-.095.11-.19.124-.355.041-.165-.082-.7-.258-1.333-.82-.493-.44-.825-.984-.922-1.149-.096-.165-.01-.255.073-.337.075-.075.165-.196.248-.295.083-.098.11-.165.165-.275.055-.11.027-.206-.014-.288-.041-.082-.37-.891-.506-1.223-.133-.32-.269-.276-.37-.281-.096-.004-.206-.005-.316-.005-.11 0-.288.041-.44.206-.15.165-.58.566-.58 1.38 0 .814.594 1.6.677 1.713.082.11 1.17 1.788 2.836 2.505.396.171.704.272.944.349.397.126.76.108 1.047.065.319-.048.97-.396 1.107-.777.137-.38.137-.706.096-.777-.04-.068-.15-.11-.316-.192z"></path>
                                    </svg>
                                    WhatsApp Broadcast
                                </button>
                            </div>
                        @endif
                        <a href="{{ route('auctions.live', $league) }}" 
                           class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors font-medium">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Public Live View
                        </a>
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
                    @php
                        // Calculate total players that need auction
                        $totalPlayers = \App\Models\LeaguePlayer::where('league_id', $league->id)
                                            ->where(function($query) {
                                                $query->whereIn('status', ['available', 'sold', 'unsold', 'auctioning'])
                                                    ->orWhere('retention', false);
                                            })->count();
                        $soldPlayers = \App\Models\LeaguePlayer::where('league_id', $league->id)
                                            ->where('status', 'sold')->count();
                        $progressPercentage = $totalPlayers > 0 ? round(($soldPlayers / $totalPlayers) * 100) : 0;
                    @endphp
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Progress</span>
                        <span>{{ $progressPercentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-green-500 to-blue-500 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ $progressPercentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Players Section (Organizer Only) -->
        @can('selectPlayer', $league)
        <div class="mb-8 {{ isset($currentPlayer) && $currentPlayer && $currentPlayer->status === 'auctioning' ? 'hidden' : '' }}" id="availablePlayersSection">
            @include('auction.partials.available-players')
        </div>
        @endcan

        <!-- Player Bidding Section -->
        <div class="flex justify-center mb-8 px-4 sm:px-0" id="biddingSection">
            @include('auction.partials.player-bidding')
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
<script src="{{ asset('js/auction-access.js') }}?v=1"></script>
<script src="{{ asset('js/auction.js') }}?v=15"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    // Inject Pusher credentials from Laravel environment
    const PUSHER_KEY = '{{ config('broadcasting.connections.pusher.key') }}';
    const PUSHER_CLUSTER = '{{ config('broadcasting.connections.pusher.options.cluster') }}';
    const PUSHER_LOG_TO_CONSOLE = {{ config('app.debug') ? 'true' : 'false' }};

    // Broadcast share helpers
    const broadcastShareUrl = @json(route('auctions.live.public', $league));
    const liveShareUrl = @json(route('auctions.live', $league));
    const broadcastLeagueName = @json($league->name);
</script>
<script src="{{ asset('js/pusher-main.js') }}?v={{ time() + 1 }}"></script>

<script>
// Override jQuery AJAX error handlers to use role-based error handling
$(document).ajaxError(function(event, xhr, settings) {
    if (xhr.status === 403 && typeof handleAuctionError === 'function') {
        const errorMessage = handleAuctionError(xhr);
        console.error('Access denied:', errorMessage);
    }
});
</script>

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
                // Debug logging removed for production
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
        const queueLeagueId = document.getElementById('league-id')?.value || '';
        const queueStartUrl = document.getElementById('auction-start-url')?.value || '';
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
                <button onclick="startBidding('${player.user_id}', '${player.id}', '${player.player_name}', '${player.base_price}', '${player.position}', '${queueLeagueId}', '${queueStartUrl}')"
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
        // Debug logging removed for production
        
        if (playerCard) {
            playerCard.classList.add('ring-2', 'ring-blue-500', 'ring-opacity-50');
            // Player highlighted successfully
        } else {
            // Player card not found
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

// Clipboard helpers
function copyLiveLink() {
    copyTextWithFallback(liveShareUrl, 'Live auction link copied to clipboard!');
}

function copyBroadcastLink() {
    copyTextWithFallback(broadcastShareUrl, 'Broadcast link copied to clipboard!');
}

// Share via WhatsApp
function shareBroadcastToWhatsApp() {
    const message = `Watch the live auction broadcast for ${broadcastLeagueName}: ${broadcastShareUrl}`;
    openWhatsAppShare(message);
}

function shareLiveToWhatsApp() {
    const message = `Join the live auction room for ${broadcastLeagueName}: ${liveShareUrl}`;
    openWhatsAppShare(message);
}

function openWhatsAppShare(message) {
    const shareUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
    const popup = window.open(shareUrl, '_blank');

    if (!popup || popup.closed || typeof popup.closed === 'undefined') {
        showMessage('Please allow pop-ups to share on WhatsApp.', 'error');
        return;
    }

    showMessage('Opening WhatsApp to share...', 'success');
}

function copyTextWithFallback(text, successMessage) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            showMessage(successMessage, 'success');
        }, function() {
            fallbackCopyTextToClipboard(text);
        });
    } else {
        fallbackCopyTextToClipboard(text);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.width = "2em";
    textArea.style.height = "2em";
    textArea.style.padding = "0";
    textArea.style.border = "none";
    textArea.style.outline = "none";
    textArea.style.boxShadow = "none";
    textArea.style.background = "transparent";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showMessage('Auction link copied to clipboard!', 'success');
        } else {
            showMessage('Failed to copy link. Please copy manually: ' + text, 'error');
        }
    } catch (err) {
        showMessage('Failed to copy link. Please copy manually: ' + text, 'error');
    }
    
    document.body.removeChild(textArea);
}

function showMessage(message, type) {
    const messageContainer = document.getElementById('messageContainer');
    const messageDiv = document.createElement('div');
    messageDiv.className = `p-4 rounded-lg shadow-lg mb-2 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
    messageDiv.innerHTML = `
        <div class="flex items-center justify-between">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    messageContainer.appendChild(messageDiv);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (messageDiv.parentElement) {
            messageDiv.remove();
        }
    }, 5000);
}
</script>
@endsection
