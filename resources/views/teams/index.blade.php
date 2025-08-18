@extends('layouts.app')

@section('title', 'Cricket Teams | CricBid')

@section('content')
<div class="py-12 bg-gradient-to-br from-gray-50 via-white to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 animate-fadeIn">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                Cricket Teams
            </h1>
            <a href="{{ route('teams.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-sm sm:text-base">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Team
            </a>
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
                    <div class="bg-white border border-gray-100 rounded-xl shadow-lg overflow-hidden 
                                hover:shadow-xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                        <div class="bg-gray-200 h-48 flex items-center justify-center">
                            @if($team->logo)
                                <img src="{{ asset($team->logo) }}" alt="{{ $team->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="text-gray-500 text-center p-4">
                                    <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>Team Logo</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="p-5">
                            <!-- Title -->
                            <div class="mb-4">
                                <h3 class="text-xl font-bold text-gray-900">{{ $team->name }}</h3>
                            </div>
                            
                            <!-- Details -->
                            <div class="space-y-3 text-sm text-gray-600">
                                <p><span class="font-medium">📍 Location:</span> {{ $team->localBody->name }}</p>
                                <p><span class="font-medium">🏟️ Home Ground:</span> {{ $team->homeGround->name }}</p>
                                <p><span class="font-medium">👤 Owner:</span> {{ $team->owner->name }}</p>
                            </div>
                            
                            <!-- View Details -->
                            <div class="mt-6 flex justify-end items-center">
                                <a href="{{ route('teams.show', $team->slug) }}"
                                   class="text-indigo-600 hover:text-indigo-800 font-medium">
                                    View Details
                                </a>
                            </div>
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
