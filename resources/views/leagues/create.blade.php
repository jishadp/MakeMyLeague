@extends('layouts.app')

@section('title', 'Create League')

@section('content')
    <div class="max-w-6xl mx-auto py-6 sm:py-12 px-4 sm:px-6 lg:px-10">
        <h1 class="text-2xl sm:text-3xl font-bold mb-6 sm:mb-10 text-gray-800">🏆 Create New League</h1>

        <form action="{{ route('leagues.store') }}" method="POST" id="leagueForm" class="space-y-6 sm:space-y-10">
            @csrf

            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Basic Info --}}
            <div class="bg-white shadow-md rounded-2xl p-4 sm:p-8 border border-gray-100">
                <h2 class="text-lg font-semibold mb-6 sm:mb-8 flex items-center gap-2">
                    <span class="text-indigo-500">📄</span> Basic Information
                </h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-8">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">League Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            minlength="3"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="game_id" class="block text-sm font-medium text-gray-700 mb-1">Game Type *</label>
                        <select id="game_id" name="game_id" required
                            class="select2 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                            @foreach ($games as $game)
                                <option value="{{ $game->id }}" {{ old('game_id') == $game->id ? 'selected' : '' }}>
                                    {{ $game->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('game_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="season" class="block text-sm font-medium text-gray-700 mb-1">Season Number (1-100)
                            *</label>
                        <input type="number" id="season" name="season" value="{{ old('season', 1) }}" min="1"
                            max="100" required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                        @error('season')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                        <select id="status" name="status" required
                            class="select2 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                            <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active
                            </option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                        <div class="relative">

                            <input type="text" name="start_date" id="start_date" value="{{ old('start_date') }}"
                                required placeholder="Select start date" autocomplete="off"
                                class="flatpickr w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4 pr-10 cursor-pointer">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        @error('start_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date *</label>
                        <div class="relative">
                            <input type="text" name="end_date" id="end_date" value="{{ old('end_date') }}" required
                                readonly placeholder="Select end date" autocomplete="off"
                                class="flatpickr w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4 pr-10 cursor-pointer">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        @error('end_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Ground & Venue --}}
            <div class="bg-white shadow-md rounded-2xl p-4 sm:p-8 border border-gray-100">
                <h2 class="text-lg font-semibold mb-6 sm:mb-8 flex items-center gap-2">
                    <span class="text-red-500">🏟️</span> Ground & Venue Details
                </h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-8 mb-6">
                    <div>
                        <label for="localbody_id" class="block text-sm font-medium text-gray-700 mb-1">Local
                            Body</label>
                        <select id="localbody_id" name="localbody_id"
                            class="select2 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                            <option value="">Select Local Body</option>
                            @foreach ($localBodies as $localBody)
                                <option value="{{ $localBody->id }}"
                                    {{ old('localbody_id') == $localBody->id ? 'selected' : '' }}>
                                    {{ $localBody->name }}, {{ $localBody->district->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('localbody_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="venue_details" class="block text-sm font-medium text-gray-700 mb-1">Additional Venue
                            Details</label>
                        <input type="text" name="venue_details" id="venue_details"
                            value="{{ old('venue_details') }}" placeholder="E.g., Near bus stand, Main entrance gate"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                        @error('venue_details')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Select Grounds for this League</label>
                    
                    {{-- Search Input --}}
                    <div class="relative mb-4">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" id="ground-search" placeholder="Search grounds by name, location, or capacity..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                    </div>

                    {{-- Selected Grounds Tags --}}
                    <div id="selected-grounds" class="mb-4 min-h-[50px] p-3 border border-gray-200 rounded-lg bg-gray-50">
                        <div class="flex flex-wrap gap-2" id="ground-tags">
                            <!-- Selected ground tags will appear here -->
                        </div>
                        <div id="no-grounds-selected" class="text-gray-500 text-sm italic">
                            No grounds selected yet. Search and select from the list below.
                        </div>
                    </div>

                    {{-- Grounds List --}}
                    <div id="grounds-list" class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg">
                        @foreach ($grounds as $ground)
                            <div class="ground-item p-3 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 cursor-pointer transition-colors"
                                data-ground-id="{{ $ground->id }}"
                                data-ground-name="{{ $ground->name }}"
                                data-ground-location="{{ $ground->localBody->name }}, {{ $ground->district->name }}"
                                data-ground-capacity="{{ $ground->capacity ? number_format($ground->capacity) : 'N/A' }}"
                                data-search-text="{{ strtolower($ground->name . ' ' . $ground->localBody->name . ' ' . $ground->district->name . ' ' . ($ground->capacity ? number_format($ground->capacity) : '')) }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-800">{{ $ground->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $ground->localBody->name }}, {{ $ground->district->name }}</div>
                                        @if ($ground->capacity)
                                            <div class="text-xs text-gray-400">Capacity: {{ number_format($ground->capacity) }}</div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Hidden inputs for form submission --}}
                    <div id="ground-inputs">
                        @if(is_array(old('ground_ids')))
                            @foreach(old('ground_ids') as $groundId)
                                <input type="hidden" name="ground_ids[]" value="{{ $groundId }}">
                            @endforeach
                        @endif
                    </div>

                    @error('ground_ids')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Team Settings --}}
            <div class="bg-white shadow-md rounded-2xl p-4 sm:p-8 border border-gray-100">
                <h2 class="text-lg font-semibold mb-6 sm:mb-8 flex items-center gap-2">
                    <span class="text-blue-500">👥</span> Team Settings
                </h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-8">
                    <div>
                        <label for="max_teams" class="block text-sm font-medium text-gray-700 mb-1">Maximum Teams
                            *</label>
                        <input type="number" id="max_teams" name="max_teams" value="{{ old('max_teams', 8) }}"
                            min="2" required onchange="updateTotalPlayers()"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                        @error('max_teams')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="max_team_players" class="block text-sm font-medium text-gray-700 mb-1">Players per
                            Team *</label>
                        <input type="number" id="max_team_players" name="max_team_players"
                            value="{{ old('max_team_players', 15) }}" min="1" required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4"
                            onchange="updateTotalPlayers()">
                        <p class="text-sm text-gray-500 mt-1">Total players capacity: <span id="total-players">120</span></p>
                        @error('max_team_players')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Fees --}}
            <div class="bg-white shadow-md rounded-2xl p-4 sm:p-8 border border-gray-100">
                <h2 class="text-lg font-semibold mb-6 sm:mb-8 flex items-center gap-2">
                    <span class="text-green-500">💰</span> Registration & Wallet
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-8">
                    <div>
                        <label for="team_reg_fee" class="block text-sm font-medium text-gray-700 mb-1">Team Reg Fee
                            (₹)
                            *</label>
                        <input type="number" name="team_reg_fee" id="team_reg_fee"
                            value="{{ old('team_reg_fee', 0) }}" min="0" step="0.01" required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                        @error('team_reg_fee')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="player_reg_fee" class="block text-sm font-medium text-gray-700 mb-1">Player Reg
                            Fee
                            (₹) *</label>
                        <input type="number" name="player_reg_fee" id="player_reg_fee"
                            value="{{ old('player_reg_fee', 0) }}" min="0" step="0.01" required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                        @error('player_reg_fee')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="team_wallet_limit" class="block text-sm font-medium text-gray-700 mb-1">Team
                            Wallet
                            Limit (₹) *</label>
                        <input type="number" name="team_wallet_limit" id="team_wallet_limit"
                            value="{{ old('team_wallet_limit', 0) }}" min="0" step="0.01" required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                        @error('team_wallet_limit')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Logo & Banner --}}
            <div class="bg-white shadow-md rounded-2xl p-4 sm:p-8 border border-gray-100">
                <h2 class="text-lg font-semibold mb-6 sm:mb-8 flex items-center gap-2">
                    <span class="text-purple-500">🖼️</span> Logo & Banner (Optional)
                </h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-8">
                    {{-- Logo Upload --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">League Logo</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <input type="file" id="create-logo-upload" accept="image/*" class="hidden">
                            <button type="button" onclick="document.getElementById('create-logo-upload').click()" class="text-indigo-600 hover:text-indigo-800">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">Click to upload logo</p>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB (1:1 ratio recommended)</p>
                            </button>
                        </div>
                    </div>

                    {{-- Banner Upload --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">League Banner</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <input type="file" id="create-banner-upload" accept="image/*" class="hidden">
                            <button type="button" onclick="document.getElementById('create-banner-upload').click()" class="text-indigo-600 hover:text-indigo-800">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">Click to upload banner</p>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB (wide format recommended)</p>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Prize Information --}}
            <div class="bg-white shadow-md rounded-2xl p-4 sm:p-8 border border-gray-100">
                <h2 class="text-lg font-semibold mb-6 sm:mb-8 flex items-center gap-2">
                    <span class="text-yellow-500">🏆</span> Prize Information (Optional)
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-8">
                    <div>
                        <label for="winner_prize" class="block text-sm font-medium text-gray-700 mb-1">Winner Prize (₹)</label>
                        <input type="number" name="winner_prize" id="winner_prize"
                            value="{{ old('winner_prize') }}" min="0" step="0.01"
                            placeholder="Enter winner prize amount"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                        @error('winner_prize')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="runner_prize" class="block text-sm font-medium text-gray-700 mb-1">Runner-up Prize (₹)</label>
                        <input type="number" name="runner_prize" id="runner_prize"
                            value="{{ old('runner_prize') }}" min="0" step="0.01"
                            placeholder="Enter runner-up prize amount"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                        @error('runner_prize')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Retention --}}
            <div class="bg-white shadow-md rounded-2xl p-4 sm:p-8 border border-gray-100">
                <h2 class="text-lg font-semibold mb-6 sm:mb-8 flex items-center gap-2">
                    <span class="text-yellow-500">🔒</span> Player Retention
                </h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-8">
                    <div>
                        <div class="flex items-center gap-4">
                            <input type="checkbox" name="retention" id="retention" value="1"
                                {{ old('retention') ? 'checked' : '' }}
                                class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="retention" class="text-sm text-gray-700">Allow Player Retention</label>
                        </div>
                        @error('retention')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="retention_players" class="block text-sm font-medium text-gray-700 mb-1">Max
                            Retention
                            Players</label>
                        <input type="number" id="retention_players" name="retention_players"
                            value="{{ old('retention_players', 0) }}" min="0"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                        @error('retention_players')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Advanced Settings --}}
            <div class="bg-white shadow-md rounded-2xl p-4 sm:p-8 border border-gray-100">
                <h2 class="text-lg font-semibold mb-6 sm:mb-8 flex items-center gap-2">
                    <span class="text-purple-500">⚙️</span> Advanced Settings
                </h2>
                <div class="flex items-center gap-4">
                    <input type="checkbox" name="is_default" id="is_default" value="1"
                        {{ old('is_default') ? 'checked' : '' }}
                        class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_default" class="text-sm text-gray-700">Set as Default League</label>
                    @error('is_default')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <a href="{{ route('leagues.index') }}"
                    class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg shadow hover:bg-gray-300 text-center">
                    Cancel
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    Save League
                </button>
            </div>
        </form>
    </div>

@endsection
@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Additional custom styles if needed */
    </style>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize Select2 for all select elements
            $('.select2').select2({
                theme: 'default',
                width: '100%',
                placeholder: function() {
                    return $(this).find('option:first').text();
                }
            });
            
            // Flatpickr is now initialized in app.js via Vite
            
            // Initialize ground selection functionality
            initializeGroundSelection();
            
            // Initialize total players calculation
            updateTotalPlayers();
        });
        
        function updateTotalPlayers() {
            const maxTeams = document.getElementById('max_teams').value || 0;
            const maxTeamPlayers = document.getElementById('max_team_players').value || 0;
            const totalPlayers = maxTeams * maxTeamPlayers;
            document.getElementById('total-players').textContent = totalPlayers;
        }
        
        // Add event listener to max_teams as well
        document.getElementById('max_teams').addEventListener('change', updateTotalPlayers);
        
        // Ground Selection Functionality
        function initializeGroundSelection() {
            const searchInput = document.getElementById('ground-search');
            const groundsList = document.getElementById('grounds-list');
            const groundItems = groundsList.querySelectorAll('.ground-item');
            const selectedGrounds = new Set();
            const groundInputs = document.getElementById('ground-inputs');
            const groundTags = document.getElementById('ground-tags');
            const noGroundsSelected = document.getElementById('no-grounds-selected');
            
            // Load previously selected grounds from old input
            const oldInputs = groundInputs.querySelectorAll('input[name="ground_ids[]"]');
            oldInputs.forEach(input => {
                const groundId = input.value;
                selectedGrounds.add(groundId);
                const groundItem = groundsList.querySelector(`[data-ground-id="${groundId}"]`);
                if (groundItem) {
                    addGroundTag(groundItem);
                    groundItem.classList.add('bg-indigo-50', 'border-indigo-200');
                }
            });
            
            // Search functionality
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                groundItems.forEach(item => {
                    const searchText = item.getAttribute('data-search-text');
                    if (searchText.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
            
            // Ground selection
            groundItems.forEach(item => {
                item.addEventListener('click', function() {
                    const groundId = this.getAttribute('data-ground-id');
                    
                    if (selectedGrounds.has(groundId)) {
                        // Remove ground
                        selectedGrounds.delete(groundId);
                        this.classList.remove('bg-indigo-50', 'border-indigo-200');
                        removeGroundTag(groundId);
                        removeGroundInput(groundId);
                    } else {
                        // Add ground
                        selectedGrounds.add(groundId);
                        this.classList.add('bg-indigo-50', 'border-indigo-200');
                        addGroundTag(this);
                        addGroundInput(groundId);
                    }
                    
                    updateNoGroundsMessage();
                });
            });
            
            function addGroundTag(groundItem) {
                const groundId = groundItem.getAttribute('data-ground-id');
                const groundName = groundItem.getAttribute('data-ground-name');
                const groundLocation = groundItem.getAttribute('data-ground-location');
                const groundCapacity = groundItem.getAttribute('data-ground-capacity');
                
                const tag = document.createElement('div');
                tag.className = 'inline-flex items-center gap-2 px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium';
                tag.innerHTML = `
                    <span>${groundName}</span>
                    <span class="text-xs text-indigo-600">(${groundLocation})</span>
                    <button type="button" onclick="removeGround('${groundId}')" class="ml-1 text-indigo-600 hover:text-indigo-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                tag.setAttribute('data-ground-id', groundId);
                groundTags.appendChild(tag);
            }
            
            function removeGroundTag(groundId) {
                const tag = groundTags.querySelector(`[data-ground-id="${groundId}"]`);
                if (tag) {
                    tag.remove();
                }
            }
            
            function addGroundInput(groundId) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ground_ids[]';
                input.value = groundId;
                groundInputs.appendChild(input);
            }
            
            function removeGroundInput(groundId) {
                const input = groundInputs.querySelector(`input[value="${groundId}"]`);
                if (input) {
                    input.remove();
                }
            }
            
            function updateNoGroundsMessage() {
                if (selectedGrounds.size === 0) {
                    noGroundsSelected.style.display = 'block';
                } else {
                    noGroundsSelected.style.display = 'none';
                }
            }
            
            // Global function for removing grounds from tags
            window.removeGround = function(groundId) {
                selectedGrounds.delete(groundId);
                const groundItem = groundsList.querySelector(`[data-ground-id="${groundId}"]`);
                if (groundItem) {
                    groundItem.classList.remove('bg-indigo-50', 'border-indigo-200');
                }
                removeGroundTag(groundId);
                removeGroundInput(groundId);
                updateNoGroundsMessage();
            };
            
            // Initialize no grounds message
            updateNoGroundsMessage();
        }
    </script>
    
    <style>
        /* Select2 Custom Styling to Match Theme */
        .select2-container--default .select2-selection--single {
            background-color: white !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.5rem !important;
            height: 48px !important;
            padding: 0 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #374151 !important;
            line-height: 46px !important;
            padding-left: 16px !important;
            padding-right: 20px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px !important;
            right: 8px !important;
            top: 1px !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
            outline: none !important;
        }

        .select2-container--default .select2-selection--single:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
        }

        .select2-dropdown {
            border: 1px solid #d1d5db !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        }

        .select2-container--default .select2-results__option {
            padding: 12px 16px !important;
            color: #374151 !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #6366f1 !important;
            color: white !important;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #e5e7eb !important;
            color: #374151 !important;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #d1d5db !important;
            border-radius: 0.5rem !important;
            padding: 8px 12px !important;
            margin: 8px !important;
        }

        /* Disabled state styling */
        .select2-container--default .select2-selection--single[aria-disabled=true] {
            background-color: #f9fafb !important;
            border-color: #d1d5db !important;
            color: #9ca3af !important;
        }

        .select2-container--default .select2-selection--single[aria-disabled=true] .select2-selection__rendered {
            color: #9ca3af !important;
        }
    </style>

    <script>
        // League Create Image Upload and Cropper.js functionality
        let createLogoCropper, createBannerCropper;
        let currentCreateUploadType = '';
        let createLogoFile = null, createBannerFile = null;

        // Create logo upload handler
        document.getElementById('create-logo-upload').addEventListener('change', function(e) {
            handleCreateImageUpload(e, 'logo');
        });

        // Create banner upload handler
        document.getElementById('create-banner-upload').addEventListener('change', function(e) {
            handleCreateImageUpload(e, 'banner');
        });

        function handleCreateImageUpload(event, type) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file size
            const maxSize = type === 'logo' ? 2 * 1024 * 1024 : 5 * 1024 * 1024; // 2MB for logo, 5MB for banner
            if (file.size > maxSize) {
                alert(`File size must be less than ${type === 'logo' ? '2MB' : '5MB'}`);
                return;
            }

            currentCreateUploadType = type;
            const reader = new FileReader();
            reader.onload = function(e) {
                showCreateCropperModal(e.target.result, type);
            };
            reader.readAsDataURL(file);
        }

        function showCreateCropperModal(imageSrc, type) {
            // Create modal HTML
            const modalHtml = `
                <div id="create-cropper-modal" class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                        <div class="flex items-center justify-between p-4 border-b">
                            <h3 class="text-lg font-semibold text-gray-900">Crop ${type === 'logo' ? 'Logo' : 'Banner'}</h3>
                            <button type="button" onclick="closeCreateCropperModal()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <div class="mb-4">
                                <img id="create-cropper-image" src="${imageSrc}" style="max-width: 100%; max-height: 400px;">
                            </div>
                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="closeCreateCropperModal()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Cancel
                                </button>
                                <button type="button" onclick="cropAndSaveCreate()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                    Crop & Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // Initialize cropper
            const image = document.getElementById('create-cropper-image');
            
            if (type === 'logo') {
                createLogoCropper = new Cropper(image, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.8,
                    restore: false,
                    guides: false,
                    center: false,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                });
            } else {
                createBannerCropper = new Cropper(image, {
                    aspectRatio: 16/9,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.8,
                    restore: false,
                    guides: false,
                    center: false,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                });
            }
        }

        function closeCreateCropperModal() {
            const modal = document.getElementById('create-cropper-modal');
            if (modal) {
                modal.remove();
            }
            if (createLogoCropper) {
                createLogoCropper.destroy();
                createLogoCropper = null;
            }
            if (createBannerCropper) {
                createBannerCropper.destroy();
                createBannerCropper = null;
            }
            currentCreateUploadType = '';
        }

        function cropAndSaveCreate() {
            const cropper = currentCreateUploadType === 'logo' ? createLogoCropper : createBannerCropper;
            if (!cropper) return;

            // Get cropped canvas
            const canvas = cropper.getCroppedCanvas({
                width: currentCreateUploadType === 'logo' ? 300 : 800,
                height: currentCreateUploadType === 'logo' ? 300 : 450,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            // Convert to blob and store for form submission
            canvas.toBlob(function(blob) {
                if (currentCreateUploadType === 'logo') {
                    createLogoFile = blob;
                } else {
                    createBannerFile = blob;
                }
                
                // Update the UI to show the cropped image
                const uploadArea = document.querySelector(`#create-${currentCreateUploadType}-upload`).parentElement;
                uploadArea.innerHTML = `
                    <div class="text-center">
                        <div class="inline-block p-2 bg-green-100 rounded-lg">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="mt-2 text-sm text-green-600 font-medium">${currentCreateUploadType === 'logo' ? 'Logo' : 'Banner'} ready for upload</p>
                        <button type="button" onclick="removeCreateImage('${currentCreateUploadType}')" class="text-red-600 text-xs hover:text-red-800 mt-1">
                            Remove
                        </button>
                    </div>
                `;
                
                closeCreateCropperModal();
            }, 'image/jpeg', 0.8);
        }

        function removeCreateImage(type) {
            if (type === 'logo') {
                createLogoFile = null;
            } else {
                createBannerFile = null;
            }
            
            // Reset the upload area
            const uploadArea = document.querySelector(`#create-${type}-upload`).parentElement;
            uploadArea.innerHTML = `
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                    <input type="file" id="create-${type}-upload" accept="image/*" class="hidden">
                    <button type="button" onclick="document.getElementById('create-${type}-upload').click()" class="text-indigo-600 hover:text-indigo-800">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">Click to upload ${type}</p>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to ${type === 'logo' ? '2MB' : '5MB'} (${type === 'logo' ? '1:1 ratio' : 'wide format'} recommended)</p>
                    </button>
                </div>
            `;
            
            // Re-attach event listener
            document.getElementById(`create-${type}-upload`).addEventListener('change', function(e) {
                handleCreateImageUpload(e, type);
            });
        }

        // Modify form submission to include cropped images
        document.getElementById('leagueForm').addEventListener('submit', function(e) {
            // Create a new FormData object
            const formData = new FormData(this);
            
            // Add cropped images if they exist
            if (createLogoFile) {
                formData.append('logo', createLogoFile, 'logo.jpg');
            }
            
            if (createBannerFile) {
                formData.append('banner', createBannerFile, 'banner.jpg');
            }
            
            // Prevent default form submission
            e.preventDefault();
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Creating League...';
            submitBtn.disabled = true;
            
            // Submit via fetch
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.ok) {
                    // If successful, redirect to leagues index
                    window.location.href = '{{ route("leagues.index") }}';
                    return;
                }
                
                // If validation error (422), parse and display errors
                if (response.status === 422) {
                    return response.json().then(data => {
                        // Display validation errors
                        let errorHtml = '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md"><ul class="list-disc pl-5">';
                        if (data.errors) {
                            Object.values(data.errors).forEach(errors => {
                                errors.forEach(error => {
                                    errorHtml += `<li>${error}</li>`;
                                });
                            });
                        } else {
                            errorHtml += '<li>Validation failed. Please check your input.</li>';
                        }
                        errorHtml += '</ul></div>';
                        
                        // Remove any existing error messages
                        const existingErrors = document.querySelector('.bg-red-100');
                        if (existingErrors) {
                            existingErrors.remove();
                        }
                        
                        // Add new error messages at the top of the form
                        const form = document.getElementById('leagueForm');
                        form.insertAdjacentHTML('afterbegin', errorHtml);
                        
                        // Scroll to top to show errors
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }).catch(() => {
                        alert('Validation failed. Please check your input and try again.');
                    });
                } else {
                    throw new Error('Network response was not ok');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to create league. Please try again.');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    </script>

    <!-- Include Cropper.js CSS and JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
@endsection

