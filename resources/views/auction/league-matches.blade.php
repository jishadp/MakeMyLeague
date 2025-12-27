@extends('layouts.app')

@section('title', $league->name . ' - Live Matches')

@section('meta_tags')
    <meta property="og:title" content="{{ $league->name }} Live Matches">
    <meta property="og:description" content="Watch live and upcoming matches of {{ $league->name }} on MakeMyLeague.">
    <meta property="og:image" content="{{ $league->logo ? url(Storage::url($league->logo)) : asset('images/default-league.png') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
@endsection

@section('content')
<div class="min-h-screen bg-zinc-950 text-white font-sans selection:bg-orange-500/30" x-data="{ activeTab: 'live' }">

    <!-- League Header -->
    <div class="relative bg-zinc-900 border-b border-orange-500/10 py-8 sm:py-12">
         <div class="absolute inset-0 bg-gradient-to-br from-orange-500/5 via-transparent to-zinc-900/50"></div>
         <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
             <div class="flex flex-col items-center text-center gap-6">
                 <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-zinc-800 flex items-center justify-center border border-white/5 shadow-2xl p-2 relative group">
                     @if($league->logo)
                         <img src="{{ url(Storage::url($league->logo)) }}" class="w-full h-full object-cover rounded-xl shadow-inner" alt="{{ $league->name }}">
                     @else
                         <i class="fa-solid fa-shield-halved text-4xl text-orange-500"></i>
                     @endif
                 </div>
                 
                 <div>
                     <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight leading-tight">{{ $league->name }}</h1>
                     <div class="flex items-center justify-center gap-3 text-sm text-zinc-400 mt-2">
                        @if($league->game)
                            <span class="px-2 py-0.5 rounded bg-zinc-800 border border-white/5 text-xs font-semibold uppercase tracking-wider">{{ $league->game->name }}</span>
                        @endif
                         @if($league->localBody && $league->localBody->district)
                             <span class="flex items-center gap-1"><i class="fa-solid fa-location-dot text-orange-500"></i> {{ $league->localBody->district->name }}</span>
                         @endif
                     </div>
                 </div>

                 <div class="flex items-center gap-3 w-full sm:w-auto">
                    <!-- Share Button -->
                    <button @click="
                        navigator.clipboard.writeText('{{ url()->current() }}');
                        $el.innerHTML = '<i class=\'fa-solid fa-check mr-2\'></i> Copied!';
                        setTimeout(() => $el.innerHTML = '<i class=\'fa-solid fa-share-nodes mr-2\'></i> Share Page', 2000);
                    " class="flex-1 sm:flex-none px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 text-sm font-semibold hover:bg-zinc-700 hover:text-white transition-all border border-white/5 hover:border-orange-500/30">
                        <i class="fa-solid fa-share-nodes mr-2"></i> Share Page
                    </button>
                    
                    <a href="{{ route('auctions.live-matches') }}" class="px-5 py-2.5 rounded-xl bg-zinc-800 text-zinc-300 text-sm font-semibold hover:bg-zinc-700 hover:text-white transition-all border border-white/5">
                        <i class="fa-solid fa-list mr-2"></i> All Leagues
                    </a>
                 </div>
             </div>
         </div>
    </div>

    <!-- Sticky Tabs -->
    <div class="sticky top-0 z-30 bg-zinc-950/80 backdrop-blur-md border-b border-white/5">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            <div class="flex justify-center gap-8">
                @php
                    $liveMatches = $fixtures->where('status', 'in_progress');
                    $upcomingMatches = $fixtures->whereIn('status', ['scheduled', 'unscheduled']);
                    $pastMatches = $fixtures->where('status', 'completed');
                @endphp
                
                <button @click="activeTab = 'live'" 
                    class="relative py-4 text-sm font-bold transition-colors flex items-center gap-2"
                    :class="activeTab === 'live' ? 'text-orange-500' : 'text-zinc-500 hover:text-zinc-300'">
                    LIVE
                    @if($liveMatches->count() > 0)
                        <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full animate-pulse">{{ $liveMatches->count() }}</span>
                    @endif
                    <div x-show="activeTab === 'live'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-orange-500" x-transition></div>
                </button>
                <button @click="activeTab = 'upcoming'" 
                    class="relative py-4 text-sm font-bold transition-colors flex items-center gap-2"
                    :class="activeTab === 'upcoming' ? 'text-orange-500' : 'text-zinc-500 hover:text-zinc-300'">
                    UPCOMING
                    @if($upcomingMatches->count() > 0)
                         <span class="bg-zinc-800 text-zinc-400 text-[10px] px-1.5 py-0.5 rounded-full border border-white/10">{{ $upcomingMatches->count() }}</span>
                    @endif
                    <div x-show="activeTab === 'upcoming'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-orange-500" x-transition></div>
                </button>
                 <button @click="activeTab = 'past'" 
                    class="relative py-4 text-sm font-bold transition-colors"
                    :class="activeTab === 'past' ? 'text-orange-500' : 'text-zinc-500 hover:text-zinc-300'">
                    RESULTS
                    <div x-show="activeTab === 'past'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-orange-500" x-transition></div>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 pb-20">

        <!-- LIVE TAB -->
        <div x-show="activeTab === 'live'" x-transition:enter="transition ease-out duration-300 opacity-0 transform translate-y-2">
            @if($liveMatches->isEmpty())
                 <div class="flex flex-col items-center justify-center py-20 text-zinc-600 rounded-2xl bg-zinc-900/30 border border-dashed border-zinc-800">
                    <div class="w-16 h-16 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-stopwatch text-2xl opacity-50"></i>
                    </div>
                    <p class="text-sm font-medium">No live matches now</p>
                    <p class="text-xs text-zinc-600 mt-1">Check upcoming fixtures</p>
                </div>
            @else
                <div class="space-y-4">
                     @foreach($liveMatches as $match)
                        @php
                            $homeTeam = $match->homeTeam?->team;
                            $awayTeam = $match->awayTeam?->team;
                        @endphp
                        <div class="bg-zinc-900 rounded-2xl border border-white/5 overflow-hidden shadow-xl group hover:border-orange-500/20 transition-all">
                             <a href="{{ route('matches.live', $match->slug) }}" class="block relative">
                                <!-- Status Banner -->
                                <div class="bg-gradient-to-r from-red-600 to-red-500 text-white text-[10px] font-bold px-3 py-1 flex justify-between items-center">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        LIVE NOW
                                    </div>
                                    <span class="opacity-90">{{ $match->match_type ? Str::headline($match->match_type) : 'TBD' }}</span>
                                </div>
                                <div class="p-6">
                                     <div class="flex items-center justify-between">
                                          <!-- Home -->
                                          <div class="flex-1 flex flex-col items-center">
                                              <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-zinc-800 p-3 mb-3 border border-white/5 shadow-inner">
                                                  @if($homeTeam && $homeTeam->logo)
                                                    <img src="{{ url(Storage::url($homeTeam->logo)) }}" class="w-full h-full object-contain" alt="">
                                                  @else
                                                    <div class="w-full h-full flex items-center justify-center text-zinc-600 font-bold text-xl">{{ substr($homeTeam?->name ?? 'H', 0, 1) }}</div>
                                                  @endif
                                              </div>
                                              <h3 class="text-sm sm:text-base font-bold text-center leading-tight">{{ $homeTeam?->name ?? 'Home' }}</h3>
                                          </div>

                                          <!-- Score -->
                                          <div class="px-6 flex flex-col items-center">
                                              <div class="text-4xl sm:text-5xl font-black text-white tabular-nums tracking-tighter">
                                                  {{ $match->home_score ?? 0 }}<span class="text-zinc-700 mx-2">-</span>{{ $match->away_score ?? 0 }}
                                              </div>
                                              <p class="text-xs text-orange-500 font-bold mt-2 animate-pulse">In Progress</p>
                                          </div>

                                          <!-- Away -->
                                          <div class="flex-1 flex flex-col items-center">
                                              <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-zinc-800 p-3 mb-3 border border-white/5 shadow-inner">
                                                  @if($awayTeam && $awayTeam->logo)
                                                    <img src="{{ url(Storage::url($awayTeam->logo)) }}" class="w-full h-full object-contain" alt="">
                                                  @else
                                                    <div class="w-full h-full flex items-center justify-center text-zinc-600 font-bold text-xl">{{ substr($awayTeam?->name ?? 'A', 0, 1) }}</div>
                                                  @endif
                                              </div>
                                              <h3 class="text-sm sm:text-base font-bold text-center leading-tight">{{ $awayTeam?->name ?? 'Away' }}</h3>
                                          </div>
                                     </div>
                                </div>
                             </a>
                             
                             <!-- Scorer Action -->
                             @auth
                                @if((auth()->id() === $match->scorer_id) || auth()->user()->canManageLeague($league->id))
                                    <div class="px-6 pb-6 pt-0 flex justify-center">
                                        <a href="{{ route('scorer.console', $match->slug) }}" class="inline-flex items-center gap-2 px-6 py-2 rounded-full bg-zinc-800 text-orange-500 hover:bg-orange-600 hover:text-white font-bold text-xs uppercase tracking-wider transition-all border border-orange-500/20">
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

        <!-- UPCOMING TAB -->
        <div x-show="activeTab === 'upcoming'" x-transition:enter="transition ease-out duration-300 opacity-0 transform translate-y-2">
             @if($upcomingMatches->isEmpty())
                 <div class="flex flex-col items-center justify-center py-20 text-zinc-600 rounded-2xl bg-zinc-900/30 border border-dashed border-zinc-800">
                    <div class="w-16 h-16 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center mb-4">
                        <i class="fa-regular fa-calendar-xmark text-2xl opacity-50"></i>
                    </div>
                    <p class="text-sm font-medium">No upcoming matches</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($upcomingMatches as $match)
                        @php
                            $homeTeam = $match->homeTeam?->team;
                            $awayTeam = $match->awayTeam?->team;
                        @endphp
                        <div class="bg-zinc-900 rounded-xl border border-white/5 p-4 sm:p-5 flex items-center justify-between group hover:border-orange-500/20 transition-all">
                             <a href="{{ route('matches.live', $match->slug) }}" class="flex-1 flex items-center justify-between">
                                  <div class="flex items-center gap-4 flex-1">
                                       <div class="w-10 h-10 rounded-full bg-zinc-800 p-1.5 border border-white/5 flex-shrink-0">
                                            @if($homeTeam && $homeTeam->logo)
                                                <img src="{{ url(Storage::url($homeTeam->logo)) }}" class="w-full h-full object-contain" alt="">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-xs font-bold text-zinc-600">{{ substr($homeTeam?->name ?? 'H', 0, 1) }}</div>
                                            @endif
                                       </div>
                                       <span class="font-bold text-sm sm:text-base text-zinc-200">{{ $homeTeam?->name ?? 'TBD' }}</span>
                                  </div>
                                  
                                  <div class="px-4 flex flex-col items-center min-w-[100px]">
                                       <span class="text-xs font-bold text-zinc-500 bg-zinc-800 px-2 py-1 rounded mb-1">{{ $match->match_time ? $match->match_time->format('h:i A') : 'TBA' }}</span>
                                       <span class="text-[10px] text-zinc-600 uppercase font-medium">{{ $match->match_date ? $match->match_date->format('M d') : 'Date TBA' }}</span>
                                  </div>

                                  <div class="flex items-center gap-4 flex-1 justify-end">
                                       <span class="font-bold text-sm sm:text-base text-zinc-200 text-right">{{ $awayTeam?->name ?? 'TBD' }}</span>
                                       <div class="w-10 h-10 rounded-full bg-zinc-800 p-1.5 border border-white/5 flex-shrink-0">
                                            @if($awayTeam && $awayTeam->logo)
                                                <img src="{{ url(Storage::url($awayTeam->logo)) }}" class="w-full h-full object-contain" alt="">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-xs font-bold text-zinc-600">{{ substr($awayTeam?->name ?? 'A', 0, 1) }}</div>
                                            @endif
                                       </div>
                                  </div>
                             </a>
                             <!-- Scorer Action for Upcoming -->
                             @auth
                                @if((auth()->id() === $match->scorer_id) || auth()->user()->canManageLeague($league->id))
                                    <div class="ml-4 pl-4 border-l border-white/5 hidden sm:block">
                                        <a href="{{ route('scorer.console', $match->slug) }}" class="w-8 h-8 rounded-full bg-zinc-800 flex items-center justify-center text-orange-500 hover:bg-orange-500 hover:text-white transition-all" title="Start Scoring">
                                            <i class="fa-solid fa-play text-xs"></i>
                                        </a>
                                    </div>
                                @endif
                             @endauth
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- PAST TAB -->
        <div x-show="activeTab === 'past'" x-transition:enter="transition ease-out duration-300 opacity-0 transform translate-y-2">
             @if($pastMatches->isEmpty())
                 <div class="flex flex-col items-center justify-center py-20 text-zinc-600 rounded-2xl bg-zinc-900/30 border border-dashed border-zinc-800">
                    <p class="text-sm font-medium">No results available</p>
                </div>
             @else
                <div class="grid gap-3 sm:grid-cols-2">
                     @foreach($pastMatches as $match)
                        @php
                            $homeTeam = $match->homeTeam?->team;
                            $awayTeam = $match->awayTeam?->team;
                            $isHomeWinner = $match->home_score > $match->away_score;
                            $isAwayWinner = $match->away_score > $match->home_score;
                        @endphp
                        <a href="{{ route('matches.live', $match->slug) }}" class="bg-zinc-900 rounded-xl border border-white/5 p-4 hover:bg-zinc-800/50 hover:border-white/10 transition-all group">
                             <div class="flex justify-between items-center mb-4 text-[10px] text-zinc-500 uppercase font-bold tracking-wider">
                                 <span>{{ $match->match_date ? $match->match_date->format('M d, Y') : '' }}</span>
                                 <span>FT</span>
                             </div>
                             
                             <div class="space-y-2">
                                 <!-- Home -->
                                 <div class="flex justify-between items-center">
                                      <div class="flex items-center gap-3">
                                          @if($homeTeam && $homeTeam->logo)
                                              <img src="{{ url(Storage::url($homeTeam->logo)) }}" class="w-6 h-6 object-contain opacity-80" alt="">
                                          @else
                                              <div class="w-6 h-6 rounded-full bg-zinc-800"></div>
                                          @endif
                                          <span class="text-sm font-semibold {{ $isHomeWinner ? 'text-white' : 'text-zinc-500' }}">{{ $homeTeam?->name ?? 'Home' }}</span>
                                      </div>
                                      <span class="font-bold {{ $isHomeWinner ? 'text-orange-500' : 'text-zinc-600' }}">{{ $match->home_score ?? 0 }}</span>
                                 </div>
                                 
                                 <!-- Away -->
                                 <div class="flex justify-between items-center">
                                      <div class="flex items-center gap-3">
                                           @if($awayTeam && $awayTeam->logo)
                                              <img src="{{ url(Storage::url($awayTeam->logo)) }}" class="w-6 h-6 object-contain opacity-80" alt="">
                                          @else
                                              <div class="w-6 h-6 rounded-full bg-zinc-800"></div>
                                          @endif
                                          <span class="text-sm font-semibold {{ $isAwayWinner ? 'text-white' : 'text-zinc-500' }}">{{ $awayTeam?->name ?? 'Away' }}</span>
                                      </div>
                                      <span class="font-bold {{ $isAwayWinner ? 'text-orange-500' : 'text-zinc-600' }}">{{ $match->away_score ?? 0 }}</span>
                                 </div>
                             </div>
                        </a>
                     @endforeach
                </div>
             @endif
        </div>

    </div>
</div>
@endsection
