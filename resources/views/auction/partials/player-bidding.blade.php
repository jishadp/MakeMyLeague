<!-- Bidding Section -->
<div class="lg:col-span-1" id="biddingSection">
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-4 py-4 sm:px-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Player Bidding</h2>
        </div>
        <div class="p-4 sm:p-6">
            <div id="noBiddingPlayer" class="text-center py-4 text-gray-500">
                <p>Select a player to start bidding</p>
            </div>

            <div id="biddingCard" class="hidden">
                <!-- Selected Player Info -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-4 mb-4 text-white">
                    <h3 class="text-xl font-bold mb-2" id="selectedPlayerName"></h3>
                    <p class="text-blue-100 text-sm" id="selectedPlayerBasePrice"></p>
                </div>

                <!-- Team Selection -->
                <div class="mb-4">
                    <label for="league_team_id" class="block text-sm font-medium text-gray-700 mb-2">Select Team</label>
                    <select name="league_team_id" id="league_team_id" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                    
                    <!-- Bid Amount Input -->
                    <div class="border border-gray-200 rounded-lg p-3 mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bid Amount</label>
                        <input type="number" id="bidAmount" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                
                <!-- Place Bid Button -->
                <button id="placeBidBtn" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg transition-colors duration-200 text-lg font-medium mb-3">
                    Place Bid
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
    <div class="bg-white rounded-lg shadow">
        <div class="px-4 py-4 sm:px-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Recent Bids</h2>
        </div>
        <div class="p-4 sm:p-6">
            <div id="biddingHistory" class="space-y-3 max-h-80 overflow-y-auto">
                <div class="text-center py-4 text-gray-500">
                    <p>No bids yet</p>
                </div>
            </div>
        </div>
    </div>
</div>
