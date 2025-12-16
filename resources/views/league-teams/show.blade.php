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
                    <a href="{{ route('league-teams.replace-form', [$league, $leagueTeam]) }}" 
                       class="px-4 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors">
                        Replace Team
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
                    
                    <!-- Team Owner Card -->
                    <div class="border border-gray-200 rounded-lg p-4 mb-6 bg-gradient-to-r from-indigo-50 to-blue-50">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-14 w-14 bg-indigo-100 rounded-full flex items-center justify-center">
                                <span class="text-xl font-bold text-indigo-700">
                                    {{ substr($leagueTeam->team->owner->name, 0, 1) }}
                                </span>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-bold text-gray-900">{{ $leagueTeam->team->owner->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $leagueTeam->team->owner->email }}</p>
                                <div class="mt-1">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                        Team Owner
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Retention Players Section -->
                    @if($league->retention && $leagueTeam->players->where('retention', true)->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Retention Players</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($leagueTeam->players->where('retention', true) as $player)
                            <div class="border border-green-200 rounded-lg p-4 bg-gradient-to-r from-green-50 to-emerald-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12 bg-green-100 rounded-full flex items-center justify-center">
                                            <span class="text-md font-bold text-green-700">
                                                {{ substr($player->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-md font-bold text-gray-900">{{ $player->user->name }}</h4>
                                            <p class="text-xs text-gray-600">{{ $player->user->position->name ?? 'No Role' }}</p>
                                            <div class="mt-1 flex items-center">
                                                <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Retained
                                                </span>
                                                <span class="ml-2 text-xs font-medium text-gray-900">
                                                    @if($player->retention)
                                                        <span title="Infinite value" class="text-green-600 font-bold">∞</span>
                                                    @else
                                                        ₹{{ number_format($player->base_price) }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <form action="{{ route('league-players.destroy', [$league, $player]) }}" method="POST" 
                                              class="remove-player-form" data-player-name="{{ $player->user->name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-800 remove-player-btn-main">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Team Details -->
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                                            <div class="text-sm text-gray-500">{{ $player->user->position->name ?? 'No Role' }}</div>
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
                                        <span class="text-sm font-medium text-gray-900">
                                            @if($player->retention)
                                                <span title="Infinite value" class="text-green-600 font-bold">∞</span>
                                            @else
                                                ₹{{ number_format($player->base_price) }}
                                            @endif
                                        </span>
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
                @if($canAdjustBalance)
                @php
                    $auditDifference = round($balanceAudit['difference'] ?? 0, 2);
                @endphp
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Balance Audit</h3>
                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full {{ $auditDifference == 0 ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $auditDifference == 0 ? 'In Sync' : 'Needs Attention' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mb-4">Calculated from team wallet limit minus sold/retained player spends.</p>
                    <dl class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-600">Team wallet limit</dt>
                            <dd class="font-semibold text-gray-900">₹{{ number_format($balanceAudit['base_wallet'] ?? 0) }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-600">Player spend (sold/retained)</dt>
                            <dd class="font-semibold text-gray-900">₹{{ number_format($balanceAudit['player_spend'] ?? 0) }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-600">Calculated balance</dt>
                            <dd class="font-semibold text-emerald-600">₹{{ number_format($balanceAudit['calculated_balance'] ?? 0) }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-600">Stored balance</dt>
                            <dd class="font-semibold text-gray-900">₹{{ number_format($balanceAudit['stored_balance'] ?? 0) }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-600">Difference</dt>
                            <dd class="font-semibold {{ $auditDifference === 0 ? 'text-gray-700' : ($auditDifference > 0 ? 'text-amber-600' : 'text-red-600') }}">
                                {{ $auditDifference > 0 ? '+' : '' }}₹{{ number_format($auditDifference, 2) }}
                            </dd>
                        </div>
                    </dl>
                    <form method="POST" action="{{ route('league-teams.updateWallet', [$league, $leagueTeam]) }}" class="mt-5 space-y-3">
                        @csrf
                        @method('PATCH')
                        <label for="wallet_balance" class="text-sm font-medium text-gray-700">Set team balance</label>
                        <input
                            type="number"
                            name="wallet_balance"
                            id="wallet_balance"
                            step="0.01"
                            min="0"
                            value="{{ old('wallet_balance', number_format($balanceAudit['calculated_balance'] ?? 0, 2, '.', '')) }}"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        >
                        <p class="text-xs text-gray-500">Prefilled with the calculated balance. Update the amount above to manually adjust if needed.</p>
                        <button type="submit" class="w-full inline-flex justify-center px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Save Balance
                        </button>
                    </form>
                </div>
                @endif

                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('posters.show', [$league, $leagueTeam]) }}" 
                           class="block w-full text-center px-4 py-2 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors">
                            View Team Poster
                        </a>
                        <a href="{{ route('league-players.index', $league) }}?team={{ $leagueTeam->team->slug }}" 
                           class="block w-full text-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors">
                            View Team Players
                        </a>
                        <a href="{{ route('teams.show', $leagueTeam->team) }}" 
                           class="block w-full text-center px-4 py-2 bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                            View Team Profile
                        </a>
                        @if($league->retention && $leagueTeam->players->where('retention', true)->count() < $league->retention_players)
                        <a href="#retention-modal" id="openRetentionModal"
                           class="block w-full text-center px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors">
                            Add Retention Players
                            <span class="text-xs block mt-1">
                                ({{ $leagueTeam->players->where('retention', true)->count() }}/{{ $league->retention_players }})
                            </span>
                        </a>
                        @endif
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

<!-- Retention Modal -->
<div id="retention-modal" class="fixed inset-0 z-50 hidden overflow-y-auto overflow-x-hidden">
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <!-- Modern Backdrop with blur effect -->
        <div class="fixed inset-0 bg-gradient-to-br from-slate-900/80 via-slate-800/60 to-slate-900/80 backdrop-blur-sm transition-all duration-300" id="modal-backdrop"></div>
        
        <!-- Modal Content with modern design -->
        <div class="relative bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden border border-white/20 animate-in fade-in-0 zoom-in-95 duration-300">
            <!-- Header with gradient background -->
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-white">
                            Player Retention
                        </h3>
                        <p class="text-indigo-100 mt-1">
                            Select players to retain for {{ $leagueTeam->team->name }}
                        </p>
                    </div>
                    <button type="button" id="closeRetentionModal" class="text-white/80 hover:text-white hover:bg-white/10 rounded-xl p-2 transition-all duration-200">
                        <span class="sr-only">Close</span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="p-6 max-h-[calc(90vh-140px)] overflow-y-auto">
                <!-- Info Card -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 mb-6 border border-blue-100">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-800">
                                You can retain up to <span class="font-bold">{{ $league->retention_players }} players</span> from your current team or available players in {{ $league->name }}.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Filter Section -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-4">
                    <h4 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Available Players
                    </h4>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium text-gray-600">Filter by:</span>
                        <select id="playerFilter" class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                            <option value="all">All Players</option>
                            <option value="team">Current Team</option>
                            <option value="league">Available for Auction</option>
                        </select>
                    </div>
                </div>
                
                <form action="{{ route('league-players.bulkStatus', $league) }}" method="POST" id="retentionForm">
                    @csrf
                    <!-- Since we might use PATCH method via JavaScript -->
                    @method('POST')
                    <input type="hidden" name="status" value="available">
                    
                    <div class="space-y-6">
                        <div id="current-team-players">
                            @if($leagueTeam->players->count() > 0)
                                <div class="mb-4">
                                    <h5 class="text-lg font-semibold text-gray-800 flex items-center">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                        Current Team Players
                                        <span class="ml-2 text-sm font-normal text-gray-500">({{ $leagueTeam->players->count() }} players)</span>
                                    </h5>
                                </div>
                                <div class="grid gap-3">
                                    @foreach($leagueTeam->players as $player)
                                        <div class="group relative bg-white border border-gray-200 rounded-xl p-4 hover:border-indigo-300 hover:shadow-lg transition-all duration-200 player-item" data-type="team">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-4">
                                                    <!-- Custom Checkbox -->
                                                    <div class="relative">
                                                        <input type="checkbox" 
                                                            name="player_ids[]" 
                                                            value="{{ $player->id }}" 
                                                            id="player-{{ $player->id }}"
                                                            class="player-checkbox sr-only"
                                                            {{ $player->retention ? 'checked' : '' }}
                                                            data-retention="{{ $player->retention ? 'true' : 'false' }}">
                                                        <label for="player-{{ $player->id }}" class="flex items-center justify-center w-6 h-6 border-2 border-gray-300 rounded-lg cursor-pointer transition-all duration-200 hover:border-indigo-400">
                                                            <svg class="w-4 h-4 text-white opacity-0 transition-opacity duration-200" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </label>
                                                    </div>
                                                    
                                                    <!-- Player Avatar -->
                                                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                                        {{ substr($player->user->name, 0, 1) }}
                                                    </div>
                                                    
                                                    <!-- Player Info -->
                                                    <div>
                                                        <h6 class="font-semibold text-gray-900 text-lg">{{ $player->user->name }}</h6>
                                                        <p class="text-sm text-gray-500 flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                                                            </svg>
                                                            {{ $player->user->position->name ?? 'No Role' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                <!-- Player Status & Actions -->
                                                <div class="flex items-center space-x-3">
                                                    @if($player->retention)
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            Retained
                                                        </span>
                                                    @endif
                                                    
                                                    <div class="text-right">
                                                        <div class="text-lg font-bold text-gray-900">
                                                            @if($player->retention)
                                                                <span title="Retained player - infinite value" class="text-green-600">∞</span>
                                                            @else
                                                                ₹{{ number_format($player->base_price) }}
                                                            @endif
                                                        </div>
                                                        <div class="text-xs text-gray-500">Base Price</div>
                                                    </div>
                                                    
                                                    <button type="button" data-player-id="{{ $player->id }}"
                                                            class="remove-player-btn p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all duration-200">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <p class="text-gray-500 font-medium">No players in this team yet</p>
                                    <p class="text-sm text-gray-400 mt-1">Add players to your team to enable retention</p>
                                </div>
                            @endif
                        </div>
                        
                        <div id="other-league-players" class="mt-8" style="display:none;">
                            <div class="mb-4">
                                <h5 class="text-lg font-semibold text-gray-800 flex items-center">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                    Available Players for Auction
                                    <span class="ml-2 text-sm font-normal text-gray-500">({{ $otherLeaguePlayers->count() }} players)</span>
                                </h5>
                            </div>
                            
                            @if($otherLeaguePlayers->count() > 0)
                                <div class="grid gap-3">
                                    @foreach($otherLeaguePlayers as $player)
                                        <div class="group relative bg-white border border-gray-200 rounded-xl p-4 hover:border-blue-300 hover:shadow-lg transition-all duration-200 player-item" data-type="league">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-4">
                                                    <!-- Custom Checkbox -->
                                                    <div class="relative">
                                                        <input type="checkbox" 
                                                            name="player_ids[]" 
                                                            value="{{ $player->id }}" 
                                                            id="player-{{ $player->id }}"
                                                            class="player-checkbox sr-only"
                                                            {{ $player->retention ? 'checked' : '' }}
                                                            data-retention="{{ $player->retention ? 'true' : 'false' }}">
                                                        <label for="player-{{ $player->id }}" class="flex items-center justify-center w-6 h-6 border-2 border-gray-300 rounded-lg cursor-pointer transition-all duration-200 hover:border-blue-400">
                                                            <svg class="w-4 h-4 text-white opacity-0 transition-opacity duration-200" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </label>
                                                    </div>
                                                    
                                                    <!-- Player Avatar -->
                                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                                        {{ substr($player->user->name, 0, 1) }}
                                                    </div>
                                                    
                                                    <!-- Player Info -->
                                                    <div>
                                                        <h6 class="font-semibold text-gray-900 text-lg">{{ $player->user->name }}</h6>
                                                        <p class="text-sm text-gray-500 flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                                                            </svg>
                                                            {{ $player->user->position->name ?? 'No Role' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                <!-- Player Status & Actions -->
                                                <div class="flex items-center space-x-3">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Available
                                                    </span>
                                                    
                                                    <div class="text-right">
                                                        <div class="text-lg font-bold text-gray-900">₹{{ number_format($player->base_price) }}</div>
                                                        <div class="text-xs text-gray-500">Base Price</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-gray-500 font-medium">No available players for auction</p>
                                    <p class="text-sm text-gray-400 mt-1">Check back later for new player additions</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Footer Actions -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-indigo-500 rounded-full"></div>
                                    <span class="text-sm font-medium text-gray-700">
                                        Selected: <span id="selectedCount" class="font-bold text-indigo-600">0</span>/{{ $league->retention_players }}
                                    </span>
                                </div>
                                <div class="hidden sm:block w-px h-4 bg-gray-300"></div>
                                <div class="text-xs text-gray-500">
                                    Players will be retained for the next season
                                </div>
                            </div>
                            <div class="flex space-x-3">
                                <button type="button" id="closeRetentionModal"
                                        class="px-6 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                                    Cancel
                                </button>
                                <button type="button" id="updateRetentionBtn"
                                        class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Update Retention Players
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal functionality
        const modal = document.getElementById('retention-modal');
        const openModalBtn = document.getElementById('openRetentionModal');
        const closeModalBtn = document.getElementById('closeRetentionModal');
        const modalBackdrop = document.getElementById('modal-backdrop');
        
        if (openModalBtn) {
            openModalBtn.addEventListener('click', function(e) {
                e.preventDefault();
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            });
        }
        
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', function() {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            });
        }
        
        if (modalBackdrop) {
            modalBackdrop.addEventListener('click', function() {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            });
        }
        
        // Retention functionality
        const playerCheckboxes = document.querySelectorAll('.player-checkbox');
        const updateRetentionBtn = document.getElementById('updateRetentionBtn');
        const maxRetention = {{ $league->retention_players }};
        let initialRetentionState = [];
        
        // Store initial retention state and setup custom checkbox styling
        playerCheckboxes.forEach(checkbox => {
            initialRetentionState.push({
                id: checkbox.value,
                retained: checkbox.dataset.retention === 'true'
            });
            
            // Setup custom checkbox styling
            const label = checkbox.nextElementSibling;
            if (label && checkbox.checked) {
                label.classList.add('bg-indigo-600', 'border-indigo-600');
                label.querySelector('svg').classList.remove('opacity-0');
            }
            
            // Handle checkbox change for custom styling and retention limits
            checkbox.addEventListener('change', function() {
                const selectedCount = document.querySelectorAll('.player-checkbox:checked').length;
                
                if (this.checked) {
                    // Check if we've exceeded the retention limit
                    if (selectedCount > maxRetention) {
                        this.checked = false;
                        alert(`You can only retain ${maxRetention} players.`);
                        return;
                    }
                    
                    label.classList.add('bg-indigo-600', 'border-indigo-600');
                    label.classList.remove('border-gray-300');
                    label.querySelector('svg').classList.remove('opacity-0');
                } else {
                    label.classList.remove('bg-indigo-600', 'border-indigo-600');
                    label.classList.add('border-gray-300');
                    label.querySelector('svg').classList.add('opacity-0');
                }
                
                updateSelectedCount();
            });
        });
        
        // Player filtering
        const playerFilter = document.getElementById('playerFilter');
        const playerItems = document.querySelectorAll('.player-item');
        const currentTeamPlayers = document.getElementById('current-team-players');
        const otherLeaguePlayers = document.getElementById('other-league-players');
        
        if (playerFilter) {
            playerFilter.addEventListener('change', function() {
                const filterValue = this.value;
                
                switch(filterValue) {
                    case 'all':
                        currentTeamPlayers.style.display = 'block';
                        otherLeaguePlayers.style.display = 'block';
                        break;
                    case 'team':
                        currentTeamPlayers.style.display = 'block';
                        otherLeaguePlayers.style.display = 'none';
                        break;
                    case 'league':
                        currentTeamPlayers.style.display = 'none';
                        otherLeaguePlayers.style.display = 'block';
                        break;
                }
            });
        }
        
        // Update selected count
        function updateSelectedCount() {
            const selectedCount = document.querySelectorAll('.player-checkbox:checked').length;
            const countElement = document.getElementById('selectedCount');
            if (countElement) {
                countElement.textContent = selectedCount;
            }
        }
        
        // Initial count update
        updateSelectedCount();
        
        // Limit retention selection (this is handled in the custom checkbox setup above)
        // The change event is already handled in the custom checkbox setup
        
        // Handle form submission
        if (updateRetentionBtn) {
            updateRetentionBtn.addEventListener('click', function() {
                const selectedPlayers = document.querySelectorAll('.player-checkbox:checked');
                const form = document.getElementById('retentionForm');
                
                // Create a new form to update retention status
                const retentionForm = document.createElement('form');
                retentionForm.method = 'POST';
                retentionForm.action = '{{ route('league-players.bulkStatus', $league) }}';
                
                // CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                retentionForm.appendChild(csrfToken);
                
                // Method - this should be POST, not PATCH to match the route
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'POST'; // Changed from PATCH to POST
                retentionForm.appendChild(methodInput);
                
                // Add player IDs and set retention status
                playerCheckboxes.forEach(checkbox => {
                    const playerIdInput = document.createElement('input');
                    playerIdInput.type = 'hidden';
                    playerIdInput.name = 'player_ids[]';
                    playerIdInput.value = checkbox.value;
                    retentionForm.appendChild(playerIdInput);
                    
                    const retentionInput = document.createElement('input');
                    retentionInput.type = 'hidden';
                    retentionInput.name = `retention[${checkbox.value}]`;
                    retentionInput.value = checkbox.checked ? '1' : '0';
                    retentionForm.appendChild(retentionInput);
                });
                
                document.body.appendChild(retentionForm);
                retentionForm.submit();
            });
        }
        
        // Handle player removal
        const removePlayerBtns = document.querySelectorAll('.remove-player-btn');
        removePlayerBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const playerId = this.getAttribute('data-player-id');
                if (confirm('Are you sure you want to remove this player?')) {
                    // Create a form to remove the player
                    const removeForm = document.createElement('form');
                    removeForm.method = 'POST';
                    removeForm.action = '{{ route('league-players.index', $league) }}/' + playerId;
                    
                    // CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    removeForm.appendChild(csrfToken);
                    
                    // Method DELETE
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    removeForm.appendChild(methodInput);
                    
                    document.body.appendChild(removeForm);
                    removeForm.submit();
                }
            });
        });
        
        // Handle player removal from main list
        const removePlayerBtnsMain = document.querySelectorAll('.remove-player-btn-main');
        removePlayerBtnsMain.forEach(btn => {
            btn.addEventListener('click', function() {
                const form = this.closest('.remove-player-form');
                const playerName = form.getAttribute('data-player-name');
                
                if (confirm(`Are you sure you want to remove ${playerName} from the team?`)) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
@endsection
