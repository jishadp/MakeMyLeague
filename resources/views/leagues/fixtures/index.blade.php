@extends('layouts.app')

@section('title', 'Fixtures - ' . $league->name)

@section('content')
<div class="bg-slate-950 text-white">
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 opacity-40 bg-[radial-gradient(circle_at_20%_20%,#22c55e,transparent_25%),radial-gradient(circle_at_80%_0%,#6366f1,transparent_25%),radial-gradient(circle_at_30%_80%,#14b8a6,transparent_20%)]"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-12 relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <p class="uppercase tracking-[0.18em] text-xs font-semibold text-green-200/80">Season {{ $league->season }}</p>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black leading-tight">{{ $league->name }} Fixtures</h1>
                    <p class="text-green-100/80 mt-2 text-sm sm:text-base">Group breakdown, head-to-head cards with retention cores, and marquee buys.</p>
                    <div class="flex items-center gap-3 mt-4 text-xs text-green-100/70">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20">
                            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Live squads locked in
                        </span>
                        <a href="{{ route('leagues.show', $league) }}" class="underline decoration-dotted underline-offset-4 hover:text-white">Back to league</a>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('leagues.fixtures.pdf', $league) }}" class="inline-flex items-center px-4 py-2.5 bg-white/10 backdrop-blur border border-white/20 rounded-xl text-sm font-semibold hover:bg-white/15">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            @if(isset($topBoughtOverall) && $topBoughtOverall->count() > 0)
            <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($topBoughtOverall as $player)
                    <div class="rounded-2xl bg-white/5 border border-white/10 p-4 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-400 to-indigo-500 flex items-center justify-center text-lg font-bold text-slate-900">
                            {{ strtoupper(substr($player->player->name ?? 'P', 0, 2)) }}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-white">{{ $player->player->name ?? 'Player' }}</p>
                            <p class="text-xs text-white/70">{{ $player->player?->position?->name ?? 'Role' }} • ₹{{ number_format($player->bid_price ?? 0) }}</p>
                            <p class="text-[11px] text-emerald-200/90">{{ $player->leagueTeam?->team?->name }}</p>
                        </div>
                        <span class="text-[11px] px-2 py-1 rounded-full bg-emerald-500/20 text-emerald-100 border border-emerald-400/30">Top Buy</span>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-8">
        @if($fixtures->count() > 0)
            @foreach($fixtures as $groupName => $groupFixtures)
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-white/60">Group</p>
                            <h2 class="text-2xl font-bold">{{ $groupName }}</h2>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/5 border border-white/10 text-white/80">
                            {{ $groupFixtures->count() }} {{ Str::plural('Match', $groupFixtures->count()) }}
                        </span>
                    </div>

                    <div class="grid gap-4">
                        @foreach($groupFixtures as $fixture)
                            @php
                                $homeRetention = $retentionByTeam->get($fixture->home_team_id) ?? collect();
                                $awayRetention = $retentionByTeam->get($fixture->away_team_id) ?? collect();
                                $homeTopBuy = optional($topBoughtByTeam->get($fixture->home_team_id))->first();
                                $awayTopBuy = optional($topBoughtByTeam->get($fixture->away_team_id))->first();
                            @endphp
                            <div class="rounded-2xl bg-white text-slate-900 shadow-xl ring-1 ring-slate-100 overflow-hidden">
                                <div class="bg-gradient-to-r from-emerald-50 via-white to-indigo-50 px-4 py-3 flex items-center justify-between border-b border-slate-100">
                                    <div class="flex items-center gap-2">
                                        <span class="h-2 w-2 rounded-full {{ $fixture->status === 'completed' ? 'bg-emerald-400' : ($fixture->status === 'in_progress' ? 'bg-amber-400 animate-pulse' : 'bg-slate-300') }}"></span>
                                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-600">{{ ucfirst(str_replace('_',' ', $fixture->status)) }}</p>
                                    </div>
                                    <div class="text-xs text-slate-500 flex items-center gap-2">
                                        @if($fixture->match_date)
                                            <span>{{ $fixture->match_date->format('M d, H:i') }}</span>
                                        @else
                                            <span>TBD</span>
                                        @endif
                                        @if($fixture->venue)
                                            <span class="text-slate-400">•</span>
                                            <span>{{ $fixture->venue }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="p-4 sm:p-6">
                                    <div class="grid sm:grid-cols-5 gap-4 sm:gap-6 items-center">
                                        <div class="sm:col-span-2 flex flex-col sm:flex-row sm:items-center gap-3">
                                            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-lg">
                                                {{ strtoupper(substr($fixture->homeTeam->team->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="text-lg font-bold text-slate-900">{{ $fixture->homeTeam->team->name }}</p>
                                                <p class="text-xs text-slate-500">Home</p>
                                                @if($homeTopBuy)
                                                    <p class="text-xs text-emerald-700 font-semibold mt-1">Top buy: {{ $homeTopBuy->player->name ?? 'Player' }} • ₹{{ number_format($homeTopBuy->bid_price ?? 0) }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            @if($fixture->home_score !== null || $fixture->away_score !== null)
                                                <div class="text-3xl font-black text-slate-900">{{ $fixture->home_score ?? 0 }} - {{ $fixture->away_score ?? 0 }}</div>
                                            @else
                                                <div class="inline-flex items-center justify-center px-4 py-2 rounded-full bg-slate-100 text-xs font-semibold text-slate-600">VS</div>
                                            @endif
                                            <p class="text-[11px] text-slate-500 mt-1">Head-to-head</p>
                                        </div>

                                        <div class="sm:col-span-2 flex flex-col sm:flex-row sm:items-center gap-3 sm:justify-end text-right">
                                            <div class="sm:order-2 w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-lg">
                                                {{ strtoupper(substr($fixture->awayTeam->team->name, 0, 2)) }}
                                            </div>
                                            <div class="sm:order-1 text-left sm:text-right">
                                                <p class="text-lg font-bold text-slate-900">{{ $fixture->awayTeam->team->name }}</p>
                                                <p class="text-xs text-slate-500">Away</p>
                                                @if($awayTopBuy)
                                                    <p class="text-xs text-indigo-700 font-semibold mt-1">Top buy: {{ $awayTopBuy->player->name ?? 'Player' }} • ₹{{ number_format($awayTopBuy->bid_price ?? 0) }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 grid sm:grid-cols-2 gap-3">
                                        <div class="rounded-xl border border-slate-200 p-3">
                                            <div class="flex items-center justify-between mb-2">
                                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Retention core</p>
                                                <span class="text-[11px] text-slate-400">{{ $homeRetention->count() }} players</span>
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                @forelse($homeRetention as $player)
                                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-medium border border-emerald-100">
                                                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                                        {{ $player->player?->name ?? 'Player' }}
                                                    </span>
                                                @empty
                                                    <p class="text-xs text-slate-400">No retention players listed.</p>
                                                @endforelse
                                            </div>
                                        </div>
                                        <div class="rounded-xl border border-slate-200 p-3">
                                            <div class="flex items-center justify-between mb-2">
                                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Retention core</p>
                                                <span class="text-[11px] text-slate-400">{{ $awayRetention->count() }} players</span>
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                @forelse($awayRetention as $player)
                                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-medium border border-indigo-100">
                                                        <span class="h-2 w-2 rounded-full bg-indigo-400"></span>
                                                        {{ $player->player?->name ?? 'Player' }}
                                                    </span>
                                                @empty
                                                    <p class="text-xs text-slate-400">No retention players listed.</p>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            <div class="bg-white/5 border border-white/10 rounded-2xl p-10 text-center">
                <div class="w-16 h-16 rounded-full bg-white/10 border border-white/20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-white text-2xl font-bold mb-2">No Fixtures Yet</h3>
                <p class="text-white/70 text-sm mb-4">Generate fixtures to showcase groups, retention cores, and marquee signings.</p>
                @if(auth()->user()->isOrganizer() && $league->user_id === auth()->id())
                    <a href="{{ route('leagues.tournament-setup', $league) }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-white text-slate-900 font-semibold shadow-lg hover:shadow-xl transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Setup Tournament
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
