@extends('layouts.app')

@section('title', 'Live Matches')

@section('content')
<div class="min-h-screen bg-slate-950 text-white py-6 sm:py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-emerald-300 font-semibold">League Matches</p>
                <h1 class="text-2xl sm:text-3xl font-bold">Live & Upcoming</h1>
                <p class="text-slate-400 text-sm mt-1">Organizers and admins can jump into fixtures for quick score updates.</p>
            </div>
            <a href="{{ route('leagues.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-800 text-white text-sm font-semibold hover:bg-slate-700">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back to leagues
            </a>
        </div>

        @forelse($leagues as $league)
            @php
                $leagueFixtures = $fixtures[$league->id] ?? collect();
                $headline = $leagueFixtures->first();
                $upcoming = $leagueFixtures->skip(1)->take(4);
                $homeTeam = $headline?->homeTeam?->team;
                $awayTeam = $headline?->awayTeam?->team;
                $homeLogo = $homeTeam?->logo ? asset('storage/' . $homeTeam->logo) : null;
                $awayLogo = $awayTeam?->logo ? asset('storage/' . $awayTeam->logo) : null;
                $kickoff = $headline?->match_time ? \Illuminate\Support\Carbon::parse($headline->match_time) : null;
                $statusLabel = [
                    'in_progress' => 'Live',
                    'scheduled' => 'Scheduled',
                    'unscheduled' => 'Draft',
                    'completed' => 'Full-time',
                ][$headline->status ?? ''] ?? 'Draft';
                $statusColor = match($headline->status ?? 'unscheduled') {
                    'in_progress' => 'text-emerald-300 border-emerald-400/40 bg-emerald-500/10',
                    'scheduled' => 'text-sky-300 border-sky-400/40 bg-sky-500/10',
                    'completed' => 'text-slate-200 border-slate-500/40 bg-slate-600/20',
                    default => 'text-slate-300 border-slate-400/40 bg-slate-500/10',
                };
            @endphp
            <div class="rounded-2xl border border-white/10 bg-slate-900/60 p-5 shadow-xl">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400 font-semibold">League</p>
                        <h2 class="text-xl font-bold text-white">{{ $league->name }}</h2>
                        <p class="text-xs text-slate-400">{{ $league->game->name ?? 'Game' }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('leagues.league-match', $league) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-full bg-slate-800 text-white text-xs font-semibold hover:bg-slate-700">
                            <i class="fa-regular fa-calendar" aria-hidden="true"></i> Fixtures
                        </a>
                        <a href="{{ route('auction.control-room', $league) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">
                            <i class="fa-solid fa-sliders-h" aria-hidden="true"></i> Control room
                        </a>
                    </div>
                </div>

                @if($headline)
                    <div class="grid gap-4 sm:grid-cols-[1fr_auto_1fr] items-center mt-4">
                        <div class="flex items-center gap-3">
                            <span class="match-badge" aria-hidden="true">
                                @if($homeLogo)
                                    <img src="{{ $homeLogo }}" alt="{{ $homeTeam?->name ?? 'Home team' }} logo">
                                @else
                                    {{ strtoupper(substr($homeTeam?->name ?? 'H', 0, 1)) }}
                                @endif
                            </span>
                            <div>
                                <p class="text-xs text-slate-400">Home</p>
                                <p class="text-lg font-semibold text-white leading-tight">{{ $homeTeam?->name ?? 'Home TBD' }}</p>
                            </div>
                        </div>
                        <div class="text-center space-y-1">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-semibold border {{ $statusColor }}">
                                {{ $statusLabel }}
                            </div>
                            <div class="flex items-center justify-center gap-3">
                                <span class="text-4xl font-black">{{ $headline->home_score ?? 0 }}</span>
                                <span class="text-lg text-slate-400">vs</span>
                                <span class="text-4xl font-black">{{ $headline->away_score ?? 0 }}</span>
                            </div>
                            <p class="text-xs text-slate-400">
                                @if($headline->match_date)
                                    {{ $headline->match_date?->format('D, d M') }}
                                    @if($kickoff)
                                        • {{ $kickoff->format('h:i A') }}
                                    @endif
                                @else
                                    Kickoff TBD
                                @endif
                            </p>
                            <div class="flex items-center justify-center">
                                @if($headline->status == 'in_progress' && $headline->started_at)
                                     <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-800 text-slate-100 text-[12px] font-semibold border border-white/10">
                                         <span class="text-xs font-bold text-emerald-300" x-data="{ time: '00:00', start: '{{ $headline->started_at }}' }" x-init="
                                            setInterval(() => {
                                                let startTime = new Date(start).getTime();
                                                let now = new Date().getTime();
                                                let diff = Math.floor((now - startTime) / 1000 / 60);
                                                time = diff + '\'';
                                            }, 1000);
                                         "><span x-text="time"></span></span>
                                         
                                         @php
                                             $lastEvent = $headline->events->first();
                                         @endphp
                                         @if($lastEvent)
                                             <span>
                                                 {{ $lastEvent->minute }}' 
                                                 @if($lastEvent->event_type == 'GOAL') ⚽ @elseif($lastEvent->event_type == 'RED_CARD') 🟥 @endif 
                                                 {{ $lastEvent->player->user->name ?? $lastEvent->player_name ?? 'Event' }}
                                             </span>
                                         @else
                                             <span>Live</span>
                                         @endif
                                     </span>
                                @elseif($headline->status == 'completed')
                                     <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-800 text-slate-100 text-[12px] font-semibold border border-white/10">FT</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3 justify-end">
                            <div class="text-right">
                                <p class="text-xs text-slate-400">Away</p>
                                <p class="text-lg font-semibold text-white leading-tight">{{ $awayTeam?->name ?? 'Away TBD' }}</p>
                            </div>
                            <span class="match-badge" aria-hidden="true">
                                @if($awayLogo)
                                    <img src="{{ $awayLogo }}" alt="{{ $awayTeam?->name ?? 'Away team' }} logo">
                                @else
                                    {{ strtoupper(substr($awayTeam?->name ?? 'A', 0, 1)) }}
                                @endif
                            </span>
                        </div>
                    </div>
                @else
                    <div class="mt-3 p-4 rounded-xl bg-slate-900/70 border border-slate-800 text-slate-300 text-sm">
                        No live or upcoming fixtures yet. Generate fixtures to manage live matches.
                    </div>
                @endif

                @if($upcoming->isNotEmpty())
                    <div class="match-mini-list mt-4">
                        @foreach($upcoming as $fixture)
                            @php
                                $miniHome = $fixture->homeTeam?->team;
                            $miniAway = $fixture->awayTeam?->team;
                            $miniKickoff = $fixture->match_time ? \Illuminate\Support\Carbon::parse($fixture->match_time) : null;
                        @endphp
                        <div class="match-mini-card">
                            <div class="flex items-center justify-between gap-2">
                                <div class="fixture-time">
                                    @if($fixture->match_date)
                                            {{ $fixture->match_date?->format('D, d M') }}
                                        @else
                                            Date TBC
                                        @endif
                                        @if($miniKickoff)
                                            • {{ $miniKickoff->format('h:i A') }}
                                        @endif
                                    </div>
                                    <span class="badge-chip badge-upcoming">{{ ucfirst(str_replace('_',' ', $fixture->status)) }}</span>
                                </div>
                                <div class="fixture-teams">
                                    {{ $miniHome?->name ?? 'Home TBD' }} vs {{ $miniAway?->name ?? 'Away TBD' }}
                                </div>
                                @if($fixture->league?->name)
                                    <div class="fixture-group">{{ $fixture->league->name }}</div>
                                @endif
                                @if($fixture->venue)
                                    <div class="fixture-group">{{ $fixture->venue }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-xl border border-white/10 bg-slate-900/70 p-5 text-sm text-slate-300">
                You don’t have any leagues to manage matches. Create or get assigned to a league.
            </div>
        @endforelse


    </div>
</div>
@endsection
