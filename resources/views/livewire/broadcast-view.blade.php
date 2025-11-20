@once
    <style>
        .broadcast-bg {
            background: radial-gradient(circle at top, rgba(56, 189, 248, 0.25), transparent 45%), #020617;
            min-height: calc(100vh - 6rem);
        }

        .broadcast-panel {
            background: rgba(15, 23, 42, 0.75);
            border: 1px solid rgba(148, 163, 184, 0.25);
            border-radius: 2rem;
            box-shadow: 0 25px 50px rgba(2, 6, 23, 0.6);
        }

        .live-dot {
            animation: pulseGlow 1.5s ease-in-out infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { opacity: 0.4; transform: scale(0.8); }
            50% { opacity: 1; transform: scale(1.1); }
        }

        .team-card {
            background: rgba(15, 23, 42, 0.55);
            border: 1px solid rgba(148, 163, 184, 0.3);
            border-radius: 1.5rem;
            padding: 1.25rem;
            transition: transform 0.3s ease, border-color 0.3s ease;
        }

        .sale-overlay {
            position: absolute;
            inset: 0;
            background: rgba(2, 6, 23, 0.88);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 2rem;
        }
    </style>
@endonce

@php
    $leagueModel = $payload['league'];
    $currentPlayer = $payload['currentPlayer'];
    $currentHighestBid = $payload['currentHighestBid'];
    $teams = $payload['teams'];
    $liveViewers = $payload['liveViewers'];
    $lastSoldPlayer = $payload['lastSoldPlayer'];
    $recentBids = $payload['recentBids'];

    $isShowingLastSold = !$currentPlayer && $lastSoldPlayer;
    $displayLeaguePlayer = $currentPlayer ?? $lastSoldPlayer;
    $displayPlayer = $displayLeaguePlayer?->player;
    $playerPhoto = $displayPlayer && $displayPlayer->photo ? asset('storage/' . $displayPlayer->photo) : asset('images/defaultplayer.jpeg');
    $playerInitial = $displayPlayer ? strtoupper(substr($displayPlayer->name, 0, 1)) : '?';
    $playerRole = $displayPlayer?->primaryGameRole?->gamePosition?->name
        ?? $displayPlayer?->position?->name
        ?? 'Awaiting player';
    $basePrice = $displayLeaguePlayer?->base_price ?? null;
    $lastSoldTeamName = $lastSoldPlayer?->leagueTeam?->team?->name;
    $currentBidAmount = $currentHighestBid?->amount ?? null;
    $soldAmount = $lastSoldPlayer?->bid_price;
    $highestAmount = $currentPlayer ? ($currentBidAmount ?? $basePrice) : ($soldAmount ?? null);
    $highestTeamName = $currentPlayer
        ? ($currentHighestBid && $currentHighestBid->leagueTeam && $currentHighestBid->leagueTeam->team
            ? $currentHighestBid->leagueTeam->team->name
            : 'Awaiting bids')
        : ($lastSoldTeamName ? 'Sold to ' . $lastSoldTeamName : 'Awaiting next player');
    $bidLabel = $currentPlayer
        ? ($currentHighestBid ? 'Current Bid' : 'Base Price')
        : ($lastSoldPlayer ? 'Sold Price' : 'Status');
    $spotlightTeam = null;
    if ($currentHighestBid && $currentHighestBid->leagueTeam) {
        $spotlightTeam = $teams->firstWhere('id', $currentHighestBid->leagueTeam->id);
    } elseif ($isShowingLastSold && $lastSoldPlayer?->leagueTeam) {
        $spotlightTeam = $teams->firstWhere('id', $lastSoldPlayer->league_team_id);
    }
    $playerStatusText = $currentPlayer
        ? ($currentPlayer->retention ? 'Retained' : 'Auctioning')
        : ($lastSoldPlayer ? ($lastSoldTeamName ? 'Sold to ' . $lastSoldTeamName : 'Sold') : 'Waiting for player');
@endphp

<div wire:poll.1s="refreshData">
<section class="broadcast-bg py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white space-y-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="flex items-center gap-3 text-emerald-300 text-sm uppercase tracking-[0.25em]">
                    <span class="w-3 h-3 bg-red-500 rounded-full live-dot shadow-lg"></span>
                    LIVE AUCTION
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold mt-2">{{ $leagueModel->name }}</h1>
                <p class="text-slate-300 text-lg">{{ $leagueModel->game->name }} League</p>
            </div>
            <div class="flex flex-wrap gap-6 text-sm items-center">
                <div>
                    <p class="text-slate-400 uppercase text-xs tracking-wide">Viewers</p>
                    <p class="text-3xl font-semibold">{{ $liveViewers }}</p>
                </div>
                <div>
                    <p class="text-slate-400 uppercase text-xs tracking-wide">Last Update</p>
                    <p class="text-3xl font-semibold">{{ $lastUpdated ?? now()->format('H:i:s') }}</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <button type="button" wire:click="refreshData"
                        class="px-4 py-2 rounded-full bg-slate-100 text-slate-900 font-semibold shadow-lg hover:bg-white transition">
                        Refresh Data
                    </button>
                    <button type="button"
                        onclick="window.location.reload()"
                        class="px-4 py-2 rounded-full border border-slate-500/60 text-slate-200 font-semibold hover:border-white transition">
                        Reload Page
                    </button>
                </div>
            </div>
        </div>

        <div class="grid gap-8 xl:grid-cols-2">
            <section class="relative broadcast-panel p-6 sm:p-8">
                @if($currentPlayer === null && $lastSoldPlayer)
                    <div class="sale-overlay visible">
                        <img src="{{ asset('images/auction/sold.png') }}" alt="Sold animation">
                    </div>
                @endif

                <div class="flex flex-col gap-8 lg:flex-row">
                    <div class="lg:w-1/2">
                        <div class="relative overflow-hidden rounded-3xl border border-slate-700/70 bg-black/30 shadow-2xl">
                            <img src="{{ $playerPhoto }}" alt="{{ $displayPlayer->name ?? 'Awaiting player' }}"
                                class="w-full h-[22rem] object-cover {{ $currentPlayer ? '' : 'opacity-70' }}">
                            <div class="absolute inset-0 flex items-center justify-center text-6xl font-bold text-white/40"
                                @if($displayPlayer) style="display:none" @endif>
                                {{ $playerInitial }}
                            </div>
                        </div>
                    </div>

                    <div class="lg:w-1/2 space-y-6">
                        <div>
                            <p class="text-slate-400 text-sm tracking-wide uppercase">{{ $isShowingLastSold ? 'Last Sold Player' : 'Current Player' }}</p>
                            <h2 class="text-3xl font-bold mt-1">{{ $displayPlayer->name ?? 'Waiting for next player' }}</h2>
                            <p class="text-lg text-slate-300">{{ $playerRole }}</p>
                            @if($isShowingLastSold)
                                <p class="text-sm text-slate-400 mt-2">Waiting for the next player to enter the auction floor.</p>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="bg-slate-800/70 rounded-2xl p-4">
                                <p class="text-slate-400 uppercase text-xs tracking-wide">Base Price</p>
                                <p class="text-2xl font-semibold mt-1">
                                    {{ $basePrice !== null ? 'Rs ' . number_format($basePrice) : '—' }}
                                </p>
                            </div>
                            <div class="bg-slate-800/70 rounded-2xl p-4">
                                <p class="text-slate-400 uppercase text-xs tracking-wide">Status</p>
                                <p class="text-2xl font-semibold mt-1">{{ $playerStatusText }}</p>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-emerald-500/10 to-cyan-500/10 border border-emerald-400/30 rounded-3xl p-6 space-y-2">
                            <p class="text-sm text-emerald-300 uppercase tracking-wide">{{ strtoupper($bidLabel) }}</p>
                            <p class="text-4xl font-bold text-white">{{ $highestAmount ? 'Rs ' . number_format($highestAmount) : '—' }}</p>
                            <p class="text-slate-200 text-lg">{{ $highestTeamName }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <div class="space-y-8">
                <section class="broadcast-panel p-6 sm:p-7">
                    <div class="flex items-center justify-between gap-6">
                        <div>
                            <p class="text-slate-400 uppercase text-xs tracking-[0.35em]">
                                {{ $spotlightTeam ? ($currentPlayer ? 'Leading Bidder' : 'Last Winner') : 'Winner pending' }}
                            </p>
                            <h3 class="text-3xl font-bold mt-2">
                                {{ $spotlightTeam->team->name ?? 'Top bid will appear here' }}
                            </h3>
                            <p class="text-slate-300">
                                @if($spotlightTeam)
                                    {{ $currentPlayer ? 'Holding the floor with Rs ' . number_format($highestAmount) : 'Secured the last player for Rs ' . number_format($soldAmount ?? 0) }}
                                @else
                                    As soon as a team wins, details will be shown
                                @endif
                            </p>
                        </div>
                        <div class="rounded-2xl border border-emerald-400/40 px-5 py-3 text-center">
                            <p class="text-xs uppercase text-emerald-200 tracking-wide">
                                {{ $currentPlayer ? 'Current Bid' : 'Sold Price' }}
                            </p>
                            <p class="text-2xl font-semibold">
                                {{ $highestAmount ? 'Rs ' . number_format($highestAmount) : '—' }}
                            </p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-6 text-sm text-slate-200">
                        <div>
                            <p class="text-slate-400 text-xs uppercase">Wallet</p>
                            <p class="text-xl font-semibold">{{ $spotlightTeam ? 'Rs ' . number_format($spotlightTeam->display_wallet ?? $spotlightTeam->wallet_balance ?? 0) : '—' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs uppercase">Players Signed</p>
                            <p class="text-xl font-semibold">{{ $spotlightTeam ? $spotlightTeam->leaguePlayers->count() : '—' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs uppercase">Players Needed</p>
                            <p class="text-xl font-semibold">{{ $spotlightTeam ? $spotlightTeam->players_needed : '—' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs uppercase">Max Bid Cap</p>
                            <p class="text-xl font-semibold">{{ $spotlightTeam ? 'Rs ' . number_format($spotlightTeam->max_bid_cap ?? 0) : '—' }}</p>
                        </div>
                    </div>
                </section>

                <section class="broadcast-panel p-6 sm:p-7">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold">Recent Bids</h3>
                        <div class="flex items-center gap-3 text-slate-400 text-sm">
                            <span class="hidden sm:inline">Auto-updating</span>
                            <button type="button"
                                    wire:click="toggleRecentBids"
                                    class="inline-flex items-center gap-1 rounded-full border border-slate-600 px-3 py-1.5 text-xs font-semibold uppercase tracking-wide transition hover:border-emerald-400 hover:text-white">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($recentBidsCollapsed)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 5v14m-7-7h14" />
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14" />
                                    @endif
                                </svg>
                                {{ $recentBidsCollapsed ? 'Show' : 'Hide' }}
                            </button>
                        </div>
                    </div>

                    @if(!$recentBidsCollapsed)
                        <div class="mt-4 space-y-3 text-sm">
                            @forelse($recentBids as $bid)
                                <div class="rounded-2xl border border-slate-700/80 bg-slate-900/30 p-4 flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-base font-semibold">{{ $bid->leagueTeam->team->name ?? 'Team' }}</p>
                                        <p class="text-xs uppercase tracking-wide text-slate-400">{{ $bid->created_at->timezone(config('app.timezone'))->format('h:i:s A') }}</p>
                                    </div>
                                    <div class="text-2xl font-bold text-emerald-300">Rs {{ number_format($bid->amount) }}</div>
                                </div>
                            @empty
                                <p class="text-slate-400">Waiting for live bids...</p>
                            @endforelse
                        </div>
                    @endif
                </section>
            </div>
        </div>

        <section class="broadcast-panel p-6 sm:p-8">
            <div class="flex items-center justify-between gap-4">
                <h3 class="text-2xl font-semibold">League Teams</h3>
                <p class="text-sm text-slate-400">Wallets and roster progress update live</p>
            </div>
            <div class="grid gap-4 mt-6 md:grid-cols-2">
                @foreach($teams as $team)
                    <div class="team-card" wire:key="team-{{ $team->id }}">
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-xl font-semibold">{{ $team->team->name }}</p>
                            <span class="text-xs uppercase tracking-wide text-slate-400">Squad</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-4 text-sm text-slate-300">
                            <div>
                                <p class="text-slate-400 text-xs uppercase">Wallet</p>
                                <p class="text-xl font-semibold">Rs {{ number_format($team->display_wallet ?? $team->wallet_balance ?? 0) }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-xs uppercase">Players</p>
                                <p class="text-xl font-semibold">{{ $team->leaguePlayers->count() }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-xs uppercase">Needed</p>
                                <p class="text-xl font-semibold">{{ $team->players_needed }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-xs uppercase">Retained</p>
                                <p class="text-xl font-semibold">{{ $team->retained_players_count }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-xs uppercase">Max Bid Cap</p>
                                <p class="text-xl font-semibold">Rs {{ number_format($team->max_bid_cap ?? 0) }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-xs uppercase">Reserve</p>
                                <p class="text-xl font-semibold">Rs {{ number_format($team->reserve_amount ?? 0) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</section>
</div>

@once
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
@endonce

<script>
    document.addEventListener('livewire:init', () => {
        const componentId = '{{ $this->id() }}';
        const leagueId = {{ $leagueModel->id }};

        window.__broadcastPusherSetup = window.__broadcastPusherSetup || {};
        if (window.__broadcastPusherSetup[componentId]) {
            return;
        }
        window.__broadcastPusherSetup[componentId] = true;

        const ensureComponent = (callback) => {
            const component = window.Livewire.find(componentId);
            if (component) {
                callback(component);
                return;
            }
            setTimeout(() => ensureComponent(callback), 150);
        };

        ensureComponent(() => {
            if (!window.Pusher) {
                console.warn('Pusher not found');
                return;
            }

            const pusher = new Pusher('464d2f6144ab8dafa4df', {
                cluster: 'ap2',
                forceTLS: true,
            });

            const channels = [
                pusher.subscribe('auctions'),
                pusher.subscribe(`auctions.league.${leagueId}`),
            ];

            const refreshComponent = () => {
                const component = window.Livewire.find(componentId);
                if (component) {
                    component.call('refreshData');
                }
            };

            ['player-sold', 'player-unsold', 'new-player-started', 'new-player-bid-call'].forEach(eventName => {
                channels.forEach(channel => {
                    channel.bind(eventName, refreshComponent);
                });
            });
        });
    });
</script>
