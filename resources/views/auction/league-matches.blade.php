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
<style>
    /* Theme Variables */
    .theme-dark {
        --bg-page: #09090b; /* zinc-950 */
        --bg-header: #18181b; /* zinc-900 */
        --bg-card: #18181b; /* zinc-900 */
        --bg-element: #27272a; /* zinc-800 */
        --bg-hover: #27272a;
        --text-main: #ffffff;
        --text-muted: #a1a1aa; /* zinc-400 */
        --border: rgba(255,255,255,0.05); /* white/5 */
        --accent: #f97316; /* orange-500 */
        --accent-hover: #ea580c; /* orange-600 */
        --shadow-color: rgba(0,0,0,0.5);
    }

    .theme-white {
        --bg-page: #f3f4f6; /* gray-100 */
        --bg-header: #ffffff;
        --bg-card: #ffffff;
        --bg-element: #f3f4f6; /* gray-100 */
        --bg-hover: #f9fafb; /* gray-50 */
        --text-main: #111827; /* gray-900 */
        --text-muted: #6b7280; /* gray-500 */
        --border: #e5e7eb; /* gray-200 */
        --accent: #f97316; /* orange-500 - Keep brand accent */
        --accent-hover: #ea580c;
        --shadow-color: rgba(0,0,0,0.1);
    }

    .theme-green {
        --bg-page: #f0fdf4; /* green-50 */
        --bg-header: #ffffff;
        --bg-card: #ffffff;
        --bg-element: #dcfce7; /* green-100 */
        --bg-hover: #f0fdf4;
        --text-main: #14532d; /* green-900 */
        --text-muted: #15803d; /* green-700 */
        --border: #bbf7d0; /* green-200 */
        --accent: #16a34a; /* green-600 */
        --accent-hover: #15803d; /* green-700 */
        --shadow-color: rgba(22, 163, 74, 0.1);
    }

    /* Transitions */
    .theme-transition {
        transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }

    /* Hide scrollbar for tabs */
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
</style>

<div class="min-h-screen font-sans selection:bg-[var(--accent)]/30 theme-transition bg-[var(--bg-page)] text-[var(--text-main)]" 
     x-data="{ activeTab: 'upcoming', theme: localStorage.getItem('league_theme') || 'dark' }"
     x-init="$watch('theme', val => localStorage.setItem('league_theme', val))"
     :class="'theme-' + theme">

    <!-- League Header -->
    <div class="relative border-b py-8 sm:py-12 bg-[var(--bg-header)] border-[var(--border)] theme-transition">
         <div class="absolute inset-0 overflow-hidden pointer-events-none">
             <!-- Dynamic gradient overlay based on theme -->
             <div class="absolute inset-0 bg-gradient-to-br from-[var(--accent)]/5 via-transparent to-transparent"></div>
         </div>

         <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
             
             <!-- Top Bar with Theme Switcher -->
             <div class="absolute top-0 right-4 sm:right-6 lg:right-8 flex gap-2">
                <div class="flex items-center bg-[var(--bg-element)] p-1 rounded-lg border border-[var(--border)]">
                    <button @click="theme = 'dark'" class="w-8 h-8 rounded-md flex items-center justify-center transition-all" :class="theme === 'dark' ? 'bg-[var(--bg-card)] text-[var(--accent)] shadow-sm' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'">
                        <i class="fa-solid fa-moon"></i>
                    </button>
                    <button @click="theme = 'white'" class="w-8 h-8 rounded-md flex items-center justify-center transition-all" :class="theme === 'white' ? 'bg-[var(--bg-card)] text-[var(--accent)] shadow-sm' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'">
                        <i class="fa-solid fa-sun"></i>
                    </button>
                    <button @click="theme = 'green'" class="w-8 h-8 rounded-md flex items-center justify-center transition-all" :class="theme === 'green' ? 'bg-[var(--bg-card)] text-[var(--accent)] shadow-sm' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'">
                        <i class="fa-solid fa-leaf"></i>
                    </button>
                </div>
             </div>

             <div class="flex flex-col items-center text-center gap-6 mt-8 sm:mt-0">
                 <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl flex items-center justify-center shadow-2xl p-2 relative group bg-[var(--bg-element)] border border-[var(--border)]">
                     @if($league->logo)
                         <img src="{{ url(Storage::url($league->logo)) }}" class="w-full h-full object-cover rounded-xl shadow-inner" alt="{{ $league->name }}">
                     @else
                         <i class="fa-solid fa-shield-halved text-4xl text-[var(--accent)]"></i>
                     @endif
                 </div>
                 
                 <div>
                     <h1 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight text-[var(--text-main)]">{{ $league->name }}</h1>
                     <div class="flex items-center justify-center gap-3 text-sm mt-2 text-[var(--text-muted)]">
                        @if($league->game)
                            <span class="px-2 py-0.5 rounded text-xs font-semibold uppercase tracking-wider bg-[var(--bg-element)] border border-[var(--border)]">{{ $league->game->name }}</span>
                        @endif
                         @if($league->localBody && $league->localBody->district)
                             <span class="flex items-center gap-1"><i class="fa-solid fa-location-dot text-[var(--accent)]"></i> {{ $league->localBody->district->name }}</span>
                         @endif
                     </div>
                 </div>

                 <div class="flex items-center gap-3 w-full sm:w-auto">
                    <!-- Share Button -->
                    <button @click="
                        navigator.clipboard.writeText('{{ url()->current() }}');
                        $el.innerHTML = '<i class=\'fa-solid fa-check mr-2\'></i> Copied!';
                        setTimeout(() => $el.innerHTML = '<i class=\'fa-solid fa-share-nodes mr-2\'></i> Share Page', 2000);
                    " class="flex-1 sm:flex-none px-5 py-2.5 rounded-xl text-sm font-semibold transition-all border bg-[var(--bg-element)] text-[var(--text-muted)] border-[var(--border)] hover:bg-[var(--bg-hover)] hover:text-[var(--text-main)] hover:border-[var(--accent)]/30">
                        <i class="fa-solid fa-share-nodes mr-2"></i> Share Page
                    </button>
                    
                    <a href="{{ route('auctions.live-matches') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all border bg-[var(--bg-element)] text-[var(--text-muted)] border-[var(--border)] hover:bg-[var(--bg-hover)] hover:text-[var(--text-main)]">
                        <i class="fa-solid fa-list mr-2"></i> All Leagues
                    </a>

                    @auth
                        @if(auth()->user()->canManageLeague($league->id) || auth()->user()->isAdmin())
                            <a href="{{ route('scorer.dashboard') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all border bg-[var(--bg-element)] text-[var(--text-muted)] border-[var(--border)] hover:bg-[var(--bg-hover)] hover:text-[var(--text-main)]">
                                <i class="fa-solid fa-gauge-high mr-2"></i> Scorer Dashboard
                            </a>
                        @endif
                    @endauth
                 </div>
             </div>
         </div>
    </div>

    <!-- Sticky Tabs -->
    <div class="sticky top-0 z-30 backdrop-blur-md border-b bg-[var(--bg-page)]/80 border-[var(--border)] theme-transition">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 overflow-x-auto scrollbar-hide">
            <div class="flex justify-start sm:justify-center gap-4 sm:gap-8 min-w-max">
                @php
                    $liveMatches = $fixtures->where('status', 'in_progress');
                    $upcomingMatches = $fixtures->whereIn('status', ['scheduled', 'unscheduled'])->sortBy(function($match) {
                        return ($match->match_date ? $match->match_date->format('Y-m-d') : '9999-12-31') . 
                               ($match->match_time ? $match->match_time->format('H:i:s') : '00:00:00');
                    });
                    $pastMatches = $fixtures->where('status', 'completed');
                    $knockoutMatches = $fixtures->whereNotIn('match_type', ['group_stage'])->sortBy(function($match) {
                         // Sort by logical round order then date
                         $order = ['qualifier' => 1, 'eliminator' => 2, 'quarter_final' => 3, 'semi_final' => 4, 'final' => 5];
                         return ($order[$match->match_type] ?? 99) . ($match->match_date ? $match->match_date->format('Y-m-d') : '9999-12-31');
                    });
                @endphp
                
                <button @click="activeTab = 'live'" 
                    class="relative py-4 text-sm font-bold transition-colors flex items-center gap-2"
                    :class="activeTab === 'live' ? 'text-[var(--accent)]' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'">
                    LIVE
                    @if($liveMatches->count() > 0)
                        <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full animate-pulse">{{ $liveMatches->count() }}</span>
                    @endif
                    <div x-show="activeTab === 'live'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[var(--accent)]" x-transition></div>
                </button>
                <button @click="activeTab = 'upcoming'" 
                    class="relative py-4 text-sm font-bold transition-colors flex items-center gap-2"
                    :class="activeTab === 'upcoming' ? 'text-[var(--accent)]' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'">
                    UPCOMING
                    @if($upcomingMatches->count() > 0)
                         <span class="text-[10px] px-1.5 py-0.5 rounded-full border bg-[var(--bg-element)] text-[var(--text-muted)] border-[var(--border)]">{{ $upcomingMatches->count() }}</span>
                    @endif
                    <div x-show="activeTab === 'upcoming'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[var(--accent)]" x-transition></div>
                </button>
                <button @click="activeTab = 'knockouts'" 
                    class="relative py-4 text-sm font-bold transition-colors"
                    :class="activeTab === 'knockouts' ? 'text-[var(--accent)]' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'">
                    KNOCKOUTS
                    <div x-show="activeTab === 'knockouts'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[var(--accent)]" x-transition></div>
                </button>
                 <button @click="activeTab = 'past'" 
                    class="relative py-4 text-sm font-bold transition-colors"
                    :class="activeTab === 'past' ? 'text-[var(--accent)]' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'">
                    RESULTS
                    <div x-show="activeTab === 'past'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[var(--accent)]" x-transition></div>
                </button>
                <button @click="activeTab = 'standings'" 
                    class="relative py-4 text-sm font-bold transition-colors"
                    :class="activeTab === 'standings' ? 'text-[var(--accent)]' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'">
                    STANDINGS
                    <div x-show="activeTab === 'standings'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[var(--accent)]" x-transition></div>
                </button>
                <button @click="activeTab = 'leaders'" 
                    class="relative py-4 text-sm font-bold transition-colors"
                    :class="activeTab === 'leaders' ? 'text-[var(--accent)]' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'">
                    LEADERS
                    <div x-show="activeTab === 'leaders'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[var(--accent)]" x-transition></div>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 pb-20 theme-transition">

        <!-- LIVE TAB -->
        <div x-show="activeTab === 'live'" x-transition:enter="transition ease-out duration-300 opacity-0 transform translate-y-2">
            @if($liveMatches->isEmpty())
                 <div class="flex flex-col items-center justify-center py-20 rounded-2xl border border-dashed bg-[var(--bg-element)]/30 border-[var(--border)] text-[var(--text-muted)]">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 bg-[var(--bg-element)] border border-[var(--border)]">
                        <i class="fa-solid fa-stopwatch text-2xl opacity-50"></i>
                    </div>
                    <p class="text-sm font-medium">No live matches now</p>
                    <p class="text-xs mt-1 opacity-70">Check upcoming fixtures</p>
                </div>
            @else
                <div class="space-y-4">
                     @foreach($liveMatches as $match)
                        @php
                            $homeTeam = $match->homeTeam?->team;
                            $awayTeam = $match->awayTeam?->team;
                        @endphp
                        <div class="rounded-2xl border overflow-hidden shadow-xl group transition-all bg-[var(--bg-card)] border-[var(--border)] hover:border-[var(--accent)]/20">
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
                                              <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full p-3 mb-3 border shadow-inner bg-[var(--bg-element)] border-[var(--border)]">
                                                  @if($homeTeam && $homeTeam->logo)
                                                    <img src="{{ url(Storage::url($homeTeam->logo)) }}" class="w-full h-full object-contain" alt="">
                                                  @else
                                                    <div class="w-full h-full flex items-center justify-center font-bold text-xl text-[var(--text-muted)]">{{ substr($homeTeam?->name ?? 'H', 0, 1) }}</div>
                                                  @endif
                                              </div>
                                              <h3 class="text-sm sm:text-base font-bold text-center leading-tight text-[var(--text-main)]">{{ $homeTeam?->name ?? 'Home' }}</h3>
                                          </div>

                                          <!-- Score -->
                                          <div class="px-6 flex flex-col items-center">
                                              <div class="text-4xl sm:text-5xl font-black tabular-nums tracking-tighter text-[var(--text-main)]">
                                                  {{ $match->home_score ?? 0 }}<span class="text-[var(--text-muted)] mx-2">-</span>{{ $match->away_score ?? 0 }}
                                              </div>
                                              <p class="text-xs font-bold mt-2 animate-pulse text-[var(--accent)]">In Progress</p>
                                          </div>

                                          <!-- Away -->
                                          <div class="flex-1 flex flex-col items-center">
                                              <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full p-3 mb-3 border shadow-inner bg-[var(--bg-element)] border-[var(--border)]">
                                                  @if($awayTeam && $awayTeam->logo)
                                                    <img src="{{ url(Storage::url($awayTeam->logo)) }}" class="w-full h-full object-contain" alt="">
                                                  @else
                                                    <div class="w-full h-full flex items-center justify-center font-bold text-xl text-[var(--text-muted)]">{{ substr($awayTeam?->name ?? 'A', 0, 1) }}</div>
                                                  @endif
                                              </div>
                                              <h3 class="text-sm sm:text-base font-bold text-center leading-tight text-[var(--text-main)]">{{ $awayTeam?->name ?? 'Away' }}</h3>
                                          </div>
                                     </div>

                                     <!-- League/Group Info -->
                                     <div class="mt-4 text-center">
                                        <div class="inline-flex items-center gap-2 text-[10px] font-semibold text-[var(--text-muted)] bg-[var(--bg-element)] px-2 py-1 rounded-full border border-[var(--border)]">
                                            <span>{{ $match->league->name ?? 'League' }}</span>
                                            @if($match->leagueGroup)
                                                <span class="w-1 h-1 rounded-full bg-[var(--text-muted)]"></span>
                                                <span>{{ $match->leagueGroup->name }}</span>
                                            @endif
                                            <span class="w-1 h-1 rounded-full bg-[var(--text-muted)]"></span>
                                            <span>{{ ucfirst(str_replace('_', ' ', $match->match_type)) }}</span>
                                        </div>
                                     </div>
                                </div>
                             </a>
                             
                             <!-- Scorer Action -->
                             @auth
                                @if((auth()->id() === $match->scorer_id) || auth()->user()->canManageLeague($league->id))
                                    <div class="px-6 pb-6 pt-0 flex justify-center">
                                        <a href="{{ route('scorer.console', $match->slug) }}" class="inline-flex items-center gap-2 px-6 py-2 rounded-full font-bold text-xs uppercase tracking-wider transition-all border bg-[var(--bg-element)] text-[var(--accent)] hover:bg-[var(--accent)] border-[var(--accent)]/20">
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

        <!-- KNOCKOUTS TAB -->
        <div x-show="activeTab === 'knockouts'" x-transition:enter="transition ease-out duration-300 opacity-0 transform translate-y-2">
            @if($knockoutMatches->isEmpty())
                 <div class="flex flex-col items-center justify-center py-20 rounded-2xl border border-dashed bg-[var(--bg-element)]/30 border-[var(--border)] text-[var(--text-muted)]">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 bg-[var(--bg-element)] border border-[var(--border)]">
                        <i class="fa-solid fa-sitemap text-2xl opacity-50"></i>
                    </div>
                    <p class="text-sm font-medium">No knockout matches yet</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($knockoutMatches->groupBy('match_type') as $type => $matches)
                        <div>
                            <h3 class="font-bold text-lg mb-4 text-[var(--accent)] uppercase tracking-wide border-b border-[var(--accent)]/20 pb-2 inline-block">
                                {{ str_replace('_', ' ', $type) }}
                            </h3>
                            <div class="space-y-4">
                                @foreach($matches as $match)
                                    @include('partials.match-card', ['match' => $match])
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- UPCOMING TAB -->
        <div x-show="activeTab === 'upcoming'" x-transition:enter="transition ease-out duration-300 opacity-0 transform translate-y-2">
             @if($upcomingMatches->isEmpty())
                 <div class="flex flex-col items-center justify-center py-20 rounded-2xl border border-dashed bg-[var(--bg-element)]/30 border-[var(--border)] text-[var(--text-muted)]">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 bg-[var(--bg-element)] border border-[var(--border)]">
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
                        <div class="rounded-xl border flex flex-col group transition-all overflow-hidden bg-[var(--bg-card)] border-[var(--border)] hover:border-[var(--accent)]/20">
                             <!-- Match Details -->
                             <a href="{{ route('matches.live', $match->slug) }}" class="p-4 sm:p-5 flex items-center justify-between flex-grow">
                                  <div class="flex items-center gap-4 flex-1">
                                       <div class="w-10 h-10 rounded-full p-1.5 border flex-shrink-0 bg-[var(--bg-element)] border-[var(--border)]">
                                            @if($homeTeam && $homeTeam->logo)
                                                <img src="{{ url(Storage::url($homeTeam->logo)) }}" class="w-full h-full object-contain" alt="">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-xs font-bold text-[var(--text-muted)]">{{ substr($homeTeam?->name ?? 'H', 0, 1) }}</div>
                                            @endif
                                       </div>
                                       <span class="font-bold text-xs sm:text-sm leading-tight text-[var(--text-main)] line-clamp-2">{{ $homeTeam?->name ?? 'TBD' }}</span>
                                  </div>
                                  
                                  <div class="px-2 sm:px-4 flex flex-col items-center min-w-[80px] sm:min-w-[100px]">
                                       <span class="text-xs font-bold px-2 py-1 rounded mb-1 bg-[var(--bg-element)] text-[var(--text-muted)]">{{ $match->match_time ? $match->match_time->format('h:i A') : 'TBA' }}</span>
                                       <span class="text-[10px] uppercase font-medium text-[var(--text-muted)]">{{ $match->match_date ? $match->match_date->format('M d') : 'Date TBA' }}</span>
                                  </div>

                                   <div class="flex items-center gap-4 flex-1 justify-end">
                                       <span class="font-bold text-xs sm:text-sm text-right leading-tight text-[var(--text-main)] line-clamp-2">{{ $awayTeam?->name ?? 'TBD' }}</span>
                                       <div class="w-10 h-10 rounded-full p-1.5 border flex-shrink-0 bg-[var(--bg-element)] border-[var(--border)]">
                                            @if($awayTeam && $awayTeam->logo)
                                                <img src="{{ url(Storage::url($awayTeam->logo)) }}" class="w-full h-full object-contain" alt="">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-xs font-bold text-[var(--text-muted)]">{{ substr($awayTeam?->name ?? 'A', 0, 1) }}</div>
                                            @endif
                                       </div>
                                  </div>
                             </a>

                             <!-- League/Group Info for Upcoming -->
                             <div class="px-4 pb-3 flex justify-center">
                                <div class="inline-flex items-center gap-2 text-[10px] font-semibold text-[var(--text-muted)] bg-[var(--bg-element)] px-2 py-0.5 rounded-full border border-[var(--border)]">
                                    <span>{{ $match->league->name ?? 'League' }}</span>
                                    @if($match->leagueGroup)
                                        <span class="w-1 h-1 rounded-full bg-[var(--text-muted)]"></span>
                                        <span>{{ $match->leagueGroup->name }}</span>
                                    @endif
                                    <span class="w-1 h-1 rounded-full bg-[var(--text-muted)]"></span>
                                    <span>{{ ucfirst(str_replace('_', ' ', $match->match_type)) }}</span>
                                </div>
                             </div>

                             <!-- Scorer Action for Upcoming (Separate Row) -->
                             @auth
                                @if((auth()->id() === $match->scorer_id) || auth()->user()->canManageLeague($league->id))
                                    <div class="p-2 border-t bg-[var(--bg-element)]/30 border-[var(--border)]">
                                        <a href="{{ route('scorer.console', $match->slug) }}" class="flex items-center justify-center gap-2 w-full py-2.5 rounded-lg font-bold text-xs uppercase tracking-wider transition-all shadow-lg hover:shadow-[var(--accent)]/20 bg-[var(--accent)] hover:bg-[var(--accent-hover)]">
                                            <i class="fa-solid fa-play"></i> Start Scoring
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
                 <div class="flex flex-col items-center justify-center py-20 rounded-2xl border border-dashed bg-[var(--bg-element)]/30 border-[var(--border)] text-[var(--text-muted)]">
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
                        <a href="{{ route('matches.live', $match->slug) }}" class="rounded-xl border p-4 transition-all group bg-[var(--bg-card)] border-[var(--border)] hover:bg-[var(--bg-hover)]">
                             <div class="flex justify-between items-center mb-4 text-[10px] uppercase font-bold tracking-wider text-[var(--text-muted)]">
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
                                              <div class="w-6 h-6 rounded-full bg-[var(--bg-element)]"></div>
                                          @endif
                                          <span class="text-sm font-semibold {{ $isHomeWinner ? 'text-[var(--text-main)]' : 'text-[var(--text-muted)]' }}">{{ $homeTeam?->name ?? 'Home' }}</span>
                                      </div>
                                      <span class="font-bold {{ $isHomeWinner ? 'text-[var(--accent)]' : 'text-[var(--text-muted)]' }}">{{ $match->home_score ?? 0 }}</span>
                                 </div>
                                 
                                 <!-- Away -->
                                  <div class="flex justify-between items-center">
                                      <div class="flex items-center gap-3">
                                           @if($awayTeam && $awayTeam->logo)
                                              <img src="{{ url(Storage::url($awayTeam->logo)) }}" class="w-6 h-6 object-contain opacity-80" alt="">
                                          @else
                                              <div class="w-6 h-6 rounded-full bg-[var(--bg-element)]"></div>
                                          @endif
                                          <span class="text-sm font-semibold {{ $isAwayWinner ? 'text-[var(--text-main)]' : 'text-[var(--text-muted)]' }}">{{ $awayTeam?->name ?? 'Away' }}</span>
                                      </div>
                                      <span class="font-bold {{ $isAwayWinner ? 'text-[var(--accent)]' : 'text-[var(--text-muted)]' }}">{{ $match->away_score ?? 0 }}</span>
                                 </div>
                             </div>

                             <!-- League/Group Info for Past -->
                             <div class="mt-3 pt-3 border-t border-[var(--border)] flex justify-center">
                                <div class="inline-flex items-center gap-2 text-[10px] font-semibold text-[var(--text-muted)]">
                                    <span>{{ $match->leagueGroup ? $match->leagueGroup->name : ($match->match_type ? ucfirst(str_replace('_', ' ', $match->match_type)) : 'Match') }}</span>
                                </div>
                             </div>
                        </a>
                     @endforeach
                </div>
             @endif
        </div>

        <!-- STANDINGS TAB -->
        <div x-show="activeTab === 'standings'" x-transition:enter="transition ease-out duration-300 opacity-0 transform translate-y-2">
            @if(empty($standingsByGroup))
                <div class="flex flex-col items-center justify-center py-20 rounded-2xl border border-dashed bg-[var(--bg-element)]/30 border-[var(--border)] text-[var(--text-muted)]">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 bg-[var(--bg-element)] border border-[var(--border)]">
                        <i class="fa-solid fa-trophy text-2xl opacity-50"></i>
                    </div>
                    <p class="text-sm font-medium">No standings available yet</p>
                    <p class="text-xs mt-1 opacity-70">Complete some matches to see standings</p>
                </div>
            @else
                <div class="space-y-8">
                    @foreach($standingsByGroup as $groupId => $groupData)
                        <div class="rounded-2xl border overflow-hidden bg-[var(--bg-card)] border-[var(--border)]">
                            @if($groupData['group'])
                                <div class="bg-[var(--bg-element)] px-4 py-3 border-b border-[var(--border)]">
                                    <h3 class="font-bold text-[var(--text-main)]">{{ $groupData['group']->name }}</h3>
                                </div>
                            @endif
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b bg-[var(--bg-element)] border-[var(--border)]">
                                            <th class="text-left py-3 px-4 font-bold text-[var(--text-muted)] text-xs uppercase tracking-wider">#</th>
                                            <th class="text-left py-3 px-2 font-bold text-[var(--text-muted)] text-xs uppercase tracking-wider">Team</th>
                                            <th class="text-center py-3 px-2 font-bold text-[var(--text-muted)] text-xs uppercase tracking-wider">P</th>
                                            <th class="text-center py-3 px-2 font-bold text-[var(--text-muted)] text-xs uppercase tracking-wider">W</th>
                                            <th class="text-center py-3 px-2 font-bold text-[var(--text-muted)] text-xs uppercase tracking-wider">D</th>
                                            <th class="text-center py-3 px-2 font-bold text-[var(--text-muted)] text-xs uppercase tracking-wider">L</th>
                                            <th class="text-center py-3 px-2 font-bold text-[var(--text-muted)] text-xs uppercase tracking-wider hidden sm:table-cell">GF</th>
                                            <th class="text-center py-3 px-2 font-bold text-[var(--text-muted)] text-xs uppercase tracking-wider hidden sm:table-cell">GA</th>
                                            <th class="text-center py-3 px-2 font-bold text-[var(--text-muted)] text-xs uppercase tracking-wider">GD</th>
                                            <th class="text-center py-3 px-4 font-bold text-[var(--accent)] text-xs uppercase tracking-wider">Pts</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($groupData['teams'] as $index => $standing)
                                            <tr class="border-b border-[var(--border)] hover:bg-[var(--bg-hover)] transition-colors">
                                                <td class="py-3 px-4 font-bold text-[var(--text-muted)]">{{ $loop->iteration }}</td>
                                                <td class="py-3 px-2">
                                                    <div class="flex items-center gap-2">
                                                        @if($standing['team'] && $standing['team']->logo)
                                                            <img src="{{ url(Storage::url($standing['team']->logo)) }}" class="w-6 h-6 object-contain" alt="">
                                                        @else
                                                            <div class="w-6 h-6 rounded-full bg-[var(--bg-element)]"></div>
                                                        @endif
                                                        <span class="font-semibold text-[var(--text-main)] truncate max-w-[120px] sm:max-w-none">{{ $standing['team']->name ?? 'Team' }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-2 text-center font-medium text-[var(--text-muted)]">{{ $standing['played'] }}</td>
                                                <td class="py-3 px-2 text-center font-medium text-emerald-500">{{ $standing['won'] }}</td>
                                                <td class="py-3 px-2 text-center font-medium text-[var(--text-muted)]">{{ $standing['drawn'] }}</td>
                                                <td class="py-3 px-2 text-center font-medium text-rose-500">{{ $standing['lost'] }}</td>
                                                <td class="py-3 px-2 text-center font-medium text-[var(--text-muted)] hidden sm:table-cell">{{ $standing['goals_for'] }}</td>
                                                <td class="py-3 px-2 text-center font-medium text-[var(--text-muted)] hidden sm:table-cell">{{ $standing['goals_against'] }}</td>
                                                <td class="py-3 px-2 text-center font-medium {{ $standing['goal_difference'] > 0 ? 'text-emerald-500' : ($standing['goal_difference'] < 0 ? 'text-rose-500' : 'text-[var(--text-muted)]') }}">{{ $standing['goal_difference'] > 0 ? '+' : '' }}{{ $standing['goal_difference'] }}</td>
                                                <td class="py-3 px-4 text-center font-black text-[var(--accent)]">{{ $standing['points'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- LEADERS TAB -->
        <div x-show="activeTab === 'leaders'" x-data="{ leaderTab: 'goals' }" x-transition:enter="transition ease-out duration-300 opacity-0 transform translate-y-2">
            @if($topScorers->isEmpty() && $topAssists->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 rounded-2xl border border-dashed bg-[var(--bg-element)]/30 border-[var(--border)] text-[var(--text-muted)]">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 bg-[var(--bg-element)] border border-[var(--border)]">
                        <i class="fa-solid fa-star text-2xl opacity-50"></i>
                    </div>
                    <p class="text-sm font-medium">No leaders data available yet</p>
                    <p class="text-xs mt-1 opacity-70">Complete some matches with goals to see leaders</p>
                </div>
            @else
                <!-- Sub-tabs for Goals and Assists -->
                <div class="flex justify-center gap-2 mb-6">
                    <button @click="leaderTab = 'goals'" 
                        class="px-6 py-2.5 rounded-full text-sm font-bold transition-all"
                        :class="leaderTab === 'goals' ? 'bg-emerald-500 text-white shadow-lg' : 'bg-[var(--bg-element)] text-[var(--text-muted)] hover:text-[var(--text-main)] border border-[var(--border)]'">
                        <i class="fa-solid fa-futbol mr-2"></i>Goals
                    </button>
                    <button @click="leaderTab = 'assists'" 
                        class="px-6 py-2.5 rounded-full text-sm font-bold transition-all"
                        :class="leaderTab === 'assists' ? 'bg-blue-500 text-white shadow-lg' : 'bg-[var(--bg-element)] text-[var(--text-muted)] hover:text-[var(--text-main)] border border-[var(--border)]'">
                        <i class="fa-solid fa-hands-helping mr-2"></i>Assists
                    </button>
                    <button @click="leaderTab = 'cards'" 
                        class="px-6 py-2.5 rounded-full text-sm font-bold transition-all"
                        :class="leaderTab === 'cards' ? 'bg-amber-500 text-white shadow-lg' : 'bg-[var(--bg-element)] text-[var(--text-muted)] hover:text-[var(--text-main)] border border-[var(--border)]'">
                        <i class="fa-solid fa-square mr-2"></i>Cards
                    </button>
                </div>

                <!-- Goals Tab Content -->
                <div x-show="leaderTab === 'goals'" x-transition>
                    <div class="rounded-2xl border overflow-hidden bg-[var(--bg-card)] border-[var(--border)]">
                        <div class="bg-[var(--bg-element)] p-4 border-b border-[var(--border)] flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-500/10 flex items-center justify-center">
                                <i class="fa-solid fa-futbol text-emerald-500"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-[var(--text-main)]">Top Scorers</h3>
                                <p class="text-xs text-[var(--text-muted)]">Most goals scored</p>
                            </div>
                        </div>
                        <div class="divide-y divide-[var(--border)]">
                            @forelse($topScorers as $index => $scorer)
                                <div class="p-3 sm:p-4 flex items-center gap-3 hover:bg-[var(--bg-hover)] transition-colors">
                                    <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm {{ $index < 3 ? 'bg-[var(--accent)]/10 text-[var(--accent)]' : 'bg-[var(--bg-element)] text-[var(--text-muted)]' }}">{{ $index + 1 }}</span>
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full overflow-hidden bg-[var(--bg-element)] flex-shrink-0 border border-[var(--border)]">
                                        @if($scorer['player'] && $scorer['player']->photo)
                                            <img src="{{ url(Storage::url($scorer['player']->photo)) }}" class="w-full h-full object-cover" alt="">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-sm font-bold text-[var(--text-muted)]">{{ substr($scorer['player']->name ?? 'P', 0, 1) }}</div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-sm sm:text-base text-[var(--text-main)] truncate">{{ $scorer['player']->name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-[var(--text-muted)] truncate">{{ $scorer['team']->name ?? '' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-black text-xl sm:text-2xl text-emerald-500">{{ $scorer['goals'] }}</span>
                                        <p class="text-[10px] text-[var(--text-muted)] uppercase">goals</p>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-[var(--text-muted)] text-sm">No goals scored yet</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Assists Tab Content -->
                <div x-show="leaderTab === 'assists'" x-transition>
                    <div class="rounded-2xl border overflow-hidden bg-[var(--bg-card)] border-[var(--border)]">
                        <div class="bg-[var(--bg-element)] p-4 border-b border-[var(--border)] flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center justify-center">
                                <i class="fa-solid fa-hands-helping text-blue-500"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-[var(--text-main)]">Top Assists</h3>
                                <p class="text-xs text-[var(--text-muted)]">Most assists provided</p>
                            </div>
                        </div>
                        <div class="divide-y divide-[var(--border)]">
                            @forelse($topAssists as $index => $assist)
                                <div class="p-3 sm:p-4 flex items-center gap-3 hover:bg-[var(--bg-hover)] transition-colors">
                                    <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm {{ $index < 3 ? 'bg-[var(--accent)]/10 text-[var(--accent)]' : 'bg-[var(--bg-element)] text-[var(--text-muted)]' }}">{{ $index + 1 }}</span>
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full overflow-hidden bg-[var(--bg-element)] flex-shrink-0 border border-[var(--border)]">
                                        @if($assist['player'] && $assist['player']->photo)
                                            <img src="{{ url(Storage::url($assist['player']->photo)) }}" class="w-full h-full object-cover" alt="">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-sm font-bold text-[var(--text-muted)]">{{ substr($assist['player']->name ?? 'P', 0, 1) }}</div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-sm sm:text-base text-[var(--text-main)] truncate">{{ $assist['player']->name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-[var(--text-muted)] truncate">{{ $assist['team']->name ?? '' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-black text-xl sm:text-2xl text-blue-500">{{ $assist['assists'] }}</span>
                                        <p class="text-[10px] text-[var(--text-muted)] uppercase">assists</p>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-[var(--text-muted)] text-sm">No assists recorded yet</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Cards Tab Content -->
                <div x-show="leaderTab === 'cards'" x-transition>
                    <div class="grid gap-6 sm:grid-cols-2">
                        <!-- Yellow Cards -->
                        <div class="rounded-2xl border overflow-hidden bg-[var(--bg-card)] border-[var(--border)]">
                            <div class="bg-amber-500/10 p-4 border-b border-[var(--border)] flex items-center gap-3">
                                <div class="w-8 h-10 bg-amber-400 rounded-sm shadow-sm"></div>
                                <div>
                                    <h3 class="font-bold text-[var(--text-main)]">Yellow Cards</h3>
                                    <p class="text-xs text-[var(--text-muted)]">Most yellows received</p>
                                </div>
                            </div>
                            <div class="divide-y divide-[var(--border)]">
                                @forelse($topYellowCards as $index => $card)
                                    <div class="p-3 flex items-center gap-3 hover:bg-[var(--bg-hover)] transition-colors">
                                        <span class="w-6 text-center font-bold text-sm {{ $index < 3 ? 'text-amber-500' : 'text-[var(--text-muted)]' }}">{{ $index + 1 }}</span>
                                        <div class="w-8 h-8 rounded-full overflow-hidden bg-[var(--bg-element)] flex-shrink-0">
                                            @if($card['player'] && $card['player']->photo)
                                                <img src="{{ url(Storage::url($card['player']->photo)) }}" class="w-full h-full object-cover" alt="">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-xs font-bold text-[var(--text-muted)]">{{ substr($card['player']->name ?? 'P', 0, 1) }}</div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-sm text-[var(--text-main)] truncate">{{ $card['player']->name ?? 'Unknown' }}</p>
                                            <p class="text-xs text-[var(--text-muted)] truncate">{{ $card['team']->name ?? '' }}</p>
                                        </div>
                                        <span class="font-black text-lg text-amber-500">{{ $card['cards'] }}</span>
                                    </div>
                                @empty
                                    <div class="p-6 text-center text-[var(--text-muted)] text-sm">No yellow cards yet</div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Red Cards -->
                        <div class="rounded-2xl border overflow-hidden bg-[var(--bg-card)] border-[var(--border)]">
                            <div class="bg-rose-500/10 p-4 border-b border-[var(--border)] flex items-center gap-3">
                                <div class="w-8 h-10 bg-rose-500 rounded-sm shadow-sm"></div>
                                <div>
                                    <h3 class="font-bold text-[var(--text-main)]">Red Cards</h3>
                                    <p class="text-xs text-[var(--text-muted)]">Most reds received</p>
                                </div>
                            </div>
                            <div class="divide-y divide-[var(--border)]">
                                @forelse($topRedCards as $index => $card)
                                    <div class="p-3 flex items-center gap-3 hover:bg-[var(--bg-hover)] transition-colors">
                                        <span class="w-6 text-center font-bold text-sm {{ $index < 3 ? 'text-rose-500' : 'text-[var(--text-muted)]' }}">{{ $index + 1 }}</span>
                                        <div class="w-8 h-8 rounded-full overflow-hidden bg-[var(--bg-element)] flex-shrink-0">
                                            @if($card['player'] && $card['player']->photo)
                                                <img src="{{ url(Storage::url($card['player']->photo)) }}" class="w-full h-full object-cover" alt="">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-xs font-bold text-[var(--text-muted)]">{{ substr($card['player']->name ?? 'P', 0, 1) }}</div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-sm text-[var(--text-main)] truncate">{{ $card['player']->name ?? 'Unknown' }}</p>
                                            <p class="text-xs text-[var(--text-muted)] truncate">{{ $card['team']->name ?? '' }}</p>
                                        </div>
                                        <span class="font-black text-lg text-rose-500">{{ $card['cards'] }}</span>
                                    </div>
                                @empty
                                    <div class="p-6 text-center text-[var(--text-muted)] text-sm">No red cards yet</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
