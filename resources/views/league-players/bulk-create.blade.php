@extends('layouts.app')

@section('title', 'Bulk Add Players - ' . $league->name)

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Bulk Add Players to League</h1>
                    <p class="text-gray-600 mt-1">{{ $league->name }}</p>
                </div>
                <a href="{{ route('league-players.index', $league) }}" 
                   class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200">
                    Back to Players
                </a>
            </div>

            <form action="{{ route('league-players.bulk-store', $league) }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="league_team_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Team <span class="text-gray-500">(optional)</span>
                    </label>
                    <select name="league_team_id" id="league_team_id"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('league_team_id') border-red-500 @enderror">
                        <option value="">No team (available for auction)</option>
                        @foreach($leagueTeams as $leagueTeam)
                            <option value="{{ $leagueTeam->id }}" {{ old('league_team_id') == $leagueTeam->id ? 'selected' : '' }}>
                                {{ $leagueTeam->team->name }} ({{ ucfirst($leagueTeam->status) }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500">
                        If you don't select a team, players will be available for auction
                    </p>
                    @error('league_team_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="base_price" class="block text-sm font-medium text-gray-700 mb-2">
                        Default Base Price <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">₹</span>
                        </div>
                        <input type="number" 
                               name="base_price" 
                               id="base_price" 
                               min="0"
                               step="0.01"
                               value="{{ old('base_price', 1000) }}"
                               placeholder="1000.00"
                               required
                               class="w-full pl-8 border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('base_price') border-red-500 @enderror">
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        This base price will be applied to all selected players
                    </p>
                    @error('base_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror">
                        <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="available" {{ old('status', 'available') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="sold" {{ old('status') === 'sold' ? 'selected' : '' }}>Sold</option>
                        <option value="unsold" {{ old('status') === 'unsold' ? 'selected' : '' }}>Unsold</option>
                        <option value="skip" {{ old('status') === 'skip' ? 'selected' : '' }}>Skip</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-medium text-gray-900">Select Players</h2>
                        <div class="flex space-x-2">
                            <div class="relative">
                                <input type="text" id="playerSearch" placeholder="Search players..." 
                                       class="border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <button type="button" id="selectAllBtn" class="bg-gray-100 px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-200">
                                Select All
                            </button>
                            <button type="button" id="clearAllBtn" class="bg-gray-100 px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-200">
                                Clear All
                            </button>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg max-h-96 overflow-y-auto">
                        <div class="space-y-2" id="playerCheckboxContainer">
                            @if($availablePlayers->isEmpty())
                                <p class="text-center text-gray-500 py-4">All players are already added to this league.</p>
                            @else
                                @foreach($availablePlayers as $player)
                                    <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 player-item">
                                        <input type="checkbox" 
                                               name="user_ids[]" 
                                               id="player-{{ $player->id }}" 
                                               value="{{ $player->id }}"
                                               class="player-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="player-{{ $player->id }}" class="ml-3 flex flex-col cursor-pointer">
                                            <span class="text-sm font-medium text-gray-900">{{ $player->name }}</span>
                                            <span class="text-xs text-gray-500">{{ $player->role->name ?? 'No Role' }} • {{ $player->email }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-2 text-sm text-gray-600">
                        Selected players: <span id="selectedCount">0</span>
                    </div>
                    
                    @error('user_ids')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('league-players.index', $league) }}" 
                       class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
                        Add Selected Players
                    </button>
                </div>
            </form>
        </div>
        
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const playerSearch = document.getElementById('playerSearch');
        const playerItems = document.querySelectorAll('.player-item');
        const playerCheckboxes = document.querySelectorAll('.player-checkbox');
        const selectAllBtn = document.getElementById('selectAllBtn');
        const clearAllBtn = document.getElementById('clearAllBtn');
        const selectedCountElem = document.getElementById('selectedCount');
        
        // Update selected count
        function updateSelectedCount() {
            const selectedCount = document.querySelectorAll('.player-checkbox:checked').length;
            selectedCountElem.textContent = selectedCount;
        }
        
        // Search functionality
        playerSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            playerItems.forEach(item => {
                const labelText = item.querySelector('label').textContent.toLowerCase();
                if (labelText.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
        
        // Select all button
        selectAllBtn.addEventListener('click', function() {
            playerCheckboxes.forEach(checkbox => {
                const item = checkbox.closest('.player-item');
                if (item.style.display !== 'none') {
                    checkbox.checked = true;
                }
            });
            updateSelectedCount();
        });
        
        // Clear all button
        clearAllBtn.addEventListener('click', function() {
            playerCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelectedCount();
        });
        
        // Individual checkbox change
        playerCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });
        
        // Initial count update
        updateSelectedCount();
    });
</script>
@endsection

@endsection
