@php
    $heroGround = $featuredGrounds->first();
    $heroTeam = $featuredTeams->first();
    $primaryLeague = $liveAuctionLeague ?? $upcomingLeague ?? ($recentLeagues->first() ?? null);
@endphp

<!-- Video Hero Section -->
<section id="hero" class="video-hero relative min-h-[90vh] flex items-center text-white overflow-hidden">
    <video class="hero-video" autoplay muted loop playsinline poster="{{ asset('images/hero.jpg') }}">
        <source src="{{ asset('videos/hero-stadium.mp4') }}" type="video/mp4">
        <source src="{{ asset('videos/hero-auction.mp4') }}" type="video/mp4">
    </video>
    <div class="hero-overlay"></div>

    <div class="relative z-10 w-full py-24 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto hero-grid">
            <div class="hero-main order-2 lg:order-1">
                <h1 class="text-4xl sm:text-5xl xl:text-6xl font-extrabold leading-tight mb-4"></h1>

                <div class="flex flex-wrap gap-4 mb-6">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-600 font-semibold text-lg shadow-2xl shadow-cyan-500/30 hover:shadow-cyan-500/40 transition-all duration-300">
                        Join {{ config('app.name') }}
                    </a>
                    <a href="{{ route('login') }}" class="btn-border inline-flex items-center justify-center px-8 py-4 rounded-2xl font-semibold text-lg gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Sign In
                    </a>
                </div>

                <div class="hero-stats">
                </div>
            </div>

            @if($liveAuctionLeague)
            <div class="hero-side-card order-1 lg:order-2">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <span class="text-white/80 uppercase tracking-[0.3em] text-xs">League Insight</span>
                    </div>
                </div>
                <p class="text-white/90 text-lg"></p>

                <div class="relative rounded-3xl overflow-hidden border border-white/10 mt-6">
                    <video class="w-full h-56 object-cover" autoplay muted loop playsinline>
                        <source src="{{ asset('videos/hero-auction.mp4') }}" type="video/mp4">
                        <source src="{{ asset('videos/hero-stories.mp4') }}" type="video/mp4">
                    </video>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent p-6 flex flex-col justify-end">
                            <div class="flex items-center justify-between text-sm text-white/80">
                                <span>Live auction • {{ $liveAuctionLeague->name }}</span>
                                <span>{{ $liveAuctionLeague->teams_count }} teams bidding</span>
                            </div>
                            <div class="text-2xl font-bold mt-2">Wallet cap ₹{{ number_format($liveAuctionLeague->team_wallet_limit ?? 0, 0) }}</div>
                            <p class="text-sm text-white/70">Started {{ optional($liveAuctionLeague->auction_started_at)->diffForHumans() }}</p>
                    </div>
                </div>

                <div class="hero-mini-grid">
                    @if($nextFixture)
                        <div class="hero-mini-card">
                            <span class="label">Next Fixture</span>
                            <strong>{{ optional($nextFixture->league)->name }}</strong>
                            <small>
                                {{ optional($nextFixture->homeTeam?->team)->name ?? 'TBA' }}
                                vs
                                {{ optional($nextFixture->awayTeam?->team)->name ?? 'TBA' }}
                            </small>
                            <small>{{ optional($nextFixture->match_date)->format('d M Y') ?? 'Date TBA' }} • {{ optional($nextFixture->match_time)->format('h:i A') ?? 'Time TBA' }}</small>
                        </div>
                    @endif

                    @if($heroGround)
                        <div class="hero-mini-card">
                            <span class="label">Ground Spotlight</span>
                            <strong>{{ $heroGround->name }}</strong>
                            <small>{{ optional($heroGround->district)->name }} · Cap {{ number_format($heroGround->capacity ?? 0) }}</small>
                        </div>
                    @endif

                    @if($heroTeam)
                        <div class="hero-mini-card">
                            <span class="label">Featured Team</span>
                            <strong>{{ $heroTeam->name }}</strong>
                            <small>{{ optional($heroTeam->localBody)->name }} · {{ optional($heroTeam->homeGround)->name }}</small>
                        </div>
                    @endif
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-white/70">
                    <div class="flex items-center gap-2">
                        <span class="status-dot bg-emerald-400"></span>
                        {{ $featuredGrounds->count() }} vetted grounds
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="status-dot bg-sky-400"></span>
                        {{ $recentLeagues->count() }} recent league updates
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="status-dot bg-rose-400"></span>
                        {{ number_format($stats['matches'] ?? 0) }} fixtures recorded
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

<style>
    .hero-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 3rem;
        align-items: center;
    }
    .hero-main {
        max-width: 600px;
    }
    .video-hero {
        background: #020715;
    }
    .hero-video {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.75;
    }
    .hero-overlay {
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top, rgba(0, 18, 61, 0.8), rgba(0, 0, 0, 0.95));
        backdrop-filter: blur(2px);
    }
    .hero-badges {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.8rem;
        margin-bottom: 1.5rem;
    }
    .hero-badge {
        border-radius: 20px;
        padding: 0.85rem 1rem;
        background: rgba(4, 9, 29, 0.65);
        border: 1px solid rgba(255, 255, 255, 0.15);
        text-align: center;
    }
    .hero-badge-value {
        display: block;
        font-size: 1.5rem;
        font-weight: 700;
        color: #fff;
        line-height: 1.1;
    }
    .hero-badge-label {
        text-transform: uppercase;
        letter-spacing: 0.2em;
        font-size: 0.7rem;
        color: rgba(255, 255, 255, 0.7);
    }
    .hero-stats {
        margin-top: 32px;
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
    }
    .hero-stat {
        flex: 1 1 160px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 18px 22px;
        background: rgba(3, 7, 25, 0.7);
        backdrop-filter: blur(10px);
    }
    .hero-stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        display: block;
        line-height: 1.1;
    }
    .hero-stat-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.2em;
       	color: rgba(255, 255, 255, 0.7);
    }
    .hero-side-card {
        background: rgba(2, 6, 26, 0.8);
        border-radius: 32px;
        padding: 32px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.45);
    }
    .hero-mini-grid {
        margin-top: 28px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 14px;
    }
    .hero-mini-card {
        padding: 16px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(8, 14, 43, 0.8);
    }
    .hero-mini-card .label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.2em;
        color: rgba(255, 255, 255, 0.6);
    }
    .hero-mini-card strong {
        display: block;
        font-size: 1.25rem;
        margin-top: 6px;
    }
    .hero-mini-card small {
        color: rgba(255, 255, 255, 0.65);
        display: block;
    }
    .btn-border {
        border: 1px solid rgba(255, 255, 255, 0.45);
        color: #fff;
        background: transparent;
        transition: all 0.25s ease;
    }
    .btn-border:hover {
        background: #fff;
        color: #0a0a0a;
    }
    .live-pill {
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        background: rgba(248, 113, 113, 0.2);
        border: 1px solid rgba(248, 113, 113, 0.5);
        color: #fecaca;
        font-size: 0.75rem;
        letter-spacing: 0.2em;
    }
    .pill-soft {
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        border: 1px solid rgba(125, 211, 252, 0.5);
        color: rgba(191, 219, 254, 0.95);
        letter-spacing: 0.2em;
        font-size: 0.75rem;
    }
    .status-dot {
        display: inline-block;
        width: 0.65rem;
        height: 0.65rem;
        border-radius: 999px;
        animation: pulse 1.8s infinite;
    }
    @keyframes pulse {
        0% { opacity: 0.4; transform: scale(0.8); }
        50% { opacity: 1; transform: scale(1.1); }
        100% { opacity: 0.4; transform: scale(0.8); }
    }
    @media (max-width: 1024px) {
        .hero-grid {
            grid-template-columns: 1fr;
            text-align: center;
        }
        .hero-main {
            margin: 0 auto;
        }
        .hero-badges {
            grid-template-columns: repeat(3, minmax(90px, 1fr));
        }
        .hero-side-card {
            max-width: 520px;
            margin: 0 auto;
        }
    }
    @media (max-width: 768px) {
        .hero-side-card {
            padding: 24px;
        }
        .hero-stat {
            flex: 1 1 100%;
        }
    }
</style>
