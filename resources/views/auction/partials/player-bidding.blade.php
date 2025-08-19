<!-- Bidding Section -->
<div class="lg:col-span-1" id="biddingSection">
    <!-- Organizer Control Card -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-lg shadow-lg mb-6">
        <div class="px-3 py-3 sm:px-6 border-b border-purple-500">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center space-x-3">
                    <div class="bg-white bg-opacity-20 p-2 rounded-lg flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h2 class="text-base sm:text-lg font-bold text-white truncate">Auction Organizer</h2>
                        <p class="text-purple-100 text-xs sm:text-sm truncate">Control the auction process</p>
                    </div>
                </div>
                <div class="bg-white bg-opacity-20 px-3 py-1 rounded-full self-start sm:self-auto">
                    <span class="text-white text-xs sm:text-sm font-medium" id="auctionStatus">
                        @if($league->auction_active)
                            Active
                        @elseif($league->auction_ended_at)
                            Ended
                        @else
                            Ready
                        @endif
                    </span>
                </div>
            </div>
        </div>
        <div class="p-3 sm:p-6">
            <!-- Bid Increment Structure -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-white mb-2">Bid Increment Structure</label>
                <select id="bidIncrementStructure" class="w-full px-3 py-2 border border-purple-400 rounded-lg focus:ring-2 focus:ring-white focus:border-transparent bg-white bg-opacity-90 text-sm mobile-input mobile-button">
                    <option value="predefined">Predefined Structure</option>
                    <option value="custom">Custom Increments</option>
                </select>
            </div>

            <!-- Predefined Increments -->
            <div id="predefinedIncrements" class="mb-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs sm:text-sm text-white">
                    <div class="bg-white bg-opacity-20 p-2 rounded">
                        <span class="font-medium">₹1-100:</span> ₹5
                    </div>
                    <div class="bg-white bg-opacity-20 p-2 rounded">
                        <span class="font-medium">₹101-500:</span> ₹10
                    </div>
                    <div class="bg-white bg-opacity-20 p-2 rounded">
                        <span class="font-medium">₹501-1000:</span> ₹25
                    </div>
                    <div class="bg-white bg-opacity-20 p-2 rounded">
                        <span class="font-medium">₹1000+:</span> ₹50
                    </div>
                </div>
            </div>

            <!-- Custom Increments -->
            <div id="customIncrements" class="mb-4 hidden">
                <div class="space-y-2">
                    <input type="number" id="customIncrement" placeholder="Custom increment amount" 
                           class="w-full px-3 py-2 border border-purple-400 rounded-lg focus:ring-2 focus:ring-white focus:border-transparent bg-white bg-opacity-90 text-sm mobile-input mobile-button"
                           min="1" step="1">
                </div>
            </div>

            <!-- Organizer Actions -->
            <div class="flex flex-col sm:flex-row gap-2">
                <button id="startAuctionBtn" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg transition-colors duration-200 text-sm font-medium mobile-button">
                    Start Auction
                </button>
                <button id="pauseAuctionBtn" class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-2 rounded-lg transition-colors duration-200 text-sm font-medium hidden mobile-button">
                    Pause
                </button>
            </div>
        </div>
    </div>

    <!-- Player Bidding Card -->
    <div class="bg-white rounded-lg shadow-lg mb-6">
        <div class="px-3 py-3 sm:px-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900">Player Bidding</h2>
                <div class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium" id="biddingStatus">
                    Waiting
                </div>
            </div>
        </div>
        <div class="p-3 sm:p-6">
            <div id="noBiddingPlayer" class="text-center py-6 sm:py-8 text-gray-500">
                <div class="bg-gray-100 rounded-full w-12 h-12 sm:w-16 sm:h-16 flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <p class="text-base sm:text-lg font-medium">Select a player to start bidding</p>
                <p class="text-xs sm:text-sm text-gray-400 mt-1">Choose from available players list</p>
            </div>

            <div id="biddingCard" class="hidden">
                <!-- Selected Player Info -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-3 sm:p-4 mb-4 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full overflow-hidden bg-white bg-opacity-20 flex items-center justify-center">
                                <img id="selectedPlayerImage" src="{{ asset('images/defaultplayer.jpeg') }}" 
                                     alt="Player" 
                                     class="w-full h-full object-cover"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-full h-full flex items-center justify-center text-white font-bold text-lg sm:text-xl" style="display: none;" id="selectedPlayerInitials">
                                    --
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg sm:text-xl font-bold truncate" id="selectedPlayerName"></h3>
                                <div class="bg-white bg-opacity-20 px-2 py-1 rounded-full text-xs flex-shrink-0 inline-block">
                                    <span id="playerRole">Batsman</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 sm:gap-4 text-xs sm:text-sm">
                        <div>
                            <p class="text-blue-100">Base Price</p>
                            <p class="font-bold text-base sm:text-lg" id="selectedPlayerBasePrice"></p>
                        </div>
                        <div>
                            <p class="text-blue-100">Current Bid</p>
                            <p class="font-bold text-base sm:text-lg" id="currentBidAmount">₹0</p>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-blue-400">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-blue-100">Next Minimum Bid:</span>
                            <span class="font-bold" id="nextMinimumBid">₹0</span>
                        </div>
                    </div>
                    
                    <!-- Last Bid Team Info -->
                    <div class="mt-3 pt-3 border-t border-blue-400" id="lastBidTeamInfo" style="display: none;">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-blue-100">Last Bid By:</span>
                            <div class="flex items-center space-x-2">
                                <div class="bg-white bg-opacity-20 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold" id="lastBidTeamAvatar">
                                    --
                                </div>
                                <span class="font-bold truncate" id="lastBidTeamName">--</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-xs mt-1">
                            <span class="text-blue-100">Team Balance:</span>
                            <span class="font-bold" id="lastBidTeamBalance">₹0</span>
                        </div>
                    </div>
                </div>

                <!-- Hidden Input for Selected Player -->
                <input type="hidden" id="selectedLeaguePlayerId">
                
                <!-- Quick Bid Buttons -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quick Bid Options</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2" id="quickBidButtons">
                        <!-- Quick bid buttons will be generated dynamically -->
                    </div>
                </div>
                
                <!-- Admin Controls -->
                <div class="flex flex-col sm:flex-row gap-2 mt-4 pt-4 border-t border-gray-200">
                    <button id="acceptBidBtn" class="flex-1 bg-blue-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg transition-colors duration-200 text-sm font-medium mobile-button">
                        Mark as Sold
                    </button>
                    <button id="skipPlayerBtn" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors duration-200 text-sm font-medium mobile-button">
                        Skip Player
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bids -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-3 py-3 sm:px-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900">Recent Bids</h2>
                <div class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium" id="bidCount">
                    0 bids
                </div>
            </div>
        </div>
        <div class="p-3 sm:p-6">
            <div id="biddingHistory" class="space-y-3 max-h-60 sm:max-h-80 overflow-y-auto">
                <div class="text-center py-6 sm:py-8 text-gray-500">
                    <div class="bg-gray-100 rounded-full w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <p class="text-sm sm:text-base">No bids yet</p>
                    <p class="text-xs sm:text-sm text-gray-400 mt-1">Bids will appear here</p>
                </div>
            </div>
        </div>
    </div>

    <!-- League Teams List -->
    <div class="bg-white rounded-lg shadow-lg mt-6">
        <div class="px-3 py-3 sm:px-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900">League Teams</h2>
                <div class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                    {{ $leagueTeams->count() }} Teams
                </div>
            </div>
        </div>
        <div class="p-3 sm:p-6">
            <div class="space-y-3 max-h-60 sm:max-h-80 overflow-y-auto">
                @foreach($leagueTeams as $leagueTeam)
                <div class="border border-gray-200 rounded-lg p-3 hover:border-blue-300 transition-colors duration-200 team-card" 
                     data-team-id="{{ $leagueTeam->id }}" 
                     data-wallet="{{ $leagueTeam->wallet_balance }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 min-w-0 flex-1">
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-full w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center text-white font-bold text-xs sm:text-sm flex-shrink-0">
                                {{ strtoupper(substr($leagueTeam->team->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-medium text-gray-900 text-sm sm:text-base truncate">{{ $leagueTeam->team->name }}</h3>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 text-xs sm:text-sm text-gray-600 space-y-1 sm:space-y-0">
                                    <span class="flex items-center">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        ₹{{ number_format($leagueTeam->wallet_balance) }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        {{ $leagueTeam->players()->count() }} Players
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right ml-2">
                            <div class="text-xs sm:text-sm font-medium text-gray-900" id="teamBidLimit_{{ $leagueTeam->id }}">
                                Max Bid: ₹{{ number_format($leagueTeam->wallet_balance) }}
                            </div>
                            <div class="text-xs text-gray-500" id="teamStatus_{{ $leagueTeam->id }}">
                                Available
                            </div>
                        </div>
                    </div>
                    
                    <!-- Team Bidding Status -->
                    <div class="mt-2 pt-2 border-t border-gray-100">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">Last Bid:</span>
                            <span class="font-medium text-gray-900 truncate" id="teamLastBid_{{ $leagueTeam->id }}">None</span>
                        </div>
                        <div class="flex items-center justify-between text-xs mt-1">
                            <span class="text-gray-500">Can Bid Up To:</span>
                            <span class="font-medium text-green-600" id="teamCanBid_{{ $leagueTeam->id }}">₹{{ number_format($leagueTeam->wallet_balance) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs mt-1">
                            <span class="text-gray-500">Next Min Bid:</span>
                            <span class="font-medium text-blue-600" id="teamNextMinBid_{{ $leagueTeam->id }}">₹0</span>
                        </div>
                    </div>
                    
                    <!-- Team Action Button -->
                    <div class="mt-3 pt-2 border-t border-gray-100">
                        <button onclick="selectTeamForBidding('{{ $leagueTeam->id }}', '{{ $leagueTeam->team->name }}')" 
                                class="w-full bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors duration-200 team-select-btn mobile-button"
                                id="teamSelectBtn_{{ $leagueTeam->id }}">
                            Select Team
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
