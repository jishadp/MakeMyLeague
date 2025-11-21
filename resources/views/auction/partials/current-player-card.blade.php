<div class="space-y-4 lg:col-span-7 xl:col-span-8">
    <div class="bg-white/90 backdrop-blur rounded-3xl shadow-xl ring-1 ring-gray-100 p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Current Player</h2>
                <p class="text-sm text-gray-500">Live roster updates refresh in real-time</p>
            </div>
            @if(isset($currentPlayer) && $currentPlayer)
                <div class="inline-flex items-center justify-between gap-3 bg-indigo-50 border border-indigo-100 text-indigo-700 font-semibold rounded-2xl px-4 py-2 shadow-sm">
                    <span class="text-xs uppercase tracking-wide text-indigo-500">Base Price</span>
                    <span class="text-lg sm:text-xl text-gray-900">₹<span class="basePrice">{{ $currentPlayer->base_price ?? 0 }}</span></span>
                </div>
            @endif
        </div>
        <div id="currentPlayerCard" class="text-center py-6 sm:py-8" tabindex="-1">
            @if(isset($currentPlayer) && $currentPlayer)
                @php
                    $leadingBudget = null;
                    if ($currentHighestBid) {
                        $leadingBudget = $teams->firstWhere('id', $currentHighestBid->league_team_id);
                    }
                @endphp
                <!-- Glassmorphism Card Style -->
                <div class="card-container relative rounded-3xl overflow-hidden shadow-2xl">
                    <!-- Animated Background Blobs -->
                    <div class="absolute inset-0 overflow-hidden rounded-3xl">
                        <div class="blob absolute -left-12 -top-24 w-32 h-32 sm:w-48 sm:h-48 bg-orange-400 rounded-full opacity-60 animate-blob"></div>
                        <div class="blob absolute right-8 sm:right-24 -top-6 w-32 h-32 sm:w-48 sm:h-48 bg-purple-500 rounded-full opacity-60 animate-blob" style="animation-delay: 1s;"></div>
                        <div class="blob absolute -left-10 top-32 sm:top-24 w-32 h-32 sm:w-48 sm:h-48 bg-pink-500 rounded-full opacity-60 animate-blob" style="animation-delay: 2s;"></div>
                        <div class="blob absolute right-8 sm:right-24 bottom-8 sm:top-44 w-32 h-32 sm:w-48 sm:h-48 bg-blue-500 rounded-full opacity-60 animate-blob" style="animation-delay: 3s;"></div>
                    </div>

                    <!-- Glassmorphism Content -->
                    <div class="card__content relative z-10 flex flex-col gap-8 p-6 sm:p-8 lg:p-10">
                        <!-- Player Header -->
                        <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:gap-8">
                            <div class="relative flex-shrink-0 mx-auto sm:mx-0">
                                <div class="w-24 h-24 sm:w-28 sm:h-28 lg:w-32 lg:h-32 rounded-3xl overflow-hidden bg-white bg-opacity-20 flex items-center justify-center ring-4 ring-blue-200 ring-opacity-50 shadow-2xl">
                                    @if($currentPlayer->player && $currentPlayer->player->photo)
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
                                        {{ strtoupper(substr($currentPlayer->player->name, 0, 1)) }}
                                    </div>
                                </div>
                                @if($currentPlayer->retention)
                                    <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg" title="Retained player" aria-hidden="true">
                                        <svg class="w-4 h-4 text-yellow-800" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="text-center sm:text-left flex-grow w-full">
                                <p class="text-xs sm:text-sm uppercase tracking-wide text-indigo-500 font-semibold mb-2 playerRole position">
                                    @if($currentPlayer->player->primaryGameRole && $currentPlayer->player->primaryGameRole->gamePosition)
                                        {{ $currentPlayer->player->primaryGameRole->gamePosition->name }}
                                    @elseif($currentPlayer->player->position)
                                        {{ $currentPlayer->player->position->name }}
                                    @else
                                        Player
                                    @endif
                                </p>
                                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-4 text-gray-800 playerName">
                                    {{ $currentPlayer->player->name }}
                                </h2>
                                @if($currentPlayer->retention)
                                    <div class="flex flex-wrap gap-2 justify-center sm:justify-start mb-4">
                                        <span class="bg-yellow-500 bg-opacity-10 backdrop-blur-sm px-4 py-2 rounded-2xl text-sm font-semibold text-yellow-700 border border-yellow-300 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            Retained Player
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Current Bid Row -->
                        <div class="stat-card bg-white bg-opacity-80 backdrop-blur-sm rounded-2xl p-4 sm:p-6 border border-blue-200 shadow-lg">
                            <!-- Current Bid (Main Highlight) -->
                            <div class="relative overflow-hidden rounded-2xl border border-blue-100 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white shadow-lg shadow-blue-100">
                                <div class="pointer-events-none absolute inset-0 opacity-50">
                                    <div class="absolute -left-14 -top-16 h-32 w-32 rounded-full bg-white/20 blur-3xl animate-pulse"></div>
                                    <div class="absolute right-4 top-10 h-28 w-28 rounded-full bg-indigo-300/50 blur-2xl animate-ping"></div>
                                </div>
                                <div class="relative z-10 flex flex-col gap-4 p-5 sm:p-6 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex-1 text-center sm:text-left">
                                        <p class="bidStatus text-[11px] sm:text-xs uppercase tracking-[0.28em] text-white/80 font-semibold">
                                            @if(isset($currentHighestBid) && $currentHighestBid)
                                                Current Bid
                                            @else
                                                Base Price
                                            @endif
                                        </p>
                                        <div class="mt-2 flex items-baseline justify-center gap-3 sm:justify-start">
                                            <span class="text-lg font-semibold text-white/80">₹</span>
                                            <span class="currentBid inline-block text-4xl sm:text-5xl font-black tracking-tight drop-shadow">
                                                {{ isset($currentHighestBid) && $currentHighestBid ? $currentHighestBid->amount : $currentPlayer->base_price }}
                                            </span>
                                            <span class="rounded-full bg-white/15 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide ring-1 ring-white/25 animate-pulse">
                                                @if(isset($currentHighestBid) && $currentHighestBid)
                                                    Live
                                                @else
                                                    Ready
                                                @endif
                                            </span>
                                        </div>
                                        <div class="mt-3 flex items-center justify-center sm:justify-start">
                                            <span class="bidTeam inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-sm font-medium text-white ring-1 ring-white/20 shadow-sm">
                                                <span class="h-2 w-2 rounded-full bg-emerald-300 animate-pulse"></span>
                                                @if(isset($currentHighestBid) && $currentHighestBid && $currentHighestBid->leagueTeam && $currentHighestBid->leagueTeam->team)
                                                    Leading: {{ $currentHighestBid->leagueTeam->team->name }}
                                                @else
                                                    Awaiting new bids..
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex justify-center sm:justify-end">
                                        <button
                                            type="button"
                                            id="currentBidRefreshButton"
                                            aria-label="Refresh current bid data"
                                            class="inline-flex items-center justify-center gap-2 rounded-full border border-white/30 bg-white/10 px-4 py-2 text-xs sm:text-sm font-semibold text-white shadow-sm backdrop-blur focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white hover:bg-white/20 transition">
                                            <svg id="currentBidRefreshIcon" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m0 0a8 8 0 111.387 8.457L4 15m.582-6H9"/>
                                            </svg>
                                            <span class="hidden sm:inline">Refresh</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @if($leadingBudget)
                                <div class="mt-5 space-y-4 sm:space-y-3">
                                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                                            <span class="inline-flex h-2 w-2">
                                                <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                            </span>
                                            Team Balance
                                        </div>
                                        <span class="text-xs text-gray-500">
                                            Leading: {{ $currentHighestBid?->leagueTeam?->team?->name ?? 'Team TBD' }}
                                        </span>
                                    </div>
                                    <div class="budget-metrics grid grid-cols-3 gap-2 sm:gap-4 text-[11px] sm:text-sm text-gray-700">
                                        <div class="mobile-budget-card group flex flex-col items-center gap-1 rounded-xl border border-gray-100 bg-white/90 px-2.5 py-2.5 text-center shadow-sm transition hover:-translate-y-1 hover:shadow-md sm:px-4 sm:py-4">
                                            <p class="uppercase tracking-wide text-gray-500 text-[10px] sm:text-xs">Players Needed</p>
                                            <p class="font-semibold text-gray-900 text-sm sm:text-lg">{{ $leadingBudget->players_needed }}</p>
                                            <span class="text-[10px] sm:text-[11px] text-gray-500">Open spots</span>
                                        </div>
                                        <div class="mobile-budget-card group flex flex-col items-center gap-1 rounded-xl border border-gray-100 bg-white/90 px-2.5 py-2.5 text-center shadow-sm transition hover:-translate-y-1 hover:shadow-md sm:px-4 sm:py-4">
                                            <p class="uppercase tracking-wide text-gray-500 text-[10px] sm:text-xs">Reserve Funds</p>
                                            <p class="font-semibold text-gray-900 text-sm sm:text-lg">₹{{ number_format($leadingBudget->reserve_amount) }}</p>
                                            <span class="text-[10px] sm:text-[11px] text-gray-500">Held aside</span>
                                        </div>
                                        <div class="mobile-budget-card group flex flex-col items-center gap-1 rounded-xl border border-gray-100 bg-white/90 px-2.5 py-2.5 text-center shadow-sm transition hover:-translate-y-1 hover:shadow-md sm:px-4 sm:py-4">
                                            <p class="uppercase tracking-wide text-gray-500 text-[10px] sm:text-xs">Max Bid</p>
                                            <p class="font-semibold text-emerald-600 text-sm sm:text-lg">₹{{ number_format($leadingBudget->max_bid_cap) }}</p>
                                            <span class="text-[10px] sm:text-[11px] text-gray-500">For this player</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p class="text-xs text-gray-400 text-center mt-4">Bid to unlock team budget info for this player.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="text-gray-500 flex flex-col items-center justify-center gap-3 py-12">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-lg font-semibold">Waiting for next player...</p>
                    <p class="text-sm text-gray-400">Stay tuned, the auctioneer is preparing the next card.</p>
                </div>
            @endif
        </div>
    </div>
</div>
