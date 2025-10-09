@extends('layouts.app')

@section('title', config('app.name').' - Dashboard')

@section('content')

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-indigo-600 to-purple-600 py-8 px-4 sm:px-6 lg:px-8 text-white animate-fadeIn">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-4 drop-shadow">
                        Hello {{auth()->user()->name}}
                    </h1>
                    <p class="text-lg sm:text-xl text-white/90">
                        Organize leagues, manage teams, track player stats, and run exciting tournaments.
                    </p>
                </div>
                <div class="hidden lg:block">
                    <img src="{{ asset('images/hero.jpg') }}"
                         alt="Cricket League"
                         class="rounded-xl shadow-xl hover:scale-[1.02] transition-transform duration-300">
                </div>
            </div>
        </div>
    </section>

    <!-- My Leagues Section -->
    @if(auth()->user()->isOrganizer())
    <section class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">My Leagues</h2>
                    <p class="text-gray-600 mt-1">Manage your cricket leagues</p>
                </div>
                <a href="{{ route('leagues.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-blue-900 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl active:scale-95">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create New League
                </a>
            </div>

            @if($userLeagues->isEmpty())
                <div class="bg-white rounded-xl shadow-lg p-6 text-center animate-fadeInUp">
                    <div class="mb-4">
                        <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5
                                     a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3
                                     m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">You haven't created any leagues yet</h3>
                    <p class="text-gray-600 mb-6">Get started by creating your first cricket league.</p>
                    <a href="{{ route('leagues.create') }}"
                       class="inline-block bg-blue-700 hover:bg-blue-800 text-blue-900 py-2 px-6 rounded-lg font-medium
                              active:scale-95 transition-all shadow-md hover:shadow-lg">
                        Create Your First League
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($userLeagues as $league)
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                            
                            <!-- Hero Image Section -->
                            <div class="relative h-48 overflow-hidden">
                                @if($league->banner)
                                    <img src="{{ Storage::url($league->banner) }}" alt="{{ $league->name }} Banner" 
                                         class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 flex items-center justify-center">
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
                                            <p class="text-sm opacity-90 drop-shadow">{{ $league->game->name }}</p>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Status Badge -->
                                <div class="absolute top-4 left-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium shadow-lg
                                        {{ $league->status === 'active'
                                            ? 'bg-green-500 text-white'
                                            : ($league->status === 'pending'
                                                ? 'bg-yellow-500 text-white'
                                                : ($league->status === 'completed'
                                                    ? 'bg-blue-500 text-white'
                                                    : 'bg-red-500 text-white')) }}">
                                        {{ ucfirst($league->status) }}
                                    </span>
                                </div>
                                
                                <!-- Default Badge -->
                                @if ($league->is_default)
                                    <div class="absolute top-4 right-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/90 text-green-700 shadow-lg">
                                            ⭐ Default
                                        </span>
                                    </div>
                                @endif
                                
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
                                                <p class="text-sm text-white/90 drop-shadow">{{ $league->game->name }}</p>
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
                                        <div class="text-2xl font-bold text-indigo-600">{{ $league->leagueTeams->count() }}/{{ $league->max_teams }}</div>
                                        <div class="text-xs text-gray-600">Teams</div>
                                    </div>
                                    <div class="text-center p-3 bg-gray-50 rounded-xl">
                                        <div class="text-2xl font-bold text-purple-600">{{ $league->leaguePlayers->where('status', '!=', 'pending')->count() }}/{{ $league->max_teams * $league->max_team_players }}</div>
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
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $league->start_date->format('M d') }} - {{ $league->end_date->format('M d, Y') }}</span>
                                    </div>
                                </div>
                                
                                <!-- Venue Info -->
                                @if ($league->localBody)
                                    <div class="flex items-center text-sm text-gray-600 mb-4">
                                        <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $league->localBody->name }}, {{ $league->localBody->district->name }}</span>
                                    </div>
                                @endif
                                
                                <!-- Prize Pool (Highlighted) -->
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
                                    class="w-full bg-indigo-600 text-black text-center py-3 px-4 rounded-xl font-semibold hover:bg-indigo-700 transition-colors shadow-lg hover:shadow-xl block">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
    @endif

    <!-- Open Leagues Section -->
    @if(auth()->user()->isPlayer() || auth()->user()->gameRoles->isNotEmpty() || auth()->user()->position_id || auth()->user()->isTeamOwner())
    <section class="py-8 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Active Leagues</h2>
                <p class="text-gray-600 mt-1">Join exciting cricket tournaments</p>
            </div>

            @if($userLeagues->isEmpty())
                <div class="bg-white rounded-xl shadow-lg p-6 text-center animate-fadeInUp">
                    <div class="mb-4">
                        <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5
                                     a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3
                                     m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No approved leagues available</h3>
                    <p class="text-gray-600 mb-6">There are currently no leagues with approved organizers available for joining.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($userLeagues as $league)
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                            
                            <!-- Hero Image Section -->
                            <div class="relative h-48 overflow-hidden">
                                @if($league->banner)
                                    <img src="{{ Storage::url($league->banner) }}" alt="{{ $league->name }} Banner" 
                                         class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 flex items-center justify-center">
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
                                            <p class="text-sm opacity-90 drop-shadow">{{ $league->game->name }}</p>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Status Badge -->
                                <div class="absolute top-4 left-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium shadow-lg
                                        {{ $league->status === 'active'
                                            ? 'bg-green-500 text-white'
                                            : ($league->status === 'pending'
                                                ? 'bg-yellow-500 text-white'
                                                : ($league->status === 'completed'
                                                    ? 'bg-blue-500 text-white'
                                                    : 'bg-red-500 text-white')) }}">
                                        {{ ucfirst($league->status) }}
                                    </span>
                                </div>
                                
                                <!-- Default Badge -->
                                @if ($league->is_default)
                                    <div class="absolute top-4 right-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/90 text-green-700 shadow-lg">
                                            ⭐ Default
                                        </span>
                                    </div>
                                @endif
                                
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
                                                <p class="text-sm text-white/90 drop-shadow">{{ $league->game->name }}</p>
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
                                        <div class="text-2xl font-bold text-indigo-600">{{ $league->leagueTeams->count() }}/{{ $league->max_teams }}</div>
                                        <div class="text-xs text-gray-600">Teams</div>
                                    </div>
                                    <div class="text-center p-3 bg-gray-50 rounded-xl">
                                        <div class="text-2xl font-bold text-purple-600">{{ $league->leaguePlayers->where('status', '!=', 'pending')->count() }}/{{ $league->max_teams * $league->max_team_players }}</div>
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
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $league->start_date->format('M d') }} - {{ $league->end_date->format('M d, Y') }}</span>
                                    </div>
                                </div>
                                
                                <!-- Venue Info -->
                                @if ($league->localBody)
                                    <div class="flex items-center text-sm text-gray-600 mb-4">
                                        <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $league->localBody->name }}, {{ $league->localBody->district->name }}</span>
                                    </div>
                                @endif
                                
                                <!-- Prize Pool (Highlighted) -->
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
                                    class="w-full bg-indigo-600 text-black text-center py-3 px-4 rounded-xl font-semibold hover:bg-indigo-700 transition-colors shadow-lg hover:shadow-xl block">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination Links -->
                @if(method_exists($userLeagues, 'links'))
                    <div class="mt-8 flex justify-center">
                        {{ $userLeagues->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>
    @endif

    @if(auth()->user()->isTeamOwner())
    <!-- Owned Teams Section -->
    <section class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Owned Teams</h2>
                    <p class="text-gray-600 mt-1">Teams you own and manage</p>
                </div>
                <a href="{{ route('teams.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-blue-900 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Create New Team
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($userOwnedTeams as $team)
                <!-- Owned Team Card -->
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
                                <div class="text-2xl font-bold text-indigo-600">{{ $team->leagueTeams->sum(function($lt) { return $lt->leaguePlayers->count(); }) }}</div>
                                <div class="text-xs text-gray-600">Players</div>
                            </div>
                        </div>
                        
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
                                <span>{{ $team->localBody->name }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                <span>Created {{ $team->created_at->format('M Y') }}</span>
                            </div>
                        </div>
                        
                        <!-- Action Button -->
                        <a href="{{ route('teams.show', $team) }}"
                            class="w-full bg-blue-600 text-black text-center py-3 px-4 rounded-xl font-semibold hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl block">
                            Manage Team
                        </a>
                    </div>
                </div>
                @empty
                <!-- No Teams Message -->
                <div class="col-span-full bg-white rounded-xl shadow-md p-6 text-center">
                    <div class="mb-4">
                        <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">You don't have any teams yet</h3>
                    <p class="text-gray-600 mb-6">Create your first team to get started</p>
                    <a href="{{ route('teams.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-700 hover:bg-blue-800 text-blue-900 rounded-md shadow-md hover:shadow-lg transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Create Your First Team
                    </a>
                </div>
                @endforelse
            </div>
        </div>
    </section>
    @endif

    <!-- My League Teams Section -->
    <section class="py-8 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">My League Teams</h2>
                    <p class="text-gray-600 mt-1">Teams you're part of in leagues</p>
                </div>
                <a href="{{ route('teams.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-blue-900 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl active:scale-95">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    My Teams
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($userLeagueTeams as $leaguePlayer)
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
                                <span>Since {{ $leaguePlayer->created_at->format('M Y') }}</span>
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
                @empty
                <!-- No Teams Message -->
                <div class="col-span-full bg-white rounded-xl shadow-md p-6 text-center">
                    <div class="mb-4">
                        <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">You're not part of any league teams yet</h3>
                    <p class="text-gray-600 mb-6">Join a league to get assigned to a team</p>
                    <a href="{{ route('leagues.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-700 hover:bg-blue-800 text-blue-900 rounded-md shadow-md hover:shadow-lg transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z" />
                        </svg>
                        Browse Leagues
                    </a>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- My Player Profile Section -->
    <section class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">My Player Profile</h2>
                    <p class="text-gray-600 mt-1">Your cricket player information</p>
                </div>
                @if($playerInfo)
                    <a href="{{ route('players.edit', $playerInfo) }}"
                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-blue-900 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Edit Profile
                    </a>
                @else
                    <a href="{{ route('players.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-blue-900 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Browse Players
                    </a>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 animate-fadeInUp">
                @if($playerInfo)
                    <div class="grid grid-cols-1 lg:grid-cols-3">
                        <!-- Profile Details -->
                        <div class="p-6 lg:p-8 bg-gradient-to-br from-indigo-500 to-purple-600 text-white relative">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-32 h-32 rounded-full overflow-hidden mb-4 border-4 border-white shadow-lg">
                                    @if($playerInfo->photo)
                                        <img src="{{ asset($playerInfo->photo) }}"
                                             alt="{{ $playerInfo->name }}"
                                             class="w-full h-full object-cover"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    @else
                                        <img src="{{ asset('images/defaultplayer.jpeg') }}"
                                             alt="{{ $playerInfo->name }}"
                                             class="w-full h-full object-cover"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    @endif
                                    <div class="w-full h-full flex items-center justify-center text-white font-bold text-2xl bg-gradient-to-br from-indigo-500 to-purple-600" style="display: none;">
                                        {{ strtoupper(substr($playerInfo->name, 0, 2)) }}
                                    </div>
                                </div>
                                <h3 class="text-2xl font-bold mb-1">{{ $playerInfo->name }}</h3>
                                <p class="text-indigo-200 mb-4">{{ $playerInfo->position->name ?? 'No Position' }}</p>
                                <div class="flex space-x-3 mb-6">
                                    <span class="bg-white/20 text-white text-xs font-medium px-3 py-1 rounded-full">{{ $playerInfo->position->name ?? 'No Position' }}</span>
                                    <span class="bg-white/20 text-white text-xs font-medium px-3 py-1 rounded-full">Player</span>
                                </div>
                                <div class="w-full space-y-3 text-left">
                                    <p class="flex justify-between">
                                        <span>Email:</span>
                                        <span class="font-medium">{{ $playerInfo->email }}</span>
                                    </p>
                                    <p class="flex justify-between">
                                        <span>Mobile:</span>
                                        <span class="font-medium">{{ $playerInfo->mobile }}</span>
                                    </p>
                                    <p class="flex justify-between">
                                        <span>Location:</span>
                                        <span class="font-medium">{{ $playerInfo->localBody->name ?? 'Not specified' }}</span>
                                    </p>
                                    <p class="flex justify-between">
                                        <span>Member Since:</span>
                                        <span class="font-medium">{{ $playerInfo->created_at->format('M Y') }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="absolute bottom-0 right-0 opacity-10">
                                <img src="https://upload.wikimedia.org/wikipedia/en/8/8d/Cricket_India_Crest.svg"
                                    alt="Cricket Logo" class="w-40 h-40 object-contain">
                            </div>
                        </div>

                        <!-- Statistics & Profile Content -->
                        <div class="lg:col-span-2 p-6 lg:p-8">
                            <h4 class="text-xl font-semibold text-gray-900 mb-6">Player Information</h4>

                            <!-- Role Description -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                <div class="flex items-center mb-4">
                                    <svg class="w-8 h-8 text-indigo-600 mr-2" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M12 14c2.206 0 4-1.794 4-4s-1.794-4-4-4-4 1.794-4 4 1.794 4 4 4zm0-6c1.103 0 2 .897 2 2s-.897 2-2 2-2-.897-2-2 .897-2 2-2z"></path>
                                        <path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path>
                                    </svg>
                                    <h5 class="text-lg font-medium text-gray-900">{{ $playerInfo->position->name ?? 'No Position' }}</h5>
                                </div>
                                <p class="text-gray-600">
                                    @switch($playerInfo->position->name ?? 'No Position')
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

                            <!-- Available for Leagues -->
                            <div class="mb-6">
                                <h5 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                    <svg class="w-6 h-6 mr-2 text-indigo-600" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M19 4h-2V2h-2v2H9V2H7v2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2V6c0-1.103-.897-2-2-2zM5 20V7h14V6l.002 14H5z"></path>
                                        <path d="m9.293 9.293-3 3a.999.999 0 0 0 0 1.414l3 3 1.414-1.414L8.414 13l2.293-2.293-1.414-1.414zm5.414 0-1.414 1.414L15.586 13l-2.293 2.293 1.414 1.414 3-3a.999.999 0 0 0 0-1.414l-3-3z"></path>
                                    </svg>
                                    League Participation Status
                                </h5>
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-green-800">Available for New Leagues</p>
                                        <p class="text-sm text-green-600 mt-1">You can join upcoming cricket leagues and tournaments</p>
                                    </div>
                                    <a href="{{ route('leagues.index') }}" class="px-4 py-2 bg-green-600 text-blue-900 rounded-md hover:bg-green-700 transition-colors">
                                        Browse Leagues
                                    </a>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <a href="{{ route('players.show', $playerInfo) }}" class="flex items-center justify-center px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M12 9a3.02 3.02 0 0 0-3 3c0 1.642 1.358 3 3 3 1.641 0 3-1.358 3-3 0-1.641-1.359-3-3-3z"></path>
                                        <path d="M12 5c-7.633 0-9.927 6.617-9.948 6.684L1.946 12l.105.316C2.073 12.383 4.367 19 12 19s9.927-6.617 9.948-6.684l.106-.316-.105-.316C21.927 11.617 19.633 5 12 5zm0 12c-5.351 0-7.424-3.846-7.926-5C4.578 10.842 6.652 7 12 7c5.351 0 7.424 3.846 7.926 5-.504 1.158-2.578 5-7.926 5z"></path>
                                    </svg>
                                    View Public Profile
                                </a>
                                <a href="{{ route('players.edit', $playerInfo) }}" class="flex items-center justify-center px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="m18.988 2.012 3 3L19.701 7.3l-3-3zM8 16h3l7.287-7.287-3-3L8 13z"></path>
                                        <path d="M19 19H8.158c-.026 0-.053.01-.079.01-.033 0-.066-.009-.1-.01H5V5h6.847l2-2H5c-1.103 0-2 .896-2 2v14c0 1.104.897 2 2 2h14a2 2 0 0 0 2-2v-8.668l-2 2V19z"></path>
                                    </svg>
                                    Edit Profile
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- No Player Profile -->
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-20 w-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="mt-4 text-xl font-semibold text-gray-900">You don't have a player profile yet</h3>
                        <p class="mt-2 text-gray-600 mb-6">
                            Contact an administrator to become a player and get assigned a role.
                        </p>
                        <div class="flex justify-center">
                            <a href="{{ route('players.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-blue-900 uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                </svg>
                                Browse All Players
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Original Become an Organizer Section (Now Hidden) -->
    <section class="hidden py-16 px-4 sm:px-6 lg:px-8 bg-indigo-50">
        <div class="max-w-5xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Become a League Organizer</h2>
            <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                Create and manage your own cricket leagues, set rules, schedule matches, and oversee competitions.
            </p>
            <div class="bg-white rounded-xl shadow-xl overflow-hidden animate-fadeInUp">
                <div class="grid grid-cols-1 md:grid-cols-2">
                    <div class="p-8 flex flex-col justify-center">
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4">Organizer Benefits</h3>
                        <ul class="space-y-3 text-left">
                            <li class="flex items-start"><span class="text-green-500 mr-2">✅</span> Create multiple leagues with customizable rules</li>
                            <li class="flex items-start"><span class="text-green-500 mr-2">✅</span> Manage team registrations and player drafts</li>
                            <li class="flex items-start"><span class="text-green-500 mr-2">✅</span> Oversee match scheduling and results</li>
                            <li class="flex items-start"><span class="text-green-500 mr-2">✅</span> Access to detailed analytics and reporting tools</li>
                        </ul>
                    </div>
                    <div class="p-8 bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex flex-col justify-center">
                        <h3 class="text-2xl font-semibold mb-4">Ready to Get Started?</h3>
                        <p class="mb-6">Join today and create your first cricket league in minutes.</p>
                        <a href="{{ route('leagues.create') }}"
                           class="bg-white text-indigo-600 px-6 py-3 rounded-xl font-medium
                                  hover:bg-indigo-50 active:scale-95 transition-all shadow-md hover:shadow-lg">
                            Create Your First League
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
