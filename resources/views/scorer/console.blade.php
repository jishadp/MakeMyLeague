@extends('layouts.app')

@section('content')
<style>
    /* Theme Variables */
    .theme-dark {
        --bg-page: #09090b; --bg-header: #18181b; --bg-card: #18181b; --bg-element: #27272a; --bg-hover: #27272a; --text-main: #e4e4e7; --text-muted: #a1a1aa; --border: #27272a; --accent: #f97316;
    }
    .theme-white {
        --bg-page: #f3f4f6; --bg-header: #ffffff; --bg-card: #ffffff; --bg-element: #f3f4f6; --bg-hover: #f9fafb; --text-main: #18181b; --text-muted: #71717a; --border: #e4e4e7; --accent: #f97316;
    }
    .theme-green {
        --bg-page: #f0fdf4; --bg-header: #ffffff; --bg-card: #ffffff; --bg-element: #dcfce7; --bg-hover: #f0fdf4; --text-main: #14532d; --text-muted: #15803d; --border: #bbf7d0; --accent: #16a34a;
    }
    .theme-transition { transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1); }
</style>

<div class="min-h-screen theme-transition bg-[var(--bg-page)] text-[var(--text-main)] pb-24 lg:pb-0 relative overflow-x-hidden selection:bg-blue-100 selection:text-blue-900" 
     :class="'theme-' + theme"
     x-data="scorerConsole()"
     x-init="$watch('theme', val => localStorage.setItem('scorerTheme', val))">

    <!-- Modern Sticky Header -->
    <div class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200 shadow-sm transition-all duration-300">
        <div class="container mx-auto px-4 py-3 flex flex-col gap-2">
            
            <!-- Match Status & Header -->
            <div class="flex justify-between items-center mb-2">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full animate-pulse" 
                          :class="status == 'in_progress' ? 'bg-emerald-500' : (status == 'completed' ? 'bg-slate-400' : 'bg-amber-400')"></span>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500" x-text="matchState.replace(/_/g, ' ')"></span>
                </div>
                
                <div class="flex items-center gap-2">
                    <a href="{{ route('matches.live', $fixture->slug) }}" target="_blank" class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-200 transition-colors shadow-sm" title="Public View">
                        <i class="fa-solid fa-arrow-up-right-from-square text-sm"></i>
                    </a>
                    <button @click="shareMatch()" class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center hover:bg-emerald-200 transition-colors shadow-sm" title="Share">
                        <i class="fa-brands fa-whatsapp text-lg"></i>
                    </button>
                    <!-- Theme Switcher -->
                    <div class="flex items-center gap-1 bg-slate-100 rounded-full p-0.5 border border-slate-200 ms-1">
                        <button @click="theme = 'dark'" class="w-6 h-6 rounded-full flex items-center justify-center transition-all" :class="theme === 'dark' ? 'bg-white shadow text-slate-900' : 'text-slate-400 hover:text-slate-600'"><i class="fa-solid fa-moon text-[10px]"></i></button>
                        <button @click="theme = 'white'" class="w-6 h-6 rounded-full flex items-center justify-center transition-all" :class="theme === 'white' ? 'bg-white shadow text-slate-900' : 'text-slate-400 hover:text-slate-600'"><i class="fa-solid fa-sun text-[10px]"></i></button>
                        <button @click="theme = 'green'" class="w-6 h-6 rounded-full flex items-center justify-center transition-all" :class="theme === 'green' ? 'bg-white shadow text-slate-900' : 'text-slate-400 hover:text-slate-600'"><i class="fa-solid fa-leaf text-[10px]"></i></button>
                    </div>
                    <!-- Header Timer Display -->
                    <div class="font-mono font-black text-2xl tracking-tight text-slate-800 tabular-nums ms-2">
                        <span x-text="timeDisplay"></span>
                    </div>
                </div>
            </div>

            <!-- Scoreboard -->
            <!-- Scoreboard -->
            <div class="flex items-center justify-between gap-2 mt-4 relative">
                <!-- Home Team -->
                <div class="flex-1 flex flex-col items-center gap-2 min-w-0 text-center">
                    <div class="w-12 h-12 md:w-16 md:h-16 rounded-full shadow-sm flex-shrink-0 flex items-center justify-center bg-white border border-slate-100 relative overflow-hidden">
                        @if($fixture->homeTeam->team->logo)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($fixture->homeTeam->team->logo) }}" class="w-full h-full object-cover">
                        @else
                            <img src="{{ asset('images/default.jpeg') }}" class="w-full h-full object-cover opacity-80">
                        @endif
                    </div>
                    <div class="flex flex-col min-w-0 w-full overflow-hidden items-center">
                        <h2 class="text-xs md:text-base font-bold truncate leading-tight w-full">{{ $fixture->homeTeam->team->name }}</h2>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Home</span>
                    </div>
                </div>

                <!-- Score -->
                <div class="flex flex-col items-center justify-start mx-2 shrink-0 z-10">
                    <div class="text-3xl md:text-5xl font-black font-mono tracking-tighter flex items-center gap-2 leading-none text-slate-900">
                        <span x-text="homeScore">{{ $fixture->home_score ?? 0 }}</span>
                        <span class="opacity-30 text-xl md:text-3xl">-</span>
                        <span x-text="awayScore">{{ $fixture->away_score ?? 0 }}</span>
                    </div>
                </div>

                <!-- Away Team -->
                <div class="flex-1 flex flex-col items-center gap-2 min-w-0 text-center">
                    <div class="w-12 h-12 md:w-16 md:h-16 rounded-full shadow-sm flex-shrink-0 flex items-center justify-center bg-white border border-slate-100 relative overflow-hidden">
                        @if($fixture->awayTeam->team->logo)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($fixture->awayTeam->team->logo) }}" class="w-full h-full object-cover">
                        @else
                            <img src="{{ asset('images/default.jpeg') }}" class="w-full h-full object-cover opacity-80">
                        @endif
                    </div>
                    <div class="flex flex-col min-w-0 w-full overflow-hidden items-center">
                        <h2 class="text-xs md:text-base font-bold truncate leading-tight w-full">{{ $fixture->awayTeam->team->name }}</h2>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Away</span>
                    </div>
                </div>
            </div>
            
            <!-- Moved Match Controls (Below Scoreboard) -->
            <div class="bg-slate-800 text-white rounded-xl p-3 shadow-lg mt-4">
                <div class="flex items-center justify-between">
                     <div class="flex items-center gap-3">
                         <span class="text-xs font-bold uppercase tracking-wide text-slate-400">Control Panel</span>
                         <div class="w-px h-6 bg-slate-600 mx-1"></div>
                         <div class="font-mono font-black text-xl tabular-nums leading-none tracking-tight text-white">
                            <span x-text="timeDisplay"></span>
                         </div>
                         <!-- Period Info (Added Time) -->
                         <div x-show="periodInfo" class="ms-3 px-2 py-0.5 rounded bg-amber-500/20 border border-amber-500/40 text-[10px] font-bold text-amber-300 uppercase tracking-wide">
                            <span x-text="periodInfo"></span>
                         </div>
                    </div>

                    <!-- Main Controls -->
                    <div class="flex items-center gap-2">
                         <button @click="togglePause()" class="w-10 h-10 rounded-full flex items-center justify-center transition-all active:scale-95"
                                :class="isRunning ? 'bg-slate-700 hover:bg-slate-600 text-rose-400' : 'bg-emerald-500 hover:bg-emerald-400 text-white shadow-lg shadow-emerald-500/30'">
                            <i class="fa-solid" :class="isRunning ? 'fa-pause' : 'fa-play ps-0.5'"></i>
                        </button>
                        
                        <button @click="showTimeAdjust = !showTimeAdjust" class="w-9 h-9 rounded-full bg-slate-700 text-slate-300 hover:text-white hover:bg-slate-600 flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-gear text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Expanded Controls Panel -->
                <div x-show="showTimeAdjust" style="display: none;" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="mt-3 pt-3 border-t border-slate-700">
                    
                    <!-- Phase Controls -->
                    <div class="mb-4">
                         <h4 class="text-[10px] font-bold uppercase text-slate-400 mb-2">Match Phase Actions</h4>
                         <div class="grid grid-cols-2 gap-2">
                             <button @click="changeState('HALF_TIME')" x-show="matchState == 'FIRST_HALF'" class="p-2 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/50 hover:bg-indigo-500/30 font-bold text-xs">End 1st Half</button>
                             <button @click="changeState('SECOND_HALF')" x-show="matchState == 'HALF_TIME'" class="p-2 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/50 hover:bg-emerald-500/30 font-bold text-xs">Start 2nd Half</button>
                             <button @click="changeState('FULL_TIME')" x-show="matchState == 'SECOND_HALF' || matchState == 'EXTRA_TIME_SECOND'" class="p-2 rounded bg-rose-500/20 text-rose-300 border border-rose-500/50 hover:bg-rose-500/30 font-bold text-xs">End Match</button>
                             <button @click="changeState('EXTRA_TIME_FIRST')" x-show="(matchState == 'SECOND_HALF' || matchState == 'FULL_TIME') && matchState != 'EXTRA_TIME_SECOND'" class="p-2 rounded bg-purple-500/20 text-purple-300 border border-purple-500/50 hover:bg-purple-500/30 font-bold text-xs">Start Overtime (ET)</button>
                         </div>
                    </div>

                    <!-- Injury Time Direct Access -->
                    <div class="mb-4 bg-slate-900/50 p-2 rounded border border-slate-700">
                        <div class="text-[10px] text-slate-400 uppercase font-bold mb-1">Add Injury Time (Mins)</div>
                        <div class="flex items-center gap-2">
                             <input type="number" x-model="injuryTimeInput" class="w-full bg-slate-800 border border-slate-600 rounded px-2 py-1 text-white font-mono text-center focus:border-emerald-500 focus:outline-none" @keydown.enter="submitInjuryTime()">
                             <button @click="submitInjuryTime()" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded uppercase tracking-wide">Update</button>
                        </div>
                    </div>
                    
                    <!-- Manual Adjust -->
                    <div>
                        <h4 class="text-[10px] font-bold uppercase text-slate-400 mb-2">Manual Clock (Minute)</h4>
                        <div class="flex items-center gap-2 bg-slate-900/50 rounded p-2 border border-slate-700">
                             <input type="number" x-model="manualMinuteInput" class="w-full bg-slate-800 border border-slate-600 rounded px-2 py-1 text-white font-mono text-center focus:border-blue-500 focus:outline-none" @keydown.enter="setManualTime()">
                             <button @click="setManualTime()" class="px-3 py-1 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded uppercase tracking-wide">Set</button>
                        </div>
                    </div>
                    
                    <!-- Match Duration Adjust -->
                    <div class="mt-4 pt-4 border-t border-slate-700">
                        <h4 class="text-[10px] font-bold uppercase text-slate-400 mb-2">Total Match Duration (Mins)</h4>
                        <div class="flex items-center gap-2 bg-slate-900/50 rounded p-2 border border-slate-700">
                             <input type="number" x-model="matchDuration" class="w-full bg-slate-800 border border-slate-600 rounded px-2 py-1 text-white font-mono text-center focus:border-emerald-500 focus:outline-none" @keydown.enter="updateDuration()">
                             <button @click="updateDuration()" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded uppercase tracking-wide">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="container mx-auto px-4 py-6 max-w-lg space-y-6">
        
        <!-- Pre-Match Setup State -->
        <template x-if="status == 'unscheduled' || status == 'scheduled'">
            <div class="bg-white rounded-3xl shadow-xl p-8 text-center border border-slate-100 mt-8">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl shadow-sm">
                    <i class="fa-solid fa-whistle"></i>
                </div>
                <h1 class="text-xl font-black text-slate-900 mb-2">Ready to Kickoff?</h1>
                <p class="text-slate-500 text-sm mb-6 leading-relaxed">Set match duration and confirm lineups before starting the timer.</p>
                <button @click="openSetupModal()" class="w-full bg-white border-2 border-blue-600 hover:bg-blue-50 active:scale-95 transition-all text-blue-600 font-bold py-4 rounded-xl shadow-lg shadow-blue-100 text-lg">
                    Setup Match
                </button>
            </div>
        </template>

        <!-- Live Game Controls -->
        <div x-show="status == 'in_progress' || status == 'completed'" class="space-y-6">
            
            <!-- Primary Actions (Goals) - Hidden when completed -->
            <div x-show="status !== 'completed'" class="grid grid-cols-2 gap-4">
                <button @click="openGoalModal({{ $fixture->home_team_id }})" class="group relative overflow-hidden bg-white hover:bg-blue-50 border-2 border-blue-100 hover:border-blue-200 rounded-2xl p-4 flex flex-col items-center justify-center gap-2 h-32 active:scale-95 transition-all shadow-sm">
                    <div class="absolute top-0 right-0 p-2 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fa-regular fa-futbol text-6xl text-blue-600 rotate-12"></i>
                    </div>
                    <span class="text-blue-600 font-black text-3xl z-10 text-shadow-sm">+1</span>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 z-10">Home Goal</span>
                </button>

                <button @click="openGoalModal({{ $fixture->away_team_id }})" class="group relative overflow-hidden bg-white hover:bg-rose-50 border-2 border-rose-100 hover:border-rose-200 rounded-2xl p-4 flex flex-col items-center justify-center gap-2 h-32 active:scale-95 transition-all shadow-sm">
                    <div class="absolute top-0 right-0 p-2 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fa-regular fa-futbol text-6xl text-rose-600 -rotate-12"></i>
                    </div>
                    <span class="text-rose-500 font-black text-3xl z-10 text-shadow-sm">+1</span>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 z-10">Away Goal</span>
                </button>
            </div>

            <!-- Secondary Actions - Hidden when completed -->
            <div x-show="status !== 'completed'" class="grid grid-cols-3 gap-3">
                <button @click="openCardModal('YELLOW_CARD')" class="bg-white hover:bg-amber-50 border border-slate-200 hover:border-amber-200 rounded-xl p-3 flex flex-col items-center justify-center gap-1 h-20 active:scale-95 transition-all shadow-sm">
                    <div class="w-6 h-8 bg-amber-400 rounded-[2px] shadow-sm mb-1 transform -rotate-6"></div>
                    <span class="text-[10px] font-bold uppercase text-slate-600">Yellow</span>
                </button>

                <button @click="openCardModal('RED_CARD')" class="bg-white hover:bg-rose-50 border border-slate-200 hover:border-rose-200 rounded-xl p-3 flex flex-col items-center justify-center gap-1 h-20 active:scale-95 transition-all shadow-sm">
                    <div class="w-6 h-8 bg-rose-500 rounded-[2px] shadow-sm mb-1 transform rotate-6"></div>
                    <span class="text-[10px] font-bold uppercase text-slate-600">Red</span>
                </button>

                <button @click="openSubModal()" class="bg-white hover:bg-purple-50 border border-slate-200 hover:border-purple-200 rounded-xl p-3 flex flex-col items-center justify-center gap-1 h-20 active:scale-95 transition-all shadow-sm">
                    <i class="fa-solid fa-arrow-right-arrow-left text-xl text-purple-500 mb-1"></i>
                    <span class="text-[10px] font-bold uppercase text-slate-600">Sub</span>
                </button>
            </div>

            <!-- Commentary & Feed -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="border-b border-slate-100 p-3 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase text-slate-500 tracking-wider">Match Feed</h3>
                    <div class="flex items-center gap-2">
                         <input type="text" x-model="commentaryText" @keydown.enter="submitCommentary()" 
                                class="bg-white border text-sm border-slate-200 rounded-full px-3 py-1 w-40 focus:w-full transition-all focus:ring-2 focus:ring-blue-100 focus:border-blue-300 outline-none placeholder:text-slate-400" 
                                placeholder="Add commentary...">
                         <button @click="submitCommentary()" class="w-8 h-8 rounded-full bg-white border border-blue-600 text-blue-600 flex items-center justify-center hover:bg-blue-50 transition-colors shadow-sm">
                            <i class="fa-solid fa-arrow-up text-xs"></i>
                         </button>
                    </div>
                </div>
                
                    <div class="p-3 space-y-3" id="feed-container">
                        <template x-for="(event, index) in events" :key="event.id">
                            <div class="relative rounded-xl border p-3 pl-16 shadow-sm overflow-hidden group transition-all"
                                 :class="{
                                    'bg-gradient-to-br from-white to-emerald-50 border-emerald-100': event.event_type == 'GOAL',
                                    'bg-gradient-to-br from-white to-rose-50 border-rose-100': event.event_type == 'RED_CARD',
                                    'bg-gradient-to-br from-white to-amber-50 border-amber-100': event.event_type == 'YELLOW_CARD',
                                    'bg-gradient-to-br from-white to-purple-50 border-purple-100': event.event_type == 'SUB',
                                    'bg-gradient-to-br from-white to-sky-50 border-sky-100': event.event_type == 'COMMENTARY',
                                    'bg-white border-slate-100': !['GOAL', 'RED_CARD', 'YELLOW_CARD', 'SUB', 'COMMENTARY'].includes(event.event_type)
                                 }">
                                
                                <!-- Player Photo -->
                                <div class="absolute left-3 top-3">
                                    <div class="w-10 h-10 rounded-full shadow-sm relative overflow-hidden ring-1 ring-white">
                                        <template x-if="event.event_type == 'COMMENTARY'">
                                            <div class="w-full h-full flex items-center justify-center font-bold text-xs bg-sky-100 text-sky-500">
                                                <i class="fa-solid fa-microphone-lines"></i>
                                            </div>
                                        </template>
                                        <template x-if="event.event_type != 'COMMENTARY'">
                                            <div>
                                                <template x-if="event.player && event.player.user">
                                                    <img :src="getPhotoUrl(event.player.user.photo)" class="w-full h-full object-cover">
                                                </template>
                                                <template x-if="!event.player || !event.player.user">
                                                    <div class="w-full h-full flex items-center justify-center font-bold text-xs bg-slate-100 text-slate-500">
                                                        <img :src="getPhotoUrl(null)" class="w-full h-full object-cover opacity-90">
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                    <!-- Team Badge -->
                                    <template x-if="event.team && event.team.team">
                                        <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden flex items-center justify-center">
                                            <img :src="getTeamLogoUrl(event.team.team.logo)" class="w-full h-full object-cover">
                                        </div>
                                    </template>
                                </div>

                                <!-- Minute and Type -->
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-white/80 border border-slate-200 text-slate-600 shadow-sm" x-text="event.minute + '\''"></span>
                                        <h3 class="font-bold text-xs uppercase tracking-wide" 
                                            :class="{
                                                'text-emerald-600': event.event_type == 'GOAL',
                                                'text-rose-600': event.event_type == 'RED_CARD',
                                                'text-amber-600': event.event_type == 'YELLOW_CARD',
                                                'text-purple-600': event.event_type == 'SUB',
                                                'text-sky-600': event.event_type == 'COMMENTARY',
                                                'text-slate-600': !['GOAL', 'RED_CARD', 'YELLOW_CARD', 'SUB', 'COMMENTARY'].includes(event.event_type)
                                            }" x-text="event.event_type.replace('_', ' ')"></h3>
                                    </div>
                                    
                                    <!-- Delete Button (Only for latest) -->
                                    <div x-show="index === 0">
                                        <button @click="deleteEvent(event.id)" class="text-slate-400 hover:text-rose-500 transition-colors p-1">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Description / Player Name -->
                                <div class="text-sm font-bold text-slate-900 leading-tight">
                                    <span x-text="event.player?.user?.name || event.player_name || event.description"></span>
                                </div>

                                <!-- Sub / Assist Details -->
                                <template x-if="event.event_type == 'GOAL' && (event.assistPlayer || event.assist_player_name)">
                                    <div class="mt-1 text-xs font-medium flex items-center gap-1.5 text-slate-500">
                                        <span class="bg-emerald-100 text-emerald-700 px-1 rounded text-[10px]">Ast</span>
                                        <span x-text="event.assistPlayer?.user?.name || event.assist_player_name"></span>
                                    </div>
                                </template>

                                <template x-if="event.event_type == 'SUB'">
                                    <div class="mt-2 text-xs flex flex-col gap-1.5 rounded bg-white/50 p-2 border border-slate-200/50">
                                        <div class="flex items-center gap-2 text-rose-500">
                                            <i class="fa-solid fa-arrow-right-from-bracket rotate-180"></i>
                                            <span class="font-semibold">OUT:</span> 
                                            <span class="text-slate-600" x-text="event.player?.user?.name || event.player_name"></span>
                                        </div>
                                        <div class="flex items-center gap-2 text-emerald-500">
                                            <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                            <span class="font-semibold">IN:</span> 
                                            <span class="text-slate-600" x-text="event.relatedPlayer?.user?.name || event.related_player_name"></span>
                                            <!-- In Player Photo -->
                                            <template x-if="event.relatedPlayer && event.relatedPlayer.user">
                                                 <div class="w-4 h-4 rounded-full overflow-hidden shadow-sm">
                                                     <img :src="getPhotoUrl(event.relatedPlayer.user.photo)" class="w-full h-full object-cover">
                                                 </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    <template x-if="events.length === 0">
                        <div class="py-12 text-center">
                            <i class="fa-regular fa-clock text-3xl text-slate-200 mb-3 block"></i>
                            <span class="text-slate-400 text-sm font-medium">Match hasn't started yet</span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Penalty Shootout Section -->
            <div x-show="hasPenalties && status !== 'completed'" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="border-b border-slate-100 p-4 bg-slate-50/50">
                    <h3 class="text-sm font-bold uppercase text-slate-700 tracking-wider text-center">Penalty Shootout</h3>
                    <p class="text-xs text-slate-500 text-center mt-1">Match ended in draw - Record penalties</p>
                </div>
                
                <div class="p-4 space-y-4">
                    <!-- Penalty Score Display -->
                    <div class="flex items-center justify-center gap-6 py-4">
                        <div class="text-center">
                            <div class="text-xs font-bold text-slate-500 mb-1">{{ $fixture->homeTeam->team->name }}</div>
                            <div class="text-4xl font-black text-blue-600 font-mono" x-text="homePenaltyScore">0</div>
                        </div>
                        <div class="text-2xl font-black text-slate-300">-</div>
                        <div class="text-center">
                            <div class="text-xs font-bold text-slate-500 mb-1">{{ $fixture->awayTeam->team->name }}</div>
                            <div class="text-4xl font-black text-rose-600 font-mono" x-text="awayPenaltyScore">0</div>
                        </div>
                    </div>

                    <!-- Add Penalty Buttons -->
                    <div class="grid grid-cols-2 gap-3">
                        <button @click="openPenaltyModal({{ $fixture->home_team_id }})" class="bg-white hover:bg-blue-50 border-2 border-blue-100 hover:border-blue-200 rounded-xl p-4 flex flex-col items-center gap-2 transition-all active:scale-95">
                            <i class="fa-solid fa-circle-dot text-2xl text-blue-600"></i>
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Home Penalty</span>
                        </button>
                        <button @click="openPenaltyModal({{ $fixture->away_team_id }})" class="bg-white hover:bg-rose-50 border-2 border-rose-100 hover:border-rose-200 rounded-xl p-4 flex flex-col items-center gap-2 transition-all active:scale-95">
                            <i class="fa-solid fa-circle-dot text-2xl text-rose-600"></i>
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Away Penalty</span>
                        </button>
                    </div>

                    <!-- Penalty History -->
                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-200">
                        <h4 class="text-xs font-bold uppercase text-slate-500 mb-2">Penalty Attempts</h4>
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            <template x-for="penalty in penalties" :key="penalty.id">
                                <div class="flex items-center justify-between p-2 rounded-lg border bg-white" :class="penalty.scored ? 'border-emerald-200' : 'border-rose-200'">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-mono font-bold px-2 py-1 rounded bg-slate-100 border border-slate-200" x-text="'#' + penalty.attempt_number"></span>
                                        <span class="text-sm font-bold text-slate-700" x-text="penalty.player_name || 'Guest'"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold px-2 py-1 rounded" :class="penalty.scored ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50'" x-text="penalty.scored ? 'SCORED' : 'MISSED'"></span>
                                        <button @click="togglePenalty(penalty.id, !penalty.scored)" class="w-6 h-6 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs transition-colors">
                                            <i class="fa-solid fa-rotate"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <template x-if="penalties.length === 0">
                                <div class="text-center py-6 text-slate-400 text-xs">No penalties recorded yet</div>
                            </template>
                        </div>
                    </div>

                    <!-- Complete Penalties Button -->
                    <button @click="completePenalties()" class="w-full bg-white border-2 border-slate-800 hover:bg-slate-800 text-slate-800 hover:text-white font-bold py-4 rounded-xl shadow-sm transition-all active:scale-95">
                        Complete Penalty Shootout
                    </button>
                </div>
            </div>

            <div x-show="status !== 'completed' && !hasPenalties" class="pt-4 pb-8">
                 <button @click="finishMatch" class="w-full text-center text-xs font-bold text-rose-500 hover:text-rose-600 uppercase tracking-widest py-4 rounded-xl border border-rose-100 hover:bg-rose-50 transition-colors">
                    End Match
                </button>
            </div>

        </div>

    </div>

    <!-- === MODERN MODALS (Centered on Mobile & Desktop) === -->
    
    <!-- Setup Modal -->
    <div x-show="showSetupModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showSetupModal = false"></div>
        <div class="relative w-full max-w-3xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white sticky top-0 z-10 shrink-0">
                <h3 class="text-lg font-black text-slate-900">Match Setup</h3>
                <button @click="showSetupModal = false" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center transition-colors"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <div class="overflow-y-auto p-6 space-y-8">
                <!-- Duration -->
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Match Duration</label>
                    <div class="flex items-center gap-4">
                        <button @click="matchDuration = Math.max(5, matchDuration - 5)" class="w-10 h-10 rounded-full bg-white border border-slate-200 shadow-sm text-slate-600 text-lg hover:bg-blue-50 transition-colors font-bold">-</button>
                        <div class="flex-1 text-center">
                            <span class="text-3xl font-black text-slate-900 tabular-nums" x-text="matchDuration"></span>
                            <span class="text-sm font-bold text-slate-400 ml-1">mins</span>
                        </div>
                        <button @click="matchDuration += 5" class="w-10 h-10 rounded-full bg-white border border-slate-200 shadow-sm text-slate-600 text-lg hover:bg-blue-50 transition-colors font-bold">+</button>
                    </div>
                </div>

                <!-- Lineups -->
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Home -->
                    <div>
                         <div class="flex items-center gap-2 mb-3">
                            <span class="w-2 h-8 rounded bg-blue-500"></span>
                            <h4 class="font-bold text-slate-800">{{ $fixture->homeTeam->team->name }}</h4>
                         </div>
                         <div class="space-y-1">
                            <template x-for="player in homeRoster" :key="player.id">
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-transparent hover:bg-slate-50 transition-colors cursor-pointer group">
                                    <div class="relative flex items-center justify-center"> <!-- Custom Checkbox -->
                                        <input type="checkbox" :value="player.id" x-model="selectedHomeStarters" class="peer appearance-none w-6 h-6 border-2 border-slate-300 rounded-lg checked:bg-blue-600 checked:border-blue-600 transition-all">
                                        <i class="fa-solid fa-check text-blue-600 text-xs absolute opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></i>
                                    </div>
                                    <span class="font-medium text-slate-700 group-hover:text-slate-900" x-text="player.user.name"></span>
                                </label>
                            </template>
                         </div>
                    </div>

                    <!-- Away -->
                    <div>
                         <div class="flex items-center gap-2 mb-3">
                            <span class="w-2 h-8 rounded bg-rose-500"></span>
                            <h4 class="font-bold text-slate-800">{{ $fixture->awayTeam->team->name }}</h4>
                         </div>
                         <div class="space-y-1">
                            <template x-for="player in awayRoster" :key="player.id">
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-transparent hover:bg-slate-50 transition-colors cursor-pointer group">
                                    <div class="relative flex items-center justify-center"> <!-- Custom Checkbox -->
                                        <input type="checkbox" :value="player.id" x-model="selectedAwayStarters" class="peer appearance-none w-6 h-6 border-2 border-slate-300 rounded-lg checked:bg-rose-600 checked:border-rose-600 transition-all">
                                        <i class="fa-solid fa-check text-rose-600 text-xs absolute opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></i>
                                    </div>
                                    <span class="font-medium text-slate-700 group-hover:text-slate-900" x-text="player.user.name"></span>
                                </label>
                            </template>
                         </div>
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 bg-white sticky bottom-0 z-10 shrink-0">
                <button @click="submitStartMatch" class="w-full bg-white border-2 border-blue-600 text-blue-600 font-bold text-lg py-4 rounded-xl shadow-lg hover:bg-blue-50 transition-colors">Start Match</button>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div x-show="showConfirmModal" style="display: none;" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-opacity" role="dialog" aria-modal="true">
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm transform transition-all scale-100" @click.away="closeConfirmModal">
            <h3 class="text-lg font-black text-slate-900 mb-2" x-text="confirmTitle">Are you sure?</h3>
            <p class="text-slate-500 text-sm leading-relaxed mb-6" x-text="confirmMessage">This action cannot be undone.</p>
            
            <div class="flex gap-3">
                <button @click="closeConfirmModal" class="flex-1 py-3 rounded-xl font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 transition-colors">
                    Cancel
                </button>
                <button @click="triggerConfirm" class="flex-1 py-3 rounded-xl font-bold text-white bg-rose-500 hover:bg-rose-600 shadow-lg shadow-rose-200 transition-colors">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <!-- Goal Modal -->
    <div x-show="showGoalModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeGoalModal"></div>
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="p-6 text-center border-b border-slate-50 shrink-0">
                <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-500 mx-auto flex items-center justify-center text-3xl mb-3 shadow-sm">
                    <i class="fa-solid fa-futbol animate-bounce"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900">Goal Scored!</h3>
            </div>

            <div class="p-6 space-y-6 overflow-y-auto">
                <div>
                     <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Who Scored?</label>
                     
                     <!-- Empty State Check -->
                     <template x-if="getEligiblePlayers(goalTeamId).length === 0">
                        <div class="p-3 rounded-xl bg-amber-50 text-amber-600 text-sm font-bold border border-amber-200 mb-2">
                            No eligible players found.
                        </div>
                     </template>

                     <div class="grid grid-cols-1 gap-2 max-h-60 overflow-y-auto mb-3">
                        <template x-for="player in getEligiblePlayers(goalTeamId)" :key="player.id">
                            <button @click="goalPlayerId = player.id; isGuestGoal = false" 
                                class="flex items-center gap-3 p-3 rounded-xl border-2 text-left transition-all relative overflow-hidden group"
                                :class="goalPlayerId == player.id 
                                    ? 'bg-emerald-50 border-emerald-500 shadow-md transform scale-[1.02]' 
                                    : 'bg-white border-slate-100 hover:border-emerald-200 hover:bg-slate-50'">
                                
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0 transition-colors" 
                                     :class="goalPlayerId == player.id ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200'">
                                     <span x-text="(player.player?.user?.name || player.custom_name || '?').charAt(0)"></span>
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-sm truncate" 
                                         :class="goalPlayerId == player.id ? 'text-emerald-900' : 'text-slate-700'"
                                         x-text="player.player?.user?.name || player.custom_name"></div>
                                    <div class="text-[10px] uppercase tracking-wider font-bold"
                                         :class="goalPlayerId == player.id ? 'text-emerald-600' : 'text-slate-400'" 
                                         x-text="'#' + (player.player?.jersey_number || '-')"></div>
                                </div>
                                
                                <div x-show="goalPlayerId == player.id" class="text-emerald-500">
                                    <i class="fa-solid fa-circle-check text-xl"></i>
                                </div>
                            </button>
                        </template>
                     </div>
                     
                     <!-- Debug Info (Temporary) -->
                     <div x-show="goalPlayerId" class="text-[10px] text-emerald-500 font-mono text-center mb-2">Selected ID: <span x-text="goalPlayerId"></span></div>
                    
                    <!-- Validation Error -->
                    <div x-show="showGoalError" class="text-rose-500 text-xs font-bold mt-1 animate-pulse">
                        <i class="fa-solid fa-circle-exclamation mr-1"></i> Please select a scorer to continue.
                    </div>

                    <div class="mt-3">
                         <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg hover:bg-slate-50 transition-colors">
                            <input type="checkbox" x-model="isGuestGoal" class="w-5 h-5 rounded border-slate-300 text-emerald-500 focus:ring-emerald-500">
                            <span class="text-sm font-medium text-slate-600">Guest / Custom Name</span>
                        </label>
                        <input x-show="isGuestGoal" type="text" x-model="goalGuestName" class="mt-2 w-full p-3 rounded-xl border border-slate-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none" placeholder="Enter name...">
                    </div>
                </div>

                <div>
                     <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Assist (Optional)</label>
                     <div class="grid grid-cols-1 gap-2 max-h-48 overflow-y-auto">
                        <button @click="goalAssistId = ''" 
                                class="w-full text-left p-3 rounded-xl border-2 transition-all font-bold text-sm text-slate-500 hover:bg-slate-50"
                                :class="goalAssistId === '' ? 'border-blue-500 bg-blue-50 text-blue-600' : 'border-slate-100'">
                            No Assist / Solo
                        </button>
                        <template x-for="player in getEligiblePlayers(goalTeamId)" :key="player.id">
                            <button @click="goalAssistId = player.id; isGuestAssist = false" 
                                class="flex items-center gap-3 p-3 rounded-xl border-2 text-left transition-all relative overflow-hidden group"
                                :class="goalAssistId == player.id 
                                    ? 'bg-blue-50 border-blue-500 shadow-md transform scale-[1.02]' 
                                    : 'bg-white border-slate-100 hover:border-blue-200 hover:bg-slate-50'">
                                
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0 transition-colors" 
                                     :class="goalAssistId == player.id ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200'">
                                     <span x-text="(player.player?.user?.name || player.custom_name || '?').charAt(0)"></span>
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-sm truncate" 
                                         :class="goalAssistId == player.id ? 'text-blue-900' : 'text-slate-700'"
                                         x-text="player.player?.user?.name || player.custom_name"></div>
                                </div>
                                
                                <div x-show="goalAssistId == player.id" class="text-blue-500">
                                    <i class="fa-solid fa-circle-check text-xl"></i>
                                </div>
                            </button>
                        </template>
                     </div>
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 bg-white grid grid-cols-2 gap-4 shrink-0 sticky bottom-0 z-10">
                <button @click="closeGoalModal" class="py-4 rounded-xl font-bold text-slate-500 hover:bg-slate-50 transition-colors">Cancel</button>
                <button @click="submitGoal" class="py-4 rounded-xl bg-white border-2 border-emerald-500 hover:bg-emerald-50 text-emerald-600 font-bold shadow-lg shadow-emerald-100 transition-all">Confirm Goal</button>
            </div>
        </div>
    </div>

    <!-- Booking Modal -->
    <div x-show="showBookingModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeBookingModal"></div>
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="p-6 text-center border-b border-slate-50 shrink-0">
                 <div class="w-16 h-12 rounded mx-auto mb-3 shadow-sm transform -rotate-3"
                      :class="bookingType == 'RED_CARD' ? 'bg-rose-500' : 'bg-amber-400'"></div>
                <h3 class="text-xl font-black text-slate-900" x-text="bookingType == 'RED_CARD' ? 'Red Card Issued' : 'Yellow Card Issued'"></h3>
            </div>

            <div class="p-4 overflow-y-auto">
                <!-- Team Toggle -->
                <div class="flex p-1 bg-slate-100 rounded-xl mb-6">
                    <button @click="bookingTeamId = {{ $fixture->home_team_id }}" class="flex-1 py-3 rounded-lg text-sm font-bold transition-all" :class="bookingTeamId == {{ $fixture->home_team_id }} ? 'bg-white shadow-sm text-slate-900' : 'text-slate-400 hover:text-slate-600'">Home</button>
                    <button @click="bookingTeamId = {{ $fixture->away_team_id }}" class="flex-1 py-3 rounded-lg text-sm font-bold transition-all" :class="bookingTeamId == {{ $fixture->away_team_id }} ? 'bg-white shadow-sm text-slate-900' : 'text-slate-400 hover:text-slate-600'">Away</button>
                </div>

                <label class="block text-xs font-bold uppercase text-slate-500 mb-2 pl-1">Select Player</label>
                <div class="max-h-60 overflow-y-auto space-y-1">
                     <template x-for="player in getTeamPlayers(bookingTeamId)" :key="player.id">
                        <button @click="bookingPlayerId = player.player_id; bookingGuestName = ''" 
                            class="w-full flex items-center gap-3 p-3 rounded-xl border transition-all"
                            :class="bookingPlayerId == player.player_id 
                                ? (bookingType == 'RED_CARD' ? 'bg-rose-50 border-rose-200 shadow-sm' : 'bg-amber-50 border-amber-200 shadow-sm') 
                                : 'bg-white border-transparent hover:bg-slate-50 text-slate-600'">
                            <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs" :class="bookingType == 'RED_CARD' ? 'bg-rose-100 text-rose-600' : 'bg-amber-100 text-amber-600'" x-text="(player.player.user.name || '?').charAt(0)"></span>
                            <span class="font-bold text-sm" :class="bookingPlayerId == player.player_id ? 'text-slate-900' : ''" x-text="player.player.user.name || player.custom_name"></span>
                        </button>
                     </template>
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 bg-white grid grid-cols-2 gap-4 shrink-0 sticky bottom-0 z-10">
                <button @click="closeBookingModal" class="py-4 rounded-xl font-bold text-slate-500 hover:bg-slate-50 transition-colors">Cancel</button>
                <button @click="submitBooking" class="py-4 rounded-xl font-bold shadow-lg transition-all border-2"
                        :class="bookingType == 'RED_CARD' ? 'bg-white border-rose-500 text-rose-600 hover:bg-rose-50 shadow-rose-100' : 'bg-white border-amber-400 text-amber-600 hover:bg-amber-50 shadow-amber-100'">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Sub Modal -->
    <div x-show="showSubModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeSubModal"></div>
        <div class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="p-6 text-center border-b border-slate-50 shrink-0">
                <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 mx-auto flex items-center justify-center text-xl mb-2 shadow-sm">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900">Make Substitution</h3>
            </div>

            <div class="p-4 overflow-y-auto">
                 <div class="flex p-1 bg-slate-100 rounded-xl mb-6">
                    <button @click="subTeamId = {{ $fixture->home_team_id }}; resetSubSelection()" class="flex-1 py-3 rounded-lg text-sm font-bold transition-all" :class="subTeamId == {{ $fixture->home_team_id }} ? 'bg-white shadow-sm text-slate-900' : 'text-slate-400 hover:text-slate-600'">Home</button>
                    <button @click="subTeamId = {{ $fixture->away_team_id }}; resetSubSelection()" class="flex-1 py-3 rounded-lg text-sm font-bold transition-all" :class="subTeamId == {{ $fixture->away_team_id }} ? 'bg-white shadow-sm text-slate-900' : 'text-slate-400 hover:text-slate-600'">Away</button>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- OUT -->
                    <div>
                        <div class="text-xs font-black text-rose-500 uppercase tracking-wider mb-2 text-center">Player Out</div>
                        <div class="h-48 overflow-y-auto space-y-1 pr-1">
                             <template x-for="p in getPlayersForSub(subTeamId, true)" :key="p.id">
                                <button @click="subPlayerOutId = p.player_id" class="w-full text-left text-xs font-bold p-2.5 rounded-lg border transition-all truncate" 
                                        :class="subPlayerOutId == p.player_id ? 'bg-rose-50 border-rose-200 text-rose-700 shadow-sm' : 'bg-white border-transparent text-slate-500 hover:bg-slate-50'">
                                    <span x-text="p.player.user.name || p.custom_name"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- IN -->
                     <div>
                        <div class="text-xs font-black text-emerald-500 uppercase tracking-wider mb-2 text-center">Player In</div>
                        <div class="h-48 overflow-y-auto space-y-1 pl-1">
                             <template x-for="p in getPlayersForSub(subTeamId, false)" :key="p.id">
                                <button @click="subPlayerInId = p.player_id" class="w-full text-left text-xs font-bold p-2.5 rounded-lg border transition-all truncate" 
                                        :class="subPlayerInId == p.player_id ? 'bg-emerald-50 border-emerald-200 text-emerald-700 shadow-sm' : 'bg-white border-transparent text-slate-500 hover:bg-slate-50'">
                                    <span x-text="p.player.user.name || p.custom_name"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 bg-white grid grid-cols-2 gap-4 shrink-0 sticky bottom-0 z-10">
                <button @click="closeSubModal" class="py-4 rounded-xl font-bold text-slate-500 hover:bg-slate-50 transition-colors">Cancel</button>
                <button @click="submitSubstitution" class="py-4 rounded-xl bg-white border-2 border-purple-600 hover:bg-purple-50 text-purple-600 font-bold shadow-lg shadow-purple-100 transition-all disabled:opacity-50 disabled:shadow-none" :disabled="!isValidSub">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Penalty Modal -->
    <div x-show="showPenaltyModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closePenaltyModal"></div>
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="p-6 text-center border-b border-slate-50 shrink-0">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-600 mx-auto flex items-center justify-center text-3xl mb-3 shadow-sm">
                    <i class="fa-solid fa-circle-dot"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900">Penalty Kick</h3>
                <p class="text-xs text-slate-500 mt-1">Attempt #<span x-text="penaltyAttemptNumber"></span></p>
            </div>

            <div class="p-6 space-y-6 overflow-y-auto">
                <div>
                     <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Select Player</label>
                     <div class="grid grid-cols-1 gap-2 max-h-60 overflow-y-auto">
                        <template x-for="player in getEligiblePlayers(penaltyTeamId)" :key="player.id">
                            <button @click="penaltyPlayerId = player.id" 
                                class="flex items-center gap-3 p-3 rounded-xl border-2 text-left transition-all"
                                :class="penaltyPlayerId == player.id 
                                    ? 'bg-blue-50 border-blue-500 shadow-md' 
                                    : 'bg-white border-slate-100 hover:border-blue-200 hover:bg-slate-50'">
                                
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0" 
                                     :class="penaltyPlayerId == player.id ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-500'">
                                     <span x-text="(player.player?.user?.name || player.custom_name || '?').charAt(0)"></span>
                                </div>
                                
                                <div class="flex-1">
                                    <div class="font-bold text-sm" 
                                         :class="penaltyPlayerId == player.id ? 'text-blue-900' : 'text-slate-700'"
                                         x-text="player.player?.user?.name || player.custom_name"></div>
                                </div>
                                
                                <div x-show="penaltyPlayerId == player.id" class="text-blue-500">
                                    <i class="fa-solid fa-circle-check text-xl"></i>
                                </div>
                            </button>
                        </template>
                     </div>
                </div>

                <div>
                     <label class="block text-xs font-bold uppercase text-slate-500 mb-3">Result</label>
                     <div class="grid grid-cols-2 gap-3">
                        <button @click="penaltyScored = true" 
                                class="p-4 rounded-xl border-2 transition-all font-bold text-center"
                                :class="penaltyScored ? 'bg-emerald-50 border-emerald-500 text-emerald-700 shadow-md' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'">
                            <i class="fa-solid fa-circle-check text-2xl mb-2 block"></i>
                            Scored
                        </button>
                        <button @click="penaltyScored = false" 
                                class="p-4 rounded-xl border-2 transition-all font-bold text-center"
                                :class="!penaltyScored ? 'bg-rose-50 border-rose-500 text-rose-700 shadow-md' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'">
                            <i class="fa-solid fa-circle-xmark text-2xl mb-2 block"></i>
                            Missed
                        </button>
                     </div>
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 bg-white grid grid-cols-2 gap-4 shrink-0">
                <button @click="closePenaltyModal" class="py-4 rounded-xl font-bold text-slate-500 hover:bg-slate-50 transition-colors">Cancel</button>
                <button @click="submitPenalty" class="py-4 rounded-xl bg-white border-2 border-blue-600 hover:bg-blue-50 text-blue-600 font-bold shadow-lg shadow-blue-100 transition-all disabled:opacity-50" :disabled="!penaltyPlayerId">Record Penalty</button>
            </div>
        </div>
    </div>

</div>

<!-- Logic Script (Unchanged) -->
<script>
function scorerConsole() {
    return {
        status: '{{ $fixture->status }}',
        matchState: '{{ $fixture->match_state }}',
        currentTime: '{{ now() }}',
        events: @json($fixture->events),
        players: @json($fixture->fixturePlayers->load('player.user')),
        homeRoster: @json($fixture->homeTeam->leaguePlayers),
        awayRoster: @json($fixture->awayTeam->leaguePlayers),
        homeScore: {{ $fixture->home_score ?? 0 }},
        awayScore: {{ $fixture->away_score ?? 0 }},
        hasPenalties: {{ $fixture->has_penalties ? 'true' : 'false' }},
        penalties: @json($fixture->penalties ?? []),
        homePenaltyScore: {{ $fixture->home_penalty_score ?? 0 }},
        awayPenaltyScore: {{ $fixture->away_penalty_score ?? 0 }},

        currentMinute: {{ $fixture->current_minute }},
        currentSeconds: 0, 
        isRunning: {{ $fixture->is_running ? 'true' : 'false' }},
        lastTickAt: '{{ $fixture->last_tick_at }}',
        serverTime: '{{ now() }}',
        
        addedTimeFirst: {{ $fixture->added_time_first_half }},
        addedTimeSecond: {{ $fixture->added_time_second_half }},
        
        commentaryText: '',
        
        // UI Controls
        showTimeAdjust: false,
        addExtraTimePanel: false,

        theme: localStorage.getItem('scorerTheme') || 'green',

        // Setup Modal State
        showSetupModal: false,
        matchDuration: {{ $fixture->match_duration ?? 45 }},
        manualMinuteInput: {{ $fixture->current_minute }}, // Initialize with current
        injuryTimeInput: 0,
        selectedHomeStarters: [],
        selectedAwayStarters: [],
        
        // Goal Modal State
        showGoalModal: false,
        goalTeamId: null,
        goalPlayerId: '',
        goalGuestName: '',
        isGuestGoal: false,
        goalAssistId: '',
        goalAssistName: '',
        isGuestAssist: false,

        // Booking Modal State
        showBookingModal: false,
        bookingType: '', 
        bookingTeamId: {{ $fixture->home_team_id }},
        bookingPlayerId: null,
        bookingGuestName: '',
        isGuestBooking: false,

        // Substitution Modal State
        showSubModal: false,
        subTeamId: {{ $fixture->home_team_id }},
        subPlayerOutId: null,
        subPlayerInId: null,

        // Confirmation Modal State
        showConfirmModal: false,
        confirmTitle: 'Are you sure?',
        confirmMessage: '',
        confirmCallback: null,

        // Penalty Modal State
        showPenaltyModal: false,
        penaltyTeamId: null,
        penaltyPlayerId: null,
        penaltyScored: true,
        penaltyAttemptNumber: 1,

        init() {
            // Initialize Seconds from Last Tick if running
            if (this.isRunning && this.lastTickAt) {
                let last = new Date(this.lastTickAt).getTime();
                let now = new Date(this.serverTime).getTime();
                let diffInSeconds = Math.floor((now - last) / 1000);
                this.currentSeconds = diffInSeconds % 60;
            }

            // Real-Time Ticker (1s = 1s)
            setInterval(async () => {
                if (this.isRunning) {
                     this.currentSeconds++;
                     if(this.currentSeconds >= 60) {
                         this.currentSeconds = 0;
                         // AWAIT server response - it may auto-pause
                         await this.performTick();
                         // currentMinute is updated from server response in performTick
                     }
                }
            }, 1000);
        },

        get timeDisplay() {
            let m = this.currentMinute;
            let s = String(Math.min(59, this.currentSeconds)).padStart(2, '0');
            
            // If stopped at HT/FT
            if(this.matchState === 'HALF_TIME') return 'HT';
            if(this.matchState === 'FULL_TIME') return 'FT';
            
            let halftimeMark = this.matchDuration;
            let fulltimeMark = this.matchDuration * 2;
            
            // Stoppage time display: 45+3, 90+2
            if (this.matchState == 'FIRST_HALF' && m > halftimeMark) {
                return halftimeMark + '+' + (m - halftimeMark);
            }
            if (this.matchState == 'SECOND_HALF' && m > fulltimeMark) {
                return fulltimeMark + '+' + (m - fulltimeMark);
            }
            
            // Normal MM:SS format
            return String(m).padStart(2, '0') + ':' + s;
        },

        get periodInfo() {
            let base = this.matchDuration; // e.g. 45
            let added = 0;
            
            // Determine context based on state
            if (this.matchState == 'FIRST_HALF' || this.matchState == 'HALF_TIME') {
                added = this.addedTimeFirst;
                // If added time exists, show Total: 45 + X'
                if(added > 0) return `Total: ${base} + ${added}'`;
            }
            else if (this.matchState == 'SECOND_HALF' || this.matchState == 'FULL_TIME') {
                added = this.addedTimeSecond;
                let totalBase = base * 2;
                // If added time exists, show Total: 90 + X'
                if(added > 0) return `Total: ${totalBase} + ${added}'`;
            }
            return '';
        },
        
        async performTick() {
             let res = await this.callTimerAction('tick');
             if(res.success) {
                 this.currentMinute = res.fixture.current_minute;
                 this.currentSeconds = 0; // Reset seconds on sync tick
                 this.matchState = res.fixture.match_state;
                 this.isRunning = res.fixture.is_running;
                 
                 // Handle auto-pause notification
                 if(res.auto_paused) {
                     let msg = res.new_state === 'HALF_TIME' ? '⏸️ HALF TIME!' : '🏁 FULL TIME!';
                     alert(msg); // Simple alert, can be replaced with toast
                 }
             }
        },

        togglePause() {
             let action = this.isRunning ? 'pause' : 'resume';
             this.callTimerAction(action).then(res => {
                 this.isRunning = res.fixture.is_running;
             });
        },
        
        changeState(newState) {
             this.callTimerAction('change_state', { state: newState }).then(res => {
                 this.matchState = res.fixture.match_state;
                 this.currentMinute = res.fixture.current_minute;
                 this.isRunning = res.fixture.is_running;
                 
                 if(this.matchState === 'HALF_TIME' || this.matchState === 'FULL_TIME') {
                     this.showTimeAdjust = false;
                 }
             });
        },
        
        adjustTime(delta) {
             let newMin = this.currentMinute + delta;
             this.callTimerAction('set_minute', { minute: newMin }).then(res => {
                 this.currentMinute = res.fixture.current_minute;
                 this.manualMinuteInput = this.currentMinute;
             });
        },
        
        setManualTime() {
             this.callTimerAction('set_minute', { minute: this.manualMinuteInput }).then(res => {
                 this.currentMinute = res.fixture.current_minute;
                 this.showTimeAdjust = false; // Close panel on set
             });
        },
        
        updateDuration() {
             // Warning if match is in progress
             if(this.status === 'in_progress') {
                 if(!confirm('Warning: Changing match duration while the match is in progress will affect halftime/fulltime triggers. Are you sure?')) {
                     return;
                 }
             }
             this.callTimerAction('set_duration', { duration: this.matchDuration }).then(res => {
                 this.showTimeAdjust = false;
             });
        },
        
        submitInjuryTime() {
             let min = parseInt(this.injuryTimeInput);
             if(isNaN(min)) return;
             
             this.callTimerAction('add_time', { minutes: min }).then(res => {
                 this.showTimeAdjust = false;
                 // Update local state is handled by reactivity via timer sync usually, 
                 // but let's update local added vars for immediate feedback if possible
                 if (this.matchState == 'FIRST_HALF' || this.matchState == 'HALF_TIME') this.addedTimeFirst = min;
                 else this.addedTimeSecond = min;
             });
        },
        
        async callTimerAction(action, data = {}) {
             let payload = { action, ...data };
             let r = await fetch('{{ route("scorer.timer", $fixture->slug) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
             });
             return await r.json();
        },

        shareMatch() {
            let leagueName = '{{ addslashes($fixture->league->name ?? "League") }}';
            let homeTeam = '{{ addslashes($fixture->homeTeam->team->name) }}';
            let awayTeam = '{{ addslashes($fixture->awayTeam->team->name) }}';
            let venue = '{{ addslashes($fixture->venue ?? "Main Ground") }}';
            let date = '{{ $fixture->match_date ? $fixture->match_date->format("d M, Y") : "Today" }}';
            let liveLink = '{{ route("matches.live", $fixture->slug) }}';

            let formatPlayer = (p) => {
                let name = p.player?.user?.name || p.custom_name || 'Guest';
                let num = p.player?.jersey_number ? ` (#${p.player.jersey_number})` : '';
                return `• ${name}${num}`;
            };

            // Get Starters
            let homeXI = this.players.filter(p => p.team_id == {{ $fixture->home_team_id }} && p.is_active)
                                     .map(formatPlayer).join('\n');
            let awayXI = this.players.filter(p => p.team_id == {{ $fixture->away_team_id }} && p.is_active)
                                     .map(formatPlayer).join('\n');

            let separator = '--------------------------------';
            
            let text = `*${leagueName}*\n` +
                       `${separator}\n` +
                       `*${homeTeam}* vs *${awayTeam}*\n` +
                       `${date} | ${venue}\n` +
                       `${separator}\n\n`;

            if (homeXI || awayXI) {
                text += `*LINEUPS*\n\n` +
                        `*${homeTeam} XI:*\n${homeXI || 'Not announced'}\n\n` +
                        `*${awayTeam} XI:*\n${awayXI || 'Not announced'}\n` +
                        `${separator}\n\n`;
            }

            text += `*WATCH LIVE & SCORE:*\n${liveLink}\n\n`;
            text += `_(Refresh the page for live updates)_`;

            let url = `https://wa.me/?text=${encodeURIComponent(text)}`;
            window.open(url, '_blank');
        },

        updateTimer() {
             // Deprecated legacy timer
        },

        getTeamName(teamId) {
            if (teamId == {{ $fixture->home_team_id }}) return '{{ $fixture->homeTeam->team->name }}';
            if (teamId == {{ $fixture->away_team_id }}) return '{{ $fixture->awayTeam->team->name }}';
            return 'Unknown Team';
        },

        getTeamPlayers(teamId) {
            return this.players.filter(p => p.team_id == teamId);
        },

        getEligiblePlayers(teamId) {
            // Get IDs of players who have received a RED_CARD
            let sentOffIds = this.events
                .filter(e => e.event_type === 'RED_CARD' && e.team_id == teamId)
                .map(e => e.player_id);
            
            // Filter fixture players: matches team AND player_id is NOT in sentOffIds
            // Note: Custom players (null player_id) are always included as they can't be tracked easily unless we track custom names.
            return this.players.filter(p => 
                p.team_id == teamId && 
                (!p.player_id || !sentOffIds.includes(p.player_id))
            );
        },

        getPhotoUrl(path) {
            if (path) return '{{ \Illuminate\Support\Facades\Storage::url("") }}' + path;
            return '{{ asset("images/defaultplayer.jpeg") }}';
        },

        getTeamLogoUrl(path) {
            if (path) return '{{ \Illuminate\Support\Facades\Storage::url("") }}' + path;
            return '{{ asset("images/default.jpeg") }}';
        },

        // --- Goal Logic ---
        // Goal Modal State
        showGoalModal: false,
        showGoalError: false,
        goalTeamId: null,
        goalPlayerId: '',
        goalGuestName: '',
        isGuestGoal: false,
        goalAssistId: '',
        goalAssistName: '',
        isGuestAssist: false,

        // ...

        // --- Goal Logic ---
        openGoalModal(teamId) {
            this.goalTeamId = teamId;
            this.goalPlayerId = '';
            this.goalGuestName = '';
            this.isGuestGoal = false;
            this.goalAssistId = '';
            this.goalAssistName = '';
            this.isGuestAssist = false;
            this.showGoalError = false;
            this.showGoalModal = true;
        },

        closeGoalModal() {
            this.showGoalModal = false;
        },

        submitGoal() {
             if (!this.goalPlayerId && (!this.isGuestGoal || !this.goalGuestName)) {
                this.showGoalError = true;
                return;
            }
            this.showGoalError = false; // Clear error if valid

            // Lookup selected player objects by FixturePlayer ID (goalPlayerId/goalAssistId now store this)
            let selectedScorer = this.goalPlayerId ? this.players.find(p => p.id == this.goalPlayerId) : null;
            let selectedAssist = this.goalAssistId ? this.players.find(p => p.id == this.goalAssistId) : null;

            let scorerId = selectedScorer ? selectedScorer.player_id : null;
            let scorerName = selectedScorer 
                ? (selectedScorer.player?.user?.name || selectedScorer.custom_name) 
                : (this.isGuestGoal ? this.goalGuestName : null);
                
            let assistId = selectedAssist ? selectedAssist.player_id : null;
            let assistName = selectedAssist 
                ? (selectedAssist.player?.user?.name || selectedAssist.custom_name) 
                : (this.isGuestAssist ? this.goalAssistName : null);

            this.postEvent({
                event_type: 'GOAL',
                minute: this.currentMinute,
                team_id: this.goalTeamId,
                player_id: scorerId,
                player_name: scorerName,
                assist_player_id: assistId,
                assist_player_name: assistName,
                description: 'Goal Scored'
            });

            this.closeGoalModal();
        },

        // --- Setup Logic ---
        openSetupModal() {
            this.showSetupModal = true;
        },
        
        submitStartMatch() {
             fetch('{{ route("scorer.start", $fixture->slug) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    duration: this.matchDuration,
                    home_starters: this.selectedHomeStarters,
                    away_starters: this.selectedAwayStarters
                })
            }).then(r => r.json()).then(data => {
                if(data.success) {
                    this.status = 'in_progress';
                    this.matchState = data.fixture.match_state;
                    this.currentMinute = data.fixture.current_minute;
                    this.isRunning = !!data.fixture.is_running;
                    this.showSetupModal = false;
                    // No reload needed now
                }
            });
        },

        finishMatch() {
             this.openConfirm('End Match', 'Are you sure you want to end the match?', () => {
                 fetch('{{ route("scorer.finish", $fixture->slug) }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
                }).then(r => r.json()).then(data => {
                    if(data.success) {
                        if(data.requires_penalties) {
                            this.hasPenalties = true;
                            this.matchState = data.fixture.match_state;
                            this.isRunning = false;
                        } else {
                            this.status = 'completed';
                            this.matchState = data.fixture.match_state;
                            this.isRunning = false;
                        }
                    }
                });
             });
        },

        openPenaltyModal(teamId) {
            this.penaltyTeamId = teamId;
            this.penaltyPlayerId = null;
            this.penaltyScored = true;
            this.penaltyAttemptNumber = this.penalties.length + 1;
            this.showPenaltyModal = true;
        },

        closePenaltyModal() {
            this.showPenaltyModal = false;
        },

        submitPenalty() {
            if(!this.penaltyPlayerId) return;
            
            let player = this.players.find(p => p.id == this.penaltyPlayerId);
            
            fetch('{{ route("scorer.penalty", $fixture->slug) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    team_id: this.penaltyTeamId,
                    player_id: player?.player_id,
                    player_name: player?.player?.user?.name || player?.custom_name,
                    scored: this.penaltyScored,
                    attempt_number: this.penaltyAttemptNumber
                })
            }).then(r => r.json()).then(res => {
                if(res.success) {
                    this.penalties.push(res.penalty);
                    this.homePenaltyScore = res.home_penalty_score;
                    this.awayPenaltyScore = res.away_penalty_score;
                    this.closePenaltyModal();
                }
            });
        },

        togglePenalty(penaltyId, newScored) {
            fetch('{{ route("scorer.penalty.update", [$fixture->slug, "PENALTY_ID"]) }}'.replace('PENALTY_ID', penaltyId), {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ scored: newScored })
            }).then(r => r.json()).then(res => {
                if(res.success) {
                    let penalty = this.penalties.find(p => p.id == penaltyId);
                    if(penalty) penalty.scored = newScored;
                    this.homePenaltyScore = res.home_penalty_score;
                    this.awayPenaltyScore = res.away_penalty_score;
                }
            });
        },

        completePenalties() {
            if(this.homePenaltyScore === this.awayPenaltyScore) {
                alert('Penalty shootout is still tied! Continue until there is a winner.');
                return;
            }
            
            let winnerId = this.homePenaltyScore > this.awayPenaltyScore ? {{ $fixture->home_team_id }} : {{ $fixture->away_team_id }};
            
            this.openConfirm('Complete Penalties', 'Confirm penalty shootout winner and end match?', () => {
                fetch('{{ route("scorer.complete-penalties", $fixture->slug) }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ winner_team_id: winnerId })
                }).then(r => r.json()).then(res => {
                    if(res.success) {
                        this.status = 'completed';
                        this.hasPenalties = false;
                    }
                });
            });
        },

        recordEvent(type, teamId = null, playerId = null, description = null) {
            this.postEvent({
                event_type: type,
                minute: this.currentMinute,
                team_id: teamId,
                player_id: playerId,
                player_name: description
            });
        },

        // --- Booking Modal Logic ---
        openCardModal(type) {
            this.bookingType = type;
            this.bookingTeamId = {{ $fixture->home_team_id }};
            this.bookingPlayerId = null;
            this.bookingGuestName = '';
            this.isGuestBooking = false;
            this.showBookingModal = true;
        },

        closeBookingModal() {
            this.showBookingModal = false;
        },

        get isValidBooking() {
            if (this.isGuestBooking) return this.bookingGuestName.trim().length > 0;
            return this.bookingPlayerId !== null;
        },

        submitBooking() {
            if (!this.isValidBooking) return;
            let desc = this.isGuestBooking ? this.bookingGuestName : null;
            this.recordEvent(this.bookingType, this.bookingTeamId, this.bookingPlayerId, desc);
            this.closeBookingModal();
        },

        // --- Substitution Modal Logic ---
        openSubModal() {
            this.subTeamId = {{ $fixture->home_team_id }};
            this.resetSubSelection();
            this.showSubModal = true;
        },

        closeSubModal() {
            this.showSubModal = false;
        },

        resetSubSelection() {
            this.subPlayerOutId = null;
            this.subPlayerInId = null;
        },

        getPlayersForSub(teamId, isActive) {
            return this.players.filter(p => p.team_id == teamId && (isActive ? p.is_active : !p.is_active));
        },

        get isValidSub() {
            return this.subPlayerOutId !== null && this.subPlayerInId !== null && this.subPlayerOutId !== this.subPlayerInId;
        },

        submitSubstitution() {
            if (!this.isValidSub) return;
             fetch('{{ route("scorer.substitute", $fixture->slug) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    minute: this.currentMinute,
                    team_id: this.subTeamId,
                    player_out_id: this.subPlayerOutId,
                    player_in_id: this.subPlayerInId
                })
            }).then(r => r.json()).then(res => {
                if(res.success) {
                    let pOut = this.players.find(p => p.player_id == this.subPlayerOutId);
                    if(pOut) { pOut.is_active = false; pOut.status = 'subbed_out'; }
                    let pIn = this.players.find(p => p.player_id == this.subPlayerInId);
                    if(pIn) { pIn.is_active = true; pIn.status = 'subbed_in'; }

                    this.events.unshift({
                        id: 'temp_' + Date.now(),
                        minute: this.currentMinute,
                        event_type: 'SUB',
                        team_id: this.subTeamId,
                        description: 'Substitution made',
                        player_name: 'Tactical Change' 
                    });
                    
                    this.closeSubModal();
                }
            });
        },

        submitCommentary() {
            if(!this.commentaryText) return;
            this.postEvent({
                event_type: 'COMMENTARY',
                minute: this.currentMinute,
                description: this.commentaryText
            });
            this.commentaryText = '';
        },

        postEvent(data) {
             fetch('{{ route("scorer.event", $fixture->slug) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            }).then(r => r.json()).then(res => {
                if(res.success) {
                    this.events.unshift(res.event);
                    if (res.new_scores) {
                        this.homeScore = res.new_scores.home;
                        this.awayScore = res.new_scores.away;
                    }
                }
            });
        },

        deleteEvent(eventId) {
            this.openConfirm('Undo Event', 'Are you sure you want to delete this event? This action cannot be undone.', () => {
                // Construct delete URL manually 
                // Note: Route is 'delete-event/{event}' prefix 'console/{fixture}' -> 'console/slug/delete-event/id'
                // Base route 'scorer.event' is 'console/slug/event', so we need to adjust
                let url = '{{ route("scorer.event", $fixture->slug) }}'.replace(/\/event$/, '/delete-event/') + eventId;
                
                fetch(url, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
                }).then(r => r.json()).then(res => {
                    if(res.success) {
                        let deletedEvent = this.events.find(e => e.id == eventId);
                        this.events = this.events.filter(e => e.id != eventId);
                        
                        if (res.new_scores) {
                            this.homeScore = res.new_scores.home;
                            this.awayScore = res.new_scores.away;
                        }
                        
                        if (deletedEvent && deletedEvent.event_type == 'SUB') {
                             window.location.reload(); 
                        }
                    }
                });
            });
        },

        // --- Confirmation Modal Logic ---
        openConfirm(title, message, callback) {
            this.confirmTitle = title;
            this.confirmMessage = message;
            this.confirmCallback = callback;
            this.showConfirmModal = true;
        },

        closeConfirmModal() {
            this.showConfirmModal = false;
            this.confirmCallback = null;
        },

        triggerConfirm() {
            if (this.confirmCallback) {
                this.confirmCallback();
            }
            this.closeConfirmModal();
        }
    }
}
</script>
@endsection
