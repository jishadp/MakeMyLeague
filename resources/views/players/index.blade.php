@extends('layouts.app')

@section('title', 'Cricket Players | ' . config('app.name'))

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-indigo-600 to-purple-600 py-8 px-4 sm:px-6 lg:px-8 text-white animate-fadeIn">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-4 drop-shadow">
                    Cricket Players
                </h1>
                <p class="text-lg sm:text-xl text-white/90">
                    Discover talented cricket players and their profiles
                </p>
            </div>
            @auth
                <a href="{{ route('players.create') }}"
                   class="inline-flex items-center px-6 py-3 bg-white/20 hover:bg-white/30 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl backdrop-blur-sm border border-white/20">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Player
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center px-6 py-3 bg-white/20 hover:bg-white/30 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl backdrop-blur-sm border border-white/20">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Login to Create Player
                </a>
            @endauth
        </div>
    </div>
</section>

<!-- Content Section -->
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        
        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 animate-fadeInUp">
            <form action="{{ route('players.index') }}" method="GET">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/>
                    </svg>
                    Filter Players
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                    <!-- Role Filter -->
                    <div>
                        <label for="position_id" class="block text-sm font-medium text-gray-700 mb-2">Playing Role</label>
                        <select name="position_id" id="position_id" class="w-full h-12 rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3"
                                onchange="this.form.submit()">
                            <option value="">All Roles</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                                    {{ $position->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Location Filter -->
                    <div>
                        <label for="local_body_id" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                        <select name="local_body_id" id="local_body_id" class="w-full h-12 rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3"
                                onchange="this.form.submit()">
                            <option value="">All Locations</option>
                            @foreach($localBodies as $localBody)
                                <option value="{{ $localBody->id }}" {{ request('local_body_id') == $localBody->id ? 'selected' : '' }}>
                                    {{ $localBody->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort Filter -->
                    <div>
                        <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                        <select name="sort_by" id="sort_by" class="w-full h-12 rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3"
                                onchange="this.form.submit()">
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Recently Added</option>
                        </select>
                    </div>

                    <!-- Sort Direction -->
                    <div>
                        <label for="sort_dir" class="block text-sm font-medium text-gray-700 mb-2">Order</label>
                        <select name="sort_dir" id="sort_dir" class="w-full h-12 rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3"
                                onchange="this.form.submit()">
                            <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>Ascending</option>
                            <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>Descending</option>
                        </select>
                    </div>
                </div>
                
                <!-- Reset Filters -->
                <div class="flex justify-end">
                    <a href="{{ route('players.index') }}" class="inline-flex items-center px-4 py-2 text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- No Players -->
        @if($players->isEmpty())
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center animate-fadeInUp">
                <div class="mb-6">
                    <svg class="w-20 h-20 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Players Found</h3>
                <p class="text-gray-600 mb-6">No players found matching your criteria.</p>
                <a href="{{ route('players.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-colors shadow-lg hover:shadow-xl">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    View All Players
                </a>
            </div>
        @else
            <!-- Player Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($players as $player)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                        
                        <!-- Player Photo Section -->
                        <div class="relative h-48 overflow-hidden">
                            @if($player->photo)
                                <img src="{{ asset($player->photo) }}" 
                                     alt="{{ $player->name }}" 
                                     class="w-full h-full object-cover"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 flex items-center justify-center {{ $player->photo ? 'hidden' : '' }}">
                                <div class="text-center text-white">
                                    <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3">
                                        <span class="text-white font-bold text-3xl">{{ strtoupper(substr($player->name, 0, 1)) }}</span>
                                    </div>
                                    <h3 class="text-xl font-bold drop-shadow-lg">{{ $player->name }}</h3>
                                </div>
                            </div>
                            
                            <!-- Position Badge -->
                            <div class="absolute top-4 left-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/90 text-indigo-700 shadow-lg">
                                    {{ $player->position->name ?? 'Player' }}
                                </span>
                            </div>
                            
                            <!-- Player Name Overlay (if photo exists) -->
                            @if($player->photo)
                                <div class="absolute bottom-4 left-4 right-4">
                                    <div class="bg-black/50 backdrop-blur-sm rounded-xl p-3">
                                        <h3 class="text-lg font-bold text-white drop-shadow-lg">{{ $player->name }}</h3>
                                        <p class="text-sm text-white/90 drop-shadow">{{ $player->position->name ?? 'Player' }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Content Section -->
                        <div class="p-6">
                            <!-- Quick Stats -->
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div class="text-center p-3 bg-gray-50 rounded-xl">
                                    <div class="text-lg font-bold text-indigo-600">
                                        @if($player->leaguePlayers)
                                            {{ $player->leaguePlayers->count() }}
                                        @else
                                            0
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-600">Leagues</div>
                                </div>
                                <div class="text-center p-3 bg-gray-50 rounded-xl">
                                    <div class="text-lg font-bold text-purple-600">
                                        @if($player->teams)
                                            {{ $player->teams->count() }}
                                        @else
                                            0
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-600">Teams</div>
                                </div>
                            </div>
                            
                            <!-- Player Details -->
                            <div class="space-y-2 mb-4">
                                @if($player->localBody)
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $player->localBody->name }}</span>
                                    </div>
                                @endif
                                @if($player->mobile)
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                        </svg>
                                        <span>{{ $player->mobile }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Joined {{ $player->created_at->format('M Y') }}</span>
                                </div>
                            </div>
                            
                            <!-- Action Button -->
                            <div class="flex justify-center">
                                <a href="{{ route('players.show', $player->slug) }}"
                                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl active:scale-95">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Profile
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-12">
                {{ $players->links() }}
            </div>
        @endif
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