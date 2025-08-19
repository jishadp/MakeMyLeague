@extends('layouts.app')

@section('title', 'Add Player - ' . $league->name)

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Add Player to League</h1>
                    <p class="text-gray-600 mt-1">{{ $league->name }}</p>
                </div>
                <a href="{{ route('league-players.index', $league) }}" 
                   class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200">
                    Back to Players
                </a>
            </div>

            <form action="{{ route('league-players.store', $league) }}" method="POST" class="space-y-6">
                @csrf

                <!-- Retention Player Checkbox - At the top -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               name="retention" 
                               id="retention" 
                               value="1"
                               {{ old('retention') ? 'checked' : '' }}
                               class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded transition-all duration-200">
                        <label for="retention" class="ml-3 block text-lg font-semibold text-blue-900">
                            Retention Player
                        </label>
                    </div>
                    <p class="mt-2 text-sm text-blue-700">
                        Check this if this player is being retained from the previous season. Retention players must be assigned to a team and do not require a base price.
                    </p>
                    @error('retention')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Team Selection - Only shown for retention players -->
                <div id="team-selection" class="transition-all duration-300 ease-in-out" style="display: none;">
                    <label for="league_team_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Team <span class="text-red-500">*</span>
                    </label>
                    <select name="league_team_id" id="league_team_id" required
                            class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('league_team_id') border-red-500 @enderror">
                        <option value="">Choose a team...</option>
                        @foreach($leagueTeams as $leagueTeam)
                            <option value="{{ $leagueTeam->id }}" {{ old('league_team_id') == $leagueTeam->id ? 'selected' : '' }}>
                                {{ $leagueTeam->team->name }} ({{ ucfirst($leagueTeam->status) }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500">
                        <span class="text-red-600 font-medium">Team selection is required for retention players.</span>
                    </p>
                    @error('league_team_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if($leagueTeams->isEmpty())
                        <p class="mt-1 text-sm text-yellow-600">No teams are available in this league.</p>
                    @endif
                </div>

                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Player <span class="text-red-500">*</span>
                    </label>
                    <select name="user_id" id="user_id" required
                            class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('user_id') border-red-500 @enderror">
                        <option value="">Choose a player...</option>
                        @foreach($availablePlayers as $player)
                            <option value="{{ $player->id }}" {{ old('user_id') == $player->id ? 'selected' : '' }}>
                                {{ $player->name }} ({{ $player->position->name ?? 'No Role' }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if($availablePlayers->isEmpty())
                        <p class="mt-1 text-sm text-yellow-600">All players are already added to this league.</p>
                    @endif
                </div>

                <!-- Base Price - Hidden for retention players -->
                <div id="base-price-section" class="transition-all duration-300 ease-in-out">
                    <label for="base_price" class="block text-sm font-medium text-gray-700 mb-2">
                        Base Price <span class="text-red-500 non-retention-required">*</span><span class="text-gray-500 retention-hidden" style="display: none;">(not required for retention players)</span>
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
                               class="w-full pl-8 border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('base_price') border-red-500 @enderror">
                    </div>
                    <p class="mt-1 text-sm text-gray-500 non-retention-required">
                        Set the base price for auction bidding
                    </p>
                    <p class="mt-1 text-sm text-gray-500 retention-hidden" style="display: none;">
                        Base price is not required for retention players
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
                            class="select2 w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror">
                        <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="available" {{ old('status') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="sold" {{ old('status') === 'sold' ? 'selected' : '' }}>Sold</option>
                        <option value="unsold" {{ old('status') === 'unsold' ? 'selected' : '' }}>Unsold</option>
                        <option value="skip" {{ old('status') === 'skip' ? 'selected' : '' }}>Skip</option>
                    </select>
                    @error('status')
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
                        Add Player
                    </button>
                </div>
            </form>
        </div>
        
    </div>
</div>

<style>
/* Custom Select2 styling to match the app theme */
.select2-container--default .select2-selection--single {
    height: 42px;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    background-color: #ffffff;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 40px;
    padding-left: 12px;
    padding-right: 40px;
    color: #374151;
    font-size: 14px;
}

.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #9ca3af;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px;
    width: 30px;
}

.select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: #6b7280 transparent transparent transparent;
    border-style: solid;
    border-width: 5px 4px 0 4px;
    height: 0;
    left: 50%;
    margin-left: -4px;
    margin-top: -2px;
    position: absolute;
    top: 50%;
    width: 0;
}

.select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
    border-color: transparent transparent #6b7280 transparent;
    border-width: 0 4px 5px 4px;
}

.select2-container--default .select2-dropdown {
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.select2-container--default .select2-results__option {
    padding: 8px 12px;
    font-size: 14px;
    color: #374151;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #6366f1;
    color: #ffffff;
}

.select2-container--default .select2-results__option[aria-selected=true] {
    background-color: #f3f4f6;
    color: #374151;
}

.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.select2-container--default.select2-container--error .select2-selection--single {
    border-color: #ef4444;
}

/* Smooth transitions */
.select2-container {
    transition: all 0.2s ease-in-out;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const retentionCheckbox = document.getElementById('retention');
    const teamSelection = document.getElementById('team-selection');
    const basePriceSection = document.getElementById('base-price-section');
    const basePriceInput = document.getElementById('base_price');
    const teamSelect = document.getElementById('league_team_id');
    
    // Elements for conditional display
    const nonRetentionRequired = document.querySelectorAll('.non-retention-required');
    const retentionHidden = document.querySelectorAll('.retention-hidden');
    
    function updateFormDisplay() {
        const isRetention = retentionCheckbox.checked;
        
        // Team selection logic - Only show for retention players
        if (isRetention) {
            teamSelection.style.display = 'block';
            teamSelect.required = true;
        } else {
            teamSelection.style.display = 'none';
            teamSelect.required = false;
            teamSelect.value = ''; // Clear selection when hidden
        }
        
        // Base price logic
        if (isRetention) {
            basePriceSection.style.display = 'none';
            basePriceInput.required = false;
            basePriceInput.value = '0';
            nonRetentionRequired.forEach(el => el.style.display = 'none');
            retentionHidden.forEach(el => el.style.display = 'inline');
        } else {
            basePriceSection.style.display = 'block';
            basePriceInput.required = true;
            if (basePriceInput.value === '0') {
                basePriceInput.value = '1000';
            }
            nonRetentionRequired.forEach(el => el.style.display = 'inline');
            retentionHidden.forEach(el => el.style.display = 'none');
        }
    }
    
    // Initialize Select2 for all select elements
    $('.select2').select2({
        width: '100%',
        placeholder: 'Choose an option...',
        allowClear: true,
        theme: 'default'
    });
    
    // Initial setup
    updateFormDisplay();
    
    // Event listener for checkbox changes
    retentionCheckbox.addEventListener('change', function() {
        updateFormDisplay();
        
        // Reinitialize Select2 for team selection when it becomes visible
        if (retentionCheckbox.checked) {
            setTimeout(() => {
                $('#league_team_id').select2({
                    width: '100%',
                    placeholder: 'Choose a team...',
                    allowClear: true,
                    theme: 'default'
                });
            }, 300); // Wait for transition
        }
    });
});
</script>
@endsection
