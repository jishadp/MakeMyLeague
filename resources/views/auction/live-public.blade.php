@extends('layouts.app')

@section('title', 'Live Broadcast - ' . $league->name)

@section('styles')
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

    .sale-overlay {
        position: absolute;
        inset: 0;
        background: rgba(2, 6, 23, 0.88);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.35s ease;
        border-radius: 2rem;
    }

    .sale-overlay img {
        max-width: 70%;
        filter: drop-shadow(0 20px 35px rgba(0, 0, 0, 0.6));
        transform: scale(0.9);
        opacity: 0;
    }

    .sale-overlay.visible {
        opacity: 1;
    }

    .sale-overlay.visible img {
        animation: overlayPop 0.65s ease forwards;
    }

    .sale-overlay.unsold img {
        max-width: 60%;
    }

    @keyframes overlayPop {
        0% { transform: scale(0.7); opacity: 0; }
        60% { transform: scale(1.05); opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
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

    .team-card.highlight {
        border-color: rgba(190, 242, 100, 0.9);
        transform: translateY(-4px);
        box-shadow: 0 15px 30px rgba(190, 242, 100, 0.1);
    }
</style>
@endsection

@section('content')
@php
    $livePlayer = $currentPlayer?->player;
    $playerPhoto = $livePlayer && $livePlayer->photo ? asset('storage/' . $livePlayer->photo) : asset('images/defaultplayer.jpeg');
    $playerInitial = $livePlayer ? strtoupper(substr($livePlayer->name, 0, 1)) : '?';
    $playerRole = $livePlayer?->primaryGameRole?->gamePosition?->name
        ?? $livePlayer?->position?->name
        ?? 'Awaiting player';
    $basePrice = $currentPlayer?->base_price ?? null;
    $highestAmount = $currentHighestBid?->amount ?? ($basePrice ?? null);
    $highestTeamName = $currentHighestBid && $currentHighestBid->leagueTeam && $currentHighestBid->leagueTeam->team
        ? $currentHighestBid->leagueTeam->team->name
        : ($currentPlayer ? 'Awaiting bids' : 'Auction paused');
    $bidLabel = $currentHighestBid ? 'Current Bid' : ($currentPlayer ? 'Base Price' : 'Status');
    $spotlightTeam = null;
    if ($currentHighestBid && $currentHighestBid->leagueTeam) {
        $spotlightTeam = $teams->firstWhere('id', $currentHighestBid->leagueTeam->id);
    }
@endphp
<input type="hidden" id="league-id" value="{{ $league->id }}">
<input type="hidden" id="league-slug" value="{{ $league->slug }}">

<div class="broadcast-bg py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white space-y-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="flex items-center gap-3 text-emerald-300 text-sm uppercase tracking-[0.25em]">
                    <span class="w-3 h-3 bg-red-500 rounded-full live-dot shadow-lg"></span>
                    LIVE AUCTION
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold mt-2">{{ $league->name }}</h1>
                <p class="text-slate-300 text-lg">{{ $league->game->name }} League</p>
            </div>
            <div class="flex flex-wrap gap-6 text-sm">
                <div>
                    <p class="text-slate-400 uppercase text-xs tracking-wide">Viewers</p>
                    <p class="text-3xl font-semibold" id="viewerCount">{{ $liveViewers }}</p>
                </div>
                <div>
                    <p class="text-slate-400 uppercase text-xs tracking-wide">Last Update</p>
                    <p class="text-3xl font-semibold" id="lastUpdated">{{ now()->format('H:i:s') }}</p>
                </div>
                <button type="button" id="forceRefresh"
                    class="px-5 py-2.5 rounded-full bg-slate-100 text-slate-900 font-semibold shadow-lg hover:bg-white transition">
                    Refresh Data
                </button>
            </div>
        </div>

        <div class="grid gap-8 xl:grid-cols-2">
            <section class="relative broadcast-panel p-6 sm:p-8" data-player-panel>
                <div class="sale-overlay" id="soldOverlay">
                    <img src="{{ asset('images/auction/sold.png') }}" alt="Sold animation">
                </div>
                <div class="sale-overlay unsold" id="unsoldOverlay">
                    <img src="{{ asset('images/auction/unsold.png') }}" alt="Unsold animation">
                </div>

                <div class="flex flex-col gap-8 lg:flex-row">
                    <div class="lg:w-1/2">
                        <div class="relative overflow-hidden rounded-3xl border border-slate-700/70 bg-black/30 shadow-2xl">
                            <img
                                src="{{ $playerPhoto }}"
                                alt="{{ $livePlayer->name ?? 'Awaiting player' }}"
                                id="playerImage"
                                class="w-full h-[22rem] object-cover {{ $currentPlayer ? '' : 'opacity-50' }}"
                                onerror="this.classList.add('hidden'); document.getElementById('playerFallback').classList.remove('hidden');">
                            <div id="playerFallback"
                                 class="absolute inset-0 hidden bg-slate-800/70 flex items-center justify-center text-6xl font-bold">
                                {{ $playerInitial }}
                            </div>
                        </div>
                    </div>

                    <div class="lg:w-1/2 space-y-6">
                        <div>
                            <p class="text-slate-400 text-sm tracking-wide uppercase">Current Player</p>
                            <h2 class="text-3xl font-bold mt-1" id="playerName">{{ $livePlayer->name ?? 'Waiting for next player' }}</h2>
                            <p class="text-lg text-slate-300" id="playerRole">{{ $playerRole }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="bg-slate-800/70 rounded-2xl p-4">
                                <p class="text-slate-400 uppercase text-xs tracking-wide">Base Price</p>
                                <p class="text-2xl font-semibold mt-1" id="playerBasePrice">
                                    {{ $basePrice !== null ? 'Rs ' . number_format($basePrice) : '—' }}
                                </p>
                            </div>
                            <div class="bg-slate-800/70 rounded-2xl p-4">
                                <p class="text-slate-400 uppercase text-xs tracking-wide">Status</p>
                                <p class="text-2xl font-semibold mt-1" id="playerStatus">
                                    {{ $currentPlayer && $currentPlayer->retention ? 'Retained' : ($currentPlayer ? 'Auctioning' : 'Paused') }}
                                </p>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-emerald-500/10 to-cyan-500/10 border border-emerald-400/30 rounded-3xl p-6 space-y-2">
                            <p class="text-sm text-emerald-300 uppercase tracking-wide" id="currentBidLabel">{{ strtoupper($bidLabel) }}</p>
                            <p class="text-4xl font-bold text-white" id="currentBidAmount">
                                {{ $highestAmount ? 'Rs ' . number_format($highestAmount) : '—' }}
                            </p>
                            <p class="text-slate-200 text-lg" id="currentBidTeam">{{ $highestTeamName }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <div class="space-y-8">
                <section class="broadcast-panel p-6 sm:p-7" id="teamSpotlight">
                    <div class="flex items-center justify-between gap-6">
                        <div>
                            <p class="text-slate-400 uppercase text-xs tracking-[0.35em]" id="teamSpotlightLabel">
                                {{ $spotlightTeam ? 'Leading Bidder' : 'Winner pending' }}
                            </p>
                            <h3 class="text-3xl font-bold mt-2" id="teamSpotlightName">
                                {{ $spotlightTeam->team->name ?? 'Top bid will appear here' }}
                            </h3>
                            <p class="text-slate-300" id="teamSpotlightMeta">
                                {{ $spotlightTeam ? 'Holding the floor with Rs ' . number_format($highestAmount) : 'As soon as a team wins, details will be shown' }}
                            </p>
                        </div>
                        <div class="rounded-2xl border border-emerald-400/40 px-5 py-3 text-center">
                            <p class="text-xs uppercase text-emerald-200 tracking-wide">Current Bid</p>
                            <p class="text-2xl font-semibold" id="teamSpotlightBid">
                                {{ $highestAmount ? 'Rs ' . number_format($highestAmount) : '—' }}
                            </p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-6 text-sm text-slate-200">
                        <div>
                            <p class="text-slate-400 text-xs uppercase">Wallet</p>
                            <p class="text-xl font-semibold" id="teamSpotlightWallet">
                                {{ $spotlightTeam ? 'Rs ' . number_format($spotlightTeam->display_wallet ?? $spotlightTeam->wallet_balance ?? 0) : '—' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs uppercase">Players Signed</p>
                            <p class="text-xl font-semibold" id="teamSpotlightPlayers">
                                {{ $spotlightTeam ? $spotlightTeam->leaguePlayers->count() : '—' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs uppercase">Players Needed</p>
                            <p class="text-xl font-semibold" id="teamSpotlightNeeded">
                                {{ $spotlightTeam ? $spotlightTeam->players_needed : '—' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs uppercase">Max Bid Cap</p>
                            <p class="text-xl font-semibold" id="teamSpotlightCap">
                                {{ $spotlightTeam ? 'Rs ' . number_format($spotlightTeam->max_bid_cap ?? 0) : '—' }}
                            </p>
                        </div>
                    </div>
                </section>

                <section class="broadcast-panel p-6 sm:p-7">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold">Recent Bids</h3>
                        <span class="text-slate-400 text-sm" id="recentBidsStatus">Connected</span>
                    </div>
                    <div id="recentBidsList" class="mt-4 space-y-3 text-sm">
                        <div class="text-slate-400">Waiting for live bids...</div>
                    </div>
                </section>
            </div>
        </div>

        <section class="broadcast-panel p-6 sm:p-8">
            <div class="flex items-center justify-between gap-4">
                <h3 class="text-2xl font-semibold">League Teams</h3>
                <p class="text-sm text-slate-400">Wallets and roster progress update live</p>
            </div>
            <div class="grid gap-4 mt-6 md:grid-cols-2" id="teamsContainer">
                @foreach($teams as $team)
                    <div class="team-card" data-team-card="{{ $team->id }}">
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-xl font-semibold">{{ $team->team->name }}</p>
                            <span class="text-xs uppercase tracking-wide text-slate-400">
                                Squad
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-4 text-sm text-slate-300">
                            <div>
                                <p class="text-slate-400 text-xs uppercase">Wallet</p>
                                <p class="text-xl font-semibold" data-team-balance>
                                    Rs {{ number_format($team->display_wallet ?? $team->wallet_balance ?? 0) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-xs uppercase">Players</p>
                                <p class="text-xl font-semibold" data-team-players>
                                    {{ $team->leaguePlayers->count() }}
                                </p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-xs uppercase">Needed</p>
                                <p class="text-xl font-semibold" data-team-needed>
                                    {{ $team->players_needed }}
                                </p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-xs uppercase">Retained</p>
                                <p class="text-xl font-semibold" data-team-retained>
                                    {{ $team->retained_players_count }}
                                </p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-xs uppercase">Max Bid Cap</p>
                                <p class="text-xl font-semibold" data-team-max>
                                    Rs {{ number_format($team->max_bid_cap ?? 0) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-xs uppercase">Reserve</p>
                                <p class="text-xl font-semibold" data-team-reserve>
                                    Rs {{ number_format($team->reserve_amount ?? 0) }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const leagueId = document.getElementById('league-id').value;
    const leagueSlug = document.getElementById('league-slug').value;
    const soldOverlay = document.getElementById('soldOverlay');
    const unsoldOverlay = document.getElementById('unsoldOverlay');
    const bidAmountEl = document.getElementById('currentBidAmount');
    const bidLabelEl = document.getElementById('currentBidLabel');
    const bidTeamEl = document.getElementById('currentBidTeam');
    const playerNameEl = document.getElementById('playerName');
    const playerRoleEl = document.getElementById('playerRole');
    const playerStatusEl = document.getElementById('playerStatus');
    const playerBaseEl = document.getElementById('playerBasePrice');
    const playerImageEl = document.getElementById('playerImage');
    const playerFallbackEl = document.getElementById('playerFallback');
    const forceRefreshBtn = document.getElementById('forceRefresh');
    const recentBidsList = document.getElementById('recentBidsList');
    const recentBidsStatus = document.getElementById('recentBidsStatus');
    const lastUpdatedEl = document.getElementById('lastUpdated');
    const spotlight = {
        label: document.getElementById('teamSpotlightLabel'),
        name: document.getElementById('teamSpotlightName'),
        meta: document.getElementById('teamSpotlightMeta'),
        bid: document.getElementById('teamSpotlightBid'),
        wallet: document.getElementById('teamSpotlightWallet'),
        players: document.getElementById('teamSpotlightPlayers'),
        needed: document.getElementById('teamSpotlightNeeded'),
        cap: document.getElementById('teamSpotlightCap')
    };
    const defaultPlayerImage = "{{ asset('images/defaultplayer.jpeg') }}";

    function setOverlay(overlay, show) {
        if (!overlay) return;
        overlay.classList.toggle('visible', !!show);
        if (show) {
            setTimeout(() => overlay.classList.remove('visible'), 4500);
        }
    }

    function updateLastUpdated() {
        if (lastUpdatedEl) {
            lastUpdatedEl.textContent = new Date().toLocaleTimeString();
        }
    }

    function formatCurrency(amount) {
        if (amount === null || amount === undefined) {
            return '—';
        }
        return 'Rs ' + new Intl.NumberFormat('en-IN').format(Math.max(0, Number(amount)));
    }

    function updateBidDisplay(amount, label, teamName) {
        if (bidAmountEl) {
            bidAmountEl.textContent = (amount === null || amount === undefined) ? '—' : formatCurrency(amount);
        }
        if (bidLabelEl && label) {
            bidLabelEl.textContent = label.toUpperCase();
        }
        if (bidTeamEl && teamName) {
            bidTeamEl.textContent = teamName;
        }
    }

    function updatePlayerDetails(playerPayload, leaguePlayerPayload) {
        if (playerNameEl) playerNameEl.textContent = playerPayload?.name || 'Waiting for next player';
        if (playerRoleEl) {
            let position = playerPayload?.primary_game_role?.game_position?.name
                || playerPayload?.position?.name
                || 'Awaiting player';
            playerRoleEl.textContent = position;
        }
        if (playerStatusEl) {
            playerStatusEl.textContent = leaguePlayerPayload?.retention ? 'Retained' : (playerPayload ? 'Auctioning' : 'Paused');
        }
        if (playerBaseEl) {
            playerBaseEl.textContent = formatCurrency(leaguePlayerPayload?.base_price ?? null);
        }
        if (playerImageEl) {
            if (playerPayload && playerPayload.photo) {
                playerImageEl.src = playerPayload.photo;
                playerImageEl.classList.remove('hidden');
                if (playerFallbackEl) {
                    playerFallbackEl.classList.add('hidden');
                }
            } else if (playerPayload) {
                playerImageEl.classList.add('hidden');
                if (playerFallbackEl) {
                    playerFallbackEl.textContent = (playerPayload.name || '?').slice(0, 1).toUpperCase();
                    playerFallbackEl.classList.remove('hidden');
                }
            } else {
                playerImageEl.src = defaultPlayerImage;
                playerImageEl.classList.remove('hidden');
                if (playerFallbackEl) {
                    playerFallbackEl.classList.add('hidden');
                }
            }
        }
    }

    function updateTeamSpotlight(teamPayload, meta) {
        if (!spotlight.name) return;
        if (teamPayload) {
            spotlight.label.textContent = meta?.label || 'Leading Bidder';
            spotlight.name.textContent = teamPayload.team?.name || teamPayload.name || 'Team';
            spotlight.meta.textContent = meta?.message || 'Holding the live floor';
            spotlight.bid.textContent = formatCurrency(meta?.bid ?? null);
            spotlight.wallet.textContent = formatCurrency(teamPayload.wallet_balance ?? meta?.wallet ?? null);
            spotlight.players.textContent = (meta?.players ?? teamPayload.league_players_count) ?? '—';
            spotlight.needed.textContent = meta?.needed ?? '—';
            spotlight.cap.textContent = formatCurrency(meta?.cap ?? null);
        } else {
            spotlight.label.textContent = 'Winner Pending';
            spotlight.name.textContent = 'Top bid will appear here';
            spotlight.meta.textContent = 'As soon as a player is sold the team appears automatically';
            spotlight.bid.textContent = '—';
            spotlight.wallet.textContent = '—';
            spotlight.players.textContent = '—';
            spotlight.needed.textContent = '—';
            spotlight.cap.textContent = '—';
        }
    }

    function updateTeamCard(teamPayload) {
        if (!teamPayload || !teamPayload.id) return;
        const card = document.querySelector(`[data-team-card="${teamPayload.id}"]`);
        if (!card) return;
        const balanceEl = card.querySelector('[data-team-balance]');
        const playersEl = card.querySelector('[data-team-players]');
        const neededEl = card.querySelector('[data-team-needed]');
        const retainedEl = card.querySelector('[data-team-retained]');
        const maxEl = card.querySelector('[data-team-max]');
        const reserveEl = card.querySelector('[data-team-reserve]');

        if (balanceEl && teamPayload.wallet_balance !== undefined) {
            balanceEl.textContent = formatCurrency(teamPayload.wallet_balance);
        }
        if (playersEl && teamPayload.players_count !== undefined) {
            playersEl.textContent = teamPayload.players_count;
        }
        if (neededEl && teamPayload.players_needed !== undefined) {
            neededEl.textContent = teamPayload.players_needed;
        }
        if (retainedEl && teamPayload.retained_players_count !== undefined) {
            retainedEl.textContent = teamPayload.retained_players_count;
        }
        if (maxEl && teamPayload.max_bid_cap !== undefined) {
            maxEl.textContent = formatCurrency(teamPayload.max_bid_cap);
        }
        if (reserveEl && teamPayload.reserve_amount !== undefined) {
            reserveEl.textContent = formatCurrency(teamPayload.reserve_amount);
        }
    }

    function highlightTeamCard(teamId) {
        document.querySelectorAll('[data-team-card]').forEach(card => card.classList.remove('highlight'));
        if (teamId) {
            const card = document.querySelector(`[data-team-card="${teamId}"]`);
            if (card) {
                card.classList.add('highlight');
                setTimeout(() => card.classList.remove('highlight'), 5000);
            }
        }
    }

    function addRecentBidCard(bid, prepend = true) {
        if (!recentBidsList) {
            return;
        }
        const wrapper = document.createElement('div');
        wrapper.className = 'rounded-2xl border border-slate-700/80 bg-slate-900/30 p-4 flex items-center justify-between gap-4';
        wrapper.innerHTML = `
            <div>
                <p class="text-base font-semibold">${bid.team || 'Team'}</p>
                <p class="text-xs uppercase tracking-wide text-slate-400">${bid.time}</p>
            </div>
            <div class="text-2xl font-bold text-emerald-300">${formatCurrency(bid.amount)}</div>
        `;
        if (recentBidsList.children.length === 1 && recentBidsList.children[0].textContent.includes('Waiting')) {
            recentBidsList.innerHTML = '';
        }
        if (prepend) {
            recentBidsList.insertBefore(wrapper, recentBidsList.firstChild);
        } else {
            recentBidsList.appendChild(wrapper);
        }
        while (recentBidsList.children.length > 6) {
            recentBidsList.removeChild(recentBidsList.lastChild);
        }
    }

    function fetchRecentBids() {
        return fetch(`/api/auctions/league/${leagueSlug}/recent-bids`)
            .then(response => response.json())
            .then(data => {
                if (!data?.success) {
                    throw new Error('Unable to fetch bids');
                }
                if (recentBidsList) {
                    recentBidsList.innerHTML = '';
                }
                data.bids.forEach(bid => {
                    addRecentBidCard({
                        team: bid.league_team?.team?.name,
                        amount: bid.amount,
                        time: new Date(bid.created_at).toLocaleTimeString()
                    }, false);
                });
                recentBidsStatus.textContent = 'Live feed';
            })
            .catch(() => {
                recentBidsStatus.textContent = 'Feed paused';
            });
    }

    function refreshTeamBalances() {
        return fetch(`/api/auctions/league/${leagueSlug}/team-balances`)
            .then(response => response.json())
            .then(data => {
                if (!data?.success) {
                    throw new Error('Unable to fetch balances');
                }
                data.teams.forEach(team => updateTeamCard(team));
            });
    }

    function forceRefresh() {
        Promise.allSettled([fetchRecentBids(), refreshTeamBalances()]).finally(updateLastUpdated);
    }

    if (forceRefreshBtn) {
        forceRefreshBtn.addEventListener('click', forceRefresh);
    }

    setInterval(forceRefresh, 8000);

    // Initialize Pusher for live updates
    const pusher = new Pusher('b62b3b015a81d2d28278', {
        cluster: 'ap2',
        forceTLS: true,
        enabledTransports: ['ws', 'wss']
    });

    const globalChannel = pusher.subscribe('auctions');
    const leagueChannel = pusher.subscribe(`auctions.league.${leagueId}`);

    function handleBidEvent(payload) {
        updateBidDisplay(payload.new_bid, 'Current Bid', payload.league_team?.team?.name || 'Live Bid');
        updateTeamSpotlight(payload.league_team, {
            label: 'Leading Bidder',
            bid: payload.new_bid,
            message: `${payload.league_team?.team?.name || 'A team'} has the highest bid`,
            wallet: payload.league_team?.wallet_balance,
            players: payload.league_team?.league_players_count,
        });
        highlightTeamCard(payload.league_team?.id);
        fetchRecentBids();
        updateLastUpdated();
    }

    function handleSoldEvent(payload) {
        setOverlay(soldOverlay, true);
        updateBidDisplay(payload.league_player?.bid_price ?? null, 'SOLD', payload.team?.team?.name || 'Team');
        updateTeamSpotlight(payload.team, {
            label: 'Sold To',
            bid: payload.league_player?.bid_price ?? null,
            message: `${payload.team?.team?.name || 'A team'} wins the player`,
            wallet: payload.team?.wallet_balance,
            players: payload.team?.league_players_count,
        });
        highlightTeamCard(payload.team?.id);
        fetchRecentBids();
        refreshTeamBalances();
        updateLastUpdated();
    }

    function handleUnsoldEvent(payload) {
        setOverlay(unsoldOverlay, true);
        updateBidDisplay(null, 'UNSOLD', payload.league_player?.player?.name || 'No sale');
        updateTeamSpotlight(null);
        fetchRecentBids();
        updateLastUpdated();
    }

    function handleNewPlayer(payload) {
        updatePlayerDetails(payload.player, payload.league_player);
        updateBidDisplay(payload.league_player?.base_price ?? null, 'Base Price', 'Awaiting bids');
        updateTeamSpotlight(null);
        setOverlay(soldOverlay, false);
        setOverlay(unsoldOverlay, false);
        updateLastUpdated();
    }

    [globalChannel, leagueChannel].forEach(channel => {
        channel.bind('new-player-bid-call', handleBidEvent);
        channel.bind('player-sold', handleSoldEvent);
        channel.bind('player-unsold', handleUnsoldEvent);
        channel.bind('new-player-started', handleNewPlayer);
    });

    forceRefresh();
});
</script>
@endsection
