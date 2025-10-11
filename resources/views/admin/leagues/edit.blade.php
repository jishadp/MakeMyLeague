@extends('layouts.app')

@section('title', 'Edit League - Admin')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('admin.leagues.index') }}" class="inline-flex items-center text-purple-600 hover:text-purple-700 font-medium mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Leagues
            </a>
            <h1 class="text-3xl font-black text-gray-900 mb-2">Edit League</h1>
            <p class="text-gray-600">Update league information</p>
        </div>

        <form action="{{ route('admin.leagues.update', $league) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-lg">
            @csrf
            @method('PUT')

            <div class="p-8 space-y-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- League Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">League Name *</label>
                            <input type="text" name="name" value="{{ old('name', $league->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Game -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Game *</label>
                            <select name="game_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('game_id') border-red-500 @enderror">
                                <option value="">Select Game</option>
                                @foreach($games as $game)
                                    <option value="{{ $game->id }}" {{ old('game_id', $league->game_id) == $game->id ? 'selected' : '' }}>{{ $game->name }}</option>
                                @endforeach
                            </select>
                            @error('game_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Season -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Season *</label>
                            <input type="number" name="season" value="{{ old('season', $league->season) }}" required min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('season') border-red-500 @enderror">
                            @error('season')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('status') border-red-500 @enderror">
                                <option value="pending" {{ old('status', $league->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="active" {{ old('status', $league->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ old('status', $league->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $league->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Dates -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Dates</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Start Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                            <input type="datetime-local" name="start_date" value="{{ old('start_date', \Carbon\Carbon::parse($league->start_date)->format('Y-m-d\TH:i')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('start_date') border-red-500 @enderror">
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date *</label>
                            <input type="datetime-local" name="end_date" value="{{ old('end_date', \Carbon\Carbon::parse($league->end_date)->format('Y-m-d\TH:i')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('end_date') border-red-500 @enderror">
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Location</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- State -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">State *</label>
                            <select id="state" name="state_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">Select State</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}" {{ old('state_id', $league->localBody->district->state_id) == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- District -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">District *</label>
                            <select id="district" name="district_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">Select District</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}" {{ old('district_id', $league->localBody->district_id) == $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Local Body -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Local Body *</label>
                            <select id="localbody" name="localbody_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('localbody_id') border-red-500 @enderror">
                                <option value="">Select Local Body</option>
                                @foreach($localBodies as $localBody)
                                    <option value="{{ $localBody->id }}" {{ old('localbody_id', $league->localbody_id) == $localBody->id ? 'selected' : '' }}>{{ $localBody->name }}</option>
                                @endforeach
                            </select>
                            @error('localbody_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- League Configuration -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">League Configuration</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Max Teams -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Teams *</label>
                            <input type="number" name="max_teams" value="{{ old('max_teams', $league->max_teams) }}" required min="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('max_teams') border-red-500 @enderror">
                            @error('max_teams')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Max Players Per Team -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Players Per Team *</label>
                            <input type="number" name="max_team_players" value="{{ old('max_team_players', $league->max_team_players) }}" required min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('max_team_players') border-red-500 @enderror">
                            @error('max_team_players')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Team Wallet Limit -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Team Wallet Limit (₹)</label>
                            <input type="number" name="team_wallet_limit" value="{{ old('team_wallet_limit', $league->team_wallet_limit) }}" min="0" step="1000" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>

                        <!-- Retention Players -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Retention Players</label>
                            <input type="number" name="retention_players" value="{{ old('retention_players', $league->retention_players) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <!-- Retention Checkbox -->
                    <div class="mt-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="retention" value="1" {{ old('retention', $league->retention) ? 'checked' : '' }} class="rounded border-gray-300 text-purple-600 shadow-sm focus:ring-purple-500">
                            <span class="ml-2 text-sm font-medium text-gray-700">Enable Player Retention</span>
                        </label>
                    </div>
                </div>

                <!-- Prizes & Fees -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Prizes & Fees</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Winner Prize -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Winner Prize (₹)</label>
                            <input type="number" name="winner_prize" value="{{ old('winner_prize', $league->winner_prize) }}" min="0" step="100" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>

                        <!-- Runner Prize -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Runner-up Prize (₹)</label>
                            <input type="number" name="runner_prize" value="{{ old('runner_prize', $league->runner_prize) }}" min="0" step="100" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>

                        <!-- Team Registration Fee -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Team Registration Fee (₹)</label>
                            <input type="number" name="team_reg_fee" value="{{ old('team_reg_fee', $league->team_reg_fee) }}" min="0" step="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>

                        <!-- Player Registration Fee -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Player Registration Fee (₹)</label>
                            <input type="number" name="player_reg_fee" value="{{ old('player_reg_fee', $league->player_reg_fee) }}" min="0" step="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Venue Details -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Venue Details</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Venue Details</label>
                        <textarea name="venue_details" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ old('venue_details', $league->venue_details) }}</textarea>
                    </div>
                </div>

                <!-- Images -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Images</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Logo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                            @if($league->logo)
                                <img src="{{ Storage::url($league->logo) }}" alt="Current Logo" class="w-32 h-32 object-cover rounded-lg mb-2">
                            @endif
                            <input type="file" name="logo" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>

                        <!-- Banner -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Banner</label>
                            @if($league->banner)
                                <img src="{{ Storage::url($league->banner) }}" alt="Current Banner" class="w-full h-32 object-cover rounded-lg mb-2">
                            @endif
                            <input type="file" name="banner" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="px-8 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-4">
                <a href="{{ route('admin.leagues.index') }}" class="px-6 py-2.5 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-bold shadow-lg hover:shadow-xl transition-all">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold shadow-lg hover:shadow-xl transition-all">
                    Update League
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Location cascading dropdowns
document.getElementById('state').addEventListener('change', function() {
    const stateId = this.value;
    const districtSelect = document.getElementById('district');
    const localbodySelect = document.getElementById('localbody');
    
    districtSelect.innerHTML = '<option value="">Select District</option>';
    localbodySelect.innerHTML = '<option value="">Select Local Body</option>';
    
    if (stateId) {
        fetch(`{{ route('admin.leagues.districts-by-state') }}?state_id=${stateId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(district => {
                    districtSelect.innerHTML += `<option value="${district.id}">${district.name}</option>`;
                });
            });
    }
});

document.getElementById('district').addEventListener('change', function() {
    const districtId = this.value;
    const localbodySelect = document.getElementById('localbody');
    
    localbodySelect.innerHTML = '<option value="">Select Local Body</option>';
    
    if (districtId) {
        fetch(`{{ route('admin.leagues.local-bodies-by-district') }}?district_id=${districtId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(localbody => {
                    localbodySelect.innerHTML += `<option value="${localbody.id}">${localbody.name}</option>`;
                });
            });
    }
});
</script>
@endsection

