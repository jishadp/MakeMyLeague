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
                     <a href="{{ route('auctions.live-matches') }}" 
                       class="group inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 text-sm font-semibold hover:bg-zinc-700 hover:text-white transition-all border border-white/5 hover:border-orange-500/30 shadow-lg shadow-black/20">
                        <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
                        <span>Back to Live Matches</span>
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
                    
                    <div class="grid gap-6 sm:grid-cols-1 lg:grid-cols-2">
                    @foreach($leagues as $league)
                        <!-- League Card -->
                        <a href="{{ route('leagues.matches', $league->slug) }}" class="bg-zinc-900 rounded-2xl border border-white/5 overflow-hidden shadow-xl hover:border-orange-500/30 hover:shadow-orange-500/10 transition-all group block relative">
                            
                            <!-- League Header -->
                            <div class="p-6 flex items-center gap-5">
                                <div class="w-16 h-16 rounded-xl bg-zinc-800 flex items-center justify-center border border-white/5 shadow-inner flex-shrink-0 text-3xl group-hover:scale-105 transition-transform duration-300">
                                    {{-- Logo Logic --}}
                                    @if($league->logo)
                                        <img src="{{ url(Storage::url($league->logo)) }}" class="w-full h-full object-cover rounded-xl" alt="{{ $league->name }}">
                                    @else
                                        <i class="fa-solid fa-shield-halved text-orange-500"></i>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h2 class="text-xl font-bold text-white group-hover:text-orange-500 transition-colors">{{ $league->name }}</h2>
                                    <div class="flex items-center gap-3 text-sm text-zinc-400 mt-1.5">
                                        @if($league->localBody && $league->localBody->district)
                                            <span class="flex items-center gap-1.5"><i class="fa-solid fa-location-dot text-zinc-600"></i> {{ $league->localBody->district->name }}</span>
                                            <span class="text-zinc-700">•</span>
                                        @endif
                                        <span class="flex items-center gap-1.5 font-medium text-zinc-300">
                                            @if($league->active_match_count > 0)
                                                <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                                                {{ $league->active_match_count }} Active Matches
                                            @else
                                                <i class="fa-regular fa-futbol text-zinc-600"></i>
                                                View Matches
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="text-zinc-600 group-hover:text-orange-500 transition-colors">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </div>
                            </div>
                        </a>
                    @endforeach
                    </div>
                </div>
            @empty
                <!-- Empty State -->
            @endforelse
        </div>
    </div>
</div>
@endsection
