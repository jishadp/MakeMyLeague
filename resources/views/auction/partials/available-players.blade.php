<!-- Available Players Section -->
<div class="lg:col-span-2">
    <div class="bg-white rounded-lg shadow">
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
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Players List -->
            <div class="space-y-3" id="playersList">
                @forelse($availablePlayers as $player)
                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-all duration-200 player-card" 
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
                                class="auction-btn bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200 w-full sm:w-auto">
                            Start Bidding
                        </button>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <p>No available players for auction</p>
                </div>
                @endforelse
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
