@php
    $hasAuctionContent = $liveAuctionLeague || $upcomingLeague || $nextFixture;
@endphp

@if($hasAuctionContent)
    <section class="py-16 sm:py-24 bg-slate-950 text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <p class="text-sm font-semibold uppercase tracking-widest text-rose-300">Live Auctions</p>
                <h2 class="text-3xl sm:text-4xl font-bold mt-2">Broadcast-ready auction control for every league</h2>
                <p class="mt-3 text-base text-slate-300">
                    Follow the action in real time, share the public broadcast link, or prep for your next bidding war.
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="bg-slate-900/80 rounded-3xl p-6 border border-white/5 shadow-2xl flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold">Live right now</h3>
                        <span class="text-xs uppercase tracking-widest text-rose-400">Live</span>
                    </div>
                    @if($liveAuctionLeague)
                        <p class="text-2xl font-bold">{{ $liveAuctionLeague->name }}</p>
                        <p class="text-sm text-slate-300 mt-1">{{ $liveAuctionLeague->game->name ?? 'Cricket' }} · {{ $liveAuctionLeague->teams_count }} Teams</p>
                        <div class="mt-6 grid gap-3">
                            <a href="{{ route('auctions.live', $liveAuctionLeague) }}" class="inline-flex items-center justify-center rounded-full bg-rose-500 px-4 py-2 font-semibold hover:bg-rose-400 transition">
                                Open Control Room
                            </a>
                            <a href="{{ route('auctions.live.public', $liveAuctionLeague) }}" class="inline-flex items-center justify-center rounded-full border border-white/20 px-4 py-2 font-semibold text-white hover:bg-white/10 transition">
                                Public Broadcast View
                            </a>
                        </div>
                    @else
                        <p class="text-lg text-slate-300">No auctions are live at this moment.</p>
                        <p class="text-sm text-slate-500 mt-2">When a league goes live, you’ll see the control-room links here.</p>
                    @endif
                </div>

                <div class="bg-slate-900/60 rounded-3xl p-6 border border-white/5 flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold">Next up</h3>
                        <span class="text-xs uppercase tracking-widest text-slate-400">Schedule</span>
                    </div>
                    @if($upcomingLeague)
                        <p class="text-lg font-semibold">{{ $upcomingLeague->name }}</p>
                        <p class="text-sm text-slate-300 mt-1">{{ optional($upcomingLeague->start_date)->format('M d, Y') ?? 'Date TBA' }}</p>
                        <p class="text-sm text-slate-400 mt-4">
                            {{ $upcomingLeague->teams_count }} Teams · {{ $upcomingLeague->game->name ?? 'Cricket' }}
                        </p>
                        <div class="mt-auto pt-6">
                            <a href="{{ route('auctions.index') }}" class="inline-flex items-center text-sm font-semibold text-rose-300 hover:text-rose-200">
                                Review auction settings →
                            </a>
                        </div>
                    @else
                        <p class="text-lg text-slate-300">Plan your next auction with our guided workflows.</p>
                        <div class="mt-auto pt-6">
                            <a href="{{ route('auctions.index') }}" class="inline-flex items-center text-sm font-semibold text-rose-300 hover:text-rose-200">
                                Explore auction planner →
                            </a>
                        </div>
                    @endif
                </div>

                <div class="bg-gradient-to-b from-rose-600/30 to-rose-900/40 rounded-3xl p-6 border border-rose-500/20 flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold">Spotlight</h3>
                        <span class="text-xs uppercase tracking-widest text-rose-100">Match Feed</span>
                    </div>
                    @if($nextFixture)
                        <p class="text-sm uppercase tracking-wider text-rose-200">{{ optional($nextFixture->match_date)->format('M d, Y') ?? 'TBA' }}</p>
                        <p class="text-2xl font-bold mt-2 text-white">
                            {{ $nextFixture->homeTeam?->team?->name ?? 'TBA' }}
                            <span class="text-sm text-rose-200 font-semibold">vs</span>
                            {{ $nextFixture->awayTeam?->team?->name ?? 'TBA' }}
                        </p>
                        <p class="text-sm text-slate-200 mt-3">{{ $nextFixture->league?->name }}</p>
                        <div class="mt-auto pt-6">
                            <a href="{{ route('auctions.index') }}" class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-sm font-semibold text-white hover:bg-white/20 transition">
                                Manage auction assets
                            </a>
                        </div>
                    @else
                        <p class="text-lg text-rose-100">Keep your auction-ready fixtures updated to feature here.</p>
                        <div class="mt-auto pt-6">
                            <a href="{{ route('auctions.index') }}" class="inline-flex items-center text-sm font-semibold text-white/80 hover:text-white">
                                Update fixtures →
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endif
