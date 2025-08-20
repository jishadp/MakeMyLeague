<!-- Player Bidding Section -->
<div class="w-full max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    
    <!-- Player Card -->
    <div class="card-container relative">
        <div class="card bg-gray-100 rounded-2xl shadow-xl border border-gray-300 overflow-hidden backdrop-blur-sm">
            <!-- Header -->
            <div class="px-4 py-4 sm:px-6 sm:py-5 border-b border-gray-300 bg-gradient-to-r from-gray-50 to-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h2 class="text-lg sm:text-xl font-bold text-gray-800">Player Bidding</h2>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="bg-red-500 text-white px-3 py-1.5 rounded-full text-xs sm:text-sm font-semibold shadow-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            90s
                        </div>
                        <div class="bg-gradient-to-r from-emerald-500 to-green-500 text-white px-3 py-1.5 rounded-full text-xs sm:text-sm font-semibold shadow-sm">
                            Active
                        </div>
                    </div>
                </div>
            </div>

            <!-- Player Information -->
            <div class="p-4 sm:p-6">
                <!-- Player Card -->
                <div class="bg-gradient-to-br from-gray-200 via-gray-300 to-gray-400 rounded-2xl p-4 sm:p-6 text-gray-800 relative overflow-hidden border border-gray-400">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white rounded-full -translate-y-16 translate-x-16"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white rounded-full translate-y-12 -translate-x-12"></div>
                    </div>
                    
                    <div class="relative z-10">
                        <!-- Player Header -->
                        <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6 mb-6">
                            <div class="relative">
                                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full overflow-hidden bg-white bg-opacity-20 flex items-center justify-center ring-4 ring-white ring-opacity-30 shadow-xl">
                                    <img src="{{ asset('images/defaultplayer.jpeg') }}" 
                                         alt="Player" 
                                         class="w-full h-full object-cover">
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-yellow-400 rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-yellow-800" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-center sm:text-left">
                                <h3 class="text-xl sm:text-2xl font-bold mb-2">{{ $player->name ?? 'Player Name' }}</h3>
                                <div class="bg-gray-600 bg-opacity-20 backdrop-blur-sm px-3 py-1.5 rounded-full text-sm font-medium inline-block text-gray-700">
                                    {{ $player->role ?? 'Batsman' }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Player Stats Grid -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-white bg-opacity-30 backdrop-blur-sm rounded-xl p-3 text-center border border-gray-400">
                                <p class="text-gray-600 text-xs font-medium mb-1">Base Price</p>
                                <p class="font-bold text-lg sm:text-xl text-gray-800">₹{{ $player->base_price ?? '1000' }}</p>
                            </div>
                            <div class="bg-white bg-opacity-30 backdrop-blur-sm rounded-xl p-3 text-center border border-gray-400">
                                <p class="text-gray-600 text-xs font-medium mb-1">Current Bid</p>
                                <p class="font-bold text-lg sm:text-xl text-blue-600">₹1111</p>
                            </div>
                        </div>
                        
                        <!-- Last Bid Team Card -->
                        <div class="bg-white bg-opacity-30 backdrop-blur-sm rounded-xl p-3 text-center border border-gray-400 mb-6">
                            <p class="text-gray-600 text-xs font-medium mb-1">Last Bid By</p>
                            <p class="font-bold text-lg sm:text-xl text-gray-800">Team XYZ</p>
                        </div>
                    </div>
                </div>

                <!-- Bidding Panel -->
                <div class="mt-6 space-y-6">
                    <!-- Quick Bid Options -->
                    <div class="bg-gray-100 rounded-2xl shadow-xl border border-gray-300 p-4 sm:p-6">
                        <div class="grid grid-cols-3 gap-3 sm:gap-4">
                            <button class="group relative overflow-hidden bg-gradient-to-br from-gray-400 to-gray-500 hover:from-gray-300 hover:to-gray-400 text-gray-800 px-3 py-4 sm:px-4 sm:py-5 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 hover:shadow-xl flex items-center justify-center shadow-md border border-gray-500 before:content-['BID'] before:absolute before:inset-0 before:flex before:items-center before:justify-center before:text-gray-400 before:text-6xl before:font-extrabold before:tracking-widest before:pointer-events-none">
                                <span class="text-xl sm:text-2xl font-bold opacity-100 z-10">₹500</span>
                            </button>
                            <button class="group relative overflow-hidden bg-gradient-to-br from-green-500 to-green-600 hover:from-green-400 hover:to-green-500 text-white px-3 py-4 sm:px-4 sm:py-5 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 hover:shadow-xl flex items-center justify-center shadow-md border border-green-500 before:content-['BID'] before:absolute before:inset-0 before:flex before:items-center before:justify-center before:text-white/10 before:text-6xl before:font-extrabold before:tracking-widest before:pointer-events-none">
                                <span class="text-xl sm:text-2xl font-bold opacity-100 z-10">₹1,000</span>
                            </button>
                            <button class="group relative overflow-hidden bg-gradient-to-br from-red-500 to-red-600 hover:from-red-400 hover:to-red-500 text-white px-3 py-4 sm:px-4 sm:py-5 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 hover:shadow-xl flex items-center justify-center shadow-md border border-red-500 before:content-['BID'] before:absolute before:inset-0 before:flex before:items-center before:justify-center before:text-white/10 before:text-6xl before:font-extrabold before:tracking-widest before:pointer-events-none">
                                <span class="text-xl sm:text-2xl font-bold opacity-100 z-10">₹2,000</span>
                            </button>
                        </div>
                </div>

                    <!-- Admin Controls -->
                    <div class="bg-gray-100 rounded-2xl shadow-xl border border-gray-300 p-4 sm:p-6">
                        
                        <div class="grid grid-cols-2 gap-3 sm:gap-4">
                            <button class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-500 hover:to-green-600 text-white px-4 py-4 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 hover:shadow-xl flex items-center justify-center shadow-md border border-green-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Sold
                            </button>
                            <button class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white px-4 py-4 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 hover:shadow-xl flex items-center justify-center shadow-md border border-red-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Unsold
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Last Bid Info (Below Card) -->
    <div class="mt-4 bg-gray-200 rounded-xl p-4 border border-gray-400">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div class="flex items-center justify-between">
                <span class="text-gray-600 font-medium">Last Bid By:</span>
                <span class="text-gray-800 font-bold">Team XYZ</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-600 font-medium">Team Balance:</span>
                <span class="text-green-600 font-bold">₹25,000</span>
            </div>
        </div>
    </div>
</div>

<style>
/* From Uiverse.io by gharsh11032000 - Adapted for our card */
.card-container {
    position: relative;
    width: 100%;
    max-width: 4xl;
}

.card {
    position: relative;
    width: 100%;
    background-color: #f3f4f6;
    display: flex;
    flex-direction: column;
    justify-content: start;
    padding: 0;
    gap: 0;
    border-radius: 16px;
    cursor: pointer;
    transition: all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.card::before {
    content: '';
    position: absolute;
    inset: 0;
    left: -5px;
    margin: auto;
    width: calc(100% + 10px);
    height: calc(100% + 10px);
    border-radius: 20px;
    background: linear-gradient(-45deg, #0ea5e9 0%, #166534 100%);
    z-index: -10;
    pointer-events: none;
    transition: all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.card::after {
    content: "";
    z-index: -1;
    position: absolute;
    inset: 0;
    background: linear-gradient(-45deg, #0284c7 0%, #15803d 100%);
    transform: translate3d(0, 0, 0) scale(0.95);
    filter: blur(20px);
    border-radius: 16px;
}

.card:hover::after {
    filter: blur(30px);
}

.card:hover::before {
    transform: scale(1.02);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
}

/* Enhanced button animations */
.group:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

/* Smooth transitions for all interactive elements */
* {
    transition: all 0.3s ease;
}
</style>