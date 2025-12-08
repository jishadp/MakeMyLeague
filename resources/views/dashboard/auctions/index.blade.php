@extends('layouts.app')

@section('title', 'Auction Dashboard')

@php
    $featureLeague = $liveAuctions->first() ?? $upcomingAuctions->first() ?? $pastAuctions->first();
@endphp

@section('content')
    <section class="relative overflow-hidden bg-gradient-to-r from-emerald-700 via-emerald-600 to-teal-500 text-white">
        <div class="absolute inset-0 opacity-25">
            <div class="absolute -left-10 -top-10 w-52 h-52 bg-white/20 rounded-full blur-3xl"></div>
            <div class="absolute right-0 bottom-0 w-64 h-64 bg-teal-300/30 rounded-full blur-3xl"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative">
            <div class="grid md:grid-cols-2 gap-8 items-center">
                <div>
                    <p class="uppercase tracking-widest text-emerald-100 text-xs font-semibold">Auction Center</p>
                    <h1 class="text-3xl md:text-4xl font-extrabold mt-2 leading-tight">
                        Real-time bidding, history, and upcoming league auctions
                    </h1>
                    <p class="mt-3 text-emerald-50 text-sm md:text-base">
                        Track live rooms, revisit completed auctions, and prepare for leagues that have not started selling players yet.
                    </p>
                    <div class="flex flex-wrap gap-3 mt-5">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-300 animate-pulse"></span>
                            Live: <strong>{{ $liveAuctions->count() }}</strong>
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-200"></span>
                            Upcoming: <strong>{{ $upcomingAuctions->count() }}</strong>
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-200"></span>
                            Past: <strong>{{ $pastAuctions->total() }}</strong>
                        </span>
                    </div>
                </div>
                <div class="bg-white/10 border border-white/20 rounded-2xl p-6 shadow-lg backdrop-blur">
                    @if($featureLeague)
                        <p class="text-sm uppercase text-emerald-100 font-semibold">Featured</p>
                        <h3 class="text-xl font-semibold mt-2">{{ $featureLeague->name }}</h3>
                        <p class="text-emerald-50 text-sm">
                            {{ optional($featureLeague->game)->name }}
                            @if($featureLeague->localBody)
                                • {{ $featureLeague->localBody->name }}
                            @endif
                        </p>
                        <div class="grid grid-cols-3 gap-3 mt-4 text-center">
                            <div class="bg-white/10 rounded-lg py-3">
                                <p class="text-sm text-emerald-100">Teams</p>
                                <p class="text-lg font-bold">{{ $featureLeague->league_teams_count ?? $featureLeague->leagueTeams->count() }}</p>
                            </div>
                            <div class="bg-white/10 rounded-lg py-3">
                                <p class="text-sm text-emerald-100">Players</p>
                                <p class="text-lg font-bold">{{ $featureLeague->league_players_count ?? $featureLeague->leaguePlayers->count() }}</p>
                            </div>
                            <div class="bg-white/10 rounded-lg py-3">
                                <p class="text-sm text-emerald-100">Status</p>
                                @if($featureLeague->isAuctionActive())
                                    <p class="text-lg font-bold text-red-200">Live</p>
                                @elseif($featureLeague->auction_ended_at)
                                    <p class="text-lg font-bold text-amber-200">Completed</p>
                                @else
                                    <p class="text-lg font-bold text-sky-100">Upcoming</p>
                                @endif
                            </div>
                        </div>
                        <div class="mt-5 flex flex-wrap gap-3">
                            @if($featureLeague->isAuctionActive())
                                <a href="{{ route('auctions.live', $featureLeague) }}"
                                   class="inline-flex items-center justify-center gap-2 bg-white text-emerald-700 font-semibold px-4 py-2 rounded-lg shadow hover:translate-y-[-1px] transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                    Watch Live
                                </a>
                            @else
                                <a href="#upcomingSection"
                                   class="inline-flex items-center justify-center gap-2 bg-white text-emerald-700 font-semibold px-4 py-2 rounded-lg shadow hover:translate-y-[-1px] transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Upcoming Schedule
                                </a>
                            @endif
                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center gap-2 border border-white/50 text-white px-4 py-2 rounded-lg hover:bg-white/10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Back to Dashboard
                            </a>
                        </div>
                    @else
                        <div class="text-emerald-50">No leagues available yet.</div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap gap-3 py-4 text-sm font-semibold">
                <button onclick="showSection('live')" id="liveTab"
                        class="tab-button border-b-2 border-transparent text-slate-500 hover:text-emerald-600 hover:border-emerald-200">
                    Live Auctions
                    @if($liveAuctions->count() > 0)
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ $liveAuctions->count() }}
                        </span>
                    @endif
                </button>
                <button onclick="showSection('past')" id="pastTab"
                        class="tab-button border-b-2 border-transparent text-slate-500 hover:text-emerald-600 hover:border-emerald-200">
                    Past Auctions
                    @if($pastAuctions->total() > 0)
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                            {{ $pastAuctions->total() }}
                        </span>
                    @endif
                </button>
                <button onclick="showSection('upcoming')" id="upcomingTab"
                        class="tab-button border-b-2 border-transparent text-slate-500 hover:text-emerald-600 hover:border-emerald-200">
                    Upcoming Auctions
                    @if($upcomingAuctions->count() > 0)
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-800">
                            {{ $upcomingAuctions->count() }}
                        </span>
                    @endif
                </button>
            </div>
        </div>
    </section>

    <section class="py-10 px-4 sm:px-6 lg:px-8 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto space-y-12">
            <div id="liveSection" class="section-content space-y-6">
                @forelse($liveAuctions as $league)
                    @php
                        $soldPlayers = $league->leaguePlayers->where('status', 'sold');
                        $availablePlayers = $league->leaguePlayers->where('status', 'available');
                        $topTeams = $league->leagueTeams->take(4);
                    @endphp
                    <div class="bg-white rounded-2xl shadow-lg border border-emerald-50 overflow-hidden">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">
                                            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span> Live
                                        </span>
                                        <span class="text-xs text-slate-500">
                                            Started {{ optional($league->auction_started_at)->diffForHumans() ?? 'just now' }}
                                        </span>
                                    </div>
                                    <h3 class="text-xl font-semibold text-slate-900">{{ $league->name }}</h3>
                                    <p class="text-sm text-slate-600">
                                        {{ optional($league->game)->name }}
                                        @if($league->localBody)
                                            • {{ $league->localBody->name }}
                                        @endif
                                    </p>
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    <div class="stat-chip bg-emerald-50 text-emerald-700">
                                        <span class="font-bold text-lg">{{ $league->league_teams_count ?? $league->leagueTeams->count() }}</span>
                                        <span class="text-xs uppercase">Teams</span>
                                    </div>
                                    <div class="stat-chip bg-sky-50 text-sky-700">
                                        <span class="font-bold text-lg">{{ $league->league_players_count ?? $league->leaguePlayers->count() }}</span>
                                        <span class="text-xs uppercase">Players</span>
                                    </div>
                                    <div class="stat-chip bg-amber-50 text-amber-700">
                                        <span class="font-bold text-lg">{{ $soldPlayers->count() }}</span>
                                        <span class="text-xs uppercase">Sold</span>
                                    </div>
                                    <div class="stat-chip bg-slate-100 text-slate-700">
                                        <span class="font-bold text-lg">{{ $availablePlayers->count() }}</span>
                                        <span class="text-xs uppercase">Remaining</span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mt-6">
                                @foreach($topTeams as $leagueTeam)
                                    <div class="p-3 rounded-xl border border-slate-100 bg-slate-50/50">
                                        <div class="text-xs text-slate-500 uppercase">Team</div>
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-semibold text-slate-900">{{ $leagueTeam->team->name }}</p>
                                            <span class="text-emerald-600 font-semibold text-sm">₹{{ number_format($leagueTeam->wallet_balance, 0) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @if(auth()->user()->canParticipateInLeagueAuction($league->id))
                                    <a href="{{ route('auction.index', $league) }}"
                                       class="live-action-button bg-emerald-600 text-white hover:bg-emerald-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Join Auction Room
                                    </a>
                                @endif
                                <a href="{{ route('auctions.live', $league) }}"
                                   class="live-action-button bg-red-600 text-white hover:bg-red-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                    Watch Live Feed
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 text-center">
                        <h3 class="text-xl font-semibold text-slate-900 mb-2">No Live Auctions</h3>
                        <p class="text-slate-600 mb-4">You do not have any active auction rooms right now.</p>
                        <a href="#upcomingSection" class="inline-flex items-center gap-2 text-emerald-700 font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7H7m6 5H7m-2 5h14M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"></path>
                            </svg>
                            Browse upcoming leagues
                        </a>
                    </div>
                @endforelse
            </div>

            <div id="pastSection" class="section-content hidden space-y-6">
                @forelse($pastAuctions as $league)
                    @php
                        $soldPlayers = $league->leaguePlayers->where('status', 'sold');
                        $totalRevenue = $soldPlayers->sum('bid_price');
                        $soldCount = $soldPlayers->count();
                        $topTeam = $league->leagueTeams
                            ->sortByDesc(function($team) {
                                return $team->players->where('status', 'sold')->sum('bid_price');
                            })
                            ->first();
                    @endphp
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-semibold">
                                        Completed
                                    </span>
                                    @if($league->auction_ended_at)
                                        <span class="text-xs text-slate-500">
                                            Ended {{ $league->auction_ended_at->format('M d, Y g:i A') }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-xl font-semibold text-slate-900 mt-1">{{ $league->name }}</h3>
                                <p class="text-sm text-slate-600">
                                    {{ optional($league->game)->name }}
                                    @if($league->localBody)
                                        • {{ $league->localBody->name }}
                                    @endif
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <div class="stat-chip bg-emerald-50 text-emerald-700">
                                    <span class="font-bold text-lg">{{ $league->league_teams_count ?? $league->leagueTeams->count() }}</span>
                                    <span class="text-xs uppercase">Teams</span>
                                </div>
                                <div class="stat-chip bg-slate-100 text-slate-700">
                                    <span class="font-bold text-lg">{{ $soldCount }}</span>
                                    <span class="text-xs uppercase">Players Sold</span>
                                </div>
                                <div class="stat-chip bg-amber-50 text-amber-700">
                                    <span class="font-bold text-lg">₹{{ number_format($totalRevenue, 0) }}</span>
                                    <span class="text-xs uppercase">Total Spend</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-5">
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                                <p class="text-xs text-slate-500 uppercase">Top spender</p>
                                <p class="text-sm font-semibold text-slate-900">
                                    {{ $topTeam?->team?->name ?? 'Not recorded' }}
                                </p>
                                @if($topTeam)
                                    <p class="text-emerald-700 font-semibold text-sm">
                                        ₹{{ number_format($topTeam->players->where('status', 'sold')->sum('bid_price'), 0) }}
                                    </p>
                                @endif
                            </div>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                                <p class="text-xs text-slate-500 uppercase">Average bid</p>
                                <p class="text-lg font-bold text-slate-900">
                                    ₹{{ $soldCount ? number_format($totalRevenue / $soldCount, 0) : 0 }}
                                </p>
                                <p class="text-xs text-slate-500">Across sold players</p>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                                <p class="text-xs text-slate-500 uppercase">Ended</p>
                                <p class="text-lg font-semibold text-slate-900">
                                    {{ optional($league->auction_ended_at)->diffForHumans() ?? 'N/A' }}
                                </p>
                                <p class="text-xs text-slate-500">Auction closed</p>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-wrap gap-3">
                            <a href="{{ route('auctions.live.public', $league) }}"
                               class="live-action-button bg-white text-emerald-700 border border-emerald-100 hover:bg-emerald-50">
                                View Broadcast Board
                            </a>
                            <a href="{{ route('auctions.live', $league) }}"
                               class="live-action-button bg-slate-900 text-white hover:bg-black">
                                Review Auction Room
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 text-center">
                        <h3 class="text-xl font-semibold text-slate-900 mb-2">No Past Auctions</h3>
                        <p class="text-slate-600 mb-4">Completed auction summaries will appear here.</p>
                    </div>
                @endforelse

                @if($pastAuctions->hasPages())
                    <div class="pt-2">
                        {{ $pastAuctions->links() }}
                    </div>
                @endif
            </div>

            <div id="upcomingSection" class="section-content hidden space-y-6">
                @forelse($upcomingAuctions as $league)
                    @php
                        $targetDate = $league->start_date ?? $league->auction_started_at;
                    @endphp
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-sky-100 text-sky-700 text-xs font-semibold">
                                        Upcoming
                                    </span>
                                    @if($league->auction_access_granted)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold">
                                            Access Granted
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-semibold">
                                            Awaiting Access
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-xl font-semibold text-slate-900 mt-1">{{ $league->name }}</h3>
                                <p class="text-sm text-slate-600">
                                    {{ optional($league->game)->name }}
                                    @if($league->localBody)
                                        • {{ $league->localBody->name }}
                                    @endif
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <div class="stat-chip bg-emerald-50 text-emerald-700">
                                    <span class="font-bold text-lg">{{ $league->league_teams_count ?? $league->leagueTeams->count() }}</span>
                                    <span class="text-xs uppercase">Teams</span>
                                </div>
                                <div class="stat-chip bg-slate-100 text-slate-700">
                                    <span class="font-bold text-lg">{{ $league->league_players_count ?? $league->leaguePlayers->count() }}</span>
                                    <span class="text-xs uppercase">Registered</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                                <p class="text-xs text-slate-500 uppercase">Auction window</p>
                                @if($targetDate)
                                    <p class="text-lg font-semibold text-slate-900">{{ $targetDate->format('M d, Y') }}</p>
                                    <p class="text-xs text-slate-500">In {{ $targetDate->diffForHumans() }}</p>
                                @else
                                    <p class="text-lg font-semibold text-slate-900">Not scheduled</p>
                                    <p class="text-xs text-slate-500">Set a date to go live</p>
                                @endif
                            </div>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                                <p class="text-xs text-slate-500 uppercase">Capacity</p>
                                <p class="text-lg font-semibold text-slate-900">
                                    {{ $league->leagueTeams->count() * ($league->max_team_players ?? 0) }} slots
                                </p>
                                <p class="text-xs text-slate-500">Based on teams × max players</p>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                                <p class="text-xs text-slate-500 uppercase">Status</p>
                                <p class="text-lg font-semibold text-slate-900">
                                    {{ $league->auction_active ? 'Staging' : 'Preparing' }}
                                </p>
                                <p class="text-xs text-slate-500">Auction has not started</p>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-wrap gap-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">
                                League not auctioned yet
                            </span>
                            <a href="{{ route('auctions.live.public', $league) }}"
                               class="live-action-button bg-white text-emerald-700 border border-emerald-100 hover:bg-emerald-50">
                                Preview Broadcast View
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 text-center">
                        <h3 class="text-xl font-semibold text-slate-900 mb-2">No Upcoming Auctions</h3>
                        <p class="text-slate-600 mb-4">Once leagues schedule their auctions, they will appear here.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <style>
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .section-content { animation: fadeIn 0.2s ease-in-out; }
        .tab-button { padding: 0.75rem 0.25rem; transition: all 0.2s ease; }
        .stat-chip {
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            display: inline-flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.15rem;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
        }
        .live-action-button {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.6rem 1rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }
    </style>

    <script>
        function showSection(sectionName) {
            document.querySelectorAll('.section-content').forEach(section => {
                section.classList.add('hidden');
            });

            document.querySelectorAll('.tab-button').forEach(tab => {
                tab.classList.remove('border-emerald-500', 'text-emerald-700', 'bg-emerald-50');
                tab.classList.add('border-transparent', 'text-slate-500');
            });

            const targetSection = document.getElementById(sectionName + 'Section');
            if (targetSection) {
                targetSection.classList.remove('hidden');
            }

            const activeTab = document.getElementById(sectionName + 'Tab');
            if (activeTab) {
                activeTab.classList.remove('border-transparent', 'text-slate-500');
                activeTab.classList.add('border-emerald-500', 'text-emerald-700', 'bg-emerald-50');
            }
        }

        const defaultSection = '{{ $liveAuctions->isNotEmpty() ? 'live' : ($upcomingAuctions->isNotEmpty() ? 'upcoming' : 'past') }}';
        showSection(defaultSection);

        setInterval(function() {
            const liveSection = document.getElementById('liveSection');
            if (liveSection && !liveSection.classList.contains('hidden') && {{ $liveAuctions->count() }} > 0) {
                location.reload();
            }
        }, 8000);
    </script>
@endsection
