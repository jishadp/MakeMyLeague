@extends('layouts.app')

@section('content')
<div class="min-h-screen transition-colors duration-300" 
     x-data="{ activeTab: 'summary', darkMode: localStorage.getItem('liveMatchDarkMode') === 'true' }"
     :class="darkMode ? 'bg-slate-900 text-slate-200' : 'bg-slate-50 text-slate-800'"
     x-init="$watch('darkMode', val => localStorage.setItem('liveMatchDarkMode', val))">
    
    <!-- Ultra-Compact Sticky Match Header -->
    <div class="sticky top-0 z-40 shadow-xl backdrop-blur-md transition-colors duration-300 border-b"
         :class="darkMode ? 'bg-slate-900/90 border-slate-800' : 'bg-white/95 border-slate-200'">
        
        <!-- Top Bar: Status & Dark Mode -->
        <div class="flex items-center justify-between px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest border-b"
             :class="darkMode ? 'border-slate-800 bg-slate-950/50 text-slate-400' : 'border-slate-100 bg-slate-50/80 text-slate-500'">
            <div class="truncate max-w-[200px]">{{ $fixture->league->name ?? 'League Match' }}</div>
            <div class="flex items-center gap-2">
                @if($fixture->status == 'in_progress')
                    <span class="flex items-center gap-1.5 text-emerald-500 animate-pulse">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> LIVE
                    </span>
                 @else
                    <span>{{ str_replace('_', ' ', $fixture->status) }}</span>
                @endif
                
                <!-- Reload Button -->
                <button @click="window.location.reload()" class="ml-2 w-5 h-5 rounded-full flex items-center justify-center transition-colors focus:outline-none focus:ring-1 focus:ring-offset-1"
                        :class="darkMode ? 'bg-slate-800 text-blue-400 hover:bg-slate-700 ring-offset-slate-900' : 'bg-slate-200 text-blue-600 hover:bg-slate-300 ring-offset-white'" title="Refresh">
                    <i class="fa-solid fa-rotate-right text-[10px]"></i>
                </button>

                <!-- Dark Mode Button -->
                <button @click="darkMode = !darkMode" class="w-5 h-5 rounded-full flex items-center justify-center transition-colors focus:outline-none focus:ring-1 focus:ring-offset-1"
                        :class="darkMode ? 'bg-slate-800 text-yellow-400 hover:bg-slate-700 ring-offset-slate-900' : 'bg-slate-200 text-slate-500 hover:bg-slate-300 ring-offset-white'">
                    <i class="fa-solid text-[10px]" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
                </button>
            </div>
        </div>

        <!-- Compact Scoreboard -->
        <div class="px-3 py-3 md:py-4">
            <div class="container mx-auto max-w-lg">
                <div class="flex items-start justify-between gap-1 md:gap-4 relative">
                    
                    <!-- Home Team -->
                    <div class="flex-1 flex flex-col items-start gap-1 min-w-0">
                        <div class="flex items-center gap-2 md:gap-3 w-full">
                             <div class="w-8 h-8 md:w-12 md:h-12 rounded-full p-1 shadow-sm flex-shrink-0 flex items-center justify-center transition-colors duration-300 relative"
                                  :class="darkMode ? 'bg-slate-800' : 'bg-white border border-slate-100'">
                                 @if($fixture->homeTeam->team->logo_url)
                                     <img src="{{ $fixture->homeTeam->team->logo_url }}" class="max-w-full max-h-full object-contain">
                                 @else
                                     <span class="font-black text-sm md:text-lg" :class="darkMode ? 'text-white' : 'text-slate-900'">{{ substr($fixture->homeTeam->team->name, 0, 1) }}</span>
                                 @endif
                                 
                                 <!-- Red Card Indicator -->
                                 @if(isset($redCards[$fixture->home_team_id]))
                                     <div class="absolute -top-1 -right-1 flex">
                                         @foreach($redCards[$fixture->home_team_id] as $rc)
                                            <div class="w-3 h-4 bg-rose-500 border border-white shadow-sm rounded-[1px] -ml-1 first:ml-0" title="Red Card"></div>
                                         @endforeach
                                     </div>
                                 @endif
                             </div>
                             <div class="flex flex-col min-w-0 overflow-hidden">
                                 <h2 class="text-xs md:text-sm font-bold truncate leading-tight">{{ $fixture->homeTeam->team->name }}</h2>
                                 <span class="text-[9px] uppercase tracking-wider opacity-60">Home</span>
                             </div>
                        </div>
                        
                        <!-- Home Scorers (Visible below team) -->
                        <div class="pl-1 mt-1 space-y-0.5">
                            @foreach($goals->where('team_id', $fixture->home_team_id) as $goal)
                                <div class="text-[10px] whitespace-nowrap leading-tight" :class="darkMode ? 'text-slate-400' : 'text-slate-600'">
                                    {{ $goal->player->user->name ?? $goal->player_name }} <span class="font-mono font-bold opacity-70">{{ $goal->minute }}'</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Score Center -->
                    <div class="flex flex-col items-center justify-start mx-1 md:mx-4 shrink-0 z-10 pt-1">
                        <div class="text-2xl md:text-4xl font-black font-mono tracking-tighter flex items-center gap-1.5 leading-none transition-colors"
                             :class="darkMode ? 'text-white' : 'text-slate-900'">
                            <span>{{ $fixture->home_score ?? 0 }}</span>
                            <span class="opacity-30 text-lg md:text-2xl">-</span>
                            <span>{{ $fixture->away_score ?? 0 }}</span>
                        </div>
                        
                        <!-- Timer Badge -->
                        <div class="mt-1 px-2 py-0.5 rounded text-[10px] md:text-xs font-mono font-bold border transition-colors duration-300 flex items-center gap-1 shadow-sm"
                             :class="darkMode ? 'bg-slate-800 text-emerald-400 border-slate-700' : 'bg-emerald-50 text-emerald-600 border-emerald-100'"
                             x-data="{ time: '00:00', start: '{{ $fixture->started_at }}', status: '{{ $fixture->status }}' }"
                             x-init="
                                if(status == 'in_progress' && start) {
                                    setInterval(() => {
                                        let startTime = new Date(start).getTime();
                                        let now = new Date().getTime();
                                        let diff = Math.floor((now - startTime) / 1000);
                                        let m = Math.floor(diff / 60);
                                        let s = diff % 60;
                                        time = m + '\'' + (s < 10 ? '0' : '') + s;
                                    }, 1000);
                                } else if (status == 'completed') {
                                    time = 'FT';
                                } else {
                                    time = '--:--';
                                }
                             ">
                             <span x-show="status == 'in_progress'" class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                            <span x-text="time"></span>
                        </div>
                    </div>

                    <!-- Away Team -->
                    <div class="flex-1 flex flex-col items-end gap-1 min-w-0 text-right">
                        <div class="flex flex-row-reverse items-center gap-2 md:gap-3 w-full">
                             <div class="w-8 h-8 md:w-12 md:h-12 rounded-full p-1 shadow-sm flex-shrink-0 flex items-center justify-center transition-colors duration-300 relative"
                                  :class="darkMode ? 'bg-slate-800' : 'bg-white border border-slate-100'">
                                 @if($fixture->awayTeam->team->logo_url)
                                     <img src="{{ $fixture->awayTeam->team->logo_url }}" class="max-w-full max-h-full object-contain">
                                 @else
                                     <span class="font-black text-sm md:text-lg" :class="darkMode ? 'text-white' : 'text-slate-900'">{{ substr($fixture->awayTeam->team->name, 0, 1) }}</span>
                                 @endif

                                 <!-- Red Card Indicator -->
                                 @if(isset($redCards[$fixture->away_team_id]))
                                     <div class="absolute -top-1 -left-1 flex flex-row-reverse">
                                         @foreach($redCards[$fixture->away_team_id] as $rc)
                                            <div class="w-3 h-4 bg-rose-500 border border-white shadow-sm rounded-[1px] -mr-1 first:mr-0" title="Red Card"></div>
                                         @endforeach
                                     </div>
                                 @endif
                             </div>
                             <div class="flex flex-col min-w-0 overflow-hidden text-right">
                                 <h2 class="text-xs md:text-sm font-bold truncate leading-tight">{{ $fixture->awayTeam->team->name }}</h2>
                                 <span class="text-[9px] uppercase tracking-wider opacity-60">Away</span>
                             </div>
                        </div>

                        <!-- Away Scorers -->
                        <div class="pr-1 mt-1 space-y-0.5 flex flex-col items-end">
                            @foreach($goals->where('team_id', $fixture->away_team_id) as $goal)
                                <div class="text-[10px] whitespace-nowrap leading-tight" :class="darkMode ? 'text-slate-400' : 'text-slate-600'">
                                    {{ $goal->player->user->name ?? $goal->player_name }} <span class="font-mono font-bold opacity-70">{{ $goal->minute }}'</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
        <!-- Modern Scrollable Tabs -->
        <div class="border-t transition-colors duration-300 overflow-x-auto no-scrollbar"
             :class="darkMode ? 'border-slate-800 bg-slate-900/50' : 'border-slate-100 bg-slate-50/50'">
            <div class="container mx-auto max-w-lg flex min-w-max md:min-w-0">
                @foreach(['summary' => 'Timeline', 'lineups' => 'Lineups', 'info' => 'Info & Stats'] as $key => $label)
                <button @click="activeTab = '{{ $key }}'" 
                        class="flex-1 px-4 py-2.5 text-xs font-bold uppercase tracking-wide border-b-[3px] transition-all duration-200 whitespace-nowrap"
                        :class="activeTab === '{{ $key }}' 
                            ? (darkMode ? 'border-emerald-500 text-emerald-400 bg-slate-800/50' : 'border-emerald-600 text-emerald-700 bg-white shadow-sm') 
                            : (darkMode ? 'border-transparent text-slate-500 hover:text-slate-300' : 'border-transparent text-slate-400 hover:text-slate-600')">
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
            
            @if($events->count() > 0)
            <div class="relative pl-6 space-y-8 my-4">
                <!-- Timeline Line -->
                <div class="absolute left-[11px] top-2 bottom-6 w-0.5 rounded-full" 
                     :class="darkMode ? 'bg-slate-800' : 'bg-slate-200'"></div>

                @foreach($events as $event)
                    <div class="relative group">
                        <!-- Timeline Dot -->
                        <div class="absolute -left-[29px] top-0 flex items-center justify-center">
                            <div class="w-8 h-8 rounded-full border-4 flex items-center justify-center text-[10px] font-black z-10 transition-transform group-hover:scale-110 shadow-sm"
                                 class="{{ $event->event_type == 'GOAL' ? 'bg-emerald-500 border-emerald-100 text-white' : 
                                         ($event->event_type == 'RED_CARD' ? 'bg-rose-500 border-rose-100 text-white' :
                                         ($event->event_type == 'YELLOW_CARD' ? 'bg-amber-400 border-amber-100 text-white' :
                                         ($event->event_type == 'SUB' ? 'bg-purple-500 border-purple-100 text-white' : 'bg-slate-500 border-slate-100 text-white'))) }}">
                                
                                @if($event->event_type == 'GOAL') <i class="fa-solid fa-futbol"></i>
                                @elseif($event->event_type == 'RED_CARD') <span class="w-2.5 h-3.5 bg-white rounded-[1px] shadow-sm transform -rotate-12"></span>
                                @elseif($event->event_type == 'YELLOW_CARD') <span class="w-2.5 h-3.5 bg-white rounded-[1px] shadow-sm transform -rotate-12"></span>
                                @elseif($event->event_type == 'SUB') <i class="fa-solid fa-arrow-right-arrow-left"></i>
                                @else <span class="text-[9px]">{{ $event->minute }}'</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Event Card -->
                        <div class="rounded-2xl p-4 shadow-sm border transition-colors duration-300 relative pl-16 md:pl-20 min-h-[5rem]"
                             :class="darkMode ? 
                                ( '{{ $event->event_type == 'RED_CARD' ? 'bg-rose-900/10 border-rose-500/20' : ($event->event_type == 'YELLOW_CARD' ? 'bg-amber-900/10 border-amber-500/20' : 'bg-slate-800 border-slate-700') }}' ) : 
                                ( '{{ $event->event_type == 'RED_CARD' ? 'bg-rose-50 border-rose-100' : ($event->event_type == 'YELLOW_CARD' ? 'bg-amber-50 border-amber-100' : 'bg-white border-slate-100') }}' )">
                            
                             <!-- Player Photo (Absolute positioned left) -->
                             <div class="absolute left-3 top-3 md:left-4 md:top-4">
                                @if($event->player && $event->player->user && $event->player->user->photo)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($event->player->user->photo) }}" 
                                         class="w-10 h-10 md:w-12 md:h-12 rounded-full object-cover border-2 shadow-sm {{ $event->event_type == 'GOAL' ? 'border-emerald-500' : 'border-white' }}">
                                @else
                                    <div class="w-10 h-10 md:w-12 md:h-12 rounded-full flex items-center justify-center font-bold text-xs md:text-sm border-2 shadow-sm"
                                         :class="darkMode ? 'bg-slate-700 text-slate-400 border-slate-600' : 'bg-slate-100 text-slate-500 border-white'">
                                        {{ substr($event->player->user->name ?? $event->player_name ?? 'P', 0, 1) }}
                                    </div>
                                @endif
                                
                                <!-- Team Logo Badge on Photo -->
                                @if($event->team && $event->team->team->logo_url)
                                    <div class="absolute -bottom-1 -right-1 w-5 h-5 md:w-6 md:h-6 rounded-full bg-white shadow-sm border p-0.5 flex items-center justify-center">
                                        <img src="{{ $event->team->team->logo_url }}" class="max-w-full max-h-full object-contain">
                                    </div>
                                @endif
                             </div>

                             <!-- Minute Tag -->
                            <div class="absolute top-3 right-3 text-[10px] font-mono opacity-50 font-bold" :class="darkMode ? 'text-slate-400' : 'text-slate-400'">
                                {{ $event->minute }}'
                            </div>
                            
                            <!-- Header -->
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-bold text-xs md:text-sm uppercase tracking-wide" 
                                    class="{{ $event->event_type == 'GOAL' ? 'text-emerald-500' : 
                                            ($event->event_type == 'RED_CARD' ? 'text-rose-500' :
                                            ($event->event_type == 'YELLOW_CARD' ? 'text-amber-500' :
                                            ($event->event_type == 'SUB' ? 'text-purple-500' : 'text-slate-500'))) }}">
                                    {{ str_replace('_', ' ', $event->event_type) }}
                                </h3>
                                @if($event->team)
                                    <span class="text-[9px] px-1.5 py-0.5 rounded font-bold uppercase hidden md:inline-block" 
                                          :class="darkMode ? 'bg-slate-700 text-slate-300' : 'bg-slate-100 text-slate-500'">
                                        {{ substr($event->team->team->name, 0, 3) }}
                                    </span>
                                @endif
                            </div>

                            <!-- Main Description -->
                             <div class="text-sm md:text-lg font-bold leading-tight" :class="darkMode ? 'text-white' : 'text-slate-800'">
                                {{ $event->player->user->name ?? $event->player_name ?? 'Player' }}
                             </div>

                             <!-- Sub / Assist Details -->
                             @if($event->event_type == 'GOAL')
                                @if($event->assistPlayer || $event->assist_player_name)
                                    <div class="mt-1 text-xs font-medium flex items-center gap-1.5" :class="darkMode ? 'text-slate-400' : 'text-slate-500'">
                                        <span class="bg-emerald-500/10 text-emerald-500 px-1 rounded">Ast</span>
                                        <span>{{ $event->assistPlayer->user->name ?? $event->assist_player_name }}</span>
                                    </div>
                                @endif
                             @endif

                             @if($event->event_type == 'SUB')
                                <div class="mt-2 text-xs flex flex-col gap-1 rounded bg-slate-50/50 p-2 border border-slate-100" :class="darkMode ? '!bg-slate-900/50 !border-slate-700' : ''">
                                    <div class="flex items-center gap-2 text-rose-500">
                                        <i class="fa-solid fa-arrow-right-from-bracket rotate-180"></i>
                                        <span class="font-semibold">OUT:</span> 
                                        <span class="text-slate-600" :class="darkMode ? '!text-slate-400' : ''">{{ $event->player->user->name ?? $event->player_name }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-emerald-500">
                                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                        <span class="font-semibold">IN:</span> 
                                        <span class="text-slate-600" :class="darkMode ? '!text-slate-400' : ''">{{ $event->relatedPlayer->user->name ?? $event->related_player_name }}</span>
                                        @if($event->relatedPlayer && $event->relatedPlayer->user && $event->relatedPlayer->user->photo)
                                             <img src="{{ \Illuminate\Support\Facades\Storage::url($event->relatedPlayer->user->photo) }}" class="w-4 h-4 rounded-full object-cover">
                                        @endif
                                    </div>
                                </div>
                             @endif

                             @if($event->description)
                                <p class="mt-2 text-xs opacity-70 italic border-l-2 pl-2" :class="darkMode ? 'border-slate-600 text-slate-400' : 'border-slate-300 text-slate-500'">
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
                    <div class="rounded-2xl border overflow-hidden shadow-sm" 
                         :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-200'">
                        
                        <!-- Team Header -->
                        <div class="p-3 w-full flex items-center justify-between {{ $isHome ? 'bg-indigo-600' : 'bg-pink-600' }} text-white">
                            <span class="font-bold text-sm uppercase tracking-wider">{{ $team->team->name }}</span>
                            <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded font-mono">{{ $isHome ? 'HOME' : 'AWAY' }}</span>
                        </div>
                        
                        <!-- Starters -->
                        <div class="p-3">
                            <div class="text-[10px] font-bold uppercase text-slate-400 mb-2">Starting XI</div>
                            <div class="grid grid-cols-1 gap-2">
                                @forelse($fixture->fixturePlayers->where('team_id', $teamId)->where('is_active', true) as $p)
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border"
                                             :class="darkMode ? 'bg-slate-800 border-slate-700 text-slate-300' : 'bg-slate-50 border-slate-100 text-slate-600'">
                                            {{ substr($p->player ? $p->player->user->name : ($p->custom_name ?? 'G'), 0, 1) }}
                                        </div>
                                        <div class="flex-1 text-sm font-medium" :class="darkMode ? 'text-slate-200' : 'text-slate-800'">
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
                            <div class="p-3 border-t" :class="darkMode ? 'border-slate-800' : 'border-slate-100'">
                                <div class="text-[10px] font-bold uppercase text-slate-400 mb-2">Substitutes</div>
                                <div class="grid grid-cols-1 gap-2 opacity-70">
                                    @foreach($fixture->fixturePlayers->where('team_id', $teamId)->where('is_active', false) as $p)
                                        <div class="flex items-center gap-3">
                                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold border"
                                                 :class="darkMode ? 'bg-slate-800 border-slate-700 text-slate-400' : 'bg-slate-50 border-slate-100 text-slate-500'">
                                                {{ substr($p->player ? $p->player->user->name : ($p->custom_name ?? 'G'), 0, 1) }}
                                            </div>
                                            <div class="flex-1 text-xs" :class="darkMode ? 'text-slate-400' : 'text-slate-600'">
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
            <div class="rounded-2xl border shadow-sm p-4" :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-200'">
                <h3 class="font-bold mb-4 text-sm uppercase tracking-wider" :class="darkMode ? 'text-slate-400' : 'text-slate-500'">Match Details</h3>
                <div class="grid grid-cols-2 gap-4">
                     <div class="p-3 rounded-xl border" :class="darkMode ? 'bg-slate-800 border-slate-700' : 'bg-slate-50 border-slate-100'">
                        <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Status</div>
                        <div class="font-medium capitalize" :class="darkMode ? 'text-white' : 'text-slate-900'">{{ str_replace('_', ' ', $fixture->status) }}</div>
                    </div>
                    <div class="p-3 rounded-xl border" :class="darkMode ? 'bg-slate-800 border-slate-700' : 'bg-slate-50 border-slate-100'">
                        <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Duration</div>
                        <div class="font-medium" :class="darkMode ? 'text-white' : 'text-slate-900'">{{ $fixture->match_duration }} Mins</div>
                    </div>
                    <div class="p-3 rounded-xl border" :class="darkMode ? 'bg-slate-800 border-slate-700' : 'bg-slate-50 border-slate-100'">
                        <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Venue</div>
                        <div class="font-medium" :class="darkMode ? 'text-white' : 'text-slate-900'">{{ $fixture->venue ?? 'Main Ground' }}</div>
                    </div>
                    <div class="p-3 rounded-xl border" :class="darkMode ? 'bg-slate-800 border-slate-700' : 'bg-slate-50 border-slate-100'">
                        <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Date</div>
                        <div class="font-medium" :class="darkMode ? 'text-white' : 'text-slate-900'">{{ $fixture->match_date ? $fixture->match_date->format('M d, Y') : 'TBD' }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- Floating Reload Button (Live Only) -->
    @if($fixture->status == 'in_progress')
        <button @click="window.location.reload()" 
                class="fixed bottom-24 right-6 z-50 w-14 h-14 rounded-full shadow-2xl flex items-center justify-center transition-transform hover:scale-105 active:scale-95 border-4"
                :class="darkMode ? 'bg-slate-800 text-emerald-400 border-slate-700 shadow-slate-900/50' : 'bg-white text-emerald-600 border-emerald-50 shadow-emerald-100'"
                title="Refresh Live Data">
            <i class="fa-solid fa-rotate-right text-xl"></i>
        </button>
    @endif
</div>
@endsection
