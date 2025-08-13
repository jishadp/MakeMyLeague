@extends('layouts.app')

@section('title', $leagueTeam->team->name . ' - ' . $league->name)

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-16 w-16">
                        @if($leagueTeam->team->logo)
                            <img class="h-16 w-16 rounded-full object-cover" 
                                 src="{{ $leagueTeam->team->logo }}" 
                                 alt="{{ $leagueTeam->team->name }}">
                        @else
                            <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-xl font-medium text-gray-700">
                                    {{ substr($leagueTeam->team->name, 0, 2) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="ml-6">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $leagueTeam->team->name }}</h1>
                        <p class="text-gray-600 mt-1">{{ $league->name }}</p>
                        <div class="flex items-center mt-2">
                            <span class="inline-flex px-2 py-1 text-sm font-semibold rounded-full
                                {{ $leagueTeam->status === 'available' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($leagueTeam->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('league-teams.edit', [$league, $leagueTeam]) }}" 
                       class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        Edit Team
                    </a>
                    <a href="{{ route('league-teams.index', $league) }}" 
                       class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        Back to Teams
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Team Details -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Team Details</h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Owner</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $leagueTeam->team->owner->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Owner Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $leagueTeam->team->owner->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Home Ground</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $leagueTeam->team->homeGround->name ?? 'Not Set' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Local Body</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $leagueTeam->team->localBody->name ?? 'Not Set' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Wallet Balance</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-medium">₹{{ number_format($leagueTeam->wallet_balance) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Players</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $leagueTeam->players->count() }} / {{ $league->max_team_players }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Team Players -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Team Players</h2>
                        <span class="text-sm text-gray-500">{{ $leagueTeam->players->count() }} players</span>
                    </div>
                    
                    @if($leagueTeam->players->count() > 0)
                        <div class="space-y-4">
                            @foreach($leagueTeam->players as $player)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ substr($player->user->name, 0, 2) }}
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $player->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $player->user->role->name ?? 'No Role' }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        @if($player->retention)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Retained
                                            </span>
                                        @endif
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'available' => 'bg-blue-100 text-blue-800',
                                                'sold' => 'bg-green-100 text-green-800',
                                                'unsold' => 'bg-red-100 text-red-800',
                                                'skip' => 'bg-gray-100 text-gray-800'
                                            ];
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$player->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($player->status) }}
                                        </span>
                                        <span class="text-sm font-medium text-gray-900">₹{{ number_format($player->base_price) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No players</h3>
                            <p class="mt-1 text-sm text-gray-500">This team has no players registered yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('league-players.index', $league) }}?team={{ $leagueTeam->team->id }}" 
                           class="block w-full text-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors">
                            View Team Players
                        </a>
                        <a href="{{ route('teams.show', $leagueTeam->team) }}" 
                           class="block w-full text-center px-4 py-2 bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                            View Team Profile
                        </a>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Team Statistics</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Available Players</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $leagueTeam->players->where('status', 'available')->count() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Sold Players</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $leagueTeam->players->where('status', 'sold')->count() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Retained Players</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $leagueTeam->players->where('retention', true)->count() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Total Base Price</dt>
                            <dd class="text-sm font-medium text-gray-900">₹{{ number_format($leagueTeam->players->sum('base_price')) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

        </div>
        
    </div>
</div>
@endsection
