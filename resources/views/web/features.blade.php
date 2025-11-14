@php
    $flowSteps = [
        ['title' => 'Create League','copy' => 'Spin up a compliant season with wallets, fee slabs, and organizer approvals in minutes.','meta' => number_format($stats['leagues'] ?? 0) . ' total seasons'],
        ['title' => 'Add Teams','copy' => 'Invite franchises or club teams, lock their slots, and share digital contracts instantly.','meta' => number_format($stats['teams'] ?? 0) . ' teams onboarded'],
        ['title' => 'Add Players','copy' => 'Players self-register or get bulk imported with verified IDs and skill tags.','meta' => number_format($stats['players'] ?? 0) . ' registered players'],
        ['title' => 'Run Auction','copy' => 'Push paddles, cap wallets, and publish winning bids so owners see exactly what they paid.','meta' => $liveAuctionLeague ? ($liveAuctionLeague->name . ' · Wallet ₹' . number_format($liveAuctionLeague->team_wallet_limit ?? 0, 0)) : 'Activate auction mode when ready'],
        ['title' => 'Make Fixtures','copy' => 'Auto-generate fixtures by group or knockout and update venues in one grid.','meta' => number_format($stats['matches'] ?? 0) . ' fixtures logged'],
        ['title' => 'Manage Finance','copy' => 'Record franchise fees, sponsor payouts, and vendor expenses with audit-ready logs.','meta' => number_format($opsMetrics['teamsThisWeek'] ?? 0) . ' payouts logged this week'],
        ['title' => 'See Progress','copy' => 'Dashboards highlight standings, dues, deliverables, and broadcast-readiness.','meta' => 'Synced ' . now()->format('M d, H:i')],
    ];
    $availableLeagues = ($recentLeagues ?? collect())->take(4);
@endphp

<section id="features" class="feature-shell">
    <div class="feature-inner">
        <div class="feature-heading">
            <p class="feature-pill">Manage your league in a professional way</p>
            <h2>Organize your league with a single, user-first flow</h2>
            <p>Every workflow card plugs into real data so organizers, teams, and players always see the same source of truth.</p>

            <div class="feature-video-card">
                <div class="feature-video-frame">
                    <video autoplay muted loop playsinline poster="{{ asset('images/hero.jpg') }}">
                        <source src="{{ asset('videos/hero-auction.mp4') }}" type="video/mp4">
                        <source src="{{ asset('videos/hero-stadium.mp4') }}" type="video/mp4">
                    </video>
                    <div class="feature-video-overlay">
                        <span class="video-status">
                            @if($liveAuctionLeague)
                                Live auction
                            @elseif($upcomingLeague)
                                Countdown
                            @else
                                Highlight reel
                            @endif
                        </span>
                        @if($liveAuctionLeague)
                            <p class="video-headline">{{ $liveAuctionLeague->name }} bidding floor is active right now.</p>
                            <div class="feature-video-meta">
                                <div>
                                    <span class="label">Wallet cap</span>
                                    <strong>₹{{ number_format($liveAuctionLeague->team_wallet_limit ?? 0, 0) }}</strong>
                                </div>
                                <div>
                                    <span class="label">Teams in room</span>
                                    <strong>{{ $liveAuctionLeague->teams_count }}</strong>
                                </div>
                                <div>
                                    <span class="label">Started</span>
                                    <strong>{{ optional($liveAuctionLeague->auction_started_at)->diffForHumans() ?? 'Moments ago' }}</strong>
                                </div>
                            </div>
                        @elseif($upcomingLeague)
                            <p class="video-headline">{{ $upcomingLeague->name }} staging gets underway {{ optional($upcomingLeague->start_date)->format('d M Y') ?? 'soon' }}.</p>
                            <div class="feature-video-meta">
                                <div>
                                    <span class="label">Wallet cap</span>
                                    <strong>₹{{ number_format($upcomingLeague->team_wallet_limit ?? 0, 0) }}</strong>
                                </div>
                                <div>
                                    <span class="label">Season</span>
                                    <strong>{{ $upcomingLeague->season ?? 'New' }}</strong>
                                </div>
                                <div>
                                    <span class="label">Teams ready</span>
                                    <strong>{{ $upcomingLeague->teams_count ?? 0 }}</strong>
                                </div>
                            </div>
                        @else
                            <p class="video-headline">Clips from real auctions, fixtures, and venue preps powered by {{ config('app.name') }}.</p>
                            <div class="feature-video-meta">
                                <div>
                                    <span class="label">Players</span>
                                    <strong>{{ number_format($stats['players'] ?? 0) }}</strong>
                                </div>
                                <div>
                                    <span class="label">Leagues</span>
                                    <strong>{{ number_format($stats['leagues'] ?? 0) }}</strong>
                                </div>
                                <div>
                                    <span class="label">Fixtures</span>
                                    <strong>{{ number_format($stats['matches'] ?? 0) }}</strong>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="feature-body">
            <div class="flow-scroll">
                <div class="flow-track">
                    @foreach($flowSteps as $index => $step)
                        <div class="flow-step">
                            <div class="flow-count">{{ $index + 1 }}</div>
                            <h3>{{ $step['title'] }}</h3>
                            <p>{{ $step['copy'] }}</p>
                            <span class="flow-meta">{{ $step['meta'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="insight-scroll">
                <div class="market-card">
                    <div class="market-card__header">
                        <h3>Auction room</h3>
                        <span class="status-pill">
                            @if($liveAuctionLeague)
                                Live
                            @elseif($upcomingLeague)
                                Incoming
                            @else
                                Idle
                            @endif
                        </span>
                    </div>
                    @if($liveAuctionLeague)
                        <p class="market-subtitle">{{ $liveAuctionLeague->name }} • {{ $liveAuctionLeague->teams_count }} teams</p>
                        <div class="market-stats">
                            <div>
                                <p class="label">Wallet cap</p>
                                <p class="value">₹{{ number_format($liveAuctionLeague->team_wallet_limit ?? 0, 0) }}</p>
                            </div>
                            <div>
                                <p class="label">Started</p>
                                <p class="value">{{ optional($liveAuctionLeague->auction_started_at)->diffForHumans() ?? 'Now' }}</p>
                            </div>
                        </div>
                    @elseif($upcomingLeague)
                        <p class="market-subtitle">{{ $upcomingLeague->name }} starts {{ optional($upcomingLeague->start_date)->format('d M Y') ?? 'soon' }}</p>
                        <p class="text-xs text-slate-200">Wallet cap ₹{{ number_format($upcomingLeague->team_wallet_limit ?? 0, 0) }} · Registration ₹{{ number_format($upcomingLeague->team_reg_fee ?? 0, 0) }}</p>
                    @else
                        <p class="market-subtitle text-slate-200">Launch a league to start recording bids and payouts.</p>
                    @endif
                </div>

                <div class="market-card">
                    <div class="market-card__header">
                        <h3>Leagues available to play</h3>
                        <a href="{{ route('leagues.index') }}" class="link-arrow">View all</a>
                    </div>
                    <div class="league-scroll">
                        <div class="league-track">
                            @forelse($availableLeagues as $league)
                                @php $slots = max(($league->max_teams ?? 0) - $league->teams_count, 0); @endphp
                                <div class="league-card">
                                    <div class="league-row">
                                        <div>
                                            <p class="league-name">{{ $league->name }}</p>
                                            <p class="league-meta">Starts {{ optional($league->start_date)->format('d M Y') ?? 'TBA' }} • {{ optional($league->localBody)->name ?? 'Location TBA' }}</p>
                                        </div>
                                        <div class="league-actions">
                                            <span class="league-season">Season {{ $league->season ?? '—' }}</span>
                                            <a href="{{ route('leagues.shareable', $league) }}" class="league-eye" title="Preview share page">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path d="M1.5 12s3.5-7 10.5-7 10.5 7 10.5 7-3.5 7-10.5 7S1.5 12 1.5 12z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="league-foot">
                                        <span>{{ $league->teams_count }} / {{ $league->max_teams ?? '∞' }} teams locked</span>
                                        <span class="slots-open">{{ $slots }} slots open</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-slate-300">No public leagues yet. Add one to surface it here.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="market-card">
                    <h3>Player-side view</h3>
                    <p class="market-subtitle">Players instantly see their auction receipts, dues, and open leagues ready to join.</p>
                    <ul class="player-points">
                        <li>Auction receipts stay attached to every player profile.</li>
                        <li>Open leagues highlight wallet caps, venues, and match counts.</li>
                        <li>Progress boards sync with scoring and finance data automatically.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .feature-shell {
        background: radial-gradient(circle at top, rgba(15, 19, 52, 0.95), #010312);
        color: #e5edff;
        padding: 5rem 1rem;
    }
    .feature-inner {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 3rem;
    }
    .feature-heading {
        text-align: center;
        max-width: 720px;
        margin: 0 auto;
    }
    .feature-pill {
        display: inline-flex;
        padding: 0.35rem 1rem;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.25);
        letter-spacing: 0.25em;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: rgba(229, 237, 255, 0.75);
        margin-bottom: 1rem;
    }
    .feature-heading h2 {
        font-size: clamp(2rem, 4vw, 3.5rem);
        font-weight: 700;
        margin-bottom: 1rem;
    }
    .feature-heading p {
        color: rgba(229, 237, 255, 0.75);
    }
    .feature-video-card {
        margin-top: 2rem;
        border-radius: 32px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(2, 5, 21, 0.85);
        overflow: hidden;
        box-shadow: 0 25px 45px rgba(1, 3, 18, 0.6);
    }
    .feature-video-frame {
        position: relative;
        border-radius: inherit;
        overflow: hidden;
    }
    .feature-video-frame video {
        width: 100%;
        height: 360px;
        object-fit: cover;
        display: block;
    }
    .feature-video-overlay {
        position: absolute;
        inset: 0;
        padding: 1.75rem;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        gap: 1rem;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0) 20%, rgba(0, 0, 0, 0.65) 100%);
    }
    .video-status {
        align-self: flex-start;
        padding: 0.3rem 1rem;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        letter-spacing: 0.3em;
        text-transform: uppercase;
        font-size: 0.68rem;
        color: rgba(255, 255, 255, 0.85);
        background: rgba(2, 6, 23, 0.4);
    }
    .video-headline {
        font-size: 1.25rem;
        font-weight: 600;
    }
    .feature-video-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 0.75rem;
        text-align: left;
    }
    .feature-video-meta strong {
        display: block;
        font-size: 1.1rem;
    }
    .feature-body {
        display: flex;
        flex-direction: column;
        gap: 2.5rem;
    }
    .flow-scroll {
        overflow-x: auto;
        padding-bottom: 1rem;
    }
    .flow-track {
        display: flex;
        gap: 1rem;
        min-width: 100%;
    }
    .flow-step {
        min-width: 260px;
        background: rgba(7, 12, 34, 0.85);
        border-radius: 22px;
        padding: 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
    }
    .flow-count {
        width: 2.4rem;
        height: 2.4rem;
        border-radius: 999px;
        background: linear-gradient(135deg, #2563eb, #0ea5e9);
        color: #fff;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .flow-step h3 {
        font-size: 1.1rem;
        font-weight: 600;
    }
    .flow-step p {
        color: rgba(229, 237, 255, 0.75);
        font-size: 0.95rem;
    }
    .flow-meta {
        font-size: 0.75rem;
        letter-spacing: 0.25em;
        text-transform: uppercase;
        color: rgba(125, 211, 252, 0.8);
    }
    .insight-scroll {
        display: flex;
        gap: 1.5rem;
        overflow-x: auto;
        padding-bottom: 1rem;
    }
    .market-card {
        min-width: 280px;
        background: rgba(5, 9, 26, 0.9);
        border-radius: 30px;
        padding: 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
    }
    .market-card__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }
    .market-card h3 {
        font-size: 1.2rem;
        font-weight: 600;
    }
    .market-subtitle {
        color: rgba(229, 237, 255, 0.75);
        margin-bottom: 1rem;
    }
    .market-stats {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }
    .label {
        text-transform: uppercase;
        letter-spacing: 0.3em;
        font-size: 0.7rem;
        color: rgba(229, 237, 255, 0.6);
    }
    .value {
        font-size: 1.5rem;
        font-weight: 700;
    }
    .status-pill {
        padding: 0.25rem 0.9rem;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        font-size: 0.7rem;
        letter-spacing: 0.2em;
        text-transform: uppercase;
    }
    .league-scroll {
        overflow-x: auto;
    }
    .league-track {
        display: flex;
        gap: 1rem;
    }
    .league-card {
        min-width: 240px;
        background: rgba(10, 14, 36, 0.9);
        border-radius: 22px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 1rem;
    }
    .league-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
    }
    .league-actions {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.4rem;
    }
    .league-name {
        font-weight: 600;
    }
    .league-meta {
        color: rgba(229, 237, 255, 0.6);
        font-size: 0.85rem;
    }
    .league-season {
        font-size: 0.75rem;
        letter-spacing: 0.3em;
        text-transform: uppercase;
        color: rgba(229, 237, 255, 0.5);
    }
    .league-eye {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.2);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #93c5fd;
        transition: background 0.2s ease, transform 0.2s ease;
    }
    .league-eye svg {
        width: 18px;
        height: 18px;
        stroke: currentColor;
    }
    .league-eye:hover {
        background: rgba(147, 197, 253, 0.15);
        transform: translateY(-1px);
    }
    .league-foot {
        margin-top: 0.6rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.8rem;
        color: rgba(229, 237, 255, 0.6);
    }
    .slots-open {
        color: #34d399;
        font-weight: 600;
    }
    .link-arrow {
        color: #93c5fd;
        font-weight: 600;
    }
    .player-points {
        list-style: none;
        padding: 0;
        margin: 0;
        color: rgba(229, 237, 255, 0.7);
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }
    .player-points li::before {
        content: '•';
        margin-right: 0.4rem;
        color: rgba(125, 211, 252, 0.9);
    }
    @media (max-width: 768px) {
        .flow-step { min-width: 220px; }
        .market-card { min-width: 240px; }
        .feature-video-frame video { height: 260px; }
    }
</style>
