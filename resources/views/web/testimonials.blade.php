@php
    $activityLeagues = $recentLeagues ?? collect();
@endphp

<!-- Live Activity Section -->
<section id="activity" class="relative py-24 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-20 left-20 w-96 h-96 bg-gradient-to-r from-cyan-500/10 to-blue-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-20 w-72 h-72 bg-gradient-to-r from-purple-500/10 to-pink-500/10 rounded-full blur-3xl"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.02"%3E%3Ccircle cx="30" cy="30" r="1"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]" aria-hidden="true"></div>
    </div>

    <div class="relative max-w-7xl mx-auto space-y-16">
        <div class="text-center">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white/90 text-sm font-medium mb-6">
                <svg class="w-4 h-4 mr-2 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618V15.5a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                Live League Activity
            </div>
            <h2 class="text-4xl sm:text-5xl font-bold text-white mb-4">
                Real organizers. Real fixtures. Zero placeholders.
            </h2>
            <p class="text-xl text-white/80 max-w-3xl mx-auto">
                The tiles below are populated straight from your Laravel database—featured leagues, fixtures, grounds, and stats update as soon as you do.
            </p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="stat-card text-center">
                <div class="stat-value" data-count="{{ $stats['leagues'] ?? 0 }}">0</div>
                <p class="stat-label">Active Leagues</p>
            </div>
            <div class="stat-card text-center">
                <div class="stat-value" data-count="{{ $stats['teams'] ?? 0 }}">0</div>
                <p class="stat-label">Registered Teams</p>
            </div>
            <div class="stat-card text-center">
                <div class="stat-value" data-count="{{ $stats['players'] ?? 0 }}">0</div>
                <p class="stat-label">Players Managed</p>
            </div>
            <div class="stat-card text-center">
                <div class="stat-value" data-count="{{ $stats['matches'] ?? 0 }}">0</div>
                <p class="stat-label">Fixtures Logged</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Recent leagues -->
            <div class="lg:col-span-2 space-y-6">
                @forelse($activityLeagues as $league)
                    <div class="activity-card bg-white/10 backdrop-blur-xl border border-white/15 rounded-3xl p-6 hover:border-white/30 transition">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                            <div>
                                <p class="text-white text-xl font-semibold">{{ $league->name }}</p>
                                <p class="text-white/60 text-sm">{{ optional($league->game)->name ?? 'Cricket format' }} • Season {{ $league->season ?? '—' }}</p>
                            </div>
                            <span class="pill-soft">
                                {{ ucfirst($league->status ?? 'active') }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <p class="text-white/50 uppercase tracking-[0.3em]">Teams</p>
                                <p class="text-white font-semibold text-lg">{{ $league->teams_count }}</p>
                            </div>
                            <div>
                                <p class="text-white/50 uppercase tracking-[0.3em]">Start date</p>
                                <p class="text-white font-semibold text-lg">{{ optional($league->start_date)->format('d M Y') ?? 'TBA' }}</p>
                            </div>
                            <div>
                                <p class="text-white/50 uppercase tracking-[0.3em]">Location</p>
                                <p class="text-white font-semibold text-lg">{{ optional($league->localBody)->name ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-white/50 uppercase tracking-[0.3em]">Wallet limit</p>
                                <p class="text-white font-semibold text-lg">₹{{ number_format($league->team_wallet_limit ?? 0, 0) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white/5 backdrop-blur-xl border border-dashed border-white/20 rounded-3xl p-8 text-center text-white/70">
                        No league activity yet. Create a league to see it appear here instantly.
                    </div>
                @endforelse
            </div>

            <!-- Snapshot column -->
            <div class="space-y-6">
                <div class="snapshot-card">
                    <h4 class="snapshot-title">Next Fixture</h4>
                    @if($nextFixture)
                        <p class="text-white text-lg font-semibold">{{ optional($nextFixture->league)->name }}</p>
                        <p class="text-white/70 text-sm mb-4">
                            {{ optional($nextFixture->match_date)->format('d M Y') ?? 'Date TBA' }} • {{ optional($nextFixture->match_time)->format('h:i A') ?? 'Time TBA' }}
                        </p>
                        <div class="space-y-2 text-sm text-white/80">
                            <div class="flex items-center justify-between">
                                <span>{{ optional($nextFixture->homeTeam?->team)->name ?? 'Home team' }}</span>
                                <span class="text-white/50">Home</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>{{ optional($nextFixture->awayTeam?->team)->name ?? 'Away team' }}</span>
                                <span class="text-white/50">Away</span>
                            </div>
                        </div>
                    @else
                        <p class="text-white/60 text-sm">Create fixtures to have them surface here.</p>
                    @endif
                </div>

                <div class="snapshot-card">
                    <h4 class="snapshot-title">Auction Status</h4>
                    @if($liveAuctionLeague)
                        <p class="text-sm text-green-300 mb-1">LIVE now</p>
                        <p class="text-white text-lg font-semibold">{{ $liveAuctionLeague->name }}</p>
                        <p class="text-white/70 text-sm mb-4">{{ $liveAuctionLeague->teams_count }} teams bidding • Wallet cap ₹{{ number_format($liveAuctionLeague->team_wallet_limit ?? 0, 0) }}</p>
                    @elseif($upcomingLeague)
                        <p class="text-sm text-amber-300 mb-1">Upcoming</p>
                        <p class="text-white text-lg font-semibold">{{ $upcomingLeague->name }}</p>
                        <p class="text-white/70 text-sm mb-4">Auction access opens {{ optional($upcomingLeague->start_date)->subDays(7)->format('d M Y') ?? 'soon' }}</p>
                    @else
                        <p class="text-white/60 text-sm">No auctions scheduled yet.</p>
                    @endif
                    <div class="text-white/60 text-xs uppercase tracking-[0.4em]">Synced {{ now()->format('H:i') }}</div>
                </div>

                <div class="snapshot-card">
                    <h4 class="snapshot-title">Ground Inventory</h4>
                    <p class="text-white text-3xl font-semibold">{{ $featuredGrounds->count() }}</p>
                    <p class="text-white/70 text-sm mb-4">Featured grounds available for booking</p>
                    <ul class="space-y-2 text-white/70 text-sm">
                        @forelse($featuredGrounds->take(3) as $ground)
                            <li class="flex items-center justify-between">
                                <span>{{ $ground->name }}</span>
                                <span class="text-white/50">{{ optional($ground->district)->name ?? '—' }}</span>
                            </li>
                        @empty
                            <li>Add a ground to surface it here.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .stat-card {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 28px;
        padding: 24px;
        backdrop-filter: blur(12px);
    }
    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: white;
    }
    .stat-label {
        color: rgba(255, 255, 255, 0.7);
        text-transform: uppercase;
        letter-spacing: 0.3em;
        font-size: 0.75rem;
        margin-top: 0.5rem;
    }
    .activity-card {
        transition: transform 0.3s ease, border-color 0.3s ease;
    }
    .activity-card:hover {
        transform: translateY(-4px);
    }
    .snapshot-card {
        background: rgba(8, 12, 35, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px;
        padding: 24px;
        backdrop-filter: blur(10px);
    }
    .snapshot-title {
        color: rgba(255, 255, 255, 0.6);
        text-transform: uppercase;
        letter-spacing: 0.3em;
        font-size: 0.75rem;
        margin-bottom: 0.5rem;
    }
    .pill-soft {
        padding: 0.35rem 0.9rem;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.25);
        text-transform: uppercase;
        letter-spacing: 0.3em;
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.85);
    }
</style>
