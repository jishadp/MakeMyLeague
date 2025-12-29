@extends('layouts.app')

@section('meta_tags')
    <meta property="og:title" content="LIVE: {{ $fixture->homeTeam->team->name }} vs {{ $fixture->awayTeam->team->name }}">
    <meta property="og:description" content="Watch live scores and updates for {{ $fixture->league->name }} on MakeMyLeague.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($fixture->league->logo)
        <meta property="og:image" content="{{ \Illuminate\Support\Facades\Storage::url($fixture->league->logo) }}">
    @else
        <meta property="og:image" content="{{ asset('images/default-league.jpg') }}">
    @endif
@endsection

@section('content')
<style>
    /* Theme Variables */
    .theme-dark {
        --bg-page: #09090b; /* zinc-950 */
        --bg-header: #18181b; /* zinc-900/90 */
        --bg-card: #18181b; /* zinc-900 */
        --bg-element: #27272a; /* zinc-800 */
        --bg-hover: #27272a;
        --text-main: #e4e4e7; /* zinc-200 */
        --text-muted: #a1a1aa; /* zinc-400 */
        --border: #27272a; /* zinc-800 */
        --accent: #f97316; /* orange-500 */
        --accent-hover: #ea580c; /* orange-600 */
        --shadow-color: rgba(0,0,0,0.5);
    }

    .theme-white {
        --bg-page: #f3f4f6; /* zinc-100 */
        --bg-header: #ffffff; /* zinc-50 */
        --bg-card: #ffffff;
        --bg-element: #f3f4f6; /* zinc-100 */
        --bg-hover: #f9fafb; /* zinc-50 */
        --text-main: #18181b; /* zinc-900 */
        --text-muted: #71717a; /* zinc-500 */
        --border: #e4e4e7; /* zinc-200 */
        --accent: #f97316; /* orange-500 */
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

    .theme-transition {
        transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }
</style>

<div class="min-h-screen theme-transition bg-[var(--bg-page)] text-[var(--text-main)]" 
     x-data="{ 
         activeTab: 'summary', 
         theme: localStorage.getItem('liveMatchTheme') || 'green',
         currentMinute: {{ $fixture->current_minute ?? 0 }},
         isRunning: {{ $fixture->is_running ? 'true' : 'false' }},
         matchState: '{{ $fixture->match_state ?? 'NOT_STARTED' }}',
         matchDuration: {{ $fixture->match_duration ?? 45 }},
         
         get timeDisplay() {
             if(this.matchState === 'HALF_TIME') return 'HT';
             if(this.matchState === 'FULL_TIME') return 'FT';
             
             // Stoppage time display
             if (this.matchState == 'FIRST_HALF' && this.currentMinute > this.matchDuration) {
                 return this.matchDuration + '+' + (this.currentMinute - this.matchDuration);
             }
             if (this.matchState == 'SECOND_HALF' && this.currentMinute > this.matchDuration * 2) {
                 return (this.matchDuration * 2) + '+' + (this.currentMinute - this.matchDuration * 2);
             }
             
             // Just show minute with apostrophe
             return this.currentMinute + String.fromCharCode(39);
         }
     }"
     x-init="
         $watch('theme', val => localStorage.setItem('liveMatchTheme', val));
         
         // Auto-refresh every 15 seconds to get latest from server
         setInterval(() => {
             window.location.reload();
         }, 15000);
     "
     :class="'theme-' + theme">
    
    <!-- Ultra-Compact Sticky Match Header -->
    <div class="sticky top-0 z-40 shadow-xl backdrop-blur-md theme-transition border-b bg-[var(--bg-header)]/95 border-[var(--border)]">
        
        <!-- Top Bar: Status & Theme Switcher -->
        <div class="flex items-center justify-between px-4 py-4 text-sm font-bold uppercase tracking-widest border-b border-[var(--border)] bg-[var(--bg-card)]/50 text-[var(--text-muted)]">
            <div class="truncate max-w-[200px] sm:max-w-md text-base">{{ $fixture->league->name ?? 'League Match' }}</div>
            <div class="flex items-center gap-3">
                @if($fixture->status == 'in_progress')
                    <span class="flex items-center gap-2 animate-pulse text-[var(--accent)]">
                        <span class="w-2.5 h-2.5 rounded-full bg-[var(--accent)]"></span> LIVE
                    </span>
                 @else
                    <span>{{ str_replace('_', ' ', $fixture->status) }}</span>
                @endif
                
                @auth
                    @if(auth()->id() == $fixture->scorer_id || ($fixture->league && auth()->id() == $fixture->league->organizer_id) || auth()->user()->is_admin)
                        <a href="{{ route('scorer.console', $fixture->slug) }}" class="ml-3 px-4 py-1.5 rounded text-xs font-bold uppercase transition-colors border bg-[var(--bg-element)] text-[var(--accent)] border-[var(--accent)]/30 hover:bg-[var(--bg-hover)]">
                            <i class="fa-solid fa-pen-to-square mr-1.5"></i> Score
                        </a>
                    @endif
                @endauth
                
                <!-- Reload Button -->
                <button @click="window.location.reload()" class="ml-3 w-8 h-8 rounded-full flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 bg-[var(--bg-element)] text-[var(--accent)] hover:bg-[var(--bg-hover)] ring-offset-[var(--bg-page)]" title="Refresh">
                    <i class="fa-solid fa-rotate-right text-sm"></i>
                </button>

                <!-- Theme Switcher -->
                <div class="flex items-center gap-1.5 ml-3 bg-[var(--bg-element)] p-1 rounded-full border border-[var(--border)]">
                    <button @click="theme = 'dark'" class="w-8 h-8 rounded-full flex items-center justify-center transition-all" :class="theme === 'dark' ? 'bg-[var(--bg-card)] text-[var(--accent)] shadow-sm' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'">
                        <i class="fa-solid fa-moon text-xs"></i>
                    </button>
                    <button @click="theme = 'white'" class="w-8 h-8 rounded-full flex items-center justify-center transition-all" :class="theme === 'white' ? 'bg-[var(--bg-card)] text-[var(--accent)] shadow-sm' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'">
                        <i class="fa-solid fa-sun text-xs"></i>
                    </button>
                     <button @click="theme = 'green'" class="w-8 h-8 rounded-full flex items-center justify-center transition-all" :class="theme === 'green' ? 'bg-[var(--bg-card)] text-[var(--accent)] shadow-sm' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'">
                        <i class="fa-solid fa-leaf text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Compact Scoreboard -->
        <div class="px-3 py-3 md:py-4">
            <div class="container mx-auto max-w-lg">
                <div class="flex items-start justify-between gap-2 md:gap-8 relative">
                    
                    <!-- Home Team -->
                    <div class="flex-1 flex flex-col items-center gap-2 min-w-0 text-center">
                         <div class="w-12 h-12 md:w-16 md:h-16 rounded-full shadow-sm flex-shrink-0 flex items-center justify-center transition-colors duration-300 relative overflow-hidden bg-[var(--bg-card)] border border-[var(--border)]">
                             @if($fixture->homeTeam->team->logo)
                                 <img src="{{ \Illuminate\Support\Facades\Storage::url($fixture->homeTeam->team->logo) }}" class="w-full h-full object-cover">
                             @else
                                 <img src="{{ asset('images/default.jpeg') }}" class="w-full h-full object-cover opacity-80">
                             @endif
                             
                             <!-- Red Card Indicator -->
                             @if(isset($redCards[$fixture->home_team_id]))
                                 <div class="absolute top-0 right-0 p-1 bg-white/80 rounded-bl-lg backdrop-blur-sm">
                                     <div class="flex gap-0.5">
                                         @foreach($redCards[$fixture->home_team_id] as $rc)
                                            <div class="w-2.5 h-3.5 bg-rose-500 border border-white shadow-sm rounded-[1px]" title="Red Card"></div>
                                         @endforeach
                                     </div>
                                 </div>
                             @endif
                         </div>
                         <div class="flex flex-col min-w-0 w-full overflow-hidden items-center">
                             <h2 class="text-xs md:text-base font-bold truncate leading-tight w-full text-[var(--text-main)]">{{ $fixture->homeTeam->team->name }}</h2>
                             
                             <!-- Scorers -->
                             <div class="mt-1 space-y-0.5 w-full">
                                @foreach($goals->where('team_id', $fixture->home_team_id) as $goal)
                                    <div class="text-[10px] whitespace-nowrap leading-tight text-[var(--text-muted)]">
                                        {{ $goal->player->user->name ?? $goal->player_name }} <span class="font-mono font-bold opacity-70">{{ $goal->minute }}'</span>
                                    </div>
                                @endforeach
                             </div>
                         </div>
                    </div>

                    <!-- Score Center -->
                    <div class="flex flex-col items-center justify-start mx-1 md:mx-4 shrink-0 z-10 pt-2">
                        <div class="text-3xl md:text-5xl font-black font-mono tracking-tighter flex items-center gap-2 leading-none transition-colors text-[var(--text-main)]">
                            <span>{{ $fixture->home_score ?? 0 }}</span>
                            <span class="opacity-30 text-xl md:text-3xl">-</span>
                            <span>{{ $fixture->away_score ?? 0 }}</span>
                        </div>
                        
                        <!-- Penalty Score (if applicable) -->
                        @if($fixture->has_penalties)
                            <div class="text-xs font-bold text-[var(--text-muted)] mt-1">
                                Penalties: <span class="text-[var(--accent)]">{{ $fixture->home_penalty_score }} - {{ $fixture->away_penalty_score }}</span>
                            </div>
                        @endif
                        
                        <!-- Match State & Timer -->
                        <div class="flex flex-col items-center">
                            <span x-show="matchState && matchState !== 'NOT_STARTED'" class="text-[10px] font-bold uppercase tracking-wider mb-0.5 text-[var(--text-muted)]" x-text="matchState.replace(/_/g, ' ')"></span>

                            <div class="px-2.5 py-0.5 rounded-full text-[10px] md:text-xs font-mono font-bold border transition-colors duration-300 flex items-center gap-1.5 shadow-sm bg-[var(--bg-element)] text-[var(--accent)] border-[var(--border)]">
                                <span x-show="isRunning" class="w-1.5 h-1.5 rounded-full animate-pulse bg-[var(--accent)]"></span>
                                <span x-text="timeDisplay"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Away Team -->
                    <div class="flex-1 flex flex-col items-center gap-2 min-w-0 text-center">
                         <div class="w-12 h-12 md:w-16 md:h-16 rounded-full shadow-sm flex-shrink-0 flex items-center justify-center transition-colors duration-300 relative overflow-hidden bg-[var(--bg-card)] border border-[var(--border)]">
                             @if($fixture->awayTeam->team->logo)
                                 <img src="{{ \Illuminate\Support\Facades\Storage::url($fixture->awayTeam->team->logo) }}" class="w-full h-full object-cover">
                             @else
                                 <img src="{{ asset('images/default.jpeg') }}" class="w-full h-full object-cover opacity-80">
                             @endif

                             <!-- Red Card Indicator -->
                             @if(isset($redCards[$fixture->away_team_id]))
                                 <div class="absolute top-0 right-0 p-1 bg-white/80 rounded-bl-lg backdrop-blur-sm">
                                     <div class="flex gap-0.5">
                                         @foreach($redCards[$fixture->away_team_id] as $rc)
                                            <div class="w-2.5 h-3.5 bg-rose-500 border border-white shadow-sm rounded-[1px]" title="Red Card"></div>
                                         @endforeach
                                     </div>
                                 </div>
                             @endif
                         </div>
                         <div class="flex flex-col min-w-0 w-full overflow-hidden items-center">
                             <h2 class="text-xs md:text-base font-bold truncate leading-tight w-full text-[var(--text-main)]">{{ $fixture->awayTeam->team->name }}</h2>
                             
                             <!-- Scorers -->
                             <div class="mt-1 space-y-0.5 w-full">
                                @foreach($goals->where('team_id', $fixture->away_team_id) as $goal)
                                    <div class="text-[10px] whitespace-nowrap leading-tight text-[var(--text-muted)]">
                                        {{ $goal->player->user->name ?? $goal->player_name }} <span class="font-mono font-bold opacity-70">{{ $goal->minute }}'</span>
                                    </div>
                                @endforeach
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modern Scrollable Tabs -->
        <div class="border-t transition-colors duration-300 overflow-x-auto no-scrollbar border-[var(--border)] bg-[var(--bg-card)]">
            <div class="container mx-auto max-w-lg flex min-w-max md:min-w-0">
                @foreach(['summary' => 'Timeline', 'lineups' => 'Lineups', 'info' => 'Info & Stats'] as $key => $label)
                <button @click="activeTab = '{{ $key }}'" 
                        class="flex-1 px-4 py-2.5 text-xs font-bold uppercase tracking-wide border-b-[3px] transition-all duration-200 whitespace-nowrap"
                        :class="activeTab === '{{ $key }}' 
                            ? 'border-[var(--accent)] text-[var(--accent)] bg-[var(--bg-element)]' 
                            : 'border-transparent text-[var(--text-muted)] hover:text-[var(--text-main)]'">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="container mx-auto max-w-lg p-3 pb-20 md:py-6 relative z-0">
        
        <!-- Summary Tab (Paginated Timeline) -->
        <div x-show="activeTab === 'summary'" x-transition.opacity.duration.300ms>
            
            <!-- Penalty Shootout Section -->
            @if($fixture->has_penalties && $penalties->count() > 0)
            <div class="rounded-2xl border shadow-sm overflow-hidden mb-6 bg-[var(--bg-card)] border-[var(--border)]">
                <div class="p-4 border-b border-[var(--border)] bg-[var(--bg-element)]">
                    <h3 class="text-sm font-bold uppercase text-[var(--text-main)] tracking-wider text-center">Penalty Shootout</h3>
                    <div class="flex items-center justify-center gap-6 mt-3">
                        <div class="text-center">
                            <div class="text-xs font-bold text-[var(--text-muted)] mb-1">{{ $fixture->homeTeam->team->name }}</div>
                            <div class="text-3xl font-black text-blue-600 font-mono">{{ $fixture->home_penalty_score }}</div>
                        </div>
                        <div class="text-xl font-black text-[var(--text-muted)]">-</div>
                        <div class="text-center">
                            <div class="text-xs font-bold text-[var(--text-muted)] mb-1">{{ $fixture->awayTeam->team->name }}</div>
                            <div class="text-3xl font-black text-rose-600 font-mono">{{ $fixture->away_penalty_score }}</div>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="space-y-2">
                        @foreach($penalties as $penalty)
                            <div class="flex items-center justify-between p-3 rounded-lg border bg-[var(--bg-element)] border-[var(--border)]">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-mono font-bold px-2 py-1 rounded bg-[var(--bg-card)] border border-[var(--border)] text-[var(--text-muted)]">#{{ $penalty->attempt_number }}</span>
                                    <span class="text-sm font-bold text-[var(--text-main)]">{{ $penalty->player_name ?? 'Guest' }}</span>
                                    <span class="text-xs text-[var(--text-muted)]">({{ $penalty->team_id == $fixture->home_team_id ? $fixture->homeTeam->team->name : $fixture->awayTeam->team->name }})</span>
                                </div>
                                <span class="text-xs font-bold px-2 py-1 rounded {{ $penalty->scored ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }}">
                                    {{ $penalty->scored ? 'SCORED' : 'MISSED' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            
            @if($events->count() > 0)
            <div class="relative pl-6 space-y-8 my-4">
                <!-- Timeline Line -->
                <div class="absolute left-[11px] top-2 bottom-6 w-0.5 rounded-full bg-[var(--border)]"></div>

                @foreach($events as $event)
                    <div class="relative group">
                        <!-- Timeline Dot -->
                        <div class="absolute -left-[29px] top-0 flex items-center justify-center">
                            <div class="w-8 h-8 rounded-full border-4 flex items-center justify-center text-[10px] font-black z-10 transition-transform group-hover:scale-110 shadow-sm"
                                 class="{{ $event->event_type == 'GOAL' ? 'bg-orange-500 border-orange-100 text-white' : 
                                         ($event->event_type == 'RED_CARD' ? 'bg-rose-500 border-rose-100 text-white' :
                                         ($event->event_type == 'YELLOW_CARD' ? 'bg-amber-400 border-amber-100 text-white' :
                                         ($event->event_type == 'SUB' ? 'bg-purple-500 border-purple-100 text-white' : 'bg-zinc-500 border-zinc-100 text-white'))) }}">
                                
                                @if($event->event_type == 'GOAL') <i class="fa-solid fa-futbol"></i>
                                @elseif($event->event_type == 'RED_CARD') <span class="w-2.5 h-3.5 bg-white rounded-[1px] shadow-sm transform -rotate-12"></span>
                                @elseif($event->event_type == 'YELLOW_CARD') <span class="w-2.5 h-3.5 bg-white rounded-[1px] shadow-sm transform -rotate-12"></span>
                                @elseif($event->event_type == 'SUB') <i class="fa-solid fa-arrow-right-arrow-left"></i>
                                @else <span class="text-[9px]">{{ $event->minute }}'</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Event Card -->
                        <div class="rounded-2xl p-4 shadow-sm border transition-colors duration-300 relative pl-16 md:pl-20 min-h-[5rem] bg-[var(--bg-card)] border-[var(--border)]">
                            
                             <!-- Player Photo (Absolute positioned left) -->
                             <div class="absolute left-3 top-3 md:left-4 md:top-4">
                                <div class="w-10 h-10 md:w-12 md:h-12 rounded-full shadow-sm relative overflow-hidden ring-1" 
                                     class="{{ $event->event_type == 'GOAL' ? 'ring-orange-500' : 'ring-white' }}">
                                    @if($event->player && $event->player->user && $event->player->user->photo)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($event->player->user->photo) }}" 
                                             class="w-full h-full object-cover">
                                    @elseif($event->player && $event->player->user)
                                        <div class="w-full h-full flex items-center justify-center font-bold text-xs md:text-sm bg-[var(--bg-element)] text-[var(--text-muted)]">
                                            <img src="{{ asset('images/defaultplayer.jpeg') }}" class="w-full h-full object-cover opacity-90">
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Team Logo Badge on Photo -->
                                @if($event->team && $event->team->team->logo)
                                    <div class="absolute -bottom-1 -right-1 w-5 h-5 md:w-6 md:h-6 rounded-full bg-white shadow-sm ring-1 ring-zinc-100 overflow-hidden">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($event->team->team->logo) }}" class="w-full h-full object-cover">
                                    </div>
                                @endif
                             </div>

                             <!-- Minute Tag -->
                            <div class="absolute top-3 right-3 text-[10px] font-mono opacity-50 font-bold text-[var(--text-muted)]">
                                {{ $event->minute }}'
                            </div>
                            
                            <!-- Header -->
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-bold text-xs md:text-sm uppercase tracking-wide" 
                                    class="{{ $event->event_type == 'GOAL' ? 'text-orange-500' : 
                                            ($event->event_type == 'RED_CARD' ? 'text-rose-500' :
                                            ($event->event_type == 'YELLOW_CARD' ? 'text-amber-500' :
                                            ($event->event_type == 'SUB' ? 'text-purple-500' : 'text-zinc-500'))) }}">
                                    {{ str_replace('_', ' ', $event->event_type) }}
                                </h3>
                                @if($event->team)
                                    <span class="text-[9px] px-1.5 py-0.5 rounded font-bold uppercase hidden md:inline-block bg-[var(--bg-element)] text-[var(--text-muted)]">
                                        {{ substr($event->team->team->name, 0, 3) }}
                                    </span>
                                @endif
                            </div>

                            <!-- Main Description -->
                             <div class="text-sm md:text-lg font-bold leading-tight text-[var(--text-main)]">
                                {{ $event->player->user->name ?? $event->player_name ?? $event->description ?? 'Player' }}
                             </div>

                             <!-- Sub / Assist Details -->
                             @if($event->event_type == 'GOAL')
                                @if($event->assistPlayer || $event->assist_player_name)
                                    <div class="mt-1 text-xs font-medium flex items-center gap-1.5 text-[var(--text-muted)]">
                                        <span class="bg-orange-500/10 text-orange-500 px-1 rounded">Ast</span>
                                        <span>{{ $event->assistPlayer->user->name ?? $event->assist_player_name }}</span>
                                    </div>
                                @endif
                             @endif

                             @if($event->event_type == 'SUB')
                                <div class="mt-2 text-xs flex flex-col gap-1 rounded p-2 border bg-[var(--bg-element)] border-[var(--border)]">
                                    <div class="flex items-center gap-2 text-rose-500">
                                        <i class="fa-solid fa-arrow-right-from-bracket rotate-180"></i>
                                        <span class="font-semibold">OUT:</span> 
                                        <span class="text-[var(--text-muted)]">{{ $event->player->user->name ?? $event->player_name }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-green-500">
                                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                        <span class="font-semibold">IN:</span> 
                                        <span class="text-[var(--text-muted)]">{{ $event->relatedPlayer->user->name ?? $event->related_player_name }}</span>
                                        @if($event->relatedPlayer && $event->relatedPlayer->user)
                                             <div class="w-5 h-5 rounded-full overflow-hidden shadow-sm">
                                                 @if($event->relatedPlayer->user->photo)
                                                     <img src="{{ \Illuminate\Support\Facades\Storage::url($event->relatedPlayer->user->photo) }}" class="w-full h-full object-cover">
                                                 @else
                                                     <img src="{{ asset('images/defaultplayer.jpeg') }}" class="w-full h-full object-cover opacity-90">
                                                 @endif
                                             </div>
                                        @endif
                                    </div>
                                </div>
                             @endif

                             @if($event->description)
                                <p class="mt-2 text-xs opacity-70 italic border-l-2 pl-2 border-[var(--border)] text-[var(--text-muted)]">
                                    "{{ $event->description }}"
                                </p>
                             @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8 flex justify-center">
                {{ $events->links('pagination::tailwind') }}
            </div>
            
            @else
                <div class="flex flex-col items-center justify-center py-20 text-center opacity-40 space-y-4">
                    <i class="fa-solid fa-stopwatch text-6xl"></i>
                    <p class="font-bold text-lg">No events yet</p>
                    <p class="text-sm max-w-[200px]">Match events will appear here live once the game starts.</p>
                </div>
            @endif
        </div>

        <!-- Lineups Tab -->
         <div x-show="activeTab === 'lineups'" style="display: none;" x-transition.opacity.duration.300ms>
            <div class="space-y-6">
                @foreach([$fixture->homeTeam, $fixture->awayTeam] as $index => $team)
                    @php $isHome = $index === 0; $teamId = $isHome ? $fixture->home_team_id : $fixture->away_team_id; @endphp
                    <div class="rounded-2xl border overflow-hidden shadow-sm bg-[var(--bg-card)] border-[var(--border)]">
                        
                        <!-- Team Header -->
                        <div class="p-3 w-full flex items-center justify-between {{ $isHome ? 'bg-indigo-600' : 'bg-pink-600' }} text-white">
                            <span class="font-bold text-sm uppercase tracking-wider">{{ $team->team->name }}</span>
                            <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded font-mono">{{ $isHome ? 'HOME' : 'AWAY' }}</span>
                        </div>
                        
                        <!-- Starters -->
                        <div class="p-3">
                            <div class="text-[10px] font-bold uppercase text-[var(--text-muted)] mb-2">Starting XI</div>
                            <div class="grid grid-cols-1 gap-2">
                                @forelse($fixture->fixturePlayers->where('team_id', $teamId)->where('is_active', true) as $p)
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border bg-[var(--bg-element)] border-[var(--border)] text-[var(--text-muted)]">
                                            {{ substr($p->player ? $p->player->user->name : ($p->custom_name ?? 'G'), 0, 1) }}
                                        </div>
                                        <div class="flex-1 text-sm font-medium text-[var(--text-main)]">
                                            {{ $p->player ? $p->player->user->name : ($p->custom_name ?? 'Guest') }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-sm italic opacity-50 py-2">Lineup not announced</div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Subs -->
                        @if($fixture->fixturePlayers->where('team_id', $teamId)->where('is_active', false)->count() > 0)
                            <div class="p-3 border-t border-[var(--border)]">
                                <div class="text-[10px] font-bold uppercase text-[var(--text-muted)] mb-2">Substitutes</div>
                                <div class="grid grid-cols-1 gap-2 opacity-70">
                                    @foreach($fixture->fixturePlayers->where('team_id', $teamId)->where('is_active', false) as $p)
                                        <div class="flex items-center gap-3">
                                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold border bg-[var(--bg-element)] border-[var(--border)] text-[var(--text-muted)]">
                                                {{ substr($p->player ? $p->player->user->name : ($p->custom_name ?? 'G'), 0, 1) }}
                                            </div>
                                            <div class="flex-1 text-xs text-[var(--text-muted)]">
                                                {{ $p->player ? $p->player->user->name : ($p->custom_name ?? 'Guest') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
         </div>

        <!-- Info Tab -->
        <div x-show="activeTab === 'info'" style="display: none;" x-transition.opacity.duration.300ms>
            <div class="rounded-2xl border shadow-sm p-4 bg-[var(--bg-card)] border-[var(--border)]">
                <h3 class="font-bold mb-4 text-sm uppercase tracking-wider text-[var(--text-muted)]">Match Details</h3>
                <div class="grid grid-cols-2 gap-4">
                     <div class="p-3 rounded-xl border bg-[var(--bg-element)] border-[var(--border)]">
                        <div class="text-[10px] uppercase font-bold text-[var(--text-muted)] mb-1">Status</div>
                        <div class="font-medium capitalize text-[var(--text-main)]">{{ str_replace('_', ' ', $fixture->status) }}</div>
                    </div>
                    <div class="p-3 rounded-xl border bg-[var(--bg-element)] border-[var(--border)]">
                        <div class="text-[10px] uppercase font-bold text-[var(--text-muted)] mb-1">Duration</div>
                        <div class="font-medium text-[var(--text-main)]">{{ $fixture->match_duration }} Mins</div>
                    </div>
                    <div class="p-3 rounded-xl border bg-[var(--bg-element)] border-[var(--border)]">
                        <div class="text-[10px] uppercase font-bold text-[var(--text-muted)] mb-1">Venue</div>
                        <div class="font-medium text-[var(--text-main)]">{{ $fixture->venue ?? 'Main Ground' }}</div>
                    </div>
                    <div class="p-3 rounded-xl border bg-[var(--bg-element)] border-[var(--border)]">
                        <div class="text-[10px] uppercase font-bold text-[var(--text-muted)] mb-1">Date</div>
                        <div class="font-medium text-[var(--text-main)]">{{ $fixture->match_date ? $fixture->match_date->format('M d, Y') : 'TBD' }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- Floating Reload Button (Live Only) -->
    @if($fixture->status == 'in_progress')
        <button @click="window.location.reload()" 
                class="fixed bottom-24 right-6 z-50 w-14 h-14 rounded-full shadow-2xl flex items-center justify-center transition-transform hover:scale-105 active:scale-95 border-4 bg-[var(--bg-card)] text-[var(--accent)] border-[var(--border)]"
                title="Refresh Live Data">
            <i class="fa-solid fa-rotate-right text-xl"></i>
        </button>
    @endif
</div>
@endsection
