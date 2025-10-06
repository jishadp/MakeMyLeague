@extends('layouts.app')

@section('title', 'My Leagues - ' . config('app.name'))

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-indigo-600 to-purple-600 py-4 px-4 sm:px-6 lg:px-8 text-white animate-fadeIn">
    <div class="max-w-7xl mx-auto">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-4 drop-shadow">
                My Leagues
            </h1>
            <p class="text-lg sm:text-xl text-white-100">
                Your league participation and management
            </p>
        </div>
    </div>
</section>

<!-- Leagues Content -->
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        @if($organizedLeagues->isNotEmpty())
        <div class="mb-12">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Organized by Me
                </h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($organizedLeagues as $league)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                    
                    <!-- Hero Image Section -->
                    <div class="relative h-48 overflow-hidden">
                        @if($league->banner)
                            <img src="{{ Storage::url($league->banner) }}" alt="{{ $league->name }} Banner" 
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 flex items-center justify-center">
                                <div class="text-center text-white">
                                    @if($league->logo)
                                        <img src="{{ Storage::url($league->logo) }}" alt="{{ $league->name }} Logo" 
                                             class="w-16 h-16 rounded-full object-cover border-4 border-white/30 mx-auto mb-3">
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3">
                                            <span class="text-white font-bold text-2xl">{{ substr($league->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <h3 class="text-2xl font-bold drop-shadow-lg">{{ $league->name }}</h3>
                                    <p class="text-sm opacity-90 drop-shadow">{{ $league->game->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Organizer Status Badge -->
                        @php
                            $organizerStatus = $league->organizers->where('id', auth()->id())->first()->pivot->status ?? 'pending';
                        @endphp
                        <div class="absolute top-4 left-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium shadow-lg
                                @if($organizerStatus === 'approved') bg-green-500 text-white
                                @elseif($organizerStatus === 'pending') bg-yellow-500 text-white
                                @else bg-red-500 text-white @endif">
                                @if($organizerStatus === 'approved') {{ ucfirst($league->status) }}
                                @elseif($organizerStatus === 'pending') Pending Approval
                                @else Rejected @endif
                            </span>
                        </div>
                        
                        <!-- League Name Overlay (if banner exists) -->
                        @if($league->banner)
                            <div class="absolute bottom-4 left-4 right-4">
                                <div class="flex items-center space-x-3">
                                    @if($league->logo)
                                        <img src="{{ Storage::url($league->logo) }}" alt="{{ $league->name }} Logo" 
                                             class="w-12 h-12 rounded-full object-cover border-2 border-white/80 shadow-lg">
                                    @endif
                                    <div>
                                        <h3 class="text-xl font-bold text-white drop-shadow-lg">{{ $league->name }}</h3>
                                        <p class="text-sm text-white/90 drop-shadow">{{ $league->game->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Content Section -->
                    <div class="p-6">
                        <!-- Quick Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <div class="text-2xl font-bold text-blue-600">{{ $league->leagueTeams->count() }}</div>
                                <div class="text-xs text-gray-600">Teams</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <div class="text-2xl font-bold text-indigo-600">{{ $league->leaguePlayers->count() }}</div>
                                <div class="text-xs text-gray-600">Players</div>
                            </div>
                        </div>
                        
                        <!-- Season & Duration -->
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-medium">Season {{ $league->season }}</span>
                            </div>
                            @php
                                $startDate = \Carbon\Carbon::parse($league->start_date);
                                $now = \Carbon\Carbon::now();
                                $diff = $startDate->diff($now);
                            @endphp
                            @if($startDate->isPast())
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-green-600 font-medium">League Started</span>
                                </div>
                            @else
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Starts in {{ $diff->days }}d {{ $diff->h }}h</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Prize Pool (if available) -->
                        @if ($league->winner_prize || $league->runner_prize)
                            <div class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-xl p-3 mb-4 text-white">
                                <div class="flex items-center mb-2">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5zM8 15a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="font-bold text-sm">Prize Pool</span>
                                </div>
                                <div class="grid grid-cols-2 gap-1">
                                    @if ($league->winner_prize)
                                        <div class="bg-white/20 rounded-lg p-1 text-center">
                                            <div class="text-xs opacity-90">🥇 Winner</div>
                                            <div class="font-bold text-xs">₹{{ number_format($league->winner_prize/1000, 0) }}K</div>
                                        </div>
                                    @endif
                                    @if ($league->runner_prize)
                                        <div class="bg-white/20 rounded-lg p-1 text-center">
                                            <div class="text-xs opacity-90">🥈 Runner-up</div>
                                            <div class="font-bold text-xs">₹{{ number_format($league->runner_prize/1000, 0) }}K</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <!-- Action Button -->
                        <a href="{{ route('leagues.show', $league) }}"
                            class="w-full bg-blue-600 text-black text-center py-3 px-4 rounded-xl font-semibold hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl block">
                            Manage League
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($playingLeagues->isNotEmpty())
        <div class="mb-12">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    Playing In
                </h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($playingLeagues as $league)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                    
                    <!-- Hero Image Section -->
                    <div class="relative h-48 overflow-hidden">
                        @if($league->banner)
                            <img src="{{ Storage::url($league->banner) }}" alt="{{ $league->name }} Banner" 
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-green-600 via-emerald-600 to-teal-600 flex items-center justify-center">
                                <div class="text-center text-white">
                                    @if($league->logo)
                                        <img src="{{ Storage::url($league->logo) }}" alt="{{ $league->name }} Logo" 
                                             class="w-16 h-16 rounded-full object-cover border-4 border-white/30 mx-auto mb-3">
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3">
                                            <span class="text-white font-bold text-2xl">{{ substr($league->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <h3 class="text-2xl font-bold drop-shadow-lg">{{ $league->name }}</h3>
                                    <p class="text-sm opacity-90 drop-shadow">{{ $league->game->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Player Status Badge -->
                        <div class="absolute top-4 left-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium shadow-lg bg-green-500 text-white">
                                {{ ucfirst($league->status) }}
                            </span>
                        </div>
                        
                        <!-- League Name Overlay (if banner exists) -->
                        @if($league->banner)
                            <div class="absolute bottom-4 left-4 right-4">
                                <div class="flex items-center space-x-3">
                                    @if($league->logo)
                                        <img src="{{ Storage::url($league->logo) }}" alt="{{ $league->name }} Logo" 
                                             class="w-12 h-12 rounded-full object-cover border-2 border-white/80 shadow-lg">
                                    @endif
                                    <div>
                                        <h3 class="text-xl font-bold text-white drop-shadow-lg">{{ $league->name }}</h3>
                                        <p class="text-sm text-white/90 drop-shadow">{{ $league->game->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Content Section -->
                    <div class="p-6">
                        <!-- Quick Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <div class="text-2xl font-bold text-green-600">{{ $league->leagueTeams->count() }}</div>
                                <div class="text-xs text-gray-600">Teams</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <div class="text-2xl font-bold text-emerald-600">{{ $league->leaguePlayers->count() }}</div>
                                <div class="text-xs text-gray-600">Players</div>
                            </div>
                        </div>
                        
                        <!-- My Team Info -->
                        @php
                            $userPlayer = auth()->user()->leaguePlayers()->where('league_id', $league->id)->first();
                        @endphp
                        @if($userPlayer && $userPlayer->leagueTeam)
                            <div class="bg-green-50 border border-green-200 rounded-xl p-3 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-green-800">My Team</span>
                                </div>
                                <div class="text-lg font-bold text-green-900 mt-1">{{ $userPlayer->leagueTeam->team->name ?? 'N/A' }}</div>
                            </div>
                        @endif
                        
                        <!-- Season Info -->
                        <div class="flex items-center text-sm text-gray-600 mb-4">
                            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">Season {{ $league->season }}</span>
                        </div>
                        
                        <!-- Prize Pool (if available) -->
                        @if ($league->winner_prize || $league->runner_prize)
                            <div class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-xl p-3 mb-4 text-white">
                                <div class="flex items-center mb-2">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5zM8 15a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="font-bold text-sm">Prize Pool</span>
                                </div>
                                <div class="grid grid-cols-2 gap-1">
                                    @if ($league->winner_prize)
                                        <div class="bg-white/20 rounded-lg p-1 text-center">
                                            <div class="text-xs opacity-90">🥇 Winner</div>
                                            <div class="font-bold text-xs">₹{{ number_format($league->winner_prize/1000, 0) }}K</div>
                                        </div>
                                    @endif
                                    @if ($league->runner_prize)
                                        <div class="bg-white/20 rounded-lg p-1 text-center">
                                            <div class="text-xs opacity-90">🥈 Runner-up</div>
                                            <div class="font-bold text-xs">₹{{ number_format($league->runner_prize/1000, 0) }}K</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <!-- Action Button -->
                        <a href="{{ route('leagues.show', $league) }}"
                            class="w-full bg-green-600 text-black text-center py-3 px-4 rounded-xl font-semibold hover:bg-green-700 transition-colors shadow-lg hover:shadow-xl block">
                            View League
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($requestedLeagues->isNotEmpty())
        <div class="mb-12">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Registration Requested
                </h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($requestedLeagues as $league)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                    
                    <!-- Hero Image Section -->
                    <div class="relative h-48 overflow-hidden">
                        @if($league->banner)
                            <img src="{{ Storage::url($league->banner) }}" alt="{{ $league->name }} Banner" 
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-yellow-600 via-orange-600 to-red-600 flex items-center justify-center">
                                <div class="text-center text-white">
                                    @if($league->logo)
                                        <img src="{{ Storage::url($league->logo) }}" alt="{{ $league->name }} Logo" 
                                             class="w-16 h-16 rounded-full object-cover border-4 border-white/30 mx-auto mb-3">
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3">
                                            <span class="text-white font-bold text-2xl">{{ substr($league->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <h3 class="text-2xl font-bold drop-shadow-lg">{{ $league->name }}</h3>
                                    <p class="text-sm opacity-90 drop-shadow">{{ $league->game->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Pending Status Badge -->
                        <div class="absolute top-4 left-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium shadow-lg bg-yellow-500 text-white">
                                Pending Approval
                            </span>
                        </div>
                        
                        <!-- League Name Overlay (if banner exists) -->
                        @if($league->banner)
                            <div class="absolute bottom-4 left-4 right-4">
                                <div class="flex items-center space-x-3">
                                    @if($league->logo)
                                        <img src="{{ Storage::url($league->logo) }}" alt="{{ $league->name }} Logo" 
                                             class="w-12 h-12 rounded-full object-cover border-2 border-white/80 shadow-lg">
                                    @endif
                                    <div>
                                        <h3 class="text-xl font-bold text-white drop-shadow-lg">{{ $league->name }}</h3>
                                        <p class="text-sm text-white/90 drop-shadow">{{ $league->game->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Content Section -->
                    <div class="p-6">
                        <!-- Quick Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <div class="text-2xl font-bold text-yellow-600">{{ $league->leagueTeams->count() }}</div>
                                <div class="text-xs text-gray-600">Teams</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <div class="text-2xl font-bold text-orange-600">{{ $league->leaguePlayers->count() }}</div>
                                <div class="text-xs text-gray-600">Players</div>
                            </div>
                        </div>
                        
                        <!-- Status Info -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 mb-4">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm font-medium text-yellow-800">Awaiting Approval</span>
                            </div>
                            <div class="text-sm text-yellow-700 mt-1">Your registration is being reviewed by the organizer</div>
                        </div>
                        
                        <!-- Season Info -->
                        <div class="flex items-center text-sm text-gray-600 mb-4">
                            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">Season {{ $league->season }}</span>
                        </div>
                        
                        <!-- Prize Pool (if available) -->
                        @if ($league->winner_prize || $league->runner_prize)
                            <div class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-xl p-3 mb-4 text-white">
                                <div class="flex items-center mb-2">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5zM8 15a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="font-bold text-sm">Prize Pool</span>
                                </div>
                                <div class="grid grid-cols-2 gap-1">
                                    @if ($league->winner_prize)
                                        <div class="bg-white/20 rounded-lg p-1 text-center">
                                            <div class="text-xs opacity-90">🥇 Winner</div>
                                            <div class="font-bold text-xs">₹{{ number_format($league->winner_prize/1000, 0) }}K</div>
                                        </div>
                                    @endif
                                    @if ($league->runner_prize)
                                        <div class="bg-white/20 rounded-lg p-1 text-center">
                                            <div class="text-xs opacity-90">🥈 Runner-up</div>
                                            <div class="font-bold text-xs">₹{{ number_format($league->runner_prize/1000, 0) }}K</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <!-- Action Button -->
                        <a href="{{ route('leagues.show', $league) }}"
                            class="w-full bg-yellow-600 text-black text-center py-3 px-4 rounded-xl font-semibold hover:bg-yellow-700 transition-colors shadow-lg hover:shadow-xl block">
                            View League
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($teamOwnerLeagues->isNotEmpty())
        <div class="mb-12">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                    </svg>
                    Team Owner
                </h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($teamOwnerLeagues as $league)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                    
                    <!-- Hero Image Section -->
                    <div class="relative h-48 overflow-hidden">
                        @if($league->banner)
                            <img src="{{ Storage::url($league->banner) }}" alt="{{ $league->name }} Banner" 
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-purple-600 via-pink-600 to-red-600 flex items-center justify-center">
                                <div class="text-center text-white">
                                    @if($league->logo)
                                        <img src="{{ Storage::url($league->logo) }}" alt="{{ $league->name }} Logo" 
                                             class="w-16 h-16 rounded-full object-cover border-4 border-white/30 mx-auto mb-3">
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3">
                                            <span class="text-white font-bold text-2xl">{{ substr($league->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <h3 class="text-2xl font-bold drop-shadow-lg">{{ $league->name }}</h3>
                                    <p class="text-sm opacity-90 drop-shadow">{{ $league->game->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Team Owner Status Badge -->
                        <div class="absolute top-4 left-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium shadow-lg bg-purple-500 text-white">
                                {{ ucfirst($league->status) }}
                            </span>
                        </div>
                        
                        <!-- League Name Overlay (if banner exists) -->
                        @if($league->banner)
                            <div class="absolute bottom-4 left-4 right-4">
                                <div class="flex items-center space-x-3">
                                    @if($league->logo)
                                        <img src="{{ Storage::url($league->logo) }}" alt="{{ $league->name }} Logo" 
                                             class="w-12 h-12 rounded-full object-cover border-2 border-white/80 shadow-lg">
                                    @endif
                                    <div>
                                        <h3 class="text-xl font-bold text-white drop-shadow-lg">{{ $league->name }}</h3>
                                        <p class="text-sm text-white/90 drop-shadow">{{ $league->game->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Content Section -->
                    <div class="p-6">
                        <!-- Quick Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <div class="text-2xl font-bold text-purple-600">{{ $league->leagueTeams->count() }}</div>
                                <div class="text-xs text-gray-600">Teams</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <div class="text-2xl font-bold text-pink-600">{{ $league->leaguePlayers->count() }}</div>
                                <div class="text-xs text-gray-600">Players</div>
                            </div>
                        </div>
                        
                        <!-- My Teams Info -->
                        @php
                            $userTeams = $league->leagueTeams->filter(function($lt) {
                                return $lt->team && $lt->team->owner_id == auth()->id();
                            });
                        @endphp
                        @if($userTeams->isNotEmpty())
                            <div class="bg-purple-50 border border-purple-200 rounded-xl p-3 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-purple-800">My Teams</span>
                                </div>
                                <div class="text-sm font-bold text-purple-900 mt-1">{{ $userTeams->pluck('team.name')->join(', ') }}</div>
                            </div>
                        @endif
                        
                        <!-- Season Info -->
                        <div class="flex items-center text-sm text-gray-600 mb-4">
                            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">Season {{ $league->season }}</span>
                        </div>
                        
                        <!-- Prize Pool (if available) -->
                        @if ($league->winner_prize || $league->runner_prize)
                            <div class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-xl p-3 mb-4 text-white">
                                <div class="flex items-center mb-2">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5zM8 15a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="font-bold text-sm">Prize Pool</span>
                                </div>
                                <div class="grid grid-cols-2 gap-1">
                                    @if ($league->winner_prize)
                                        <div class="bg-white/20 rounded-lg p-1 text-center">
                                            <div class="text-xs opacity-90">🥇 Winner</div>
                                            <div class="font-bold text-xs">₹{{ number_format($league->winner_prize/1000, 0) }}K</div>
                                        </div>
                                    @endif
                                    @if ($league->runner_prize)
                                        <div class="bg-white/20 rounded-lg p-1 text-center">
                                            <div class="text-xs opacity-90">🥈 Runner-up</div>
                                            <div class="font-bold text-xs">₹{{ number_format($league->runner_prize/1000, 0) }}K</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <!-- Action Button -->
                        <a href="{{ route('leagues.show', $league) }}"
                            class="w-full bg-purple-600 text-black text-center py-3 px-4 rounded-xl font-semibold hover:bg-purple-700 transition-colors shadow-lg hover:shadow-xl block">
                            Manage Teams
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($organizedLeagues->isEmpty() && $playingLeagues->isEmpty() && $requestedLeagues->isEmpty() && $teamOwnerLeagues->isEmpty())
        <div class="bg-white rounded-xl shadow-lg p-12 text-center animate-fadeInUp">
            <div class="mb-4">
                <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Leagues Yet</h3>
            <p class="text-gray-600 mb-6">You haven't joined or created any leagues yet.</p>
            <a href="{{ route('leagues.index') }}" class="inline-block bg-blue-700 hover:bg-blue-800 text-white py-2 px-6 rounded-lg font-medium active:scale-95 transition-all shadow-md hover:shadow-lg">
                Browse Available Leagues
            </a>
        </div>
        @endif

        <!-- Create New League Card -->
        <div class="mt-12">
            <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl shadow-xl overflow-hidden animate-fadeInUp">
                <div class="p-8 text-center text-white">
                    <div class="mb-6">
                        <div class="w-20 h-20 mx-auto bg-white/20 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold mb-2">Create New League</h2>
                        <p class="text-white/90 text-lg mb-6">Start organizing your own cricket league and bring teams together</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm">
                            <div class="w-12 h-12 mx-auto bg-white/20 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-white mb-2">Easy Setup</h3>
                            <p class="text-white/80 text-sm">Quick and simple league creation process</p>
                        </div>
                        
                        <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm">
                            <div class="w-12 h-12 mx-auto bg-white/20 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-white mb-2">Full Control</h3>
                            <p class="text-white/80 text-sm">Manage teams, players, and fixtures</p>
                        </div>
                        
                        <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm">
                            <div class="w-12 h-12 mx-auto bg-white/20 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-white mb-2">Professional</h3>
                            <p class="text-white/80 text-sm">Organize tournaments like a pro</p>
                        </div>
                    </div>
                    
                    <a href="{{ route('leagues.create') }}" 
                       class="inline-flex items-center justify-center px-8 py-4 bg-white text-indigo-600 font-bold rounded-xl hover:bg-gray-50 transition-all duration-200 shadow-lg hover:shadow-xl active:scale-95">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create New League As Organizer
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fadeIn { animation: fadeIn 0.5s ease-in-out; }
.animate-fadeInUp { animation: fadeInUp 0.4s ease-in-out; }
</style>
@endsection