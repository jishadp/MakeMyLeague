@extends('layouts.app')

@section('title', 'League Manager - ' . $league->name)

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Main Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-6 sm:p-10">
                
                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start mb-10 gap-6">
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900 mb-3">{{ $league->name }}</h1>
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold 
                                {{ $league->status === 'active' ? 'bg-green-100 text-green-800' : 
                                   ($league->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($league->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                                {{ ucfirst($league->status) }}
                            </span>
                            @if($league->is_default)
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold bg-green-50 text-green-700 border border-green-200">
                                    Default League
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <a href="{{ route('leagues.edit', $league) }}" 
                           class="px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg shadow-sm hover:bg-indigo-700 transition-colors text-center">
                            Edit League
                        </a>
                        <a href="{{ route('leagues.index') }}" 
                           class="px-6 py-2.5 bg-gray-100 text-gray-800 font-medium rounded-lg shadow-sm hover:bg-gray-200 transition-colors text-center">
                            Back to Leagues
                        </a>
                    </div>
                </div>
                
                <!-- League Summary -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                    <!-- Card -->
                    <div class="bg-gray-50 p-6 rounded-xl shadow-sm border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Info</h3>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Game:</dt>
                                <dd class="font-medium">{{ $league->game->name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Season:</dt>
                                <dd class="font-medium">{{ $league->season }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Organizer:</dt>
                                <dd class="font-medium">{{ $league->organizer->name }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-xl shadow-sm border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Schedule</h3>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Start Date:</dt>
                                <dd class="font-medium">{{ $league->start_date->format('M d, Y') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">End Date:</dt>
                                <dd class="font-medium">{{ $league->end_date->format('M d, Y') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Duration:</dt>
                                <dd class="font-medium">{{ $league->start_date->diffInDays($league->end_date) + 1 }} days</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-xl shadow-sm border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Team Configuration</h3>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Max Teams:</dt>
                                <dd class="font-medium">{{ $league->max_teams }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Players per Team:</dt>
                                <dd class="font-medium">{{ $league->max_team_players }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Wallet Limit:</dt>
                                <dd class="font-medium">₹{{ number_format($league->team_wallet_limit, 2) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Registration Information -->
                <div class="mb-12">
                    <h3 class="text-2xl font-semibold text-gray-900 mb-6">Registration Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                        <div class="bg-gray-50 p-6 rounded-xl shadow-sm border border-gray-200">
                            <h4 class="text-lg font-medium text-gray-800 mb-4">Team Registration</h4>
                            <dl class="space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Fee:</dt>
                                    <dd class="font-medium">₹{{ number_format($league->team_reg_fee, 2) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Teams Registered:</dt>
                                    <dd class="font-medium">0 / {{ $league->max_teams }}</dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-gray-600">Status:</dt>
                                    <dd class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        Open
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-xl shadow-sm border border-gray-200">
                            <h4 class="text-lg font-medium text-gray-800 mb-4">Player Registration</h4>
                            <dl class="space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Fee:</dt>
                                    <dd class="font-medium">₹{{ number_format($league->player_reg_fee, 2) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Retention:</dt>
                                    <dd class="font-medium">{{ $league->retention ? 'Enabled' : 'Disabled' }}</dd>
                                </div>
                                @if($league->retention)
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600">Max Retained Players:</dt>
                                        <dd class="font-medium">{{ $league->retention_players }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-6">Quick Actions</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <a href="#" class="flex items-center justify-center p-4 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            Manage Teams
                        </a>

                        <a href="#" class="flex items-center justify-center p-4 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Manage Players
                        </a>

                        <a href="#" class="flex items-center justify-center p-4 bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 transition-colors shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                            </svg>
                            Auction Setup
                        </a>

                        <a href="#" class="flex items-center justify-center p-4 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            View Statistics
                        </a>
                    </div>
                </div>

            </div>
        </div>
        
    </div>
</div>
@endsection
