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
        <!-- Notifications Section -->
        @php
            $unreadNotifications = auth()->user()->unreadNotifications()->whereIn('type', ['auctioneer_assigned', 'auctioneer_removed'])->get();
        @endphp
        @if($unreadNotifications->isNotEmpty())
            <div class="mb-8 bg-blue-50 border border-blue-200 rounded-xl p-4">
                <div class="flex items-center mb-3">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5zM8 15a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-blue-900">Auctioneer Notifications</h3>
                </div>
                <div class="space-y-2">
                    @foreach($unreadNotifications as $notification)
                        <div class="bg-white rounded-lg p-3 border border-blue-100">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ $notification->title }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                                <button onclick="markNotificationAsRead({{ $notification->id }})" 
                                        class="text-blue-600 hover:text-blue-800 text-sm">
                                    Mark as read
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
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
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                        </svg>
                                        <span class="text-sm font-medium text-purple-800">My Teams</span>
                                    </div>
                                </div>
                                
                                @foreach($userTeams as $leagueTeam)
                                    <div class="bg-white rounded-lg p-3 mb-2 last:mb-0 border border-purple-100">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="font-semibold text-purple-900">{{ $leagueTeam->team->name }}</div>
                                                @if($leagueTeam->auctioneer)
                                                    <div class="text-xs text-green-600 mt-1">
                                                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Auctioneer: {{ $leagueTeam->auctioneer->name }}
                                                    </div>
                                                @else
                                                    <div class="text-xs text-orange-600 mt-1">
                                                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                        </svg>
                                                        No auctioneer assigned
                                                    </div>
                                                @endif
                                                
                                                <!-- Default Team Status -->
                                                @if(auth()->user()->default_team_id == $leagueTeam->team_id)
                                                    <div class="text-xs text-blue-600 mt-1">
                                                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                                        </svg>
                                                        Default Team for Bidding
                                                        @if($leagueTeam->auctioneer)
                                                            - {{ $leagueTeam->auctioneer->name }}
                                                        @else
                                                            - {{ $leagueTeam->team->owner->name }}
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex space-x-2">
                                                @if($leagueTeam->auctioneer)
                                                    <button onclick="removeAuctioneer('{{ $league->slug }}', '{{ $leagueTeam->slug }}')" 
                                                            class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200 transition-colors">
                                                        Remove
                                                    </button>
                                                @endif
                                                <button onclick="assignAuctioneer('{{ $league->slug }}', '{{ $leagueTeam->slug }}', '{{ $leagueTeam->team->name }}')" 
                                                        class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded hover:bg-purple-200 transition-colors">
                                                    {{ $leagueTeam->auctioneer ? 'Change' : 'Assign' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        <!-- Auction Link (only show if auction is live) -->
                        @if($league->isAuctionActive())
                            <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-4 mb-4 text-white shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-lg">Auction is Live!</h3>
                                            <p class="text-green-100 text-sm">Join the live bidding session</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('auction.index', $league) }}" 
                                       class="bg-white text-green-600 px-6 py-2 rounded-lg font-semibold hover:bg-green-50 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-5-8V7a3 3 0 116 0v1M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        Join Auction
                                    </a>
                                </div>
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
                        
                        <!-- Action Buttons -->
                        <div class="space-y-3">
                            <a href="{{ route('league-teams.manage', $league) }}"
                                class="w-full bg-purple-600 text-center py-3 px-4 rounded-xl font-semibold hover:bg-purple-700 transition-colors shadow-lg hover:shadow-xl block">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                Manage Squad
                            </a>
                            <a href="{{ route('leagues.show', $league) }}"
                                class="w-full bg-gray-600 text-center py-2 px-4 rounded-xl font-medium hover:bg-gray-700 transition-colors shadow-lg hover:shadow-xl block">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                View League Details
                            </a>
                        </div>
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

<!-- Auctioneer Assignment Modal -->
<div id="auctioneerModal" class="fixed inset-0 bg-white bg-opacity-90 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center p-4">
    <div class="white-glass-card relative w-full max-w-md mx-auto p-8 animate-fadeInUp">
        <!-- Close Button -->
        <button onclick="closeAuctioneerModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mb-4 shadow-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2" id="modalTitle">Assign Auctioneer</h3>
            <p class="text-sm text-gray-600" id="teamInfo"></p>
        </div>
        
        <!-- Search Section -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-3">Search Users</label>
            <div class="relative">
                <input type="text" id="userSearch" placeholder="Type to search users..." 
                       class="white-glass-input w-full px-4 py-3 rounded-xl border-0 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200 text-gray-800 placeholder-gray-500">
                <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            <div id="searchResults" class="mt-3 max-h-48 overflow-y-auto white-glass-card hidden">
                <!-- Search results will be populated here -->
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex space-x-4">
            <button onclick="closeAuctioneerModal()" 
                    class="flex-1 white-glass-button px-6 py-3 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition-all duration-200">
                Cancel
            </button>
            <button id="assignButton" onclick="confirmAssignment()" 
                    class="flex-1 white-glass-button px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg" disabled>
                <span class="flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Assign
                </span>
            </button>
        </div>
    </div>
</div>

<script>
let currentLeagueSlug = '';
let currentLeagueTeamSlug = '';
let selectedUserId = null;

function assignAuctioneer(leagueSlug, leagueTeamSlug, teamName) {
    currentLeagueSlug = leagueSlug;
    currentLeagueTeamSlug = leagueTeamSlug;
    
    document.getElementById('modalTitle').textContent = 'Assign Auctioneer';
    document.getElementById('teamInfo').textContent = `Assign auctioneer for team: ${teamName}`;
    
    // Reset search input
    const searchInput = document.getElementById('userSearch');
    searchInput.value = '';
    searchInput.classList.remove('bg-blue-50', 'border-blue-300');
    
    document.getElementById('searchResults').classList.add('hidden');
    document.getElementById('assignButton').disabled = true;
    selectedUserId = null;
    
    document.getElementById('auctioneerModal').classList.remove('hidden');
}

function removeAuctioneer(leagueSlug, leagueTeamSlug) {
    if (!confirm('Are you sure you want to remove the auctioneer for this team?')) {
        return;
    }
    
    fetch(`/leagues/${leagueSlug}/teams/${leagueTeamSlug}/auctioneer/remove`, {
        method: 'DELETE',
        credentials: 'same-origin',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while removing the auctioneer.');
    });
}

function closeAuctioneerModal() {
    document.getElementById('auctioneerModal').classList.add('hidden');
}

function confirmAssignment() {
    if (!selectedUserId) {
        alert('Please select a user first.');
        return;
    }
    
    fetch(`/leagues/${currentLeagueSlug}/teams/${currentLeagueTeamSlug}/auctioneer/assign`, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            auctioneer_id: selectedUserId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeAuctioneerModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while assigning the auctioneer.');
    });
}

// Search functionality
document.getElementById('userSearch').addEventListener('input', function(e) {
    const query = e.target.value.trim();
    
    if (query.length < 2) {
        document.getElementById('searchResults').classList.add('hidden');
        return;
    }
    
    fetch(`/leagues/${currentLeagueSlug}/auctioneers/search?query=${encodeURIComponent(query)}`, {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displaySearchResults(data.users);
            } else {
                console.error('Search error:', data.message);
            }
        })
        .catch(error => {
            console.error('Search error:', error);
        });
});

function displaySearchResults(users) {
    const resultsContainer = document.getElementById('searchResults');
    
    if (users.length === 0) {
        resultsContainer.innerHTML = `
            <div class="p-4 text-center">
                <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <div class="text-gray-500 text-sm">No users found</div>
            </div>
        `;
    } else {
        resultsContainer.innerHTML = users.map(user => `
            <div class="p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-all duration-200 rounded-lg m-2" 
                 onclick="selectUser(${user.id}, '${user.name}', '${user.email}')">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                        ${user.name.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-800">${user.name}</div>
                        <div class="text-sm text-gray-600">${user.email}</div>
                        ${user.phone ? `<div class="text-xs text-gray-500">${user.phone}</div>` : ''}
                    </div>
                    <div class="text-blue-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            </div>
        `).join('');
    }
    
    resultsContainer.classList.remove('hidden');
}

function selectUser(userId, userName, userEmail) {
    selectedUserId = userId;
    
    // Update the search input with selected user info
    const searchInput = document.getElementById('userSearch');
    searchInput.value = `${userName} (${userEmail})`;
    searchInput.classList.add('bg-blue-50', 'border-blue-300');
    
    // Hide search results
    document.getElementById('searchResults').classList.add('hidden');
    
    // Enable assign button and add visual feedback
    const assignButton = document.getElementById('assignButton');
    assignButton.disabled = false;
    assignButton.classList.add('animate-pulse');
    
    // Remove pulse animation after 2 seconds
    setTimeout(() => {
        assignButton.classList.remove('animate-pulse');
    }, 2000);
}

// Close modal when clicking outside
document.getElementById('auctioneerModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAuctioneerModal();
    }
});

function markNotificationAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the notification from the DOM
            const notificationElement = document.querySelector(`[onclick="markNotificationAsRead(${notificationId})"]`).closest('.bg-white');
            notificationElement.remove();
            
            // If no notifications left, hide the entire notifications section
            const notificationsSection = document.querySelector('.bg-blue-50');
            if (notificationsSection && notificationsSection.querySelectorAll('.bg-white').length === 0) {
                notificationsSection.remove();
            }
        } else {
            alert('Error marking notification as read.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while marking the notification as read.');
    });
}
</script>

<style>
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fadeIn { animation: fadeIn 0.5s ease-in-out; }
.animate-fadeInUp { animation: fadeInUp 0.4s ease-in-out; }

/* White Glassmorphism Styles */
.white-glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.1),
        0 4px 16px rgba(0, 0, 0, 0.05),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
    border-radius: 20px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.white-glass-card:hover {
    background: rgba(255, 255, 255, 0.98);
    box-shadow: 
        0 16px 48px rgba(0, 0, 0, 0.15),
        0 8px 24px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
    transform: translateY(-2px);
}

.white-glass-button {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    box-shadow: 
        0 4px 16px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
    transition: all 0.3s ease;
}

.white-glass-button:hover {
    background: rgba(255, 255, 255, 0.9);
    box-shadow: 
        0 6px 20px rgba(0, 0, 0, 0.15),
        inset 0 1px 0 rgba(255, 255, 255, 1);
    transform: translateY(-1px);
}

.white-glass-input {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    box-shadow: 
        0 4px 16px rgba(0, 0, 0, 0.05),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
    transition: all 0.3s ease;
}

.white-glass-input:focus {
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 
        0 6px 20px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
    transform: translateY(-1px);
}
</style>
@endsection