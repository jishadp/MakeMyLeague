@extends('layouts.app')

@section('title', $player->name . ' | ' . config('app.name'))

@php
    $profilePhoto = $player->photo ? asset('storage/' . $player->photo) : null;
    $initials = collect(explode(' ', trim($player->name)))
        ->filter()
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->take(2)
        ->implode('');
    $primaryPosition = $player->position->name ?? 'Cricket Player';
    $locationLabel = optional($player->localBody)->name ?? 'Location unavailable';
    $memberSinceLabel = $player->created_at ? $player->created_at->format('F Y') : 'Unknown';
    $totalLeagues = $player->leaguePlayers->count();
    $totalTeams = $leagueTeams->count();
    $totalAuctions = $recentAuctions->count();
@endphp

@section('content')
<section class="player-hero text-white py-10 sm:py-16">
    <div class="player-hero__blur" aria-hidden="true"></div>
    <div class="player-hero__glow" aria-hidden="true"></div>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 text-white/80">
                <li class="inline-flex items-center">
                    <a href="{{ route('players.index') }}" class="inline-flex items-center text-sm font-medium hover:text-white">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Players
                    </a>
                </li>
                <li aria-current="page" class="flex items-center text-sm font-medium text-white">
                    <svg class="w-6 h-6 text-white/60" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 md:ml-2">{{ $player->name }}</span>
                </li>
            </ol>
        </nav>

        <div class="grid gap-10 lg:grid-cols-2 items-center">
            <div class="space-y-5">
                <div>
                    <p class="text-xs uppercase tracking-[0.45em] text-white/60">Player Profile</p>
                    <h1 class="text-3xl sm:text-4xl font-extrabold">{{ $player->name }}</h1>
                    <p class="text-lg text-white/80">{{ $primaryPosition }}</p>
                </div>

                <div class="flex flex-wrap gap-3 text-sm text-white/80">
                    <span class="pill">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.567 3-3.5S13.657 4 12 4 9 5.567 9 7.5 10.343 11 12 11z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v9" />
                        </svg>
                        {{ $locationLabel }}
                    </span>
                    <span class="pill">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
                            <circle cx="12" cy="12" r="9" stroke-width="2" />
                        </svg>
                        Member since {{ $memberSinceLabel }}
                    </span>
                    @if($player->mobile)
                        <span class="pill">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h2l3.6 7.59a1 1 0 00.9.59h6.9a1 1 0 00.96-.74l1.53-5.5H7" />
                                <circle cx="10.5" cy="19.5" r="1.5" />
                                <circle cx="17.5" cy="19.5" r="1.5" />
                            </svg>
                            Contact available
                        </span>
                    @endif
                </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="stat-card">
                        <p class="text-xs uppercase tracking-[0.4em] text-white/60">Leagues</p>
                        <p class="text-3xl font-black">{{ $totalLeagues }}</p>
                    </div>
                    <div class="stat-card">
                        <p class="text-xs uppercase tracking-[0.4em] text-white/60">Teams</p>
                        <p class="text-3xl font-black">{{ $totalTeams }}</p>
                    </div>
                    <div class="stat-card">
                        <p class="text-xs uppercase tracking-[0.4em] text-white/60">Auctions</p>
                        <p class="text-3xl font-black">{{ $totalAuctions }}</p>
                    </div>
                </div>

                @auth
                    @if(auth()->id() === $player->id || auth()->user()->isOrganizer())
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('players.edit', $player) }}" class="action-button">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Profile
                            </a>

                            @if(auth()->id() === $player->id)
                                <a href="{{ route('profile.show') }}" class="action-button bg-blue-500/20 border-blue-300/40">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    User Profile
                                </a>
                            @endif

                            @if(auth()->user()->isOrganizer())
                                <form action="{{ route('players.destroy', $player) }}" method="POST" onsubmit="return confirm('Delete this player? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-button bg-red-500/20 border-red-300/40">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                @endauth
            </div>

            <div class="relative">
                <div class="player-photo-card">
                    @if($profilePhoto)
                        <img src="{{ $profilePhoto }}" alt="{{ $player->name }}" class="w-full h-full object-cover" loading="lazy">
                    @else
                        <div class="flex items-center justify-center h-full">
                            <span class="text-6xl font-black text-white/80">{{ $initials }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        <div class="rounded-3xl border border-slate-200 p-6 sm:p-10">
            <div class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
                <div>
                    <div class="mb-6">
                        <p class="text-xs uppercase tracking-[0.4em] text-slate-500">Profile Overview</p>
                        <h2 class="text-2xl font-bold text-slate-900">Player Information</h2>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <p class="text-sm text-slate-500">Full Name</p>
                            <p class="text-lg font-semibold text-slate-900">{{ $player->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Game Role</p>
                            <p class="text-lg font-semibold text-slate-900">{{ $primaryPosition }}</p>
                        </div>
                        @if($player->email)
                            <div>
                                <p class="text-sm text-slate-500">Email</p>
                                <p class="text-lg text-slate-900 break-all">{{ $player->email }}</p>
                            </div>
                        @endif
                        @if($player->mobile)
                            <div>
                                <p class="text-sm text-slate-500">Mobile</p>
                                <p class="text-lg text-slate-900">{{ $player->mobile }}</p>
                            </div>
                        @endif
                        @if($player->localBody)
                            <div>
                                <p class="text-sm text-slate-500">Home Base</p>
                                <p class="text-lg text-slate-900">{{ $player->localBody->name }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-slate-500">Member Since</p>
                            <p class="text-lg text-slate-900">{{ $memberSinceLabel }}</p>
                        </div>
                    </div>

                    @if($player->position)
                        <div class="mt-8 rounded-2xl bg-slate-50 p-6">
                            <h3 class="text-lg font-semibold text-slate-900 mb-3">About the {{ $primaryPosition }} role</h3>
                            <p class="text-slate-600 leading-relaxed">
                                @switch($player->position->name)
                                    @case('Batter')
                                        Batters focus on building innings and converting starts into match-winning totals for their teams.
                                        @break
                                    @case('Bowler')
                                        Bowlers are tasked with taking wickets and controlling the scoring rate through disciplined spells.
                                        @break
                                    @case('All-Rounder')
                                        All-rounders contribute across departments, giving squads flexibility in both batting and bowling.
                                        @break
                                    @case('Wicket-Keeper Batter')
                                        Wicket-keeper batters manage duties behind the stumps while anchoring crucial phases with the bat.
                                        @break
                                    @default
                                        This role helps stabilize team combinations and adapts to situational demands.
                                @endswitch
                            </p>
                        </div>
                    @endif
                </div>

                <div class="space-y-6">
                    <div>
                        <p class="text-xs uppercase tracking-[0.4em] text-slate-500">Quick Snapshot</p>
                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="snapshot-card">
                                <p class="text-sm text-slate-500">Latest Activity</p>
                                <p class="text-lg font-semibold text-slate-900">
                                    @if($totalAuctions > 0)
                                        {{ $recentAuctions->first()->created_at->diffForHumans() }}
                                    @else
                                        No recent auctions
                                    @endif
                                </p>
                            </div>
                            <div class="snapshot-card">
                                <p class="text-sm text-slate-500">Current Engagements</p>
                                <p class="text-lg font-semibold text-slate-900">{{ $registeredLeagues->count() }} leagues</p>
                            </div>
                            <div class="snapshot-card">
                                <p class="text-sm text-slate-500">Teams This Season</p>
                                <p class="text-lg font-semibold text-slate-900">{{ $totalTeams }}</p>
                            </div>
                            <div class="snapshot-card">
                                <p class="text-sm text-slate-500">Leagues Played</p>
                                <p class="text-lg font-semibold text-slate-900">{{ $totalLeagues }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 p-5">
                        <p class="text-sm text-slate-500">Contact Status</p>
                        <p class="text-lg font-semibold text-slate-900">
                            {{ $player->mobile ? 'Phone available' : 'Phone not provided' }}
                        </p>
                        <p class="text-sm text-slate-500 mt-2">Reach out through the organizer dashboard for official queries.</p>
                    </div>
                </div>
            </div>
        </div>

        @if($recentAuctions->count() > 0)
            <div class="rounded-3xl border border-slate-200 p-6 sm:p-10">
                <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.4em] text-slate-500">Market Activity</p>
                        <h3 class="text-2xl font-bold text-slate-900">Recent Auction Highlights</h3>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($recentAuctions as $auction)
                        <div class="activity-card">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-slate-900">{{ $auction->leaguePlayer->league->name ?? 'Unknown League' }}</h4>
                                <span class="text-xs text-slate-500">{{ $auction->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="space-y-2 text-sm text-slate-600">
                                <div class="flex justify-between">
                                    <span>Bidding Team</span>
                                    <span class="font-semibold text-slate-900">{{ $auction->leagueTeam->team->name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Bid Amount</span>
                                    <span class="font-semibold text-emerald-600">₹{{ number_format($auction->amount) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Status</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $auction->status === 'won' ? 'bg-emerald-100 text-emerald-800' : ($auction->status === 'ask' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">{{ ucfirst($auction->status) }}</span>
                                </div>
                                @if($auction->leaguePlayer->status === 'sold' && $auction->leaguePlayer->leagueTeam)
                                    <div class="flex justify-between pt-2 border-t border-slate-200">
                                        <span>Final Team</span>
                                        <span class="font-semibold text-indigo-600">{{ $auction->leaguePlayer->leagueTeam->team->name ?? 'N/A' }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($leagueTeams->count() > 0)
            <div class="rounded-3xl border border-slate-200 p-6 sm:p-10">
                <div class="mb-6">
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-500">Team Journey</p>
                    <h3 class="text-2xl font-bold text-slate-900">League Team Highlights</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($leagueTeams as $leaguePlayer)
                        <div class="activity-card">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-slate-900">{{ $leaguePlayer->league->name ?? 'Unknown League' }}</h4>
                                <span class="text-xs text-slate-500">{{ $leaguePlayer->created_at->format('M Y') }}</span>
                            </div>
                            <div class="space-y-2 text-sm text-slate-600">
                                <div class="flex justify-between">
                                    <span>Team</span>
                                    <span class="font-semibold text-slate-900">{{ $leaguePlayer->leagueTeam->team->name ?? 'No Team' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Status</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $leaguePlayer->status === 'sold' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">{{ ucfirst($leaguePlayer->status) }}</span>
                                </div>
                                @if($leaguePlayer->bid_price)
                                    <div class="flex justify-between">
                                        <span>Bid Price</span>
                                        <span class="font-semibold text-emerald-600">₹{{ number_format($leaguePlayer->bid_price) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($registeredLeagues->count() > 0)
            <div class="rounded-3xl border border-slate-200 p-6 sm:p-10">
                <div class="mb-6">
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-500">Registered Leagues</p>
                    <h3 class="text-2xl font-bold text-slate-900">Current Commitments</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($registeredLeagues as $leaguePlayer)
                        <div class="activity-card">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-slate-900">{{ $leaguePlayer->league->name }}</h4>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $leaguePlayer->status === 'pending' ? 'bg-amber-100 text-amber-800' : ($leaguePlayer->status === 'available' ? 'bg-emerald-100 text-emerald-800' : ($leaguePlayer->status === 'sold' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-800')) }}">{{ ucfirst($leaguePlayer->status) }}</span>
                            </div>
                            <div class="space-y-2 text-sm text-slate-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ $leaguePlayer->league->game->name }}</span>
                                </div>
                                @if($leaguePlayer->league->localBody)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                        </svg>
                                        <span>{{ $leaguePlayer->league->localBody->name }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2 10a8 8 0 0113.856-5.857l.5.5-1.414 1.414-.5-.5A6 6 0 104 10h2l-3.5 4L0 10h2z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Updated {{ $leaguePlayer->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="flex justify-center">
            <a href="{{ route('players.index') }}" class="inline-flex items-center px-6 py-3 rounded-2xl font-medium text-white bg-slate-900 hover:bg-slate-800 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Players
            </a>
        </div>
    </div>
</section>

<style>
.player-hero {
    position: relative;
    overflow: hidden;
    background: radial-gradient(circle at top left, rgba(99,102,241,0.45), rgba(15,23,42,0.95));
}

.player-hero__blur,
.player-hero__glow {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.player-hero__blur::before,
.player-hero__blur::after {
    content: '';
    position: absolute;
    width: 360px;
    height: 360px;
    border-radius: 50%;
    filter: blur(120px);
    opacity: 0.45;
}

.player-hero__blur::before {
    top: -120px;
    left: -80px;
    background: rgba(59,130,246,0.9);
}

.player-hero__blur::after {
    bottom: -160px;
    right: -60px;
    background: rgba(236,72,153,0.7);
}

.player-hero__glow {
    background: radial-gradient(circle at 70% 30%, rgba(255,255,255,0.18), transparent 55%);
}

.pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.4rem 0.9rem;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,0.2);
    background: rgba(255,255,255,0.08);
}

.stat-card {
    padding: 1rem;
    border-radius: 24px;
    border: 1px solid rgba(255,255,255,0.15);
    background: rgba(15,23,42,0.35);
    text-align: center;
}

.action-button {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.55rem 1rem;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,0.25);
    background: rgba(255,255,255,0.08);
    font-size: 0.9rem;
    font-weight: 600;
    color: white;
}

.player-photo-card {
    border-radius: 32px;
    overflow: hidden;
    border: 2px solid rgba(255,255,255,0.15);
    min-height: 320px;
    background: radial-gradient(circle at top, rgba(79,70,229,0.4), rgba(15,23,42,0.9));
}

.snapshot-card {
    border-radius: 20px;
    border: 1px solid rgba(148,163,184,0.3);
    padding: 1rem;
    background: white;
}

.activity-card {
    border-radius: 24px;
    border: 1px solid rgba(148,163,184,0.35);
    padding: 1.25rem;
    background: white;
}

@media (max-width: 640px) {
    .action-button {
        width: 100%;
        justify-content: center;
    }

    .activity-card,
    .snapshot-card {
        padding: 1rem;
    }
}
</style>
@endsection
