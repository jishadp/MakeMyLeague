@extends('layouts.app')

@section('title', 'Digital Assets Library')

@section('content')
<div class="min-h-screen bg-slate-50 relative overflow-hidden" x-data="{ activeLeagueId: null }">
    <!-- Background Elements -->
    <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-indigo-900 via-slate-900 to-slate-50 -z-10"></div>
    <div class="absolute top-0 inset-x-0 h-96 overflow-hidden -z-10 opacity-20 pointer-events-none">
        <svg class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[1000px]" fill="none" viewBox="0 0 1000 1000" xmlns="http://www.w3.org/2000/svg">
            <circle cx="500" cy="500" r="400" stroke="white" stroke-width="2" stroke-dasharray="10 10" opacity="0.1"/>
            <circle cx="500" cy="500" r="300" stroke="white" stroke-width="40" opacity="0.05"/>
            <circle cx="500" cy="500" r="200" stroke="white" stroke-width="2" opacity="0.1"/>
        </svg>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 relative z-10">
        
        <!-- Header -->
        <div class="text-center mb-16 space-y-4">
            <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight uppercase" 
                x-text="activeLeagueId ? 'Select Team' : 'Digital Assets'">
                Digital Assets
            </h1>
            <p class="text-indigo-200 text-lg max-w-2xl mx-auto"
               x-text="activeLeagueId ? 'Choose a team to generate professional posters and media assets.' : 'Select a league to access team branding and promotional materials.'">
                Select a league to access team branding and promotional materials.
            </p>
            
            <!-- Breadcrumb / Back Button -->
            <div x-show="activeLeagueId" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="pt-4">
                <button @click="activeLeagueId = null" 
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-white/10 hover:bg-white/20 text-white backdrop-blur-md border border-white/20 transition-all group">
                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Leagues
                </button>
            </div>
        </div>

        <!-- Leagues Grid -->
        <div x-show="!activeLeagueId" 
             x-transition:enter="transition ease-out duration-500 delay-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            @foreach($leagues as $league)
                <div @click="activeLeagueId = {{ $league->id }}" 
                     class="group relative bg-white rounded-2xl shadow-xl overflow-hidden cursor-pointer hover:-translate-y-2 hover:shadow-2xl transition-all duration-300 border border-slate-100">
                    
                    <!-- Card Header / Pattern -->
                    <div class="h-32 bg-gray-900 relative overflow-hidden">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($league->name) }}&background=random&color=fff&size=512" 
                             class="absolute inset-0 w-full h-full object-cover opacity-30 blur-xl scale-125">
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent"></div>
                    </div>

                    <!-- League Logo -->
                    <div class="absolute top-16 left-1/2 -translate-x-1/2 w-24 h-24 bg-white rounded-2xl p-2 shadow-lg ring-4 ring-white">
                        <div class="w-full h-full bg-slate-50 rounded-xl flex items-center justify-center overflow-hidden">
                            @if($league->logo)
                                <img src="{{ Storage::url($league->logo) }}" class="w-full h-full object-contain p-1">
                            @else
                                <span class="text-2xl font-black text-slate-300">{{ substr($league->name, 0, 1) }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="pt-14 pb-8 px-6 text-center space-y-2">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold uppercase tracking-wider rounded-full mb-2">
                            {{ $league->season ?? date('Y') }} Season
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 line-clamp-1 group-hover:text-indigo-600 transition-colors">
                            {{ $league->name }}
                        </h3>
                        <p class="text-sm text-slate-500">
                            {{ $league->leagueTeams->count() }} Teams Registered
                        </p>
                    </div>

                    <!-- Action Footer -->
                    <div class="border-t border-slate-100 px-6 py-4 bg-slate-50/50 flex items-center justify-between group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                        <span class="text-sm font-semibold text-slate-600 group-hover:text-white">View Collection</span>
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-white transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Teams Grid (Per League) -->
        @foreach($leagues as $league)
            <div x-show="activeLeagueId === {{ $league->id }}" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                @foreach($league->leagueTeams as $leagueTeam)
                    <a href="{{ route('posters.show', [$league, $leagueTeam]) }}" 
                       class="group bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-xl hover:border-indigo-200 transition-all duration-300 flex flex-col">
                        
                        <div class="p-6 flex flex-col items-center text-center flex-grow space-y-4">
                            <!-- Team Logo -->
                            <div class="w-20 h-20 rounded-full bg-slate-50 p-2 shadow-inner ring-1 ring-slate-100 group-hover:scale-110 transition-transform duration-300">
                                @if($leagueTeam->team->logo)
                                    <img src="{{ Storage::url($leagueTeam->team->logo) }}" class="w-full h-full object-contain">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-full font-bold text-xl">
                                        {{ substr($leagueTeam->team->name, 0, 2) }}
                                    </div>
                                @endif
                            </div>

                            <div>
                                <h4 class="font-bold text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-1">
                                    {{ $leagueTeam->team->name }}
                                </h4>
                                <p class="text-xs text-slate-500 mt-1 uppercase tracking-wide">
                                    {{ $leagueTeam->players->count() }} Players
                                </p>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-4 py-3 border-t border-slate-100 flex items-center justify-center gap-2 text-indigo-600 font-semibold text-sm group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Create Poster
                        </div>
                    </a>
                @endforeach
            </div>
        @endforeach

        @if($leagues->isEmpty())
             <div class="text-center py-20 bg-white rounded-3xl shadow-sm border border-slate-200 opacity-90">
                <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <h3 class="text-lg font-medium text-slate-900">No Leagues Found</h3>
                <p class="text-slate-500">Active leagues with teams will appear here.</p>
             </div>
        @endif

    </div>
</div>
@endsection
