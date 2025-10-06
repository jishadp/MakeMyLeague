@extends('layouts.app')

@section('title', 'Cricket Teams | ' . config('app.name'))

@section('content')
<div class="py-12 bg-gradient-to-br from-gray-50 via-white to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 animate-fadeIn">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                Cricket Teams
            </h1>
            @auth
                <a href="{{ route('teams.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-sm sm:text-base">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Team
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-sm sm:text-base">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Login to Create Team
                </a>
            @endauth
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-md p-5 mb-8 animate-fadeInUp">
            <form action="{{ route('teams.index') }}" method="GET">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Filter Teams</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                    <!-- Local Body Filter -->
                    <div>
                        <label for="local_body_id" class="block text-sm font-medium text-gray-700 mb-1">Local Body</label>
                        <select name="local_body_id" id="local_body_id" class="w-full h-12 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 text-center"
                                onchange="this.form.submit()">
                            <option value="">All Local Bodies</option>
                            @foreach($localBodies as $localBody)
                                <option value="{{ $localBody->id }}" {{ request('local_body_id') == $localBody->id ? 'selected' : '' }}>
                                    {{ $localBody->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Sort By -->
                    <div>
                        <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                        <select name="sort_by" id="sort_by" class="w-full h-12 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 text-center">
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                        </select>
                    </div>
                    
                    <!-- Sort Direction -->
                    <div>
                        <label for="sort_dir" class="block text-sm font-medium text-gray-700 mb-1">Direction</label>
                        <select name="sort_dir" id="sort_dir" class="w-full h-12 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 text-center">
                            <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>Ascending</option>
                            <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>Descending</option>
                        </select>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex items-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Apply
                        </button>
                    </div>
                </div>
                
                <!-- Reset Filters -->
                <div class="flex justify-end">
                    <a href="{{ route('teams.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                        Reset Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- No Teams -->
        @if($teams->isEmpty())
            <div class="bg-white border border-gray-200 rounded-xl shadow-md p-8 text-center animate-fadeInUp">
                <p class="text-gray-600 mb-4">No teams found matching your criteria.</p>
                <a href="{{ route('teams.index') }}"
                   class="text-indigo-600 hover:text-indigo-800 font-medium">
                    View all teams
                </a>
            </div>
        @else
            <!-- Team Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($teams as $team)
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
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $team->localBody->name }}</span>
                                </div>
                                @if($team->homeGround)
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5zM8 15a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $team->homeGround->name }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                    </svg>
                                    <span>{{ $team->owner->name }}</span>
                                </div>
                            </div>
                            
                            <!-- Action Button -->
                            <a href="{{ route('teams.show', $team->slug) }}"
                                class="w-full bg-blue-600 text-black text-center py-3 px-4 rounded-xl font-semibold hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl block">
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $teams->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Animations -->
<style>
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fadeIn { animation: fadeIn 0.5s ease-in-out; }
.animate-fadeInUp { animation: fadeInUp 0.4s ease-in-out; }
</style>
@endsection
