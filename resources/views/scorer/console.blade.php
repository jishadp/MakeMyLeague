@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 font-sans pb-24 lg:pb-0 relative overflow-x-hidden selection:bg-blue-100 selection:text-blue-900" 
     x-data="scorerConsole()">

    <!-- Modern Sticky Header -->
    <div class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200 shadow-sm transition-all duration-300">
        <div class="container mx-auto px-4 py-3 flex flex-col gap-2">
            
            <!-- Match Status & Timer -->
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full animate-pulse" 
                          :class="status == 'in_progress' ? 'bg-emerald-500' : (status == 'completed' ? 'bg-slate-400' : 'bg-amber-400')"></span>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500" x-text="status.replace('_', ' ')"></span>
                </div>
                
                <div class="font-mono font-black text-2xl tracking-tight text-slate-800 tabular-nums">
                    <span x-text="matchTimeDisplay">00:00</span>
                </div>
            </div>

            <!-- Scoreboard -->
            <div class="flex items-center justify-between gap-4 mt-1">
                <!-- Home Team -->
                <div class="flex-1 flex items-center justify-end gap-3 text-right">
                    <div class="flex flex-col">
                        <span class="font-bold text-sm md:text-base leading-tight">{{ $fixture->homeTeam->team->name }}</span>
                         <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Home</span>
                    </div>
                </div>

                <!-- Score -->
                <div class="flex items-center gap-3 bg-slate-100 px-4 py-1.5 rounded-full font-bold text-xl md:text-2xl shadow-inner border border-slate-200/60">
                    <span class="text-blue-600" x-text="homeScore">{{ $fixture->home_score ?? 0 }}</span>
                    <span class="text-slate-300 text-lg">-</span>
                    <span class="text-rose-500" x-text="awayScore">{{ $fixture->away_score ?? 0 }}</span>
                </div>

                <!-- Away Team -->
                <div class="flex-1 flex items-center justify-start gap-3 text-left">
                    <div class="flex flex-col">
                        <span class="font-bold text-sm md:text-base leading-tight">{{ $fixture->awayTeam->team->name }}</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Away</span>
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
            
            <!-- Primary Actions (Goals) -->
            <div class="grid grid-cols-2 gap-4">
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

            <!-- Secondary Actions -->
            <div class="grid grid-cols-3 gap-3">
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
                
                <div class="max-h-[300px] overflow-y-auto p-0" id="feed-container">
                    <template x-for="(event, index) in events" :key="event.id">
                        <div class="flex gap-3 p-4 border-b border-slate-50 last:border-0 hover:bg-slate-50 transition-colors relative group">
                            <div class="min-w-[40px] text-center">
                                <span class="font-mono text-xs font-black text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded" x-text="event.minute + '\''"></span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="text-sm font-bold"
                                        :class="{
                                            'text-blue-600': event.event_type == 'GOAL',
                                            'text-amber-500': event.event_type == 'YELLOW_CARD',
                                            'text-rose-500': event.event_type == 'RED_CARD',
                                            'text-purple-500': event.event_type == 'SUB',
                                            'text-slate-700': !['GOAL', 'YELLOW_CARD', 'RED_CARD', 'SUB'].includes(event.event_type)
                                        }" 
                                        x-text="event.event_type.replace('_', ' ')">
                                    </span>
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 uppercase tracking-wide" x-show="event.team_id" x-text="getTeamName(event.team_id).substring(0,3)"></span>
                                </div>
                                <p class="text-sm text-slate-600 leading-snug" x-text="event.description || event.player_name || 'Event'"></p>
                            </div>
                            
                            <!-- Delete Button (Only for latest event) -->
                            <div x-show="index === 0" class="absolute right-2 top-3">
                                <button @click="deleteEvent(event.id)" class="w-8 h-8 rounded-full bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 flex items-center justify-center transition-all shadow-sm">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>
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

            <div class="pt-4 pb-8">
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

</div>

<!-- Logic Script (Unchanged) -->
<script>
function scorerConsole() {
    return {
        status: '{{ $fixture->status }}',
        startedAt: '{{ $fixture->started_at }}',
        events: @json($fixture->events),
        players: @json($fixture->fixturePlayers->load('player.user')),
        homeRoster: @json($fixture->homeTeam->leaguePlayers),
        awayRoster: @json($fixture->awayTeam->leaguePlayers),
        homeScore: {{ $fixture->home_score ?? 0 }},
        awayScore: {{ $fixture->away_score ?? 0 }},

        currentMinute: 0,
        commentaryText: '',
        matchTimeDisplay: '00:00',

        // Setup Modal State
        showSetupModal: false,
        matchDuration: 45,
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

        init() {
            this.updateTimer();
            setInterval(() => this.updateTimer(), 1000); 
        },

        updateTimer() {
            if (this.status === 'in_progress' && this.startedAt) {
                 let start = new Date(this.startedAt).getTime();
                 let now = new Date().getTime();
                 let diff = now - start;
                 let totalSeconds = Math.floor(diff / 1000);
                 let minutes = Math.floor(totalSeconds / 60);
                 let seconds = totalSeconds % 60;
                 this.currentMinute = minutes;
                 this.matchTimeDisplay = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            } else {
                 this.matchTimeDisplay = '00:00';
            }
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
                    this.startedAt = new Date().toISOString();
                    this.showSetupModal = false;
                    window.location.reload();
                }
            });
        },

        finishMatch() {
             this.openConfirm('End Match', 'Are you sure you want to end the match? This cannot be undone.', () => {
                 fetch('{{ route("scorer.finish", $fixture->slug) }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
                }).then(r => r.json()).then(data => {
                    if(data.success) this.status = 'completed';
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
