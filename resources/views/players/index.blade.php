@extends('layouts.app')

@section('title', 'Cricket Players | CricBid')

@section('content')
<div class="py-12 bg-gradient-to-br from-gray-50 via-white to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 animate-fadeIn">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                Cricket Players
            </h1>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-md p-5 mb-8 animate-fadeInUp">
            <form action="{{ route('players.index') }}" method="GET">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Filter Players</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                    <!-- Role Filter -->
                    <div>
                        <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1">Playing Role</label>
                        <select name="role_id" id="role_id" class="w-full h-12 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 text-center"
                                onchange="this.form.submit()">
                            <option value="">All Roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
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
                        <button type="submit" class="w-full h-12 bg-indigo-600 text-white rounded-lg px-4 py-2 hover:bg-indigo-700 transition-colors font-medium">
                            Apply Filters
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
                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 h-32 flex items-center justify-center">
                            <div class="text-white text-center p-4">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm font-medium">{{ $player->role->name ?? 'Player' }}</span>
                            </div>
                        </div>
                        
                        <div class="p-5">
                            <!-- Name -->
                            <div class="mb-4">
                                <h3 class="text-lg font-bold text-gray-900">{{ $player->name }}</h3>
                            </div>
                            
                            <!-- Details -->
                            <div class="space-y-2 text-sm text-gray-600">
                                <p><span class="font-medium">🏏 Role:</span> {{ $player->role->name ?? 'N/A' }}</p>
                                @if($player->localBody)
                                    <p><span class="font-medium">📍 Location:</span> {{ $player->localBody->name }}</p>
                                @endif
                                @if($player->mobile)
                                    <p><span class="font-medium">📱 Mobile:</span> {{ $player->mobile }}</p>
                                @endif
                            </div>
                            
                            <!-- View Details -->
                            <div class="mt-4 flex justify-end items-center">
                                <a href="{{ route('players.show', $player->id) }}"
                                   class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">
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
