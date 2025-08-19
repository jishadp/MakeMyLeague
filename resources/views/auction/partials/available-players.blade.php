<!-- Available Players Section -->
<div class="lg:col-span-2">
    <div class="glass-card">
        <!-- Header Section -->
        <div class="px-4 py-4 sm:px-6 border-b border-gray-200/30">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Available Players</h2>
                    <p class="text-sm text-gray-600">Select players to auction by bidding (Retention players excluded)</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $availablePlayers->total() }} Players
                    </div>
                    <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $availablePlayers->count() }} on this page
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 sm:p-6">
            <!-- Design Toggle -->
            <div class="mb-6 flex justify-center">
                <div class="bg-gray-100 rounded-lg p-1 flex">
                    <button id="cardViewBtn" class="px-4 py-2 rounded-md text-sm font-medium bg-white text-gray-900 shadow-sm transition-all duration-200 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                        <span class="hidden sm:inline">Card View</span>
                    </button>
                    <button id="listViewBtn" class="px-4 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900 transition-all duration-200 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        <span class="hidden sm:inline">List View</span>
                    </button>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" id="playerSearch" placeholder="Search players by name, mobile, or role..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent glass-input">
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <select id="roleFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent glass-input">
                            <option value="">All Roles</option>
                            <option value="Batsman">Batsman</option>
                            <option value="Bowler">Bowler</option>
                            <option value="All-rounder">All-rounder</option>
                            <option value="Wicket-keeper">Wicket-keeper</option>
                        </select>
                        <select id="priceFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent glass-input">
                            <option value="">All Prices</option>
                            <option value="0-100">₹0 - ₹100</option>
                            <option value="101-200">₹101 - ₹200</option>
                            <option value="201-500">₹201 - ₹500</option>
                            <option value="500+">₹500+</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Card View Design -->
            <div id="cardView" class="players-container">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="playersList">
                    @forelse($availablePlayers as $player)
                    <div class="glass-card p-4 hover:shadow-lg transition-all duration-300 player-card group" 
                         data-player-id="{{ $player->user->id }}"
                         data-player-name="{{ $player->user->name }}"
                         data-base-price="{{ $player->base_price }}"
                         data-league-player-id="{{ $player->id }}"
                         data-player-role="{{ $player->user->position->name ?? 'Unknown' }}"
                         data-player-mobile="{{ $player->user->mobile }}">
                        
                        <!-- Player Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-full overflow-hidden bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center shadow-lg">
                                    <img src="{{ asset('images/defaultplayer.jpeg') }}" 
                                         alt="{{ $player->user->name }}" 
                                         class="w-full h-full object-cover"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-full h-full flex items-center justify-center text-white font-bold text-lg" style="display: none;">
                                        {{ strtoupper(substr($player->user->name, 0, 2)) }}
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 text-lg truncate">{{ $player->user->name }}</h3>
                                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                            {{ $player->user->position->name ?? 'Unknown' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-green-600">₹{{ number_format($player->base_price) }}</div>
                                <div class="text-xs text-gray-500">Base Price</div>
                            </div>
                        </div>

                        <!-- Player Details -->
                        <div class="grid grid-cols-1 gap-3 mb-4 text-sm">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span class="text-gray-600 truncate">{{ $player->user->mobile }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="text-gray-600 truncate">{{ $player->user->localBody->name ?? 'Unknown' }}</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2">
                            <button onclick="selectPlayerForBidding('{{ $player->id }}', '{{ $player->user->name }}', '{{ $player->base_price }}', '{{ $player->user->position->name ?? 'Unknown' }}')"
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center group-hover:shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                <span class="hidden sm:inline">Start Bidding</span>
                                <span class="sm:hidden">Bid</span>
                            </button>
                            <button onclick="viewPlayerDetails('{{ $player->id }}')"
                                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-12 text-gray-500">
                        <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <p class="text-lg font-medium">No available players for auction</p>
                        <p class="text-sm text-gray-400 mt-1">All players have been sold or are not available</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- List View Design -->
            <div id="listView" class="players-container hidden">
                <div class="space-y-3" id="playersListList">
                    @forelse($availablePlayers as $player)
                    <div class="glass-card p-4 hover:shadow-lg transition-all duration-300 player-card group" 
                         data-player-id="{{ $player->user->id }}"
                         data-player-name="{{ $player->user->name }}"
                         data-base-price="{{ $player->base_price }}"
                         data-league-player-id="{{ $player->id }}"
                         data-player-role="{{ $player->user->position->name ?? 'Unknown' }}"
                         data-player-mobile="{{ $player->user->mobile }}">
                        
                        <div class="flex items-center space-x-4">
                            <!-- Player Avatar -->
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center shadow-lg flex-shrink-0">
                                <img src="{{ asset('images/defaultplayer.jpeg') }}" 
                                     alt="{{ $player->user->name }}" 
                                     class="w-full h-full object-cover"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-full h-full flex items-center justify-center text-white font-bold text-lg" style="display: none;">
                                    {{ strtoupper(substr($player->user->name, 0, 2)) }}
                                </div>
                            </div>

                            <!-- Player Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-semibold text-gray-900 text-lg truncate">{{ $player->user->name }}</h3>
                                    <div class="text-right">
                                        <div class="text-xl font-bold text-green-600">₹{{ number_format($player->base_price) }}</div>
                                        <div class="text-xs text-gray-500">Base Price</div>
                                    </div>
                                </div>
                                
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                            {{ $player->user->position->name ?? 'Unknown' }}
                                        </span>
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                            <span class="truncate">{{ $player->user->mobile }}</span>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span class="truncate">{{ $player->user->localBody->name ?? 'Unknown' }}</span>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex space-x-2">
                                        <button onclick="selectPlayerForBidding('{{ $player->id }}', '{{ $player->user->name }}', '{{ $player->base_price }}', '{{ $player->user->position->name ?? 'Unknown' }}')"
                                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center space-x-2 group-hover:shadow-md">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                            </svg>
                                            <span>Bid</span>
                                        </button>
                                        <button onclick="viewPlayerDetails('{{ $player->id }}')"
                                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm transition-colors duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12 text-gray-500">
                        <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <p class="text-lg font-medium">No available players for auction</p>
                        <p class="text-sm text-gray-400 mt-1">All players have been sold or are not available</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Pagination -->
            @if($availablePlayers->hasPages())
            <div class="mt-6 overflow-x-auto">
                {{ $availablePlayers->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- JavaScript for View Toggle -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cardViewBtn = document.getElementById('cardViewBtn');
    const listViewBtn = document.getElementById('listViewBtn');
    const cardView = document.getElementById('cardView');
    const listView = document.getElementById('listView');

    // Set default view (card view)
    let currentView = 'card';
    
    function switchView(view) {
        if (view === 'card') {
            cardView.classList.remove('hidden');
            listView.classList.add('hidden');
            cardViewBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
            cardViewBtn.classList.remove('text-gray-600');
            listViewBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
            listViewBtn.classList.add('text-gray-600');
            currentView = 'card';
        } else {
            listView.classList.remove('hidden');
            cardView.classList.add('hidden');
            listViewBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
            listViewBtn.classList.remove('text-gray-600');
            cardViewBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
            cardViewBtn.classList.add('text-gray-600');
            currentView = 'list';
        }
    }

    cardViewBtn.addEventListener('click', () => switchView('card'));
    listViewBtn.addEventListener('click', () => switchView('list'));

    // Responsive behavior - switch to list view on mobile by default
    if (window.innerWidth < 640) {
        switchView('list');
    }

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth < 640 && currentView === 'card') {
            switchView('list');
        } else if (window.innerWidth >= 640 && currentView === 'list') {
            switchView('card');
        }
    });
});
</script>

<!-- Additional Mobile-First CSS -->
<style>
/* Mobile-first responsive improvements */
@media (max-width: 640px) {
    .glass-card {
        margin: 0 -0.5rem;
        border-radius: 12px;
    }
    
    .players-container {
        margin: 0 -0.5rem;
    }
    
    /* Improve touch targets on mobile */
    button {
        min-height: 44px;
    }
    
    /* Better spacing for mobile */
    .player-card {
        margin-bottom: 0.75rem;
    }
}

/* Smooth transitions for view switching */
.players-container {
    transition: opacity 0.3s ease-in-out;
}

/* Enhanced hover effects for better mobile experience */
@media (hover: hover) {
    .player-card:hover {
        transform: translateY(-2px);
    }
}

/* Touch-friendly improvements */
@media (pointer: coarse) {
    .player-card {
        cursor: pointer;
    }
    
    button {
        padding: 0.75rem 1rem;
    }
}

/* Loading states */
.player-card.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Accessibility improvements */
.player-card:focus-within {
    outline: 2px solid #4a90e2;
    outline-offset: 2px;
}

/* Dark mode support (if needed) */
@media (prefers-color-scheme: dark) {
    .glass-card {
        background: rgba(30, 58, 82, 0.1);
        border-color: rgba(176, 224, 230, 0.1);
    }
}
</style>
