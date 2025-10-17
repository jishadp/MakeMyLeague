<!-- Player Bidding Section - Laravel Blade Partial -->
<div class="bidMain w-full max-w-sm sm:max-w-md md:max-w-lg lg:max-w-2xl mx-auto px-2 sm:px-4 lg:px-6 xl:px-8 py-3 sm:py-6 lg:py-8 {{ isset($currentPlayer) && $currentPlayer ? '' : 'hidden' }}">
<input type="hidden" id="league-id" value="{{ $league->id ?? '' }}">

    <!-- Header Card -->
    {{-- <div class="glassmorphism rounded-3xl shadow-2xl mb-6 sm:mb-8 overflow-hidden">
        <div class="p-4 sm:p-6 lg:p-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-6">
                <!-- Title Section -->
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div class="relative">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-500 via-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-400 rounded-full animate-ping"></div>
                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full"></div>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                            Player Bidding
                        </h1>
                        <p class="text-sm sm:text-base text-gray-600 mt-1">Live Auction Interface</p>
                    </div>
                </div>

                <!-- Status Section -->
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <!-- Countdown Timer -->
                    <div class="relative">
                        <svg class="w-16 h-16 sm:w-20 sm:h-20 countdown-ring" viewBox="0 0 36 36">
                            <path class="text-gray-300" stroke="currentColor" stroke-width="3" fill="transparent"
                                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            <path class="text-red-500" stroke="currentColor" stroke-width="3" fill="transparent" stroke-linecap="round"
                                  stroke-dasharray="75, 100"
                                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-lg sm:text-xl font-bold text-red-600">90</div>
                                <div class="text-xs text-gray-500">sec</div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="bg-gradient-to-r from-emerald-500 to-green-500 text-white px-4 py-2 sm:px-6 sm:py-3 rounded-2xl font-semibold shadow-lg flex items-center space-x-2 animate-pulse-slow">
                        <div class="w-2 h-2 bg-white rounded-full animate-ping"></div>
                        <span class="text-sm sm:text-base">LIVE</span>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Main Content - Full Width Player Card -->
    <div class="w-full">
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
                            @if(isset($currentPlayer) && $currentPlayer->player && $currentPlayer->player->photo)
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
                                P
                            </div>
                        </div>
                        <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-4 h-4 text-yellow-800" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-center sm:text-left flex-grow">
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-3 text-gray-800 playerName">
                            @if(isset($currentPlayer) && $currentPlayer->player)
                                {{ $currentPlayer->player->name }}
                            @else
                                Select a Player
                            @endif
                        </h2>
                        <div class="flex flex-wrap gap-2 justify-center sm:justify-start mb-4">
                            @if(isset($currentPlayer) && $currentPlayer->player && $currentPlayer->player->primaryGameRole && $currentPlayer->player->primaryGameRole->gamePosition)
                                <span class="bg-blue-500 bg-opacity-20 backdrop-blur-sm px-4 py-2 rounded-2xl text-sm font-semibold text-blue-700 border border-blue-300 position">
                                    {{ $currentPlayer->player->primaryGameRole->gamePosition->name }}
                                </span>
                            @endif
                            <span class="bg-green-500 bg-opacity-20 backdrop-blur-sm px-4 py-2 rounded-2xl text-sm font-semibold text-green-700 border border-green-300">
                                Base Price ₹<span class="basePrice">
                                    @if(isset($currentPlayer))
                                        {{ $currentPlayer->base_price ?? 0 }}
                                    @else
                                        0
                                    @endif
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 1. Current Bid Row with Side Stats -->
                <div class="stat-card bg-white bg-opacity-80 backdrop-blur-sm rounded-2xl p-4 sm:p-6 border border-blue-200 shadow-lg mb-6">
                    <!-- Current Bid (Main Highlight) -->
                    <div class="text-center mb-4">
                        <p class="text-gray-600 text-xs sm:text-sm font-medium mb-2 bidStatus">
                            @if(isset($currentHighestBid) && $currentHighestBid)
                                Current Bid
                            @elseif(isset($currentPlayer))
                                Base Price
                            @else
                                Select a Player
                            @endif
                        </p>
                        <p class="font-bold text-2xl sm:text-3xl lg:text-4xl text-blue-600 mb-2">₹ <span class="currentBid">
                            @if(isset($currentHighestBid) && $currentHighestBid)
                                {{ $currentHighestBid->amount }}
                            @elseif(isset($currentPlayer))
                                {{ $currentPlayer->base_price ?? 0 }}
                            @else
                                0
                            @endif
                        </span></p>
                        <p class="text-gray-600 text-sm font-medium bidTeam">
                            @if(isset($currentHighestBid) && $currentHighestBid && $currentHighestBid->leagueTeam && $currentHighestBid->leagueTeam->team)
                                {{ $currentHighestBid->leagueTeam->team->name }}
                            @elseif(isset($currentPlayer))
                                Awaiting new bids..
                            @else
                                No player selected
                            @endif
                        </p>
                    </div>

                    <!-- Bottom Stats Row -->
                    <div class="grid grid-cols-2 gap-4 sm:gap-6 pt-3 border-t border-gray-200 hidden teamBidDetls">
                        <!-- Total Players (Small) -->
                        <div class="text-center">
                            <p class="text-gray-500 text-xs font-medium mb-1">Total Players</p>
                            <p class="font-bold text-sm sm:text-base text-gray-700 leageTeamPlayers"></p>
                        </div>

                        <!-- Team Balance (Small) -->
                        <div class="text-center">
                            <p class="text-gray-500 text-xs font-medium mb-1">Team Balance</p>
                            <p class="font-bold text-sm sm:text-base text-gray-700 teamBalance"></p>
                        </div>
                    </div>
                </div>

                <!-- 3. Quick Bid Row -->
                <div class="mb-6">
                    @if(auth()->user()->isOrganizerForLeague($league->id) || auth()->user()->isAdmin())
                        <!-- Organizer View: 2 mobile-friendly options -->
                        @php
                            $currentBid = isset($currentHighestBid) && $currentHighestBid ? $currentHighestBid->amount : ($currentPlayer->base_price ?? 0);
                            $nextBid = $league->calculateNextBid($currentBid);
                            $increment = $nextBid - $currentBid;
                        @endphp
                        <div class="grid grid-cols-2 gap-4" call-bid-action="{{route('auction.call')}}" token="{{csrf_token()}}">
                            <!-- Bid Button -->
                            <div class="callBid stat-card bg-white bg-opacity-80 backdrop-blur-sm rounded-2xl p-4 sm:p-6 text-center border border-green-300 shadow-lg cursor-pointer hover:scale-105 transition-all duration-300" 
                                 increment="{{ $increment }}"
                                 league-id="{{ $league->id ?? '' }}"
                                 player-id="{{ $currentPlayer->player->id ?? '' }}"
                                 base-price="{{ $currentBid }}"
                                 league-player-id="{{ $currentPlayer->id ?? '' }}">
                                <div class="flex flex-col items-center justify-center h-full">
                                    <p class="font-bold text-3xl sm:text-4xl text-green-600">{{ number_format($increment, 0) }}</p>
                                </div>
                            </div>
                            <!-- Custom Button -->
                            <div class="stat-card bg-white bg-opacity-80 backdrop-blur-sm rounded-2xl p-4 sm:p-6 text-center border border-purple-300 shadow-lg cursor-pointer hover:scale-105 transition-all duration-300"
                                 onclick="openCustomBidModal()">
                                <div class="flex flex-col items-center space-y-2">
                                    <p class="font-bold text-lg sm:text-xl text-purple-600">C</p>
                                    <p class="text-xs text-gray-600">Custom</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Auctioneer/Team Owner View: 1 increment option -->
                        @php
                            $currentBid = isset($currentHighestBid) && $currentHighestBid ? $currentHighestBid->amount : ($currentPlayer->base_price ?? 0);
                            $nextBid = $league->calculateNextBid($currentBid);
                            $increment = $nextBid - $currentBid;
                        @endphp
                        <div class="flex justify-center" call-bid-action="{{route('auction.call')}}" token="{{csrf_token()}}">
                            <div class="callBid stat-card bg-white bg-opacity-80 backdrop-blur-sm rounded-2xl p-6 sm:p-8 text-center border border-blue-300 shadow-lg cursor-pointer hover:scale-105 transition-all duration-300 w-full max-w-md" 
                                 increment="{{ $increment }}"
                                 league-id="{{ $league->id ?? '' }}"
                                 player-id="{{ $currentPlayer->player->id ?? '' }}"
                                 base-price="{{ $currentBid }}"
                                 league-player-id="{{ $currentPlayer->id ?? '' }}">
                                <div class="flex flex-col items-center justify-center h-full">
                                    <p class="font-bold text-4xl sm:text-5xl lg:text-6xl text-blue-600">{{ number_format($increment, 0) }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- 4. Admin Controls Row -->
                @if(auth()->user()->isOrganizer())
                <div class="mb-6" mark-sold-action="{{ route('auction.sold')}}" mark-unsold-action="{{ route('auction.unsold')}}" token="{{ csrf_token()}}">
                    <div class="grid grid-cols-2 gap-3 sm:gap-6">
                        <div class="markSold stat-card bg-white bg-opacity-80 backdrop-blur-sm rounded-2xl p-4 sm:p-6 text-center border border-green-300 shadow-lg cursor-pointer hover:scale-105 transition-all duration-300"
                             league-player-id="{{ $currentPlayer->id ?? '' }}">
                            <p class="font-bold text-xl sm:text-2xl lg:text-3xl text-green-600">SOLD</p>
                        </div>
                        <div class="markUnSold stat-card bg-white bg-opacity-80 backdrop-blur-sm rounded-2xl p-4 sm:p-6 text-center border border-red-300 shadow-lg cursor-pointer hover:scale-105 transition-all duration-300"
                             league-player-id="{{ $currentPlayer->id ?? '' }}">
                            <p class="font-bold text-xl sm:text-2xl lg:text-3xl text-red-600">UNSOLD</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

<style>
/* Enhanced Glassmorphism Effects */
.glassmorphism {
    background: linear-gradient(rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.3));
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.card__content {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 24px;
}

/* Enhanced Button Effects */
.btn-shimmer {
    position: relative;
    overflow: hidden;
}

.btn-shimmer::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    transition: left 0.5s;
}

.btn-shimmer:hover::before {
    left: 100%;
}

.bid-button:active {
    transform: scale(0.95);
}

/* Enhanced Animations */
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

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-20px);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-pulse-slow {
    animation: pulse 3s ease-in-out infinite;
}

/* Stat Card Hover Effects */
.stat-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.stat-card:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Countdown Timer */
.countdown-ring {
    transform: rotate(-90deg);
}

/* Mobile Optimizations */
@media (max-width: 640px) {
    .card__content {
        padding: 1.5rem !important;
    }

    .bid-button {
        padding: 1rem !important;
        min-height: 60px;
    }

    /* Mobile-friendly bid buttons */
    .grid-cols-2 .stat-card {
        padding: 1rem !important;
        min-height: 80px;
    }
    
    .grid-cols-2 .stat-card p {
        font-size: 0.875rem !important;
    }

    /* Ensure proper spacing on mobile */
    .grid-cols-3 .bid-button {
        font-size: 0.875rem;
    }

    .grid-cols-2 button {
        font-size: 0.875rem;
    }
}

/* Tablet Optimizations */
@media (min-width: 641px) and (max-width: 1024px) {
    .bid-button {
        padding: 1.5rem !important;
    }
}

/* Desktop Enhancements */
@media (min-width: 1025px) {
    .w-full.max-w-7xl {
        max-width: 90rem;
    }

    .card__content {
        padding: 2.5rem !important;
    }
}

/* Ultra-wide Screen Optimization */
@media (min-width: 1920px) {
    .w-full.max-w-7xl {
        max-width: 100rem;
    }
}

/* High DPI Screen Optimization */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .glassmorphism {
        backdrop-filter: blur(30px);
        -webkit-backdrop-filter: blur(30px);
    }
}

</style>

<!-- Custom Bid Modal -->
<div id="customBidModal" class="fixed inset-0 bg-white bg-opacity-90 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center p-4">
    <div class="white-glass-card relative w-full max-w-md mx-auto p-8 animate-fadeInUp">
        <!-- Close Button -->
        <button onclick="closeCustomBidModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full flex items-center justify-center mb-4 shadow-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Custom Bid Amount</h3>
            <p class="text-sm text-gray-600">Set your desired bid amount</p>
        </div>

        <!-- Current Bid Display -->
        <div class="white-glass-card p-4 mb-6">
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-1">Current Bid</p>
                <p class="text-2xl font-bold text-gray-800" id="currentBidDisplay">
                    ₹{{ isset($currentHighestBid) && $currentHighestBid ? number_format($currentHighestBid->amount, 0) : number_format($currentPlayer->base_price ?? 0, 0) }}
                </p>
            </div>
        </div>

        <!-- Custom Amount Input -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-3">Enter Bid Amount (₹)</label>
            <div class="relative">
                <input type="number" 
                       id="customBidAmount" 
                       min="1" 
                       step="1"
                       placeholder="Enter amount"
                       class="white-glass-input w-full px-4 py-3 rounded-xl border-0 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-200 text-gray-800 placeholder-gray-500 text-center text-xl font-bold">
            </div>
        </div>

        <!-- Next Bid Preview -->
        <div class="white-glass-card p-4 mb-6">
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-1">Next Bid Will Be</p>
                <p class="text-xl font-bold text-gray-800" id="nextBidPreview">₹0</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-4">
            <button onclick="closeCustomBidModal()" 
                    class="flex-1 white-glass-button px-6 py-3 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition-all duration-200">
                Cancel
            </button>
            <button onclick="placeCustomBid()" 
                    class="flex-1 white-glass-button px-6 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 font-semibold hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 shadow-lg">
                <span class="flex items-center justify-center text-white">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    Place Bid
                </span>
            </button>
        </div>
    </div>
</div>

<script>
function openCustomBidModal() {
    document.getElementById('customBidModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Set current bid value and update preview
    const currentBid = {{ isset($currentHighestBid) && $currentHighestBid ? $currentHighestBid->amount : ($currentPlayer->base_price ?? 0) }};
    document.getElementById('customBidAmount').value = '';
    updateBidPreview();
    
    // Focus on input
    setTimeout(() => {
        document.getElementById('customBidAmount').focus();
    }, 100);
}

function closeCustomBidModal() {
    document.getElementById('customBidModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function updateBidPreview() {
    const customAmount = document.getElementById('customBidAmount').value;
    const preview = document.getElementById('nextBidPreview');
    
    if (customAmount && customAmount > 0) {
        preview.textContent = '₹' + parseInt(customAmount).toLocaleString();
    } else {
        preview.textContent = '₹0';
    }
}

function placeCustomBid() {
    const customAmount = document.getElementById('customBidAmount').value;
    
    if (!customAmount || customAmount <= 0) {
        alert('Please enter a valid bid amount');
        return;
    }
    
    // Calculate the increment needed
    const currentBid = {{ isset($currentHighestBid) && $currentHighestBid ? $currentHighestBid->amount : ($currentPlayer->base_price ?? 0) }};
    const increment = customAmount - currentBid;
    
    if (increment <= 0) {
        alert('Bid amount must be higher than current bid');
        return;
    }
    
        // Get the bid action URL and token from existing bid buttons
    const bidContainer = document.querySelector('[call-bid-action]');
    if (!bidContainer) {
        alert('Error: Could not find bid configuration. Please refresh the page and try again.');
        return;
    }
    
    const callBidAction = bidContainer.getAttribute('call-bid-action');
    const token = bidContainer.getAttribute('token');
    
    // Get league and player data
    const leagueId = '{{ $league->id ?? "" }}';
    const playerId = '{{ $currentPlayer->player->id ?? "" }}';
    const leaguePlayerId = '{{ $currentPlayer->id ?? "" }}';
    
    if (!leagueId || !playerId || !leaguePlayerId) {
        alert('Error: Missing player or league data. Please refresh the page and try again.');
        return;
    }
    
    // Disable the button to prevent double-clicking
    const placeBidBtn = document.querySelector('button[onclick="placeCustomBid()"]');
    const originalText = placeBidBtn.innerHTML;
    placeBidBtn.disabled = true;
    placeBidBtn.innerHTML = '<span class="flex items-center justify-center text-white"><svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Placing...</span>';
    
    // Make the AJAX request
    $.ajax({
        url: callBidAction,
        type: "post",
        headers: {'X-CSRF-TOKEN': token},
        data: {
            league_id: leagueId,
            player_id: playerId,
            base_price: currentBid,
            increment: increment,
            league_player_id: leaguePlayerId,
        },
        success: function (response) {
            console.log("Custom bid placed:", response);
            
            if (response.success) {
                // Close the modal
                closeCustomBidModal();
                
                // Show success message
                if (typeof showMessage === 'function') {
                    showMessage('Custom bid placed successfully!', 'success');
                } else {
                    alert('Custom bid placed successfully!');
                }
                
                // Refresh the page to show updated bid
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                alert('Error: ' + (response.message || 'Failed to place bid'));
            }
        },
        error: function (xhr) {
            console.error("Error placing custom bid:", xhr.responseText);
            let errorMessage = 'Error placing bid. Please try again.';
            
            try {
                const errorData = JSON.parse(xhr.responseText);
                errorMessage = errorData.message || errorMessage;
            } catch (e) {
                // Use default error message
            }
            
            alert(errorMessage);
        },
        complete: function() {
            // Restore button state
            placeBidBtn.disabled = false;
            placeBidBtn.innerHTML = originalText;
        }
    });
}

// Update preview on input change
document.addEventListener('DOMContentLoaded', function() {
    const customBidInput = document.getElementById('customBidAmount');
    if (customBidInput) {
        customBidInput.addEventListener('input', updateBidPreview);
    }
});
</script>
