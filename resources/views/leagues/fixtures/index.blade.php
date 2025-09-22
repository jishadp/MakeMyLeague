@extends('layouts.app')

@section('title', 'Fixtures - ' . $league->name)

@section('content')
<div class="py-4 sm:py-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-4 sm:p-6 lg:p-10">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start mb-8 gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Tournament Fixtures</h1>
                        <p class="text-gray-600">{{ $league->name }} - Season {{ $league->season }}</p>
                    </div>
                    <div class="w-full sm:w-auto flex gap-2">
                        <a href="{{ route('leagues.fixtures.pdf', $league) }}" 
                           class="inline-flex items-center justify-center px-4 py-2.5 bg-red-600 text-white font-medium rounded-lg shadow-sm hover:bg-red-700 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download PDF
                        </a>
                        <a href="{{ route('leagues.show', $league) }}" 
                           class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-gray-100 text-gray-800 font-medium rounded-lg shadow-sm hover:bg-gray-200 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to League
                        </a>
                    </div>
                </div>

                @if($fixtures->count() > 0)
                    <!-- Fixtures by Group -->
                    @foreach($fixtures as $groupName => $groupFixtures)
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">{{ $groupName }}</h2>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $groupFixtures->count() }} {{ Str::plural('Match', $groupFixtures->count()) }}
                                </span>
                            </div>

                            <div class="grid gap-4">
                                @foreach($groupFixtures as $fixture)
                                    <div class="bg-gray-50 rounded-lg border border-gray-200 hover:shadow-md transition-shadow relative">
                                        
                                        <!-- Mobile Layout: Vertical Card-Based Design -->
                                        <div class="p-4 sm:hidden">
                                            <div class="absolute top-3 right-3">
                                                 <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                    {{ $fixture->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                       ($fixture->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 
                                                        ($fixture->status === 'scheduled' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $fixture->status)) }}
                                                </span>
                                            </div>
                                            <div class="flex flex-col gap-3">
                                                <!-- Home Team Card -->
                                                <div class="bg-white p-3 border border-gray-200 rounded-lg flex justify-between items-center">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                           <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                                        </div>
                                                        <span class="font-semibold text-gray-900">{{ $fixture->homeTeam->team->name }}</span>
                                                    </div>
                                                    @if($fixture->home_score !== null)
                                                        <span class="text-xl font-bold text-gray-900">{{ $fixture->home_score }}</span>
                                                    @endif
                                                </div>

                                                <!-- VS Divider -->
                                                <div class="text-center">
                                                    <span class="text-xs font-bold text-gray-500">VS</span>
                                                     @if($fixture->match_date)
                                                        <p class="text-xs text-gray-500">{{ $fixture->match_date->format('M d, H:i') }}</p>
                                                    @endif
                                                </div>

                                                <!-- Away Team Card -->
                                                <div class="bg-white p-3 border border-gray-200 rounded-lg flex justify-between items-center">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                                        </div>
                                                        <span class="font-semibold text-gray-900">{{ $fixture->awayTeam->team->name }}</span>
                                                    </div>
                                                    @if($fixture->away_score !== null)
                                                         <span class="text-xl font-bold text-gray-900">{{ $fixture->away_score }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Desktop Layout: Horizontal Design -->
                                        <div class="hidden sm:flex items-center justify-between p-4">
                                            <!-- Teams -->
                                            <div class="flex items-center space-x-4 flex-1">
                                                <!-- Home Team -->
                                                <div class="flex items-center space-x-3 flex-1">
                                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                                    </div>
                                                    <div class="text-right flex-1">
                                                        <h3 class="font-semibold text-gray-900">{{ $fixture->homeTeam->team->name }}</h3>
                                                        <p class="text-sm text-gray-600">Home</p>
                                                    </div>
                                                </div>

                                                <!-- Score / VS -->
                                                <div class="px-4 text-center">
                                                    @if($fixture->home_score !== null || $fixture->away_score !== null)
                                                        <div class="text-2xl font-bold text-gray-900">
                                                            {{ $fixture->home_score ?? 0 }} - {{ $fixture->away_score ?? 0 }}
                                                        </div>
                                                    @else
                                                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mx-auto">
                                                            <span class="text-xs font-bold text-gray-600">VS</span>
                                                        </div>
                                                    @endif
                                                    @if($fixture->match_date)
                                                        <p class="text-xs text-gray-500 mt-1">{{ $fixture->match_date->format('M d') }}</p>
                                                    @else
                                                        <p class="text-xs text-gray-500 mt-1">TBD</p>
                                                    @endif
                                                </div>

                                                <!-- Away Team -->
                                                <div class="flex items-center space-x-3 flex-1">
                                                    <div class="text-left flex-1">
                                                        <h3 class="font-semibold text-gray-900">{{ $fixture->awayTeam->team->name }}</h3>
                                                        <p class="text-sm text-gray-600">Away</p>
                                                    </div>
                                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Match Status -->
                                            <div class="ml-6">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                                    {{ $fixture->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                       ($fixture->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 
                                                        ($fixture->status === 'scheduled' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $fixture->status)) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- No Fixtures -->
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Fixtures Generated</h3>
                        <p class="text-gray-600 mb-6">Tournament fixtures haven't been created yet.</p>
                        @if(auth()->user()->isOrganizer() && $league->user_id === auth()->id())
                            <a href="{{ route('leagues.tournament-setup', $league) }}" 
                               class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Setup Tournament
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection