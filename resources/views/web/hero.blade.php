@php
    $heroGround = $featuredGrounds->first();
    $heroTeam = $featuredTeams->first();
    $primaryLeague = $liveAuctionLeague ?? $upcomingLeague ?? ($recentLeagues->first() ?? null);
@endphp

<!-- Video Hero Section -->
<section id="hero" class="video-hero relative min-h-[90vh] flex items-center text-white overflow-visible">
    <div class="hero-visual" aria-hidden="true"></div>
    <div class="hero-overlay"></div>

    <div class="relative z-10 w-full py-24 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto hero-grid">
            <div class="hero-main order-2 lg:order-1">
                <h1 class="text-4xl sm:text-5xl xl:text-6xl font-extrabold leading-tight mb-4"></h1>

                <div class="flex flex-wrap gap-4 mb-6">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 rounded-2xl bg-gradient-to-r from-emerald-500 to-lime-500 font-semibold text-lg shadow-2xl shadow-emerald-500/30 hover:shadow-emerald-500/50 transition-all duration-300">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-600 font-semibold text-lg shadow-2xl shadow-cyan-500/30 hover:shadow-cyan-500/40 transition-all duration-300">
                            Join {{ config('app.name') }}
                        </a>
                        <a href="{{ route('login') }}" class="btn-border inline-flex items-center justify-center px-8 py-4 rounded-2xl font-semibold text-lg gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Sign In
                        </a>
                    @endauth
                    <a href="https://wa.me/919400960223" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center px-8 py-4 rounded-2xl bg-gradient-to-r from-emerald-400 to-green-600 font-semibold text-lg shadow-2xl shadow-emerald-500/30 hover:shadow-emerald-500/50 transition-all duration-300 gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="w-5 h-5 fill-current">
                            <path d="M16 2.667c-7.355 0-13.333 5.86-13.333 13.093 0 2.307.63 4.56 1.824 6.536l-1.938 7.037 7.248-1.89A13.286 13.286 0 0 0 16 28.76c7.355 0 13.333-5.86 13.333-13.093C29.333 8.526 23.355 2.667 16 2.667Zm0 23.894c-2.032 0-4.02-.555-5.747-1.603l-.41-.248-4.3 1.121 1.146-4.162-.266-.427a10.34 10.34 0 0 1-1.56-5.12c0-5.67 4.652-10.288 10.333-10.288s10.333 4.617 10.333 10.288S21.68 26.561 16 26.561Zm6.111-7.722c-.334-.167-1.98-.978-2.287-1.091-.307-.115-.531-.167-.755.166-.223.333-.866 1.091-1.062 1.313-.195.223-.389.247-.723.082-.334-.166-1.41-.516-2.685-1.646-.992-.885-1.663-1.977-1.857-2.31-.195-.333-.021-.513.146-.676.151-.15.334-.388.5-.582.167-.194.222-.333.333-.555.111-.223.056-.416-.028-.582-.083-.166-.755-1.812-1.035-2.478-.273-.657-.552-.569-.755-.58-.195-.01-.417-.012-.64-.012-.223 0-.584.084-.889.416-.305.333-1.167 1.14-1.167 2.778 0 1.638 1.194 3.22 1.361 3.444.167.223 2.35 3.58 5.69 4.86.795.306 1.414.489 1.897.626.797.227 1.524.195 2.099.119.64-.095 1.98-.81 2.26-1.592.28-.78.28-1.448.195-1.592-.083-.144-.307-.222-.64-.389Z"/>
                        </svg>
                        WhatsApp +91 9400960223
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
                    <img src="{{ asset('images/hero.jpg') }}" alt="Live auction highlight" class="w-full h-56 object-cover" loading="lazy">
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
        overflow: visible;
    }
    .hero-visual {
        position: absolute;
        inset: 0;
        background: linear-gradient(115deg, rgba(7, 16, 46, 0.92) 0%, rgba(8, 18, 52, 0.85) 40%, rgba(5, 13, 36, 0.75) 100%), url('{{ asset('images/hero.jpg') }}');
        background-size: cover;
        background-position: center;
        opacity: 0.9;
        filter: saturate(1.05);
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
