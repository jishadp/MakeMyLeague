@extends('layouts.app')

@section('title', 'Cricket Team | ' . config('app.name'))

@section('content')
<div class="py-12 bg-gradient-to-br from-gray-50 via-white to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 animate-fadeIn">
        
        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('teams.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            Teams
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $team->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        
        <!-- Team Details Card - Dashboard Style -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8 animate-fadeInUp hover:shadow-2xl hover:scale-[1.02] transition-all duration-300">
            
            <!-- Hero Image Section -->
            <div class="relative h-48 overflow-hidden">
                @if($team->banner)
                    <img src="{{ asset('storage/' . $team->banner) }}" alt="{{ $team->name }} Banner" 
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                @elseif($team->logo)
                    <img src="{{ asset('storage/' . $team->logo) }}" alt="{{ $team->name }} Logo" 
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
                                <img src="{{ asset('storage/' . $team->logo) }}" alt="{{ $team->name }} Logo" 
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
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium">Owner: {{ $team->owner->name }}</span>
                    </div>
                    @if($team->homeGround)
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5zM8 15a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ $team->homeGround->name ?? 'Not specified' }}</span>
                        </div>
                    @endif
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ $team->localBody->name }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Created {{ $team->created_at->format('M Y') }}</span>
                    </div>
                </div>
                
                <!-- Action Button -->
                <a href="{{ route('grounds.show', $team->homeGround) }}"
                    class="w-full bg-blue-600 text-white text-center py-3 px-4 rounded-xl font-semibold hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl block mb-4">
                    View Ground Details
                </a>
            </div>
            
            <!-- Action buttons for team owner or admin -->
            @auth
                @if(Auth::id() === $team->owner_id || Auth::user()->isOrganizer())
                <div class="p-6 pt-0 flex justify-end space-x-3">
                    <a href="{{ route('teams.edit', $team) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#4a90e2] to-[#87ceeb] text-white rounded-md hover:opacity-90 shadow-md hover:shadow-lg transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Edit Team
                    </a>
                    
                    <form action="{{ route('teams.destroy', $team) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this team? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 shadow-md hover:shadow-lg transition-all duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Delete Team
                        </button>
                    </form>
                </div>
                @endif
            @endauth
        </div>
        
        <!-- Back button -->
        <div class="flex justify-start mb-8">
            <a href="{{ route('teams.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Teams
            </a>
        </div>
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
