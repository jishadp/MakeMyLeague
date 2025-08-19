@extends('layouts.app')

@section('title', 'Cricket Players | ' . config('app.name'))

@section('content')
<div class="py-12 bg-gradient-to-br from-gray-50 via-white to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 animate-fadeIn">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                Cricket Players
            </h1>
            <a href="{{ route('players.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-sm sm:text-base">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Player
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-md p-5 mb-8 animate-fadeInUp">
            <form action="{{ route('players.index') }}" method="GET">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Filter Players</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                    <!-- Role Filter -->
                    <div>
                        <label for="position_id" class="block text-sm font-medium text-gray-700 mb-1">Playing Role</label>
                        <select name="position_id" id="position_id" class="w-full h-12 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 text-center"
                                onchange="this.form.submit()">
                            <option value="">All Roles</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                                    {{ $position->name }}
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
                    <a href="{{ route('players.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                        Reset Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- No Players -->
        @if($players->isEmpty())
            <div class="bg-white border border-gray-200 rounded-xl shadow-md p-8 text-center animate-fadeInUp">
                <p class="text-gray-600 mb-4">No players found matching your criteria.</p>
                <a href="{{ route('players.index') }}"
                   class="text-indigo-600 hover:text-indigo-800 font-medium">
                    View all players
                </a>
            </div>
        @else
            <!-- Player Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($players as $player)
                    <div class="bg-white border border-gray-100 rounded-xl shadow-lg overflow-hidden 
                                hover:shadow-xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 h-32 flex items-center justify-center relative overflow-hidden">
                            <div class="w-full h-full flex items-center justify-center">
                                <div class="w-20 h-20 rounded-full overflow-hidden bg-white bg-opacity-20 flex items-center justify-center">
                                    <img src="{{ asset('images/defaultplayer.jpeg') }}" 
                                         alt="{{ $player->name }}" 
                                         class="w-full h-full object-cover"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-full h-full flex items-center justify-center text-white font-bold text-lg" style="display: none;">
                                        {{ strtoupper(substr($player->name, 0, 2)) }}
                                    </div>
                                </div>
                            </div>
                            <div class="absolute bottom-2 left-2 right-2">
                                <span class="text-white text-xs font-medium bg-black bg-opacity-30 px-2 py-1 rounded-full">
                                    {{ $player->position->name ?? 'Player' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-5">
                            <!-- Name -->
                            <div class="mb-4">
                                <h3 class="text-lg font-bold text-gray-900">{{ $player->name }}</h3>
                            </div>
                            
                            <!-- Details -->
                            <div class="space-y-2 text-sm text-gray-600">
                                <p><span class="font-medium">🏏 Role:</span> {{ $player->position->name ?? 'N/A' }}</p>
                                @if($player->localBody)
                                    <p><span class="font-medium">📍 Location:</span> {{ $player->localBody->name }}</p>
                                @endif
                                @if($player->mobile)
                                    <p><span class="font-medium">📱 Mobile:</span> {{ $player->mobile }}</p>
                                @endif
                            </div>
                            
                            <!-- View Details -->
                            <div class="mt-4 flex justify-end items-center">
                                <a href="{{ route('players.show', $player->slug) }}"
                                   class="inline-flex items-center px-3 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors text-sm font-medium">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="mt-8">
                {{ $players->links() }}
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
