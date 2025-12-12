@extends('layouts.app')

@section('title', $leagueTeam->team->name . ' Poster')

@section('styles')
<style>
    @font-face {
        font-family: 'SportsFont';
        src: url('https://fonts.googleapis.com/css2?family=Teko:wght@300;400;500;600;700&display=swap');
    }
    .font-sports {
        font-family: 'Teko', sans-serif;
    }
    .text-outline-number {
        -webkit-text-stroke: 2px #000;
        color: transparent;
        font-weight: 900;
        z-index: 10;
        position: relative;
    }
    .clip-wavy-bottom {
        clip-path: polygon(0 0, 100% 0, 100% 85%, 50% 100%, 0 85%);
    }
    /* Hide scrollbar for clean UI */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    
    @media print {
        @page {
            margin: 0;
            size: auto;
        }
        body * {
            visibility: hidden;
        }
        #poster-container, #poster-container * {
            visibility: visible;
        }
        #poster-container {
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background: white; 
            z-index: 9999;
        }
        /* Ensure specific print styles */
        .print-force-colors {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        /* Hide buttons in print */
        button {
            display: none !important;
        }
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-100 pb-12">
    <!-- Toolbar -->
    <div class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-200 shadow-sm px-4 py-3 print:hidden">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('posters.list') }}" class="text-gray-500 hover:text-gray-900 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 class="text-lg font-bold text-gray-900">{{ $leagueTeam->team->name }}</h1>
            </div>

            <div class="flex items-center gap-2 bg-gray-100 p-1 rounded-lg">
                <button onclick="switchDesign('classic')" id="btn-classic" class="px-4 py-1.5 rounded-md text-sm font-medium bg-white shadow-sm text-gray-900 transition-all">
                    Classic
                </button>
                <button onclick="switchDesign('sporting')" id="btn-sporting" class="px-4 py-1.5 rounded-md text-sm font-medium text-gray-500 hover:text-gray-900 transition-all">
                    Sporting
                </button>
            </div>

            <div class="flex items-center gap-2 bg-gray-100 p-1 rounded-lg">
                <button onclick="setRatio('9/16')" id="btn-ratio-9-16" class="px-3 py-1.5 rounded-md text-sm font-medium text-gray-500 hover:text-gray-900 transition-all flex items-center gap-1">
                   <svg class="w-4 h-4" viewBox="0 0 10 16" fill="currentColor"><rect width="10" height="16" rx="1" stroke="currentColor" fill="none"/></svg> 9:16
                </button>
                <button onclick="setRatio('16/9')" id="btn-ratio-16-9" class="px-3 py-1.5 rounded-md text-sm font-medium text-gray-500 hover:text-gray-900 transition-all flex items-center gap-1">
                    <svg class="w-4 h-4 rotate-90" viewBox="0 0 10 16" fill="currentColor"><rect width="10" height="16" rx="1" stroke="currentColor" fill="none"/></svg> 16:9
                </button>
                <button onclick="setRatio('auto')" id="btn-ratio-auto" class="px-3 py-1.5 rounded-md text-sm font-medium bg-white shadow-sm text-gray-900 transition-all">
                    Auto
                </button>
            </div>
            
            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 shadow-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download/Print
            </button>
        </div>
    </div>

    <!-- Poster Canvas -->
    <div class="flex justify-center p-4 sm:p-8 overflow-auto">
        <div id="poster-container" class="relative bg-white shadow-2xl transition-all duration-300 ease-in-out w-full max-w-4xl print-force-colors overflow-hidden">
            
            <!-- CLASSIC DESIGN -->
            <div id="design-classic" class="w-full h-full relative bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 p-6 sm:p-10 flex flex-col">
                <!-- Background Effects -->
                <div class="pointer-events-none absolute inset-0 opacity-70 z-0">
                    <div class="absolute -top-32 right-0 h-64 w-64 bg-pink-500/30 blur-[120px]"></div>
                    <div class="absolute bottom-0 left-0 h-72 w-72 bg-indigo-500/40 blur-[120px]"></div>
                </div>

                <!-- Header -->
                <div class="relative z-10 mb-8 flex items-center justify-between border-b border-white/10 pb-6">
                    <!-- Classic Header Content -->
                    <div class="space-y-2">
                         <div class="flex items-center gap-3">
                            <span class="px-2 py-0.5 rounded bg-white/10 text-[10px] sm:text-xs text-white uppercase tracking-widest">{{ $league->name }}</span>
                            <span class="h-1 w-1 rounded-full bg-white/40"></span>
                            <span class="text-[10px] sm:text-xs text-white/60 uppercase tracking-widest">{{ $league->season ?? date('Y') }}</span>
                         </div>
                         <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight">{{ $leagueTeam->team->name }}</h1>
                         <p class="text-sm text-indigo-200 font-medium flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $leagueTeam->team->homeGround->name ?? 'Home Ground TBD' }}
                         </p>
                    </div>
                    
                    <div class="flex items-center gap-4">
                         @if($league->logo)
                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/5 rounded-2xl p-2 backdrop-blur-sm border border-white/10">
                                <img src="{{ Storage::url($league->logo) }}" class="w-full h-full object-contain">
                            </div>
                        @else
                             <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/5 rounded-2xl p-2 backdrop-blur-sm border border-white/10 flex items-center justify-center text-white font-bold text-xl">
                                {{ strtoupper(substr($league->name, 0, 1)) }}
                             </div>
                        @endif
                        
                        @if($leagueTeam->team->logo)
                             <div class="w-20 h-20 sm:w-24 sm:h-24 bg-white/5 rounded-full p-2 backdrop-blur-sm border border-white/10 shadow-lg shadow-indigo-500/20">
                                <img src="{{ Storage::url($leagueTeam->team->logo) }}" class="w-full h-full object-cover rounded-full">
                            </div>
                        @else
                            <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-black text-2xl shadow-lg border-2 border-white/20">
                                {{ strtoupper(substr($leagueTeam->team->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="relative z-10 grid grid-cols-12 gap-8 flex-grow">
                    <!-- Left Stats Panel -->
                    <div class="col-span-12 md:col-span-3 space-y-4">
                         <!-- Owner Card -->
                         @php
                            $owner = $leagueTeam->team->owners->where('role', 'owner')->first();
                         @endphp
                         @if($owner)
                         <div class="bg-white/5 border border-white/10 rounded-2xl p-4 backdrop-blur-sm">
                            <p class="text-[10px] uppercase tracking-widest text-amber-400 mb-2">Team Owner</p>
                            <div class="flex items-center gap-3">
                                @if($owner->user->photo)
                                    <img src="{{ Storage::url($owner->user->photo) }}" class="w-10 h-10 rounded-full object-cover border border-white/20">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-amber-500/20 flex items-center justify-center text-amber-200 font-bold text-xs ring-1 ring-amber-500/40">
                                        {{ substr($owner->user->name, 0, 2) }}
                                    </div>
                                @endif
                                <p class="text-white font-semibold text-sm">{{ $owner->user->name }}</p>
                            </div>
                         </div>
                         @endif

                         <!-- Stats -->
                         <div class="grid grid-cols-2 md:grid-cols-1 gap-2">
                             <div class="bg-indigo-600/20 border border-indigo-500/30 rounded-2xl p-4 text-center">
                                 <div class="text-3xl font-black text-white">{{ $leagueTeam->players->count() }}</div>
                                 <div class="text-[10px] text-indigo-200 uppercase tracking-widest mt-1">SQUAD SIZE</div>
                             </div>
                              <div class="bg-white/5 border border-white/10 rounded-2xl p-4 text-center">
                                 <div class="text-xl font-bold text-white">{{ $leagueTeam->points ?? 0 }}</div>
                                 <div class="text-[10px] text-slate-400 uppercase tracking-widest mt-1">POINTS</div>
                             </div>
                         </div>
                    </div>

                    <!-- Right Players Grid -->
                    <div class="col-span-12 md:col-span-9">
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 player-grid">
                            @foreach($leagueTeam->players->sortByDesc('retention') as $player)
                                @php
                                    $photoUrl = $player->user->photo ? Storage::url($player->user->photo) : asset('images/defaultplayer.jpeg');
                                @endphp
                                <div class="group relative aspect-[3/4] rounded-xl overflow-hidden bg-slate-800 border {{ $player->retention ? 'border-amber-400/50 shadow-[0_0_15px_rgba(251,191,36,0.2)]' : 'border-white/10' }}">
                                    <img src="{{ $photoUrl }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 opacity-80 group-hover:opacity-100">
                                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black via-black/80 to-transparent p-3 pt-12">
                                        @if($player->retention)
                                            <div class="absolute top-2 right-2 bg-amber-400 text-black text-[10px] font-bold px-2 py-0.5 rounded shadow-sm">RETAINED</div>
                                        @endif
                                        <h3 class="text-white font-bold leading-tight truncate">{{ $player->user->name }}</h3>
                                        @if($player->user->position)
                                            <p class="text-xs text-indigo-300 font-medium mt-0.5">{{ $player->user->position->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- SPORTING DESIGN (New) -->
            <div id="design-sporting" class="w-full h-full bg-slate-100 relative hidden flex-col">
                <!-- Top Header Section with Black Wavy background -->
                <div class="bg-[#1a1a1a] pt-8 pb-16 relative">
                     <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-[0]">
                         <!-- Simple wavy SVG -->
                        <svg class="relative block w-[calc(110%+1.3px)] h-[50px]" fill="#f1f5f9" viewBox="0 0 1200 120" preserveAspectRatio="none">
                            <path d="M985.66,92.83C906.67,72.00,823.78,31.00,747.46,87.16C430.87,320.67,247.46,4.16,8.71,5.16L0,120L1200,120L1200,8.75C1138.89,17.25,1065.66,113.67,985.66,92.83Z"></path>
                        </svg>
                     </div>

                     <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center relative z-10 font-sports gap-6">
                         <!-- Left: League Logo -->
                         <div class="flex flex-col items-center flex-shrink-0">
                            @if($league->logo)
                                <img src="{{ Storage::url($league->logo) }}" class="h-20 w-20 md:h-24 md:w-24 object-contain filter drop-shadow-lg bg-white/10 rounded-full p-2">
                            @else
                                <div class="h-20 w-20 md:h-24 md:w-24 rounded-full border-4 border-white/20 flex items-center justify-center text-white text-3xl font-bold bg-white/5">
                                    {{ substr($league->name, 0, 1) }}
                                </div>
                            @endif
                            <span class="text-white/50 text-xs mt-2 tracking-widest uppercase font-sans">{{ $league->short_name }}</span>
                         </div>

                         <!-- Center: Team Name -->
                         <div class="text-center flex-1 px-4 min-w-0">
                             <h1 class="text-5xl sm:text-7xl md:text-8xl font-bold text-amber-500 uppercase tracking-tighter leading-[0.85] break-words w-full" style="font-family: 'Teko', sans-serif;">
                                {{ $leagueTeam->team->name }}
                             </h1>
                             @if($leagueTeam->team->homeGround)
                                <div class="mt-4 inline-block px-4 py-1 border border-white/30 text-white/70 rounded-full text-sm uppercase tracking-widest font-sans">
                                    {{ $leagueTeam->team->homeGround->name }}
                                </div>
                             @endif
                         </div>

                         <!-- Right: Team Logo -->
                         <div class="flex flex-col items-center flex-shrink-0">
                            @if($leagueTeam->team->logo)
                                <img src="{{ Storage::url($leagueTeam->team->logo) }}" class="h-24 w-24 md:h-28 md:w-28 object-contain filter drop-shadow-2xl">
                            @else
                                <div class="h-24 w-24 md:h-28 md:w-28 rounded-full border-4 border-amber-500 flex items-center justify-center text-amber-500 text-3xl font-bold bg-black">
                                    {{ substr($leagueTeam->team->name, 0, 2) }}
                                </div>
                            @endif
                         </div>
                     </div>
                </div>

                <!-- Players Grid Sporting -->
                <div class="flex-grow p-8 bg-slate-100 relative">
                     <!-- Background texture/text -->
                     <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-[0.03]">
                         <span class="text-[200px] font-black absolute -top-20 -left-20 rotate-[-10deg]">{{ $league->short_name }}</span>
                         <span class="text-[200px] font-black absolute bottom-0 right-0 rotate-[-10deg]">{{ date('Y') }}</span>
                     </div>

                     <div class="max-w-7xl mx-auto relative z-10 flex flex-col gap-10">
                        @php
                            $retained = $leagueTeam->players->filter(fn($p) => $p->retention);
                            $others = $leagueTeam->players->reject(fn($p) => $p->retention)->sortBy(fn($p) => $p->user->name);
                        @endphp

                        <!-- RETAINED PLAYERS ROW -->
                        @if($retained->isNotEmpty())
                        <div class="flex flex-wrap justify-center gap-8">
                             @foreach($retained as $player)
                                @php
                                    $photoUrl = $player->user->photo ? Storage::url($player->user->photo) : asset('images/defaultplayer.jpeg');
                                @endphp
                                <div class="flex flex-col group w-48 sm:w-56 relative">
                                    <div class="absolute -top-6 -right-6 z-30 text-amber-500 drop-shadow-lg animate-pulse">
                                         <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    </div>
                                    <div class="relative bg-amber-400 aspect-[4/5] mb-0 overflow-hidden shadow-2xl ring-4 ring-amber-500/50 rounded-sm">
                                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 text-[100px] font-bold text-black opacity-10 leading-none font-sports z-0 select-none">
                                            R
                                        </div>
                                        <img src="{{ $photoUrl }}" class="absolute bottom-0 left-0 w-full h-[95%] object-cover object-top z-10 hover:scale-105 transition-transform duration-500">
                                    </div>
                                    <div class="bg-black text-white p-2 text-center relative z-20 -mt-2 mx-1 border-t-2 border-amber-500">
                                        <h3 class="text-sm font-bold uppercase leading-tight font-sports tracking-wide break-words">
                                            {{ $player->user->name }}
                                        </h3>
                                    </div>
                                </div>
                             @endforeach
                        </div>
                        @endif

                        <!-- OTHER PLAYERS GRID (3 per row, centered last row) -->
                        <div class="flex flex-wrap justify-center gap-6" id="sporting-others-grid">
                             @foreach($others as $index => $player)
                                @php
                                    $photoUrl = $player->user->photo ? Storage::url($player->user->photo) : asset('images/defaultplayer.jpeg');
                                @endphp
                                <div class="flex flex-col group w-[45%] md:w-[30%] lg:w-[30%]">
                                    <div class="relative bg-slate-200 aspect-[4/5] mb-0 overflow-hidden shadow-lg transition-transform hover:-translate-y-2">
                                        <!-- Number Background -->
                                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 text-[100px] font-bold text-black opacity-5 leading-none font-sports z-0 select-none">
                                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        </div>
                                        
                                        <img src="{{ $photoUrl }}" class="absolute bottom-0 left-0 w-full h-[90%] object-cover object-top z-10 transition-transform duration-500 hover:scale-105">
                                    </div>
                                    
                                    <div class="bg-black text-white p-2 text-center relative z-20 -mt-2 mx-2">
                                        <p class="text-amber-500 text-[9px] font-bold tracking-[0.2em] uppercase leading-none mb-1 font-sports">
                                            {{ $player->user->position->name ?? 'PLAYER' }}
                                        </p>
                                        <h3 class="text-sm font-bold uppercase leading-tight font-sports tracking-wide break-words">
                                            {{ $player->user->name }}
                                        </h3>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                     </div>
                </div>

                <!-- Footer / Managers -->
                <div class="bg-[#1a1a1a] text-white p-8 mt-auto relative overflow-hidden">
                    <div class="max-w-7xl mx-auto">
                        <!-- Divider -->
                        <div class="flex items-center gap-4 mb-6">
                            <div class="h-px bg-white/20 flex-grow"></div>
                            <h2 class="text-amber-500 font-bold uppercase tracking-widest text-sm bg-[#1a1a1a] px-4 font-sports text-2xl">Management</h2>
                            <div class="h-px bg-white/20 flex-grow"></div>
                        </div>

                         <div class="flex flex-wrap justify-center gap-8">
                             <!-- Owner -->
                             @if($owner)
                             <div class="flex flex-col items-center">
                                 <div class="w-16 h-16 rounded grayscale mb-2 overflow-hidden border-2 border-amber-500/50">
                                    @if($owner->user->photo)
                                        <img src="{{ Storage::url($owner->user->photo) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-800"></div>
                                    @endif
                                 </div>
                                 <p class="font-bold text-sm uppercase">{{ $owner->user->name }}</p>
                                 <p class="text-xs text-white/50 uppercase tracking-wider">Owner</p>
                             </div>
                             @endif
                        </div>
                        
                        <!-- Watermark -->
                        <div class="absolute bottom-0 left-0 w-full text-center opacity-5 pointer-events-none">
                            <h1 class="text-[80px] sm:text-[120px] font-black text-white uppercase leading-[0.8] truncate pb-4 font-sports">
                                {{ $league->name }}
                            </h1>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // State
    let currentDesign = 'sporting'; // Default to new design
    let currentRatio = 'auto';

    // Elements
    const container = document.getElementById('poster-container');
    const classicDesign = document.getElementById('design-classic');
    const sportingDesign = document.getElementById('design-sporting');
    
    // Controls
    const btnClassic = document.getElementById('btn-classic');
    const btnSporting = document.getElementById('btn-sporting');
    const btnsRatio = {
        '9/16': document.getElementById('btn-ratio-9-16'),
        '16/9': document.getElementById('btn-ratio-16-9'),
        'auto': document.getElementById('btn-ratio-auto')
    };

    function updateUI() {
        // Update Design Visibility
        if (currentDesign === 'classic') {
            classicDesign.classList.remove('hidden');
            classicDesign.classList.add('flex');
            sportingDesign.classList.add('hidden');
            sportingDesign.classList.remove('flex');
            
            // Button Styles
            btnClassic.className = "px-4 py-1.5 rounded-md text-sm font-medium bg-white shadow-sm text-gray-900 transition-all";
            btnSporting.className = "px-4 py-1.5 rounded-md text-sm font-medium text-gray-500 hover:text-gray-900 transition-all";
        } else {
            classicDesign.classList.add('hidden');
            classicDesign.classList.remove('flex');
            sportingDesign.classList.remove('hidden');
            sportingDesign.classList.add('flex');
            
            // Button Styles
            btnSporting.className = "px-4 py-1.5 rounded-md text-sm font-medium bg-black text-white shadow-sm transition-all";
            btnClassic.className = "px-4 py-1.5 rounded-md text-sm font-medium text-gray-500 hover:text-gray-900 transition-all";
        }

        // Update Ratio
        container.classList.remove('aspect-[9/16]', 'aspect-[16/9]', 'max-w-md', 'max-w-4xl', 'w-full');
        
        // Reset button styles
        Object.values(btnsRatio).forEach(btn => {
            btn.className = "px-3 py-1.5 rounded-md text-sm font-medium text-gray-500 hover:text-gray-900 transition-all flex items-center gap-1";
            // Check if it has svg, simpler reset logic
        });

        // Set Active Ratio Button Style
        const activeBtn = btnsRatio[currentRatio];
        if(activeBtn) {
            activeBtn.className = "px-3 py-1.5 rounded-md text-sm font-medium bg-white shadow-sm text-gray-900 transition-all flex items-center gap-1";
        }

        if (currentRatio === '9/16') {
            container.classList.add('aspect-[9/16]', 'max-w-md'); // Portrait constraint
            updateGridColumns(2); // Fewer cols for portrait
        } else if (currentRatio === '16/9') {
            container.classList.add('aspect-[16/9]', 'max-w-6xl'); // Landscape constraint
            updateGridColumns(5); // More cols for landscape
        } else {
            container.classList.add('w-full', 'max-w-5xl', 'min-h-[800px]'); // Auto/Responsive
            updateGridColumns(4); // Default responsive
        }
    }

    function updateGridColumns(cols) {
        const classicGroups = document.querySelectorAll('.player-grid');
        classicGroups.forEach(grid => {
            grid.classList.remove('grid-cols-2', 'grid-cols-3', 'grid-cols-4', 'grid-cols-5');
             if (currentRatio !== 'auto') {
                if (currentRatio === '9/16') {
                    grid.classList.add('grid-cols-2');
                } else {
                    grid.classList.add('grid-cols-5');
                }
            }
        });
        
        const sportingGrid = document.getElementById('sporting-others-grid');
        if(sportingGrid) {
            // No strict column adjustments for Sporting flex grid, handled by flex-wrap
        }
    }

    window.switchDesign = function(design) {
        currentDesign = design;
        updateUI();
    }

    window.setRatio = function(ratio) {
        currentRatio = ratio;
        updateUI();
    }

    // Initialize
    updateUI();
</script>
@endsection
