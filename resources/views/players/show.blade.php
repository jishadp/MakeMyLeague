@extends('layouts.app')

@section('title', $player->name . ' | ' . config('app.name'))

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-indigo-600 to-purple-600 py-8 px-4 sm:px-6 lg:px-8 text-white animate-fadeIn">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('players.index') }}" class="inline-flex items-center text-sm font-medium text-white/80 hover:text-white">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                Players
                            </a>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-white/60" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-1 text-sm font-medium text-white md:ml-2">{{ $player->name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-4 drop-shadow">
                    {{ $player->name }}
                </h1>
                <p class="text-lg sm:text-xl text-white/90">
                    {{ $player->position->name ?? 'Cricket Player' }}
                </p>
            </div>
            
            @auth
                @if(auth()->id() === $player->id || auth()->user()->isOrganizer())
                <div class="flex space-x-2">
                    <a href="{{ route('players.edit', $player) }}" class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl backdrop-blur-sm border border-white/20">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Profile
                    </a>
                    
                    @if(auth()->user()->isOrganizer())
                        <form action="{{ route('players.destroy', $player) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this player? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-white rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl backdrop-blur-sm border border-red-500/20">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete
                            </button>
                        </form>
                    @endif
                </div>
                @endif
            @endauth
        </div>
    </div>
</section>

<!-- Content Section -->
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        
        <!-- Player Profile Card -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8 animate-fadeInUp">
            <!-- Photo Section -->
            <div class="relative h-64 overflow-hidden">
                @if($player->photo)
                    <img src="{{ asset($player->photo) }}" 
                         alt="{{ $player->name }}" 
                         class="w-full h-full object-cover"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                @endif
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 flex items-center justify-center {{ $player->photo ? 'hidden' : '' }}">
                    <div class="text-center text-white">
                        <div class="w-32 h-32 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-4">
                            <span class="text-white font-bold text-5xl">{{ strtoupper(substr($player->name, 0, 1)) }}</span>
                        </div>
                        <h2 class="text-3xl font-bold drop-shadow-lg">{{ $player->name }}</h2>
                        <p class="text-lg text-white/90 drop-shadow">{{ $player->position->name ?? 'Cricket Player' }}</p>
                    </div>
                </div>
                
                <!-- Position Badge -->
                <div class="absolute top-6 left-6">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white/90 text-indigo-700 shadow-lg">
                        {{ $player->position->name ?? 'Player' }}
                    </span>
                </div>
            </div>
            
            <!-- Player Details Below Photo -->
            <div class="p-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Basic Information -->
                    <div class="lg:col-span-2">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                            Player Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">Full Name</h4>
                                    <p class="text-lg text-gray-900 font-medium">{{ $player->name }}</p>
                                </div>
                                
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">Playing Role</h4>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                        {{ $player->position->name ?? 'N/A' }}
                                    </span>
                                </div>
                                
                                @if($player->email)
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Email</h4>
                                        <p class="text-lg text-gray-900">{{ $player->email }}</p>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="space-y-4">
                                @if($player->localBody)
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Location</h4>
                                        <p class="text-lg text-gray-900">{{ $player->localBody->name }}</p>
                                    </div>
                                @endif
                                
                                @if($player->mobile)
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Mobile Number</h4>
                                        <p class="text-lg text-gray-900">{{ $player->mobile }}</p>
                                    </div>
                                @endif
                                
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">Member Since</h4>
                                    <p class="text-lg text-gray-900">{{ $player->created_at->format('F Y') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Role Description -->
                        @if($player->position)
                            <div class="mt-8 p-6 bg-gray-50 rounded-xl">
                                <h4 class="text-lg font-semibold text-gray-900 mb-3">About {{ $player->position->name }}</h4>
                                <p class="text-gray-600 leading-relaxed">
                                    @switch($player->position->name)
                                        @case('Batter')
                                            Specializes in scoring runs and building partnerships. Key responsibility is to face deliveries and accumulate runs for the team.
                                            @break
                                        @case('Bowler')
                                            Responsible for delivering the ball to dismiss batters and restrict scoring. Focuses on taking wickets and maintaining pressure.
                                            @break
                                        @case('All-Rounder')
                                            A versatile player who contributes significantly with both bat and ball. Provides balance to the team composition.
                                            @break
                                        @case('Wicket-Keeper Batter')
                                            Combines wicket-keeping duties with batting responsibilities. Essential for team's defensive setup and batting order.
                                            @break
                                        @default
                                            A cricket player contributing to team success through their specialized skills and role.
                                    @endswitch
                                </p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Quick Stats -->
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Quick Stats
                        </h3>
                        
                        <div class="space-y-4">
                            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 rounded-xl">
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-indigo-600">{{ $player->leaguePlayers->count() }}</div>
                                    <div class="text-sm text-gray-600 mt-1">Total Leagues</div>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-r from-green-50 to-blue-50 p-6 rounded-xl">
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-green-600">{{ $leagueTeams->count() }}</div>
                                    <div class="text-sm text-gray-600 mt-1">Teams Joined</div>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-6 rounded-xl">
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-purple-600">{{ $recentAuctions->count() }}</div>
                                    <div class="text-sm text-gray-600 mt-1">Recent Auctions</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Auction Highlights -->
        @if($recentAuctions->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 animate-fadeInUp">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Recent Auction Highlights
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($recentAuctions as $auction)
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-6 rounded-xl border border-yellow-200">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-semibold text-gray-900">{{ $auction->leaguePlayer->league->name ?? 'Unknown League' }}</h4>
                            <span class="text-xs text-gray-500">{{ $auction->created_at->format('M d, Y') }}</span>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Team:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $auction->leaguePlayer->leagueTeam->name ?? 'Unsold' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Bid Amount:</span>
                                <span class="text-sm font-bold text-green-600">₹{{ number_format($auction->amount) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Status:</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $auction->leaguePlayer->status === 'sold' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($auction->leaguePlayer->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- League Team Highlights -->
        @if($leagueTeams->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 animate-fadeInUp">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z" clip-rule="evenodd"/>
                </svg>
                League Team Highlights
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($leagueTeams as $leaguePlayer)
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-200">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-semibold text-gray-900">{{ $leaguePlayer->league->name ?? 'Unknown League' }}</h4>
                            <span class="text-xs text-gray-500">{{ $leaguePlayer->created_at->format('M Y') }}</span>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Team:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $leaguePlayer->leagueTeam->name ?? 'No Team' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Status:</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $leaguePlayer->status === 'sold' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($leaguePlayer->status) }}
                                </span>
                            </div>
                            @if($leaguePlayer->bid_price)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Bid Price:</span>
                                    <span class="text-sm font-bold text-green-600">₹{{ number_format($leaguePlayer->bid_price) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Back Button -->
        <div class="flex justify-center">
            <a href="{{ route('players.index') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Players
            </a>
        </div>
    </div>
</section>

<!-- Animations -->
<style>
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fadeIn { animation: fadeIn 0.5s ease-in-out; }
.animate-fadeInUp { animation: fadeInUp 0.4s ease-in-out; }
</style>
@endsection