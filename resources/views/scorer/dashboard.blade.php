@extends('layouts.app')

@section('content')
<style>
    /* Theme Variables - Matching league-matches.blade.php */
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
     x-data="{ activeTab: 'live', theme: localStorage.getItem('scorer_theme') || 'dark' }"
     x-init="$watch('theme', val => localStorage.setItem('scorer_theme', val))"
     :class="'theme-' + theme">

    <!-- Header -->
    <div class="relative border-b py-8 bg-[var(--bg-header)] border-[var(--border)] theme-transition">
         <div class="absolute inset-0 overflow-hidden pointer-events-none">
             <div class="absolute inset-0 bg-gradient-to-br from-[var(--accent)]/5 via-transparent to-transparent"></div>
         </div>

         <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
             
             <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                 <div class="text-center md:text-left">
                     <h1 class="text-3xl font-black tracking-tight leading-tight text-[var(--text-main)]">Scorer Dashboard</h1>
                     <p class="text-[var(--text-muted)] mt-1">Manage and score your league matches</p>
                 </div>

                 <div class="flex flex-col sm:flex-row items-center gap-4">
                     <!-- Theme Switcher -->
                     <div class="flex items-center bg-[var(--bg-element)] p-1 rounded-xl border border-[var(--border)]">
                        <button @click="theme = 'dark'" class="w-9 h-9 rounded-lg flex items-center justify-center transition-all" :class="theme === 'dark' ? 'bg-[var(--bg-card)] text-[var(--accent)] shadow-sm' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'">
                            <i class="fa-solid fa-moon"></i>
                        </button>
                        <button @click="theme = 'white'" class="w-9 h-9 rounded-lg flex items-center justify-center transition-all" :class="theme === 'white' ? 'bg-[var(--bg-card)] text-[var(--accent)] shadow-sm' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'">
                            <i class="fa-solid fa-sun"></i>
                        </button>
                        <button @click="theme = 'green'" class="w-9 h-9 rounded-lg flex items-center justify-center transition-all" :class="theme === 'green' ? 'bg-[var(--bg-card)] text-[var(--accent)] shadow-sm' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'">
                            <i class="fa-solid fa-leaf"></i>
                        </button>
                    </div>

                     <a href="{{ route('scorer.matches.create') }}" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all border bg-[var(--accent)] border-[var(--accent)] hover:bg-[var(--accent-hover)] shadow-lg shadow-[var(--accent)]/20 flex items-center gap-2">
                         <i class="fa-solid fa-plus"></i> Create Match
                     </a>
                 </div>
             </div>
         </div>
    </div>

    <!-- Sticky Tabs -->
    <div class="sticky top-0 z-30 backdrop-blur-md border-b bg-[var(--bg-page)]/80 border-[var(--border)] theme-transition">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 overflow-x-auto scrollbar-hide">
            <div class="flex justify-start gap-8 min-w-max">
                @php
                    $liveMatches = $fixtures->whereIn('status', ['in_progress']);
                    $upcomingMatches = $fixtures->whereIn('status', ['scheduled', 'unscheduled'])->sortBy('match_date');
                    $completedMatches = $fixtures->whereIn('status', ['completed', 'cancelled'])->sortByDesc('match_date');
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

                 <button @click="activeTab = 'completed'" 
                    class="relative py-4 text-sm font-bold transition-colors"
                    :class="activeTab === 'completed' ? 'text-[var(--accent)]' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'">
                    COMPLETED
                    <div x-show="activeTab === 'completed'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[var(--accent)]" x-transition></div>
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
                </div>
            @else
                <div class="space-y-4">
                     @foreach($liveMatches as $match)
                        @include('scorer.partials.match-card', ['match' => $match, 'isLive' => true])
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
                        @include('scorer.partials.match-card', ['match' => $match, 'isUpcoming' => true])
                    @endforeach
                </div>
            @endif
        </div>

        <!-- COMPLETED TAB -->
        <div x-show="activeTab === 'completed'" x-transition:enter="transition ease-out duration-300 opacity-0 transform translate-y-2">
             @if($completedMatches->isEmpty())
                 <div class="flex flex-col items-center justify-center py-20 rounded-2xl border border-dashed bg-[var(--bg-element)]/30 border-[var(--border)] text-[var(--text-muted)]">
                    <p class="text-sm font-medium">No completed matches</p>
                </div>
             @else
                <div class="space-y-3">
                     @foreach($completedMatches as $match)
                        @include('scorer.partials.match-card', ['match' => $match, 'isCompleted' => true])
                     @endforeach
                </div>
             @endif
        </div>
    </div>
</div>
@endsection
