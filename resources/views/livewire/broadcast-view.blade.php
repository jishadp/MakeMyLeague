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

        #broadcastRoot {
            --display-contrast: 1;
            --display-brightness: 1;
            --display-saturation: 1;
        }

        #broadcastRoot:fullscreen,
        #broadcastRoot:-webkit-full-screen,
        #broadcastRoot:-ms-fullscreen {
            overflow-y: auto;
            height: 100vh;
            -webkit-overflow-scrolling: touch;
        }

        #broadcastRoot .broadcast-bg,
        #broadcastRoot .broadcast-panel,
        #broadcastRoot .team-card {
            filter:
                contrast(var(--display-contrast))
                brightness(var(--display-brightness))
                saturate(var(--display-saturation));
        }

        .reload-fab {
            position: fixed;
            inset: auto 1.25rem calc(4.5rem + env(safe-area-inset-bottom, 0px)) auto;
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, #22d3ee 0%, #0ea5e9 100%);
            color: #0f172a;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(14, 165, 233, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.15);
            z-index: 60;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .reload-fab:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 35px rgba(14, 165, 233, 0.45);
            background: linear-gradient(135deg, #38bdf8 0%, #22d3ee 100%);
        }

        @media (max-width: 640px) {
            .reload-fab {
                width: 3.25rem;
                height: 3.25rem;
                inset: auto 1rem calc(4.25rem + env(safe-area-inset-bottom, 0px)) auto;
            }
        }

        .reload-fab:active {
            transform: scale(0.96);
        }

        .theme-switcher {
            position: sticky;
            bottom: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            background: rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(148, 163, 184, 0.35);
            border-radius: 9999px;
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.35);
            z-index: 40;
        }

        .theme-switcher button {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 9999px;
            border: 1px solid transparent;
            background: transparent;
            color: #e2e8f0;
            font-weight: 700;
            font-size: 12px;
            letter-spacing: 0.02em;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .theme-switcher button .dot {
            width: 12px;
            height: 12px;
            border-radius: 9999px;
            border: 1px solid rgba(255,255,255,0.4);
        }

        .theme-switcher button.is-active {
            border-color: rgba(255,255,255,0.45);
            background: rgba(255,255,255,0.08);
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.5);
        }

        .display-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(2px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 70;
            padding: 1rem;
        }

        .display-modal.is-open {
            display: flex;
        }

        .display-card {
            background: #0f172a;
            border: 1px solid rgba(148, 163, 184, 0.35);
            border-radius: 1.5rem;
            padding: 1.5rem;
            max-width: 520px;
            width: 100%;
            box-shadow: 0 22px 60px rgba(0, 0, 0, 0.4);
            color: #e2e8f0;
        }

        .display-card h3 {
            margin: 0;
        }

        .display-control {
            display: grid;
            grid-template-columns: 1fr 120px;
            gap: 10px;
            align-items: center;
        }

        .display-control input[type="range"] {
            width: 100%;
        }

        @media (max-width: 640px) {
            .display-card {
                max-width: 100%;
            }

            .display-control {
                grid-template-columns: 1fr;
            }
        }

        #broadcastRoot[data-broadcast-theme="light"] .broadcast-bg {
            background: radial-gradient(circle at 20% 20%, rgba(148, 163, 184, 0.2), transparent 40%), #f8fafc;
            color: #0f172a;
        }

        #broadcastRoot[data-broadcast-theme="light"] .broadcast-panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
        }

        #broadcastRoot[data-broadcast-theme="light"] .team-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
        }

        #broadcastRoot[data-broadcast-theme="light"] .text-white,
        #broadcastRoot[data-broadcast-theme="light"] .text-slate-200,
        #broadcastRoot[data-broadcast-theme="light"] .text-slate-300,
        #broadcastRoot[data-broadcast-theme="light"] .text-slate-400,
        #broadcastRoot[data-broadcast-theme="light"] .text-slate-500 {
            color: #0f172a !important;
        }

        #broadcastRoot[data-broadcast-theme="light"] .bg-slate-800\/70,
        #broadcastRoot[data-broadcast-theme="light"] .bg-slate-900\/50,
        #broadcastRoot[data-broadcast-theme="light"] .bg-slate-900\/60,
        #broadcastRoot[data-broadcast-theme="light"] .bg-slate-900\/40 {
            background: #f8fafc !important;
            border-color: #e2e8f0 !important;
        }

        #broadcastRoot[data-broadcast-theme="light"] .team-logo,
        #broadcastRoot[data-broadcast-theme="light"] .player-thumb {
            background: linear-gradient(135deg, #e2e8f0, #f8fafc);
            color: #0f172a;
            border-color: #cbd5e1;
        }

        #broadcastRoot[data-broadcast-theme="light"] .reload-fab {
            background: linear-gradient(135deg, #e2e8f0 0%, #f8fafc 100%);
            color: #0f172a;
            box-shadow: 0 10px 30px rgba(148, 163, 184, 0.35);
        }

        #broadcastRoot[data-broadcast-theme="ice"] .broadcast-bg {
            background: radial-gradient(circle at 30% 20%, rgba(59, 130, 246, 0.2), transparent 45%), #e0f2fe;
            color: #0b1b33;
        }

        #broadcastRoot[data-broadcast-theme="ice"] .broadcast-panel {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(59, 130, 246, 0.25);
            box-shadow: 0 20px 50px rgba(59, 130, 246, 0.15);
        }

        #broadcastRoot[data-broadcast-theme="ice"] .team-card {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(14, 165, 233, 0.25);
        }

        #broadcastRoot[data-broadcast-theme="ice"] .text-white,
        #broadcastRoot[data-broadcast-theme="ice"] .text-slate-200,
        #broadcastRoot[data-broadcast-theme="ice"] .text-slate-300,
        #broadcastRoot[data-broadcast-theme="ice"] .text-slate-400,
        #broadcastRoot[data-broadcast-theme="ice"] .text-slate-500 {
            color: #0b1b33 !important;
        }

        #broadcastRoot[data-broadcast-theme="ice"] .team-logo,
        #broadcastRoot[data-broadcast-theme="ice"] .player-thumb {
            background: linear-gradient(135deg, #e0f2fe, #bae6fd);
            color: #0b1b33;
            border-color: rgba(59, 130, 246, 0.35);
        }

        #broadcastRoot[data-broadcast-theme="sunrise"] .broadcast-bg {
            background: radial-gradient(circle at 30% 20%, rgba(251, 191, 36, 0.3), transparent 45%), #fff7ed;
            color: #3b1b0f;
        }

        #broadcastRoot[data-broadcast-theme="sunrise"] .broadcast-panel {
            background: #fff7ed;
            border: 1px solid rgba(234, 179, 8, 0.35);
            box-shadow: 0 18px 40px rgba(234, 179, 8, 0.15);
        }

        #broadcastRoot[data-broadcast-theme="sunrise"] .team-card {
            background: #fff7ed;
            border: 1px solid rgba(234, 179, 8, 0.35);
        }

        #broadcastRoot[data-broadcast-theme="sunrise"] .text-white,
        #broadcastRoot[data-broadcast-theme="sunrise"] .text-slate-200,
        #broadcastRoot[data-broadcast-theme="sunrise"] .text-slate-300,
        #broadcastRoot[data-broadcast-theme="sunrise"] .text-slate-400,
        #broadcastRoot[data-broadcast-theme="sunrise"] .text-slate-500 {
            color: #3b1b0f !important;
        }

        #broadcastRoot[data-broadcast-theme="sunrise"] .team-logo,
        #broadcastRoot[data-broadcast-theme="sunrise"] .player-thumb {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #3b1b0f;
            border-color: rgba(234, 179, 8, 0.45);
        }

        #broadcastRoot[data-broadcast-theme="sunrise"] .reload-fab {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: #3b1b0f;
            box-shadow: 0 10px 30px rgba(251, 191, 36, 0.35);
        }

        .logo-circle {
            width: 3.25rem;
            height: 3.25rem;
            border-radius: 9999px;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0ea5e9, #22d3ee);
            color: #0a1729;
            font-weight: 700;
            font-size: 1.1rem;
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 12px 30px rgba(14, 165, 233, 0.35);
        }

        .logo-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .team-logo {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.85rem;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(255,255,255,0.12), rgba(255,255,255,0.05));
            border: 1px solid rgba(255, 255, 255, 0.15);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #cbd5e1;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .team-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .player-thumb {
            width: 2.1rem;
            height: 2.1rem;
            border-radius: 9999px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.15);
            background: linear-gradient(135deg, rgba(16,185,129,0.25), rgba(59,130,246,0.25));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #e2e8f0;
            font-weight: 700;
            font-size: 0.8rem;
        }

        .player-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
@endonce

@php
    $leagueModel = $payload['league'];
    $currentPlayer = $payload['currentPlayer'];
    $currentHighestBid = $payload['currentHighestBid'];
    $currentBids = $payload['currentBids'];
    $teams = $payload['teams'];
    $liveViewers = $payload['liveViewers'];
    $lastOutcomePlayer = $payload['lastOutcomePlayer'];
    $recentSoldPlayers = $payload['recentSoldPlayers'];
    $bidStartTime = $currentPlayer?->updated_at ?? null;

    $isShowingLastResult = !$currentPlayer && $lastOutcomePlayer;
    $displayLeaguePlayer = $currentPlayer ?? $lastOutcomePlayer;
    $displayPlayer = $displayLeaguePlayer?->player;
    $playerPhoto = $displayPlayer && $displayPlayer->photo ? asset('storage/' . $displayPlayer->photo) : asset('images/defaultplayer.jpeg');
    $playerInitial = $displayPlayer ? strtoupper(substr($displayPlayer->name, 0, 1)) : '?';
    $playerRole = $displayPlayer?->primaryGameRole?->gamePosition?->name
        ?? $displayPlayer?->position?->name
        ?? 'Awaiting player';
    $basePrice = $displayLeaguePlayer?->base_price ?? null;
    $lastOutcomeTeamName = $lastOutcomePlayer?->leagueTeam?->team?->name;
    $currentBidAmount = $currentHighestBid?->amount ?? null;
    $soldAmount = $lastOutcomePlayer?->bid_price;
    $lastOutcomeStatus = $lastOutcomePlayer?->status;
    $isSoldOutcome = $isShowingLastResult && $lastOutcomeStatus === 'sold';
    $isUnsoldOutcome = $isShowingLastResult && $lastOutcomeStatus === 'unsold';
    $highestAmount = $currentPlayer ? ($currentBidAmount ?? $basePrice) : ($isSoldOutcome ? $soldAmount : null);
    $highestTeamName = $currentPlayer
        ? ($currentHighestBid && $currentHighestBid->leagueTeam && $currentHighestBid->leagueTeam->team
            ? $currentHighestBid->leagueTeam->team->name
            : 'Awaiting bids')
        : ($isSoldOutcome
            ? ($lastOutcomeTeamName ? 'Sold to ' . $lastOutcomeTeamName : 'Sold')
            : ($isUnsoldOutcome ? 'Unsold - back to pool' : 'Waiting for player'));
    $bidLabel = $currentPlayer
        ? ($currentHighestBid ? 'Current Bid' : 'Base Price')
        : ($lastOutcomePlayer ? ($isUnsoldOutcome ? 'Status' : 'Sold Price') : 'Status');
    $spotlightTeam = null;
    if ($currentHighestBid && $currentHighestBid->leagueTeam) {
        $spotlightTeam = $teams->firstWhere('id', $currentHighestBid->leagueTeam->id);
    } elseif ($isShowingLastResult && $lastOutcomePlayer?->leagueTeam) {
        $spotlightTeam = $teams->firstWhere('id', $lastOutcomePlayer->league_team_id);
    }
    $playerLocationText = $displayPlayer?->localBody?->name ?? 'Location not shared';
    $leagueLogo = $leagueModel->logo ? asset('storage/' . $leagueModel->logo) : null;
    $leagueInitial = strtoupper(substr($leagueModel->name, 0, 1));
    $bidCallGroups = $currentBids ?? collect();
    $bidCallsForDisplay = collect();
    if ($currentPlayer) {
        $bidCallsForDisplay = $bidCallGroups->get($currentPlayer->id) ?? collect();
    } elseif ($lastOutcomePlayer) {
        $bidCallsForDisplay = $bidCallGroups->get($lastOutcomePlayer->id) ?? collect();
    }
    $recentBidCalls = collect($bidCallsForDisplay)->sortByDesc('created_at')->take(3);
    $latestBidCall = $recentBidCalls->first();
    $olderBidCalls = $recentBidCalls->skip(1);
    $bidStatus = $displayLeaguePlayer?->status;
    $statusLabel = match ($bidStatus) {
        'auctioning' => 'Ongoing',
        'sold' => 'Sold',
        'unsold' => 'Unsold',
        default => 'Pending',
    };
    $statusTone = match ($bidStatus) {
        'auctioning' => ['bg' => 'bg-emerald-500/10 border-emerald-400/40', 'pill' => 'bg-emerald-500/20 text-emerald-100 border border-emerald-400/50', 'amount' => 'text-emerald-100'],
        'sold' => ['bg' => 'bg-red-500/10 border-red-400/60', 'pill' => 'bg-red-500/20 text-red-100 border border-red-400/60', 'amount' => 'text-red-100'],
        'unsold' => ['bg' => 'bg-amber-500/10 border-amber-400/50', 'pill' => 'bg-amber-500/20 text-amber-100 border border-amber-400/50', 'amount' => 'text-amber-100'],
        default => ['bg' => 'bg-slate-800/60 border-slate-700/60', 'pill' => 'bg-slate-700/60 text-slate-200 border border-slate-600/60', 'amount' => 'text-slate-100'],
    };
@endphp

@php
    $themeOptions = [
        ['key' => 'dark', 'label' => 'Dark'],
        ['key' => 'light', 'label' => 'Light'],
        ['key' => 'ice', 'label' => 'Ice'],
        ['key' => 'sunrise', 'label' => 'Sunrise'],
    ];
@endphp

<div wire:poll.30s="refreshData" id="broadcastRoot" data-broadcast-theme="dark">
<section class="broadcast-bg py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white space-y-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="flex items-center gap-3 text-emerald-300 text-sm uppercase tracking-[0.25em]">
                    <span class="w-3 h-3 bg-red-500 rounded-full live-dot shadow-lg"></span>
                    LIVE AUCTION
                </div>
                <div class="flex items-center gap-4 mt-2">
                    <div class="logo-circle" aria-hidden="true">
                        @if($leagueLogo)
                            <img src="{{ $leagueLogo }}" alt="{{ $leagueModel->name }} logo">
                        @else
                            {{ $leagueInitial }}
                        @endif
                    </div>
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-bold">{{ $leagueModel->name }}</h1>
                        <p class="text-slate-300 text-lg">{{ $leagueModel->game->name }} League</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-4 sm:gap-6 text-sm items-start sm:items-center justify-start lg:justify-end">
                <div>
                    <p class="text-slate-400 uppercase text-xs tracking-wide">Viewers</p>
                    <p class="text-3xl font-semibold leading-tight">{{ $liveViewers }}</p>
                </div>
                <div>
                    <p class="text-slate-400 uppercase text-xs tracking-wide">Bid Start</p>
                    <p class="text-3xl font-semibold leading-tight">
                        {{ $bidStartTime ? $bidStartTime->timezone(config('app.timezone'))->format('d M, H:i') : 'N/A' }}
                    </p>
                </div>
                <div>
                    <p class="text-slate-400 uppercase text-xs tracking-wide">Last Update</p>
                    <p class="text-3xl font-semibold leading-tight">{{ $lastUpdated ?? now()->format('H:i:s') }}</p>
                </div>
                <div class="flex w-full sm:w-auto flex-col gap-2 sm:flex-row">
                    <button type="button" wire:click="refreshData"
                        class="px-4 py-2 rounded-full bg-slate-100 text-slate-900 font-semibold shadow-lg hover:bg-white transition w-full sm:w-auto text-center">
                        Refresh Data
                    </button>
                    <button type="button"
                        id="broadcastFullscreenToggle"
                        aria-pressed="false"
                        class="px-4 py-2 rounded-full bg-emerald-400 text-slate-900 font-semibold shadow-lg hover:bg-emerald-300 transition w-full sm:w-auto text-center">
                        Enter Fullscreen
                    </button>
                </div>
            </div>
        </div>

        <div class="grid gap-8 xl:grid-cols-2">
            <section class="relative broadcast-panel p-6 sm:p-8">
                <div class="flex flex-col gap-8 lg:flex-row">
                    <div class="lg:w-1/2">
                        <div class="relative overflow-hidden rounded-3xl border border-slate-700/70 bg-black/30 shadow-2xl">
                            <img src="{{ $playerPhoto }}" alt="{{ $displayPlayer->name ?? 'Awaiting player' }}"
                                class="w-full h-[22rem] object-cover {{ $currentPlayer ? '' : 'opacity-100' }}">
                            <div class="absolute inset-0 flex items-center justify-center text-6xl font-bold text-white/40"
                                @if($displayPlayer) style="display:none" @endif>
                                {{ $playerInitial }}
                            </div>
                            @include('components.auction-result-badge', [
                                'show' => $isShowingLastResult,
                                'isSold' => $isSoldOutcome,
                                'isUnsold' => $isUnsoldOutcome,
                            ])
                        </div>
                    </div>

                    <div class="lg:w-1/2 space-y-6">
                        <div>
                            <p class="text-slate-400 text-sm tracking-wide uppercase">{{ $isShowingLastResult ? 'Last Player' : 'Current Player' }}</p>
                            <p class="text-xs text-slate-400 font-semibold mt-1 uppercase">Base Price: <span class="text-slate-200">{{ $basePrice !== null ? 'Rs ' . number_format($basePrice) : '—' }}</span></p>
                            <h2 class="text-3xl font-bold mt-1">{{ $displayPlayer->name ?? 'Waiting for next player' }}</h2>
                            @if($isShowingLastResult && ($isSoldOutcome || $isUnsoldOutcome))
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-wide {{ $isSoldOutcome ? 'bg-emerald-500/10 text-emerald-200 border border-emerald-400/40' : 'bg-amber-500/10 text-amber-100 border border-amber-300/40' }}">
                                        {{ $isSoldOutcome ? 'Sold' : 'Unsold' }}
                                    </span>
                                    @if($isSoldOutcome && $lastOutcomeTeamName)
                                        <span class="text-sm text-slate-200">to {{ $lastOutcomeTeamName }}</span>
                                    @endif
                                </div>
                            @endif
                            <p class="text-lg text-slate-300">{{ $playerRole }}</p>
                            @if($isShowingLastResult)
                                <p class="text-sm text-slate-400 mt-2">Waiting for the next player to enter the auction floor.</p>
                            @endif
                        </div>

                        <div class="bg-gradient-to-r from-emerald-500/10 to-cyan-500/10 border border-emerald-400/30 rounded-3xl p-6 space-y-2">
                            <p class="text-sm text-emerald-300 uppercase tracking-wide">{{ strtoupper($bidLabel) }}</p>
                            <p class="text-4xl font-bold text-white">{{ $highestAmount ? 'Rs ' . number_format($highestAmount) : '—' }}</p>
                            <p class="text-slate-200 text-lg">{{ $highestTeamName }}</p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 text-sm">
                            <div class="bg-slate-800/70 rounded-2xl p-4">
                                <p class="text-slate-400 uppercase text-xs tracking-wide">Location</p>
                                <p class="text-lg sm:text-2xl font-semibold mt-1 leading-snug truncate" title="{{ $playerLocationText }}">
                                    {{ $playerLocationText }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="space-y-8">
                <section class="broadcast-panel p-6 sm:p-7">
                    @if($isSoldOutcome && $spotlightTeam)
                        <div class="flex items-start justify-between gap-6">
                            <div class="flex items-start gap-3 sm:gap-4">
                                @php
                                    $winnerTeamLogoPath = $spotlightTeam?->team?->logo ?? null;
                                    $winnerTeamLogo = $winnerTeamLogoPath ? asset('storage/' . $winnerTeamLogoPath) : null;
                                    $winnerTeamInitial = $spotlightTeam && $spotlightTeam->team ? strtoupper(substr($spotlightTeam->team->name ?? '?', 0, 1)) : '?';
                                @endphp
                                <span class="team-logo w-12 h-12 rounded-2xl" aria-hidden="true">
                                    @if($winnerTeamLogo)
                                        <img src="{{ $winnerTeamLogo }}" alt="{{ $spotlightTeam->team->name ?? 'Team' }} logo">
                                    @else
                                        {{ $winnerTeamInitial }}
                                    @endif
                                </span>
                                <div>
                                    <p class="text-slate-400 uppercase text-xs tracking-[0.35em]">Sold To</p>
                                    <h3 class="text-3xl font-bold mt-2">
                                        {{ $spotlightTeam->team->name ?? 'Team pending' }}
                                    </h3>
                                    <p class="text-slate-300">
                                        Secured the player for Rs {{ number_format($soldAmount ?? 0) }}
                                    </p>
                                </div>
                            </div>
                            <div class="rounded-2xl border border-red-400/60 px-5 py-3 text-center bg-red-500/10">
                                <p class="text-xs uppercase text-red-100 tracking-wide">Sold Price</p>
                                <p class="text-2xl font-semibold text-red-100">
                                    {{ $soldAmount ? 'Rs ' . number_format($soldAmount) : '—' }}
                                </p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-6 text-sm text-slate-200">
                            <div>
                                <p class="text-slate-400 text-xs uppercase">Wallet</p>
                                <p class="text-xl font-semibold">Rs {{ number_format($spotlightTeam->display_wallet ?? $spotlightTeam->wallet_balance ?? 0) }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-xs uppercase">Players Signed</p>
                                <p class="text-xl font-semibold">{{ $spotlightTeam->leaguePlayers->count() }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-xs uppercase">Players Needed</p>
                                <p class="text-xl font-semibold">{{ $spotlightTeam->players_needed }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-xs uppercase">Max Bid Cap</p>
                                <p class="text-xl font-semibold">Rs {{ number_format($spotlightTeam->max_bid_cap ?? 0) }}</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-start justify-between gap-6">
                            <div class="flex items-start gap-3 sm:gap-4">
                                @php
                                    $winnerTeamLogoPath = $spotlightTeam?->team?->logo ?? null;
                                    $winnerTeamLogo = $winnerTeamLogoPath ? asset('storage/' . $winnerTeamLogoPath) : null;
                                    $winnerTeamInitial = $spotlightTeam && $spotlightTeam->team ? strtoupper(substr($spotlightTeam->team->name ?? '?', 0, 1)) : '?';
                                @endphp
                                <span class="team-logo w-12 h-12 rounded-2xl" aria-hidden="true">
                                    @if($winnerTeamLogo)
                                        <img src="{{ $winnerTeamLogo }}" alt="{{ $spotlightTeam->team->name ?? 'Team' }} logo">
                                    @else
                                        {{ $winnerTeamInitial }}
                                    @endif
                                </span>
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
                    @endif
                </section>

                <section class="broadcast-panel p-6 sm:p-7">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Bid Activity</p>
                            <h3 class="text-xl font-semibold mt-1">Last Calls</h3>
                            <p class="text-sm text-slate-400 mt-1">Latest three bid calls for this player.</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[11px] font-semibold uppercase tracking-wide {{ $statusTone['pill'] }}">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="mt-6 space-y-2">
                        @forelse($recentBidCalls as $call)
                            @php
                                $callTeam = $call->leagueTeam?->team;
                                $callTeamLogoPath = $callTeam?->logo ?? null;
                                $callTeamLogo = $callTeamLogoPath ? asset('storage/' . $callTeamLogoPath) : null;
                                $callTeamInitial = $callTeam ? strtoupper(substr($callTeam->name ?? 'T', 0, 1)) : 'T';
                                $callTime = $call->created_at?->timezone(config('app.timezone'))->format('H:i:s');
                                $callAmount = $call->amount ?? 0;
                            @endphp
                            <div class="flex items-center justify-between gap-4 rounded-xl border {{ $statusTone['bg'] }} px-4 py-3 shadow-sm">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="team-logo w-10 h-10 rounded-xl" aria-hidden="true">
                                        @if($callTeamLogo)
                                            <img src="{{ $callTeamLogo }}" alt="{{ $callTeam->name ?? 'Team' }} logo">
                                        @else
                                            {{ $callTeamInitial }}
                                        @endif
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-base font-semibold text-white truncate">{{ $callTeam->name ?? 'Team TBD' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs uppercase text-slate-400">Rs {{ number_format($callAmount) }}</p>
                                    <p class="text-[11px] text-slate-500">at {{ $callTime ?? '—' }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-slate-700/60 bg-slate-900/60 px-5 py-4 sm:col-span-2 xl:col-span-3">
                                <p class="text-sm text-slate-300 font-semibold">No bids have been placed yet.</p>
                                <p class="text-xs text-slate-500 mt-1">Status: {{ $statusLabel }}</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>

        <section class="broadcast-panel p-6 sm:p-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Recent Auctions</p>
                    <h3 class="text-xl font-semibold mt-1">Player Spotlight</h3>
                </div>
                <p class="text-sm text-slate-400">Latest sold players</p>
            </div>
            <div class="mt-4 overflow-x-auto pb-2">
                <div class="flex gap-4 min-w-full">
                    @forelse($recentSoldPlayers as $sold)
                        @php
                            $soldPlayer = $sold->player;
                            $soldPhoto = $soldPlayer?->photo ? asset('storage/' . $soldPlayer->photo) : asset('images/defaultplayer.jpeg');
                            $soldTeam = $sold->leagueTeam?->team;
                            $soldTeamLogoPath = $soldTeam?->logo ?? null;
                            $soldTeamLogo = $soldTeamLogoPath ? asset('storage/' . $soldTeamLogoPath) : null;
                            $soldTeamInitial = $soldTeam ? strtoupper(substr($soldTeam->name ?? 'T', 0, 1)) : 'T';
                            $soldRole = $soldPlayer?->primaryGameRole?->gamePosition?->name ?? $soldPlayer?->position?->name ?? 'Player';
                        @endphp
                        <div class="min-w-[16rem] max-w-[18rem] bg-slate-900/40 border border-slate-700/60 rounded-2xl p-4 flex flex-col gap-3 shadow-xl">
                            <div class="flex items-center gap-3">
                                <span class="player-thumb w-12 h-12">
                                    <img src="{{ $soldPhoto }}" alt="{{ $soldPlayer->name ?? 'Player' }} photo">
                                </span>
                                <div>
                                    <p class="text-sm uppercase text-slate-400">Sold for</p>
                                    <p class="text-2xl font-bold text-white">Rs {{ number_format($sold->bid_price ?? 0) }}</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-slate-400 uppercase tracking-wide">Player</p>
                                <p class="text-lg font-semibold text-white leading-tight">{{ $soldPlayer->name ?? 'Player' }}</p>
                                <p class="text-xs text-slate-400">{{ $soldRole }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="team-logo w-10 h-10 rounded-xl" aria-hidden="true">
                                    @if($soldTeamLogo)
                                        <img src="{{ $soldTeamLogo }}" alt="{{ $soldTeam->name ?? 'Team' }} logo">
                                    @else
                                        {{ $soldTeamInitial }}
                                    @endif
                                </span>
                                <div>
                                    <p class="text-xs uppercase text-slate-400">Team</p>
                                    <p class="text-sm font-semibold text-white">{{ $soldTeam->name ?? 'Team TBD' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-400">
                                <span>Base: Rs {{ number_format($sold->base_price ?? 0) }}</span>
                                <span>{{ $sold->updated_at?->timezone(config('app.timezone'))->format('d M, H:i') }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400">No recent auctions yet.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="broadcast-panel p-6 sm:p-8">
            <div class="flex items-center justify-between gap-4">
                <h3 class="text-2xl font-semibold">League Teams</h3>
                <p class="text-sm text-slate-400">Wallets and roster progress update live</p>
            </div>
            <div class="grid gap-4 mt-6 md:grid-cols-2">
                @foreach($teams as $team)
                    <div class="team-card" wire:key="team-{{ $team->id }}">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                @php
                                    $teamLogoPath = $team->team->logo ?? null;
                                    $teamLogo = $teamLogoPath ? asset('storage/' . $teamLogoPath) : null;
                                    $teamInitial = strtoupper(substr($team->team->name, 0, 1));
                                @endphp
                                <span class="team-logo" aria-hidden="true">
                                    @if($teamLogo)
                                        <img src="{{ $teamLogo }}" alt="{{ $team->team->name }} logo">
                                    @else
                                        {{ $teamInitial }}
                                    @endif
                                </span>
                                <p class="text-xl font-semibold">{{ $team->team->name }}</p>
                            </div>
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
                        @php
                            $retainedPlayers = $team->leaguePlayers->filter(fn ($player) => $player->retention || $player->status === 'retained');
                            $boughtPlayers = $team->leaguePlayers->filter(fn ($player) => $player->status === 'sold' && ! $player->retention);
                        @endphp
                        <div class="mt-5 space-y-3">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-semibold text-white">Retained Players</p>
                                <span class="text-xs text-slate-400">{{ $retainedPlayers->count() }} total</span>
                            </div>
                            @if($retainedPlayers->isNotEmpty())
                                <div class="flex flex-wrap gap-2">
                                    @foreach($retainedPlayers as $player)
                                        @php
                                            $retainedPhoto = $player->player?->photo ? asset('storage/' . $player->player->photo) : asset('images/defaultplayer.jpeg');
                                        @endphp
                                        <span class="inline-flex items-center gap-2 rounded-full bg-amber-400/15 border border-amber-400/40 px-3 py-1 text-xs text-amber-100">
                                            <span class="player-thumb">
                                                <img src="{{ $retainedPhoto }}" alt="{{ $player->player->name ?? 'Player' }} photo">
                                            </span>
                                            <span class="max-w-[9rem] truncate sm:max-w-none">{{ $player->player->name ?? 'Player' }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-slate-500">No retained players yet.</p>
                            @endif
                        </div>
                        <div class="mt-4 space-y-3">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-semibold text-white">Bought Players</p>
                                <span class="text-xs text-slate-400">{{ $boughtPlayers->count() }} signed</span>
                            </div>
                            @if($boughtPlayers->isNotEmpty())
                                <div class="flex flex-col gap-2 sm:grid sm:grid-cols-2">
                                    @foreach($boughtPlayers as $player)
                                        @php
                                            $boughtPhoto = $player->player?->photo ? asset('storage/' . $player->player->photo) : asset('images/defaultplayer.jpeg');
                                        @endphp
                                        <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-700/60 bg-slate-900/50 px-3 py-2">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="player-thumb w-8 h-8">
                                                    <img src="{{ $boughtPhoto }}" alt="{{ $player->player->name ?? 'Player' }} photo">
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-semibold text-white truncate">{{ $player->player->name ?? 'Player' }}</p>
                                                    @if($player->player?->position || $player->player?->primaryGameRole?->gamePosition)
                                                        <p class="text-[11px] text-slate-400 truncate">
                                                            {{ $player->player?->primaryGameRole?->gamePosition?->name ?? $player->player?->position?->name }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <p class="text-sm font-bold text-emerald-300 whitespace-nowrap">Rs {{ number_format($player->bid_price ?? 0) }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-slate-500">Once a player is bought, the amount will show here.</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="flex flex-col items-center gap-3 mt-6 pb-8">
            <div class="theme-switcher" id="broadcastThemeSwitcher" aria-label="Theme switcher">
                @php
                    $themeDotColors = [
                        'dark' => '#0ea5e9',
                        'light' => '#0f172a',
                        'ice' => '#38bdf8',
                        'sunrise' => '#f59e0b',
                    ];
                @endphp
                @foreach($themeOptions as $theme)
                    <button type="button"
                            data-theme-choice="{{ $theme['key'] }}"
                            aria-label="Switch to {{ $theme['label'] }} theme">
                        <span class="dot" style="background: {{ $themeDotColors[$theme['key']] ?? '#0ea5e9' }};"></span>
                        <span>{{ $theme['label'] }}</span>
                    </button>
                @endforeach
            </div>
            <button type="button"
                    id="openDisplayModal"
                    class="px-4 py-2 rounded-full bg-slate-800 text-white font-semibold border border-slate-600 hover:bg-slate-700 transition">
                Display adjustments
            </button>
        </div>

    </div>
</section>

    <button type="button"
            id="broadcastReloadFab"
            aria-label="Reload page"
            class="reload-fab group">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-slate-900 group-hover:text-slate-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 9.75a7.5 7.5 0 0112.69-3.75H18a.75.75 0 000-1.5h-3.75a.75.75 0 00-.75.75V8a.75.75 0 001.5 0V6.84A6 6 0 1112 18a.75.75 0 00-1.5 0 7.5 7.5 0 01-6-12.75" />
        </svg>
    </button>

    <div class="display-modal" id="broadcastDisplayModal" role="dialog" aria-modal="true" aria-labelledby="displayModalTitle">
        <div class="display-card">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Display</p>
                    <h3 class="text-xl font-semibold mt-1" id="displayModalTitle">Adjust visibility</h3>
                    <p class="text-sm text-slate-400">Tune for projectors or bright rooms. Changes save per browser.</p>
                </div>
                <button type="button" id="closeDisplayModal" aria-label="Close display settings" class="text-slate-300 hover:text-white">
                    ✕
                </button>
            </div>
            <div class="mt-5 space-y-4">
                <div class="display-control">
                    <label for="displayContrast" class="text-sm font-semibold text-slate-200">Contrast</label>
                    <input type="range" id="displayContrast" name="display-contrast" min="80" max="140" value="100">
                </div>
                <div class="display-control">
                    <label for="displayBrightness" class="text-sm font-semibold text-slate-200">Brightness</label>
                    <input type="range" id="displayBrightness" name="display-brightness" min="80" max="140" value="100">
                </div>
                <div class="display-control">
                    <label for="displaySaturation" class="text-sm font-semibold text-slate-200">Saturation</label>
                    <input type="range" id="displaySaturation" name="display-saturation" min="70" max="150" value="100">
                </div>
            </div>
            <div class="mt-6 flex items-center justify-between gap-3">
                <button type="button" id="resetDisplaySettings" class="px-4 py-2 rounded-full border border-slate-500 text-slate-100 hover:bg-slate-800">Reset</button>
                <button type="button" id="closeDisplayModalFooter" class="px-4 py-2 rounded-full bg-emerald-500 text-slate-900 font-semibold hover:bg-emerald-400">Done</button>
            </div>
        </div>
    </div>
</div>

@once
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
@endonce

<script>
    document.addEventListener('livewire:init', () => {
        const componentId = '{{ $this->id() }}';
        const leagueId = {{ $leagueModel->id }};
        const fullscreenButton = document.getElementById('broadcastFullscreenToggle');
        const broadcastRoot = document.getElementById('broadcastRoot');
        const reloadFab = document.getElementById('broadcastReloadFab');
        const themeButtons = document.querySelectorAll('[data-theme-choice]');
        const themeStorageKey = 'broadcast_theme';
        const availableThemes = Array.from(themeButtons).map(btn => btn.dataset.themeChoice);
        const displayModal = document.getElementById('broadcastDisplayModal');
        const openDisplayModal = document.getElementById('openDisplayModal');
        const closeDisplayModal = document.getElementById('closeDisplayModal');
        const closeDisplayModalFooter = document.getElementById('closeDisplayModalFooter');
        const resetDisplaySettings = document.getElementById('resetDisplaySettings');
        const sliderContrast = document.getElementById('displayContrast');
        const sliderBrightness = document.getElementById('displayBrightness');
        const sliderSaturation = document.getElementById('displaySaturation');
        const displayStorageKey = 'broadcast_display_settings';
        const fullscreenTarget = document.documentElement;

        const displayDefaults = {
            contrast: 1,
            brightness: 1,
            saturation: 1,
        };

        const getFullscreenElement = () => document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement;
        const isFullscreenActive = () => Boolean(getFullscreenElement());
        const requestFullscreen = (target) => {
            if (!target) {
                return Promise.reject('No fullscreen target');
            }
            if (target.requestFullscreen) return target.requestFullscreen();
            if (target.webkitRequestFullscreen) return target.webkitRequestFullscreen();
            if (target.msRequestFullscreen) return target.msRequestFullscreen();
            return Promise.reject('Fullscreen not supported');
        };
        const exitFullscreen = () => {
            if (document.exitFullscreen) return document.exitFullscreen();
            if (document.webkitExitFullscreen) return document.webkitExitFullscreen();
            if (document.msExitFullscreen) return document.msExitFullscreen();
            return Promise.resolve();
        };

        window.__broadcastPusherSetup = window.__broadcastPusherSetup || {};

        const ensureComponent = (callback) => {
            const component = window.Livewire.find(componentId);
            if (component) {
                callback(component);
                return;
            }
            setTimeout(() => ensureComponent(callback), 150);
        };

        const refreshComponent = () => {
            ensureComponent((component) => component.call('refreshData'));
        };

        const setTheme = (theme) => {
            if (!broadcastRoot || !theme) return;
            const choice = availableThemes.includes(theme) ? theme : (availableThemes[0] || 'dark');
            broadcastRoot.setAttribute('data-broadcast-theme', choice);
            try {
                localStorage.setItem(themeStorageKey, choice);
            } catch (e) {}
            themeButtons.forEach(btn => {
                const isActive = btn.dataset.themeChoice === choice;
                btn.classList.toggle('is-active', isActive);
            });
        };

        const initTheme = () => {
            let stored = null;
            try {
                stored = localStorage.getItem(themeStorageKey);
            } catch (e) {}
            setTheme(stored || 'dark');
        };

        initTheme();

        themeButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const choice = btn.dataset.themeChoice;
                setTheme(choice);
            });
        });

        const applyDisplaySettings = (settings) => {
            if (!broadcastRoot) return;
            const merged = { ...displayDefaults, ...settings };
            broadcastRoot.style.setProperty('--display-contrast', merged.contrast);
            broadcastRoot.style.setProperty('--display-brightness', merged.brightness);
            broadcastRoot.style.setProperty('--display-saturation', merged.saturation);
            if (sliderContrast) sliderContrast.value = Math.round(merged.contrast * 100);
            if (sliderBrightness) sliderBrightness.value = Math.round(merged.brightness * 100);
            if (sliderSaturation) sliderSaturation.value = Math.round(merged.saturation * 100);
        };

        const loadDisplaySettings = () => {
            try {
                const saved = localStorage.getItem(displayStorageKey);
                if (saved) {
                    return JSON.parse(saved);
                }
            } catch (e) {}
            return { ...displayDefaults };
        };

        const persistDisplaySettings = (settings) => {
            try {
                localStorage.setItem(displayStorageKey, JSON.stringify(settings));
            } catch (e) {}
        };

        let currentDisplaySettings = loadDisplaySettings();
        applyDisplaySettings(currentDisplaySettings);

        const syncFromSliders = () => {
            currentDisplaySettings = {
                contrast: (Number(sliderContrast?.value) || 100) / 100,
                brightness: (Number(sliderBrightness?.value) || 100) / 100,
                saturation: (Number(sliderSaturation?.value) || 100) / 100,
            };
            applyDisplaySettings(currentDisplaySettings);
            persistDisplaySettings(currentDisplaySettings);
        };

        [sliderContrast, sliderBrightness, sliderSaturation].forEach(slider => {
            if (slider) {
                slider.addEventListener('input', syncFromSliders);
                slider.addEventListener('change', syncFromSliders);
            }
        });

        const openModal = () => {
            if (displayModal) displayModal.classList.add('is-open');
        };

        const closeModal = () => {
            if (displayModal) displayModal.classList.remove('is-open');
        };

        if (openDisplayModal) openDisplayModal.addEventListener('click', openModal);
        if (closeDisplayModal) closeDisplayModal.addEventListener('click', closeModal);
        if (closeDisplayModalFooter) closeDisplayModalFooter.addEventListener('click', closeModal);
        if (displayModal) {
            displayModal.addEventListener('click', (e) => {
                if (e.target === displayModal) {
                    closeModal();
                }
            });
        }

        if (resetDisplaySettings) {
            resetDisplaySettings.addEventListener('click', () => {
                currentDisplaySettings = { ...displayDefaults };
                applyDisplaySettings(currentDisplaySettings);
                persistDisplaySettings(currentDisplaySettings);
            });
        }

        if (!window.__broadcastPusherSetup[componentId]) {
            window.__broadcastPusherSetup[componentId] = true;
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

                ['player-sold', 'player-unsold', 'new-player-started', 'new-player-bid-call'].forEach(eventName => {
                    channels.forEach(channel => {
                        channel.bind(eventName, refreshComponent);
                    });
                });
            });
        }

        const updateFullscreenButton = () => {
            if (!fullscreenButton) {
                return;
            }
            const isActive = isFullscreenActive();
            fullscreenButton.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            fullscreenButton.textContent = isActive ? 'Exit Fullscreen' : 'Enter Fullscreen';
        };

        if (fullscreenButton) {
            fullscreenButton.addEventListener('click', () => {
                const target = fullscreenTarget || broadcastRoot || document.documentElement;
                if (!isFullscreenActive()) {
                    requestFullscreen(target).catch(() => {
                        if (target !== document.documentElement) {
                            requestFullscreen(document.documentElement).catch(() => {});
                        }
                    });
                } else {
                    exitFullscreen();
                }
            });

            ['fullscreenchange', 'webkitfullscreenchange', 'msfullscreenchange'].forEach(eventName => {
                document.addEventListener(eventName, updateFullscreenButton);
            });
            updateFullscreenButton();
        }

        if (reloadFab) {
            reloadFab.addEventListener('click', (event) => {
                if (isFullscreenActive()) {
                    event.preventDefault();
                    refreshComponent();
                    setTimeout(() => {
                        const component = window.Livewire?.find(componentId);
                        if (!component) {
                            window.location.reload();
                        }
                    }, 600);
                    return;
                }
                window.location.reload();
            });
        }
    });
</script>
