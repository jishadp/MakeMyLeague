<!-- Highest Bids Section -->
<div class="bg-white rounded-lg shadow-lg">
    <div class="px-4 py-4 sm:px-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Highest Bids</h2>
            <div class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                3 Sold
            </div>
        </div>
    </div>
    <div class="p-4 sm:p-6">
        <div class="space-y-3 max-h-96 overflow-y-auto" id="highestBidsList">
            <!-- Static Sold Players -->
            <!-- Player 1 -->
            <div class="border border-gray-200 rounded-lg p-3 hover:border-purple-300 transition-colors duration-200">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full overflow-hidden bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center">
                            <img src="{{ asset('images/defaultplayer.jpeg') }}" 
                                 alt="MS Dhoni" 
                                 class="w-full h-full object-cover"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-full h-full flex items-center justify-center text-white font-bold text-sm" style="display: none;">
                                MD
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 text-sm">MS Dhoni</h3>
                            <div class="flex items-center space-x-2 text-xs text-gray-600">
                                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs font-medium">
                                    Wicket-keeper
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-purple-600">₹85,000</div>
                        <div class="text-xs text-gray-500">Sold Price</div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between text-xs text-gray-600">
                    <div class="flex items-center space-x-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Chennai Super Kings</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Dec 15, 14:30</span>
                    </div>
                </div>
                
                <div class="mt-2 pt-2 border-t border-gray-100">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500">Base Price:</span>
                        <span class="font-medium text-gray-900">₹50,000</span>
                    </div>
                    <div class="flex items-center justify-between text-xs mt-1">
                        <span class="text-gray-500">Profit:</span>
                        <span class="font-medium text-green-600">₹35,000</span>
                    </div>
                </div>
            </div>

            <!-- Player 2 -->
            <div class="border border-gray-200 rounded-lg p-3 hover:border-purple-300 transition-colors duration-200">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full overflow-hidden bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center">
                            <img src="{{ asset('images/defaultplayer.jpeg') }}" 
                                 alt="Ravindra Jadeja" 
                                 class="w-full h-full object-cover"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-full h-full flex items-center justify-center text-white font-bold text-sm" style="display: none;">
                                RJ
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 text-sm">Ravindra Jadeja</h3>
                            <div class="flex items-center space-x-2 text-xs text-gray-600">
                                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs font-medium">
                                    All-rounder
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-purple-600">₹75,000</div>
                        <div class="text-xs text-gray-500">Sold Price</div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between text-xs text-gray-600">
                    <div class="flex items-center space-x-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Chennai Super Kings</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Dec 15, 13:45</span>
                    </div>
                </div>
                
                <div class="mt-2 pt-2 border-t border-gray-100">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500">Base Price:</span>
                        <span class="font-medium text-gray-900">₹45,000</span>
                    </div>
                    <div class="flex items-center justify-between text-xs mt-1">
                        <span class="text-gray-500">Profit:</span>
                        <span class="font-medium text-green-600">₹30,000</span>
                    </div>
                </div>
            </div>

            <!-- Player 3 -->
            <div class="border border-gray-200 rounded-lg p-3 hover:border-purple-300 transition-colors duration-200">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full overflow-hidden bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center">
                            <img src="{{ asset('images/defaultplayer.jpeg') }}" 
                                 alt="Bhuvneshwar Kumar" 
                                 class="w-full h-full object-cover"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-full h-full flex items-center justify-center text-white font-bold text-sm" style="display: none;">
                                BK
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 text-sm">Bhuvneshwar Kumar</h3>
                            <div class="flex items-center space-x-2 text-xs text-gray-600">
                                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs font-medium">
                                    Bowler
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-purple-600">₹70,000</div>
                        <div class="text-xs text-gray-500">Sold Price</div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between text-xs text-gray-600">
                    <div class="flex items-center space-x-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Sunrisers Hyderabad</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Dec 15, 12:15</span>
                    </div>
                </div>
                
                <div class="mt-2 pt-2 border-t border-gray-100">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500">Base Price:</span>
                        <span class="font-medium text-gray-900">₹40,000</span>
                    </div>
                    <div class="flex items-center justify-between text-xs mt-1">
                        <span class="text-gray-500">Profit:</span>
                        <span class="font-medium text-green-600">₹30,000</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
