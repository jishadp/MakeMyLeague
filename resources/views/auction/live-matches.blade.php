@extends('layouts.app')

@section('title', 'League Live Matches')

@section('content')
<div class="min-h-screen bg-zinc-950 text-white font-sans selection:bg-orange-500/30">
    <!-- Hero Header -->
    <div class="relative bg-zinc-900 border-b border-orange-500/10 py-8 sm:py-12">
        <div class="absolute inset-0 bg-gradient-to-br from-orange-500/5 via-transparent to-zinc-900/50"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                        <p class="text-xs font-bold uppercase tracking-wider text-orange-400">Match Centre</p>
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                        Live Matches <span class="text-orange-500">&</span> Scores
                    </h1>
                    <p class="mt-2 text-zinc-400 max-w-xl text-sm sm:text-base">
                        Follow real-time updates, check upcoming fixtures, and view past match results across all leagues.
                    </p>
                </div>
                <div>
                     <a href="{{ route('leagues.index') }}" 
                       class="group inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 text-sm font-semibold hover:bg-zinc-700 hover:text-white transition-all border border-white/5 hover:border-orange-500/30 shadow-lg shadow-black/20">
                        <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
                        <span>Back to Leagues</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ activeGame: '{{ $games->first() ?? 'Football' }}' }">
        
        <!-- Game Category Tabs -->
        @if($games->count() > 0)
            <div class="flex overflow-x-auto pb-4 gap-2 mb-8 hide-scrollbar">
                @foreach($games as $game)
                    <button 
                        @click="activeGame = '{{ $game }}'"
                        :class="activeGame === '{{ $game }}' 
                            ? 'bg-orange-600 text-white shadow-lg shadow-orange-500/20 border-orange-500 ring-1 ring-white/20' 
                            : 'bg-zinc-900 text-zinc-400 hover:text-white hover:bg-zinc-800 border-white/5'"
                        class="flex px-6 py-3 rounded-xl text-sm font-bold whitespace-nowrap transition-all duration-200 border items-center gap-2">
                        
                        @if(Str::contains(Str::lower($game), 'football') || Str::contains(Str::lower($game), 'soccer'))
                            <i class="fa-regular fa-futbol"></i>
                        @elseif(Str::contains(Str::lower($game), 'cricket'))
                             <i class="fa-solid fa-baseball-bat-ball"></i>
                        @else
                             <i class="fa-solid fa-trophy"></i>
                        @endif
                        
                        {{ $game }}
                    </button>
                @endforeach
            </div>
        @else
             <div class="text-center py-12 rounded-2xl border border-dashed border-zinc-800 bg-zinc-900/30">
                <i class="fa-regular fa-calendar-xmark text-4xl text-zinc-600 mb-4"></i>
                <h3 class="text-lg font-semibold text-zinc-300">No Matches Found</h3>
                <p class="text-zinc-500 mt-1">There are no active leagues with matches at the moment.</p>
            </div>
        @endif

        <!-- League Cards Container -->
        <div class="space-y-8">
            @forelse($leaguesByGame as $gameName => $leagues)
                <div x-show="activeGame === '{{ $gameName }}'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                    
                    @foreach($leagues as $league)
                        @php
                            $leagueFixtures = $fixturesByLeague->get($league->id) ?? collect();
                            // Group fixtures by status for this league
                            $liveMatches = $leagueFixtures->where('status', 'in_progress');
                            $upcomingMatches = $leagueFixtures->whereIn('status', ['scheduled', 'unscheduled']);
                            $pastMatches = $leagueFixtures->where('status', 'completed');
                        @endphp

                        <!-- League Card -->
                        <div class="bg-zinc-900 rounded-2xl border border-white/5 overflow-hidden mb-8 shadow-xl" x-data="{ activeTab: 'live' }">
                            
                            <!-- League Header -->
                            <div class="p-5 sm:p-6 border-b border-white/5 flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center bg-zinc-800/30">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-zinc-800 flex items-center justify-center border border-white/5 shadow-inner flex-shrink-0 text-2xl">
                                        {{-- Logo Logic --}}
                                        @if($league->logo)
                                            <img src="{{ url(Storage::url($league->logo)) }}" class="w-full h-full object-cover rounded-xl" alt="{{ $league->name }}">
                                        @else
                                            <i class="fa-solid fa-shield-halved text-orange-500"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold text-white">{{ $league->name }}</h2>
                                        <div class="flex items-center gap-3 text-xs sm:text-sm text-zinc-400 mt-1">
                                            @if($league->localBody && $league->localBody->district)
                                                <span class="flex items-center gap-1"><i class="fa-solid fa-location-dot"></i> {{ $league->localBody->district->name }}</span>
                                                <span class="text-zinc-600">•</span>
                                            @endif
                                            <span>{{ $leagueFixtures->count() }} Matches</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                     <a href="{{ route('leagues.public-teams', $league->slug) }}" class="px-3 sm:px-4 py-2 rounded-lg bg-zinc-800 text-zinc-400 text-xs font-semibold hover:bg-zinc-700 hover:text-white transition-colors border border-white/5">
                                        View Teams
                                    </a>
                                </div>
                            </div>

                            <!-- Match Type Tabs -->
                            <div class="flex border-b border-white/5 bg-zinc-900/50 px-2 sm:px-6 overflow-x-auto hide-scrollbar">
                                <button @click="activeTab = 'live'" 
                                    class="relative px-4 sm:px-6 py-4 text-sm font-semibold transition-colors flex items-center gap-2 whitespace-nowrap"
                                    :class="activeTab === 'live' ? 'text-white' : 'text-zinc-500 hover:text-zinc-300'">
                                    <span>Live</span>
                                    @if($liveMatches->count() > 0)
                                        <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold animate-pulse">{{ $liveMatches->count() }}</span>
                                    @endif
                                    <div x-show="activeTab === 'live'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-orange-500 rounded-t-full" x-transition></div>
                                </button>
                                <button @click="activeTab = 'upcoming'" 
                                    class="relative px-4 sm:px-6 py-4 text-sm font-semibold transition-colors flex items-center gap-2 whitespace-nowrap"
                                    :class="activeTab === 'upcoming' ? 'text-white' : 'text-zinc-500 hover:text-zinc-300'">
                                    <span>Upcoming</span>
                                    @if($upcomingMatches->count() > 0)
                                        <span class="bg-zinc-700 text-zinc-300 text-[10px] px-1.5 py-0.5 rounded-full">{{ $upcomingMatches->count() }}</span>
                                    @endif
                                    <div x-show="activeTab === 'upcoming'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-orange-500 rounded-t-full" x-transition></div>
                                </button>
                                <button @click="activeTab = 'past'" 
                                    class="relative px-4 sm:px-6 py-4 text-sm font-semibold transition-colors whitespace-nowrap"
                                    :class="activeTab === 'past' ? 'text-white' : 'text-zinc-500 hover:text-zinc-300'">
                                    Past Results
                                    <div x-show="activeTab === 'past'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-orange-500 rounded-t-full" x-transition></div>
                                </button>
                            </div>

                            <!-- Match Lists -->
                            <div class="p-4 sm:p-6 min-h-[150px] bg-zinc-950/30">

                                <!-- LIVE Matches Section -->
                                <div x-show="activeTab === 'live'" x-transition:enter="transition ease-out duration-200 opacity-0" x-transition:enter-end="opacity-100">
                                    @if($liveMatches->isEmpty())
                                        <div class="flex flex-col items-center justify-center py-10 text-zinc-600">
                                            <i class="fa-solid fa-stopwatch text-3xl mb-3 opacity-30"></i>
                                            <p class="text-sm font-medium">No live matches at the moment</p>
                                        </div>
                                    @else
                                        <div class="grid gap-4 sm:grid-cols-1 lg:grid-cols-2">
                                            @foreach($liveMatches as $match)
                                                @php
                                                    $homeTeam = $match->homeTeam?->team;
                                                    $awayTeam = $match->awayTeam?->team;
                                                @endphp
                                                <div class="bg-zinc-800/40 rounded-xl border border-white/5 relative overflow-hidden group hover:border-orange-500/20 transition-all">
                                                    
                                                    <!-- Clickable Card Wrapper for Public View -->
                                                    <a href="{{ route('matches.live', $match->slug) }}" class="block p-4 sm:p-5">
                                                        <!-- Live Indicator -->
                                                        <div class="absolute top-0 left-1/2 -translate-x-1/2 bg-zinc-900/90 text-red-500 text-[10px] font-bold px-3 py-1 rounded-b-lg border-x border-b border-white/5 shadow-sm z-10 flex items-center gap-1.5">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                                            LIVE
                                                        </div>

                                                        <div class="flex items-center justify-between mt-3">
                                                            <!-- Home -->
                                                            <div class="flex-1 flex flex-col items-center text-center gap-2">
                                                                <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-zinc-700/50 flex items-center justify-center p-2 border border-white/5">
                                                                    @if($homeTeam && $homeTeam->logo)
                                                                        <img src="{{ url(Storage::url($homeTeam->logo)) }}" class="w-full h-full object-contain" alt="{{ $homeTeam->name }}">
                                                                    @else
                                                                        <span class="text-lg font-bold text-zinc-500">{{ substr($homeTeam?->name ?? 'H', 0, 1) }}</span>
                                                                    @endif
                                                                </div>
                                                                <h3 class="text-sm sm:text-base font-bold text-white leading-tight line-clamp-2">{{ $homeTeam?->name ?? 'Home' }}</h3>
                                                            </div>

                                                            <!-- Score -->
                                                            <div class="px-4 flex flex-col items-center">
                                                                <div class="text-3xl sm:text-4xl font-black text-white tracking-widest tabular-nums">
                                                                    {{ $match->home_score ?? 0 }}<span class="text-zinc-600 mx-1">:</span>{{ $match->away_score ?? 0 }}
                                                                </div>
                                                                <span class="text-xs text-orange-400 font-medium mt-1 bg-orange-500/10 px-2 py-0.5 rounded border border-orange-500/20">
                                                                    In Progress
                                                                </span>
                                                            </div>

                                                            <!-- Away -->
                                                            <div class="flex-1 flex flex-col items-center text-center gap-2">
                                                                <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-zinc-700/50 flex items-center justify-center p-2 border border-white/5">
                                                                    @if($awayTeam && $awayTeam->logo)
                                                                        <img src="{{ url(Storage::url($awayTeam->logo)) }}" class="w-full h-full object-contain" alt="{{ $awayTeam->name }}">
                                                                    @else
                                                                        <span class="text-lg font-bold text-zinc-500">{{ substr($awayTeam?->name ?? 'A', 0, 1) }}</span>
                                                                    @endif
                                                                </div>
                                                                <h3 class="text-sm sm:text-base font-bold text-white leading-tight line-clamp-2">{{ $awayTeam?->name ?? 'Away' }}</h3>
                                                            </div>
                                                        </div>
                                                    </a>

                                                    <!-- Scorer Action (Outside anchor to prevent conflict, or styled as button inside) -->
                                                    <!-- Ideally actions should not be inside the main card anchor. We'll put it below match content but inside the container -->
                                                    @auth
                                                        @if((auth()->id() === $match->scorer_id) || auth()->user()->canManageLeague($league->id))
                                                            <div class="px-4 sm:px-5 pb-4 sm:pb-5 pt-0 flex justify-center sticky-action z-20 relative">
                                                                <a href="{{ route('scorer.console', $match->slug) }}" class="inline-flex items-center gap-2 px-6 py-2 rounded-full bg-orange-600 hover:bg-orange-500 text-white text-sm font-bold shadow-lg shadow-orange-600/20 transition-all transform hover:scale-105">
                                                                    <i class="fa-solid fa-pen-to-square"></i> Score Match
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @endauth
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- UPCOMING Matches Section -->
                                <div x-show="activeTab === 'upcoming'" style="display: none;" x-transition:enter="transition ease-out duration-200 opacity-0" x-transition:enter-end="opacity-100">
                                    @if($upcomingMatches->isEmpty())
                                        <div class="flex flex-col items-center justify-center py-10 text-zinc-600">
                                            <i class="fa-regular fa-calendar text-3xl mb-3 opacity-30"></i>
                                            <p class="text-sm font-medium">No upcoming matches scheduled</p>
                                        </div>
                                    @else
                                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                            @foreach($upcomingMatches as $match)
                                                @php
                                                    $homeTeam = $match->homeTeam?->team;
                                                    $awayTeam = $match->awayTeam?->team;
                                                @endphp
                                                <div class="bg-zinc-800/20 rounded-xl border border-white/5 flex flex-col hover:bg-zinc-800/40 hover:border-orange-500/20 transition-all group relative">
                                                    <!-- Link Wrapper -->
                                                    <a href="{{ route('matches.live', $match->slug) }}" class="block p-4 flex-grow">
                                                        <div class="flex items-center justify-between text-xs text-zinc-500 mb-3">
                                                            <span class="group-hover:text-zinc-400 transition-colors">{{ $match->match_date ? $match->match_date->format('D, M d') : 'Date TBD' }}</span>
                                                            <span class="group-hover:text-zinc-400 transition-colors">{{ $match->match_time ? $match->match_time->format('h:i A') : 'Time TBD' }}</span>
                                                        </div>
                                                        <div class="flex items-center justify-between gap-4 mt-auto">
                                                            <div class="flex items-center gap-3">
                                                                 @if($homeTeam && $homeTeam->logo)
                                                                    <img src="{{ url(Storage::url($homeTeam->logo)) }}" class="w-8 h-8 object-contain" alt="">
                                                                @else
                                                                    <div class="w-8 h-8 rounded-full bg-zinc-700 flex items-center justify-center text-xs font-bold text-zinc-400">{{ substr($homeTeam?->name ?? 'H', 0, 1) }}</div>
                                                                @endif
                                                                <span class="font-semibold text-zinc-300 text-sm line-clamp-1 group-hover:text-white transition-colors">{{ $homeTeam?->name ?? 'TBD' }}</span>
                                                            </div>
                                                            <span class="text-xs font-bold text-zinc-600">VS</span>
                                                            <div class="flex items-center gap-3 justify-end text-right">
                                                                <span class="font-semibold text-zinc-300 text-sm line-clamp-1 group-hover:text-white transition-colors">{{ $awayTeam?->name ?? 'TBD' }}</span>
                                                                 @if($awayTeam && $awayTeam->logo)
                                                                    <img src="{{ url(Storage::url($awayTeam->logo)) }}" class="w-8 h-8 object-contain" alt="">
                                                                @else
                                                                    <div class="w-8 h-8 rounded-full bg-zinc-700 flex items-center justify-center text-xs font-bold text-zinc-400">{{ substr($awayTeam?->name ?? 'A', 0, 1) }}</div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </a>

                                                    <!-- Upcoming Scorer Action -->
                                                    @auth
                                                        @if((auth()->id() === $match->scorer_id) || auth()->user()->canManageLeague($league->id))
                                                            <div class="px-4 pb-4 pt-0 z-20 relative">
                                                                <a href="{{ route('scorer.console', $match->slug) }}" class="block w-full text-center py-2 rounded-lg bg-zinc-800 hover:bg-orange-600 text-orange-500 hover:text-white text-xs font-bold border border-orange-500/20 hover:border-orange-500/50 transition-all">
                                                                    <i class="fa-solid fa-pen-to-square"></i> Score Match
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @endauth
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- PAST Matches Section -->
                                <div x-show="activeTab === 'past'" style="display: none;" x-transition:enter="transition ease-out duration-200 opacity-0" x-transition:enter-end="opacity-100">
                                    @if($pastMatches->isEmpty())
                                        <div class="flex flex-col items-center justify-center py-10 text-zinc-600">
                                            <i class="fa-solid fa-history text-3xl mb-3 opacity-30"></i>
                                            <p class="text-sm font-medium">No completed matches found</p>
                                        </div>
                                    @else
                                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                            @foreach($pastMatches as $match)
                                                 @php
                                                    $homeTeam = $match->homeTeam?->team;
                                                    $awayTeam = $match->awayTeam?->team;
                                                    $winnerId = null;
                                                    if($match->home_score > $match->away_score) $winnerId = $match->home_team_id;
                                                    elseif($match->away_score > $match->home_score) $winnerId = $match->away_team_id;
                                                @endphp
                                                <!-- Link Wrapper for Past Matches too -->
                                                <a href="{{ route('matches.live', $match->slug) }}" class="bg-zinc-800/20 rounded-xl border border-white/5 p-4 hover:border-white/10 transition-colors block">
                                                    <div class="flex justify-between items-center mb-4 border-b border-white/5 pb-2">
                                                        <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider bg-zinc-800 px-2 py-0.5 rounded">Full Time</span>
                                                        <span class="text-xs text-zinc-500">{{ $match->match_date ? $match->match_date->format('d M') : '' }}</span>
                                                    </div>
                                                    
                                                    <div class="space-y-3">
                                                        <!-- Home Row -->
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex items-center gap-3">
                                                                @if($homeTeam && $homeTeam->logo)
                                                                    <img src="{{ url(Storage::url($homeTeam->logo)) }}" class="w-6 h-6 object-contain grayscale opacity-80" alt="">
                                                                @else
                                                                    <div class="w-6 h-6 rounded-full bg-zinc-700"></div>
                                                                @endif
                                                                <span class="text-sm font-medium {{ $winnerId == $match->home_team_id ? 'text-white' : 'text-zinc-500' }}">{{ $homeTeam?->name ?? 'Home' }}</span>
                                                            </div>
                                                            <span class="text-base font-bold {{ $winnerId == $match->home_team_id ? 'text-orange-500' : 'text-zinc-600' }}">{{ $match->home_score ?? 0 }}</span>
                                                        </div>
                                                        
                                                        <!-- Away Row -->
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex items-center gap-3">
                                                                @if($awayTeam && $awayTeam->logo)
                                                                    <img src="{{ url(Storage::url($awayTeam->logo)) }}" class="w-6 h-6 object-contain grayscale opacity-80" alt="">
                                                                @else
                                                                    <div class="w-6 h-6 rounded-full bg-zinc-700"></div>
                                                                @endif
                                                                <span class="text-sm font-medium {{ $winnerId == $match->away_team_id ? 'text-white' : 'text-zinc-500' }}">{{ $awayTeam?->name ?? 'Away' }}</span>
                                                            </div>
                                                            <span class="text-base font-bold {{ $winnerId == $match->away_team_id ? 'text-orange-500' : 'text-zinc-600' }}">{{ $match->away_score ?? 0 }}</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            @empty
                <!-- Empty State -->
            @endforelse
        </div>
    </div>
</div>
@endsection
