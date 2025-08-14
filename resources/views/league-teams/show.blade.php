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
                                            <p class="text-xs text-gray-600">{{ $player->user->role->name ?? 'No Role' }}</p>
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
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
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
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" id="modal-backdrop"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between p-4 md:p-5 border-b">
                <h3 class="text-xl font-semibold text-gray-900">
                    Add Retention Players
                </h3>
                <button type="button" id="closeRetentionModal" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg p-1.5">
                    <span class="sr-only">Close</span>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
            
            <div class="p-4 md:p-5 max-h-[calc(90vh-120px)] overflow-y-auto">
                <p class="mb-4 text-gray-600">
                    Select players to retain for {{ $leagueTeam->team->name }} in {{ $league->name }}.
                    <span class="font-medium">You can retain {{ $league->retention_players }} players.</span>
                </p>
                
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-medium text-gray-900">Retention Players</h4>
                    <div class="flex items-center">
                        <span class="text-sm text-gray-600 mr-2">Filter:</span>
                        <select id="playerFilter" class="text-sm border-gray-300 rounded-md">
                            <option value="all">All Players</option>
                            <option value="team">Current Team</option>
                            <option value="league">Available for Auction</option>
                        </select>
                    </div>
                </div>
                
                <form action="{{ route('league-players.bulkStatus', $league) }}" method="POST" id="retentionForm">
                    @csrf
                    <input type="hidden" name="status" value="available">
                    
                    <div class="space-y-4">
                        <div id="current-team-players">
                            @if($leagueTeam->players->count() > 0)
                                <h5 class="font-medium text-sm text-gray-600 mb-2">Current Team Players</h5>
                                @foreach($leagueTeam->players as $player)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg mb-2 player-item" data-type="team">
                                        <div class="flex items-center">
                                            <input type="checkbox" 
                                                name="player_ids[]" 
                                                value="{{ $player->id }}" 
                                                id="player-{{ $player->id }}"
                                                class="player-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                                {{ $player->retention ? 'checked' : '' }}
                                                data-retention="{{ $player->retention ? 'true' : 'false' }}">
                                            <label for="player-{{ $player->id }}" class="ml-3 block">
                                                <span class="text-sm font-medium text-gray-900">{{ $player->user->name }}</span>
                                                <span class="text-sm text-gray-500 block">{{ $player->user->role->name ?? 'No Role' }}</span>
                                            </label>
                                        </div>
                                        <div class="flex items-center">
                                            @if($player->retention)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 mr-2">
                                                    Retained
                                                </span>
                                            @endif
                                            <span class="text-sm font-medium text-gray-900 mr-2">
                                                @if($player->retention)
                                                    <span title="Infinite value" class="text-green-600 font-bold">∞</span>
                                                @else
                                                    ₹{{ number_format($player->base_price) }}
                                                @endif
                                            </span>
                                            <button type="button" data-player-id="{{ $player->id }}"
                                                    class="remove-player-btn text-sm text-red-600 hover:text-red-800 focus:outline-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-500">No players in this team yet.</p>
                                </div>
                            @endif
                        </div>
                        
                                                <div id="other-league-players" class="mt-6" style="display:none;">
                            <h5 class="font-medium text-sm text-gray-600 mb-2">Available Players for Auction</h5>
                            
                            @php
                                $otherLeaguePlayers = \App\Models\LeaguePlayer::with(['user', 'user.role'])
                                    ->whereNull('league_team_id')
                                    ->where('status', 'available')
                                    ->get();
                            @endphp
                            
                            @if($otherLeaguePlayers->count() > 0)
                                @foreach($otherLeaguePlayers as $player)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg mb-2 player-item" data-type="league">
                                        <div class="flex items-center">
                                            <input type="checkbox" 
                                                name="player_ids[]" 
                                                value="{{ $player->id }}" 
                                                id="player-{{ $player->id }}"
                                                class="player-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                                {{ $player->retention ? 'checked' : '' }}
                                                data-retention="{{ $player->retention ? 'true' : 'false' }}">
                                            <label for="player-{{ $player->id }}" class="ml-3 block">
                                                <span class="text-sm font-medium text-gray-900">{{ $player->user->name }}</span>
                                                <span class="text-sm text-gray-500 block">{{ $player->user->role->name ?? 'No Role' }}</span>
                                            </label>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mr-2">
                                                Available
                                            </span>
                                            <span class="text-sm font-medium text-gray-900">₹{{ number_format($player->base_price) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-500">No other available players in this league.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-between">
                        <div>
                            <span class="text-sm text-gray-600">Selected: <span id="selectedCount">0</span>/{{ $league->retention_players }}</span>
                        </div>
                        <button type="button" id="updateRetentionBtn"
                                class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
                            Update Retention Players
                        </button>
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
        
        // Store initial retention state
        playerCheckboxes.forEach(checkbox => {
            initialRetentionState.push({
                id: checkbox.value,
                retained: checkbox.dataset.retention === 'true'
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
        
        // Limit retention selection
        playerCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const selectedCount = document.querySelectorAll('.player-checkbox:checked').length;
                
                if (selectedCount > maxRetention) {
                    this.checked = false;
                    alert(`You can only retain ${maxRetention} players.`);
                }
                
                updateSelectedCount();
            });
        });
        
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
                
                // Method PATCH
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PATCH';
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
