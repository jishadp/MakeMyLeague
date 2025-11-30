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
                    <span class="text-lg sm:text-xl text-gray-900">₹<span class="basePrice">{{ number_format($currentPlayer->base_price ?? 0) }}</span></span>
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
                    <div class="card__content relative z-10 flex flex-col gap-6 p-5 sm:p-8 lg:p-10">
                        <div class="relative overflow-hidden rounded-3xl border border-white/20 bg-gradient-to-br from-blue-700 via-indigo-700 to-purple-700 text-white shadow-2xl">

                            <div class="pointer-events-none absolute inset-0 opacity-60">
                                <div class="absolute -left-10 -top-16 h-32 w-32 rounded-full bg-white/15 blur-3xl animate-pulse"></div>
                                <div class="absolute right-6 top-8 h-28 w-28 rounded-full bg-indigo-300/40 blur-2xl animate-ping"></div>
                            </div>

                            <div class="absolute top-4 left-4 sm:left-6 z-20">
                                <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide ring-1 ring-white/20">
                                    <span class="h-2 w-2 rounded-full bg-emerald-300 animate-pulse"></span>
                                    @if(isset($currentHighestBid) && $currentHighestBid)
                                        Live
                                    @else
                                        Ready
                                    @endif
                                </span>
                            </div>

                            <div class="relative z-10 grid gap-6 sm:grid-cols-12 items-center p-6 sm:p-8">
                                <div class="sm:col-span-4 flex justify-center">
                                    <div class="relative">
                                        <div class="w-28 h-28 sm:w-32 sm:h-32 lg:w-36 lg:h-36 rounded-3xl overflow-hidden bg-white/10 flex items-center justify-center ring-4 ring-white/40 shadow-2xl">

                                            @php
                                                $playerPhoto = $currentPlayer->player->photo ?? null;
                                                $playerPhotoUrl = $playerPhoto
                                                    ? \Illuminate\Support\Facades\Storage::url($playerPhoto)
                                                    : asset('images/defaultplayer.jpeg');
                                            @endphp

                                            <img src="{{ $playerPhotoUrl }}"
                                                 alt="{{ $currentPlayer->player->name }}"
                                                 class="w-full h-full object-cover rounded-3xl"
                                                 onerror="handleImageError(this);">

                                            <div class="w-full h-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl hidden rounded-3xl">
                                                {{ strtoupper(substr($currentPlayer->player->name, 0, 1)) }}
                                            </div>
                                        </div>

                                        @if($currentPlayer->retention)
                                            <div class="absolute -bottom-3 -right-3 w-9 h-9 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg">
                                                <svg class="w-4.5 h-4.5 text-yellow-800" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="sm:col-span-8 flex flex-col gap-4 text-center items-center w-full">
                                    <p class="text-[11px] sm:text-xs uppercase tracking-[0.25em] text-white/80 font-semibold playerRole position">
                                        @if($currentPlayer->player->primaryGameRole && $currentPlayer->player->primaryGameRole->gamePosition)
                                            {{ $currentPlayer->player->primaryGameRole->gamePosition->name }}
                                        @elseif($currentPlayer->player->position)
                                            {{ $currentPlayer->player->position->name }}
                                        @else
                                            Player
                                        @endif
                                    </p>

                                    <div class="flex flex-wrap items-center justify-center gap-2">
                                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold playerName">{{ $currentPlayer->player->name }}</h2>

                                        @if($currentPlayer->retention)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-yellow-300/20 text-yellow-100 px-3 py-1 text-xs font-semibold border border-yellow-200/50">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                Retained
                                            </span>
                                        @endif
                                    </div>

                                    <div class="w-full text-center flex flex-col items-center gap-2">
                                        <span class="bidStatus inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1 uppercase tracking-[0.25em] text-[11px] font-semibold ring-1 ring-white/15">
                                            @if(isset($currentHighestBid) && $currentHighestBid)
                                                Current Bid
                                            @else
                                                Base Price
                                            @endif
                                        </span>

                                        <span class="inline-flex items-baseline gap-2 text-5xl sm:text-6xl font-black tracking-tight drop-shadow">
                                            <span class="text-white/80">₹</span>
                                            <span class="currentBid">
                                                {{ number_format(isset($currentHighestBid) && $currentHighestBid ? $currentHighestBid->amount : $currentPlayer->base_price) }}
                                            </span>
                                        </span>

                                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-sm font-medium text-white ring-1 ring-white/20 shadow-sm bidTeam">
                                            <span class="h-2 w-2 rounded-full bg-emerald-300 animate-pulse"></span>
                                            @if(isset($currentHighestBid) && $currentHighestBid && $currentHighestBid->leagueTeam && $currentHighestBid->leagueTeam->team)
                                                Leading: {{ $currentHighestBid->leagueTeam->team->name }}
                                            @else
                                                Awaiting new bids..
                                            @endif
                                        </span>
                                    </div>

                                    <div class="flex flex-wrap gap-2 mt-2 justify-center">
                                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white ring-1 ring-white/20 basePrice">
                                            Base: ₹{{ number_format($currentPlayer->base_price ?? 0) }}
                                        </span>

                                        <button
                                            type="button"
                                            id="currentBidRefreshButton"
                                            aria-label="Refresh current bid data"
                                            class="inline-flex items-center justify-center gap-2 rounded-full border border-white/30 bg-white/10 px-4 py-2 text-xs sm:text-sm font-semibold text-white shadow-sm backdrop-blur hover:bg-white/20 transition">
                                            <svg id="currentBidRefreshIcon" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m0 0a8 8 0 111.387 8.457L4 15m.582-6H9"/>
                                            </svg>
                                            <span class="hidden sm:inline">Refresh</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            @else
                <div class="text-gray-500 flex flex-col items-center justify-center gap-3 py-12">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-lg font-semibold">Waiting for next player...</p>
                    <p class="text-sm text-gray-400">Stay tuned, the auctioneer is preparing the next card.</p>
                </div>
            @endif
        </div>
    </div>
</div>
