@extends('layouts.app')

@section('title', 'My Teams - ' . config('app.name'))

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-indigo-600 to-purple-600 py-4 px-4 sm:px-6 lg:px-8 text-white animate-fadeIn">
    <div class="max-w-7xl mx-auto">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-4 drop-shadow">
                My Teams
            </h1>
            <p class="text-lg sm:text-xl text-white-100">
                Your team ownership and participation
            </p>
        </div>
    </div>
</section>

<!-- Teams Content -->
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        @if($ownedTeams->isNotEmpty())
        <div class="mb-12">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Owned by Me
                </h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($ownedTeams as $team)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                    
                    <!-- Hero Image Section -->
                    <div class="relative h-48 overflow-hidden">
                        @if($team->banner)
                            <img src="{{ Storage::url($team->banner) }}" alt="{{ $team->name }} Banner" 
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                        @elseif($team->logo)
                            <img src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }} Logo" 
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3">
                                        <span class="text-white font-bold text-2xl">{{ substr($team->name, 0, 1) }}</span>
                                    </div>
                                    <h3 class="text-2xl font-bold drop-shadow-lg">{{ $team->name }}</h3>
                                    <p class="text-sm opacity-90 drop-shadow">Cricket Team</p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Owner Badge -->
                        <div class="absolute top-4 left-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium shadow-lg bg-blue-500 text-white">
                                Owner
                            </span>
                        </div>
                        
                        <!-- Team Name Overlay (if banner/logo exists) -->
                        @if($team->banner || $team->logo)
                            <div class="absolute bottom-4 left-4 right-4">
                                <div class="flex items-center space-x-3">
                                    @if($team->logo)
                                        <img src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }} Logo" 
                                             class="w-12 h-12 rounded-full object-cover border-2 border-white/80 shadow-lg">
                                    @endif
                                    <div>
                                        <h3 class="text-xl font-bold text-white drop-shadow-lg">{{ $team->name }}</h3>
                                        <p class="text-sm text-white/90 drop-shadow">Cricket Team</p>
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
                                <div class="text-2xl font-bold text-blue-600">{{ $team->leagueTeams->count() }}</div>
                                <div class="text-xs text-gray-600">Leagues</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <div class="text-2xl font-bold text-indigo-600">{{ $team->leagueTeams->sum(function($lt) { return $lt->leaguePlayers->where('status', 'sold')->count(); }) }}</div>
                                <div class="text-xs text-gray-600">League Players</div>
                            </div>
                        </div>
                        
                        <!-- Active Auctions for this team -->
                        @php
                            $activeAuctions = $team->leagueTeams->filter(function($lt) {
                                return $lt->league && $lt->league->isAuctionActive();
                            });
                        @endphp
                        @if($activeAuctions->isNotEmpty())
                            <div class="mb-4 space-y-2">
                                @foreach($activeAuctions as $leagueTeam)
                                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-4 text-white shadow-lg">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center mb-1">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <h3 class="font-bold text-sm">Auction Live!</h3>
                                                </div>
                                                <p class="text-green-100 text-xs">{{ $leagueTeam->league->name }}</p>
                                            </div>
                                            <a href="{{ route('auction.index', $leagueTeam->league) }}" 
                                               class="bg-white text-green-600 px-4 py-2 rounded-lg font-semibold hover:bg-green-50 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 text-sm flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-5-8V7a3 3 0 116 0v1M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                </svg>
                                                Join
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        <!-- Team Details -->
                        <div class="space-y-2 mb-4">
                            @if($team->homeGround)
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5zM8 15a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $team->homeGround->name ?? 'Not specified' }}</span>
                                </div>
                            @endif
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $team->localBody->name ?? 'Not specified' }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                <span>Created {{ $team->created_at->format('M Y') }}</span>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
<div class="grid grid-cols-3 gap-3 mt-4">
    <a href="{{ route('teams.show', $team) }}"
        class="flex items-center justify-center gap-2 bg-blue-600 text-white text-center py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-all duration-200 shadow-md hover:shadow-lg hover:-translate-y-0.5 text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
        </svg>
        <span>Manage</span>
    </a>
    
    <!-- Default Team Button -->
    @if(auth()->user()->default_team_id == $team->id)
        <button onclick="removeDefaultTeam()" 
                class="flex items-center justify-center gap-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-center py-3 px-4 rounded-lg font-semibold hover:from-green-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg hover:-translate-y-0.5 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>Default</span>
        </button>
    @else
        <button onclick="setDefaultTeam({{ $team->id }}, '{{ $team->name }}')" 
                class="flex items-center justify-center gap-2 bg-gradient-to-r from-purple-500 to-indigo-600 text-white text-center py-3 px-4 rounded-lg font-semibold hover:from-purple-600 hover:to-indigo-700 transition-all duration-200 shadow-md hover:shadow-lg hover:-translate-y-0.5 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
            </svg>
            <span>Set Default</span>
        </button>
    @endif
    
    <button onclick="transferTeam('{{ $team->slug }}', '{{ $team->name }}')" 
            class="flex items-center justify-center gap-2 bg-gradient-to-r from-orange-500 to-red-500 text-white text-center py-3 px-4 rounded-lg font-semibold hover:from-orange-600 hover:to-red-600 transition-all duration-200 shadow-md hover:shadow-lg hover:-translate-y-0.5 text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
        </svg>
        <span>Transfer</span>
    </button>
</div>  
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($playerTeams->isNotEmpty())
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
                @foreach($playerTeams as $leaguePlayer)
                @if($leaguePlayer->leagueTeam && $leaguePlayer->leagueTeam->team)
                @php $team = $leaguePlayer->leagueTeam->team; @endphp
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                    
                    <!-- Hero Image Section -->
                    <div class="relative h-48 overflow-hidden">
                        @if($team->banner)
                            <img src="{{ Storage::url($team->banner) }}" alt="{{ $team->name }} Banner" 
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                        @elseif($team->logo)
                            <img src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }} Logo" 
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-green-600 via-emerald-600 to-teal-600 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3">
                                        <span class="text-white font-bold text-2xl">{{ substr($team->name, 0, 1) }}</span>
                                    </div>
                                    <h3 class="text-2xl font-bold drop-shadow-lg">{{ $team->name }}</h3>
                                    <p class="text-sm opacity-90 drop-shadow">Cricket Team</p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Player Status Badge -->
                        <div class="absolute top-4 left-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium shadow-lg bg-green-500 text-white">
                                {{ ucfirst($leaguePlayer->status) }}
                            </span>
                        </div>
                        
                        <!-- Team Name Overlay (if banner/logo exists) -->
                        @if($team->banner || $team->logo)
                            <div class="absolute bottom-4 left-4 right-4">
                                <div class="flex items-center space-x-3">
                                    @if($team->logo)
                                        <img src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }} Logo" 
                                             class="w-12 h-12 rounded-full object-cover border-2 border-white/80 shadow-lg">
                                    @endif
                                    <div>
                                        <h3 class="text-xl font-bold text-white drop-shadow-lg">{{ $team->name }}</h3>
                                        <p class="text-sm text-white/90 drop-shadow">Cricket Team</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Content Section -->
                    <div class="p-6">
                        <!-- League Info -->
                        @if($leaguePlayer->leagueTeam && $leaguePlayer->leagueTeam->league)
                            <div class="bg-green-50 border border-green-200 rounded-xl p-3 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5zM8 15a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm font-medium text-green-800">League</span>
                                </div>
                                <div class="text-lg font-bold text-green-900 mt-1">{{ $leaguePlayer->leagueTeam->league->name }}</div>
                            </div>
                        @endif
                        
                        <!-- Team Details -->
                        <div class="space-y-2 mb-4">
                            @if($team->homeGround)
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5zM8 15a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $team->homeGround->name ?? 'Not specified' }}</span>
                                </div>
                            @endif
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $team->localBody->name ?? 'Not specified' }}</span>
                            </div>
                        </div>
                        
                        <!-- Action Button -->
                        <a href="{{ route('teams.show', $team) }}"
                            class="w-full bg-green-600 text-black text-center py-3 px-4 rounded-xl font-semibold hover:bg-green-700 transition-colors shadow-lg hover:shadow-xl block">
                            View Team
                        </a>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @endif

        @if($requestedTeams->isNotEmpty())
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
                @foreach($requestedTeams as $leaguePlayer)
                @if($leaguePlayer->leagueTeam && $leaguePlayer->leagueTeam->team)
                @php $team = $leaguePlayer->leagueTeam->team; @endphp
                <a href="{{ route('teams.show', $team) }}" class="block">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp cursor-pointer">
                        <div class="h-40 overflow-hidden relative">
                            @if($team->logo)
                                <img src="{{ asset($team->logo) }}" alt="{{ $team->name }}" class="w-full h-full object-cover">
                            @else
                                <img src="{{ asset('images/leagueteams.jpg') }}" alt="{{ $team->name }}" class="w-full h-full object-cover">
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end">
                                <h3 class="text-xl font-semibold text-white p-4">{{ $team->name }}</h3>
                            </div>
                            <span class="absolute top-3 right-3 bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full shadow-sm">
                                Pending Approval
                            </span>
                        </div>
                        <div class="p-6">
                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                @if($leaguePlayer->leagueTeam && $leaguePlayer->leagueTeam->league)
                                <p><span class="font-medium">🏆 League:</span> {{ $leaguePlayer->leagueTeam->league->name }}</p>
                                @endif
                                <p><span class="font-medium">⏳ Status:</span> <span class="text-yellow-600">Awaiting Approval</span></p>
                                <p><span class="font-medium">📍 Location:</span> {{ $team->localBody->name ?? 'Not specified' }}</p>
                            </div>
                            <div class="mt-6">
                                <span class="text-indigo-600 hover:text-indigo-800 font-medium">
                                    View Team →
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
                @endif
                @endforeach
            </div>
        </div>
        @endif

        @if($ownedTeams->isEmpty() && $playerTeams->isEmpty() && $requestedTeams->isEmpty())
        <div class="bg-white rounded-xl shadow-lg p-12 text-center animate-fadeInUp">
            <div class="mb-4">
                <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Teams Yet</h3>
            <p class="text-gray-600 mb-6">You haven't joined or created any teams yet.</p>
            <a href="{{ route('teams.index') }}" class="inline-block bg-blue-700 hover:bg-blue-800 text-white py-2 px-6 rounded-lg font-medium active:scale-95 transition-all shadow-md hover:shadow-lg">
                Browse Available Teams
            </a>
        </div>
        @endif

        <!-- Create Your Team Card -->
        <div class="mt-8">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl shadow-lg overflow-hidden animate-fadeInUp">
                <div class="p-6 text-center text-black">
                    <div class="mb-4">
                        <div class="w-16 h-16 mx-auto bg-white/20 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-black">Create Your Team</h3>
                        <p class="text-black/90 text-sm mb-4">Start your own cricket team and compete in leagues</p>
                    </div>
                    
                    <a href="{{ route('teams.create') }}" 
                       class="inline-flex items-center justify-center px-6 py-3 bg-white text-purple-600 font-semibold rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-md hover:shadow-lg active:scale-95">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Your Team
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Transfer Modal -->
<div id="teamTransferModal" class="fixed inset-0 bg-white bg-opacity-90 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center p-4">
    <div class="white-glass-card relative w-full max-w-md mx-auto p-8 animate-fadeInUp">
        <!-- Close Button -->
        <button onclick="closeTeamTransferModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto bg-gradient-to-br from-orange-500 to-red-600 rounded-full flex items-center justify-center mb-4 shadow-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2" id="transferModalTitle">Transfer Team</h3>
            <p class="text-sm text-gray-600" id="teamTransferInfo"></p>
        </div>
        
        <!-- Search Section -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-3">Search Users</label>
            <div class="relative">
                <input type="text" id="userTransferSearch" placeholder="Type to search users..." 
                       class="white-glass-input w-full px-4 py-3 rounded-xl border-0 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all duration-200 text-gray-800 placeholder-gray-500">
                <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            <div id="transferSearchResults" class="mt-3 max-h-48 overflow-y-auto white-glass-card hidden">
                <!-- Search results will be populated here -->
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex space-x-4">
            <button onclick="closeTeamTransferModal()" 
                    class="flex-1 white-glass-button px-6 py-3 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition-all duration-200">
                Cancel
            </button>
            <button id="transferButton" onclick="confirmTransfer()" 
                    class="flex-1 white-glass-button px-6 py-3 rounded-xl bg-gradient-to-r from-orange-600 to-red-600 font-semibold hover:from-orange-700 hover:to-red-700 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg" disabled>
                <span class="flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    Transfer
                </span>
            </button>
        </div>
    </div>
</div>

<script>
let currentTeamSlug = '';
let selectedTransferUserId = null;

function transferTeam(teamSlug, teamName) {
    currentTeamSlug = teamSlug;
    
    document.getElementById('transferModalTitle').textContent = 'Transfer Team';
    document.getElementById('teamTransferInfo').textContent = `Transfer ownership of team: ${teamName}`;
    
    // Reset search input
    const searchInput = document.getElementById('userTransferSearch');
    searchInput.value = '';
    searchInput.classList.remove('bg-orange-50', 'border-orange-300');
    
    document.getElementById('transferSearchResults').classList.add('hidden');
    document.getElementById('transferButton').disabled = true;
    selectedTransferUserId = null;
    
    document.getElementById('teamTransferModal').classList.remove('hidden');
}

function closeTeamTransferModal() {
    document.getElementById('teamTransferModal').classList.add('hidden');
}

function confirmTransfer() {
    if (!selectedTransferUserId) {
        alert('Please select a user first.');
        return;
    }
    
    if (!confirm('Are you sure you want to transfer this team? This action cannot be undone.')) {
        return;
    }
    
    fetch(`/teams/${currentTeamSlug}/transfer`, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            new_owner_id: selectedTransferUserId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeTeamTransferModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while transferring the team.');
    });
}

// Search functionality
document.getElementById('userTransferSearch').addEventListener('input', function(e) {
    const query = e.target.value.trim();
    
    if (query.length < 2) {
        document.getElementById('transferSearchResults').classList.add('hidden');
        return;
    }
    
    fetch(`/team-transfer/search?query=${encodeURIComponent(query)}`, {
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
                displayTransferSearchResults(data.users);
            } else {
                console.error('Search error:', data.message);
            }
        })
        .catch(error => {
            console.error('Search error:', error);
        });
});

function displayTransferSearchResults(users) {
    const resultsContainer = document.getElementById('transferSearchResults');
    
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
                 onclick="selectTransferUser(${user.id}, '${user.name}', '${user.email}')">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-red-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                        ${user.name.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-800">${user.name}</div>
                        <div class="text-sm text-gray-600">${user.email}</div>
                        <div class="text-xs text-gray-500">${user.team_count} teams owned</div>
                    </div>
                    <div class="text-orange-500">
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

function selectTransferUser(userId, userName, userEmail) {
    selectedTransferUserId = userId;
    
    // Update the search input with selected user info
    const searchInput = document.getElementById('userTransferSearch');
    searchInput.value = `${userName} (${userEmail})`;
    searchInput.classList.add('bg-orange-50', 'border-orange-300');
    
    // Hide search results
    document.getElementById('transferSearchResults').classList.add('hidden');
    
    // Enable transfer button and add visual feedback
    const transferButton = document.getElementById('transferButton');
    transferButton.disabled = false;
    transferButton.classList.add('animate-pulse');
    
    // Remove pulse animation after 2 seconds
    setTimeout(() => {
        transferButton.classList.remove('animate-pulse');
    }, 2000);
}

// Close modal when clicking outside
document.getElementById('teamTransferModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTeamTransferModal();
    }
});

// Default Team Management Functions
function setDefaultTeam(teamId, teamName) {
    if (!confirm(`Are you sure you want to set "${teamName}" as your default team for bidding?`)) {
        return;
    }
    
    fetch('/default-team/set', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            team_id: teamId
        })
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
        alert('An error occurred while setting the default team.');
    });
}

function removeDefaultTeam() {
    if (!confirm('Are you sure you want to remove your default team? You will need to manually select a team for bidding in leagues where you have multiple teams.')) {
        return;
    }
    
    fetch('/default-team/remove', {
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
        alert('An error occurred while removing the default team.');
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