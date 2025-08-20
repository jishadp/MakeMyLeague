<!-- Bidding Section -->
<div class="w-full" id="biddingSection">

    <!-- Player Bidding Card -->
    <div class="glacier-card">
        <div class="px-3 py-3 sm:px-6 border-b glacier-border">
            <div class="flex items-center justify-between">
                <h2 class="text-base sm:text-lg font-semibold glacier-text-primary">Player Bidding</h2>
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
                <div class="glacier-gradient rounded-lg p-3 sm:p-4 mb-4 text-white">
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
                        <div class="flex items-center justify-between text-xs mt-1">
                            <span class="text-blue-100">Max Bid Balance:</span>
                            <span class="font-bold" id="lastBidTeamBalance">₹0</span>
                        </div>
                    </div>
                </div>

                <!-- Hidden Input for Selected Player -->
                <input type="hidden" id="selectedLeaguePlayerId">
                
                
                <!-- Quick Bid Buttons -->
                <div class="mb-4">
                    <div class="grid grid-cols-3 gap-3" id="quickBidButtons">
                        <button onclick="placeQuickBid(210)" class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg text-sm font-medium transition-colors duration-200 mobile-button flex flex-col items-center justify-center ">
                            <span class="font-bold">Bid</span>
                            <span class="text-xs opacity-90">₹210</span>
                            
                        </button>
                    
                        <button onclick="placeQuickBid(1000)" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-3 rounded-lg text-sm font-medium transition-colors duration-200 mobile-button flex flex-col items-center justify-center ">
                            <span class="font-bold">Bid</span>
                            <span class="text-xs opacity-90">₹1,000</span>
                            
                        </button>
                    
                        <button onclick="placeQuickBid(2000)" class="bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg text-sm font-medium transition-colors duration-200 mobile-button flex flex-col items-center justify-center ">
                            <span class="font-bold">Bid</span>
                            <span class="text-xs opacity-90">₹2,000</span>
                            
                        </button>
                    </div>
                </div>
                
                <!-- Admin Controls -->
                 @if(auth()->user()->isOrganizer())
                <div class="flex flex-col sm:flex-row gap-2 mt-4 pt-4 border-t glacier-border">
                    <button id="acceptBidBtn" class="flex-1 bg-blue-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg transition-colors duration-200 text-sm font-medium mobile-button">
                        Mark as Sold
                    </button>
                    <button id="skipPlayerBtn" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors duration-200 text-sm font-medium mobile-button">
                        Skip Player
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
