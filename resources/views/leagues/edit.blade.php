@extends('layouts.app')

@section('title', 'Edit League')

@section('content')
    <div class="max-w-6xl mx-auto py-6 sm:py-12 px-4 sm:px-6 lg:px-10">
        <h1 class="text-2xl sm:text-3xl font-bold mb-6 sm:mb-10 text-gray-800">🏆 Edit League</h1>

        <form action="{{ route('leagues.update', $league) }}" method="POST" id="leagueForm" class="space-y-6 sm:space-y-10">
            @csrf
            @method('PUT')

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
                        <input type="text" name="name" id="name" value="{{ old('name', $league->name) }}"
                            required minlength="3"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="game_id" class="block text-sm font-medium text-gray-700 mb-1">Game Type *</label>
                        <select id="game_id" name="game_id" required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                            @foreach ($games as $game)
                                <option value="{{ $game->id }}"
                                    {{ old('game_id', $league->game_id) == $game->id ? 'selected' : '' }}>
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
                        <input type="number" id="season" name="season" value="{{ old('season', $league->season) }}"
                            min="1" max="100" required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                        @error('season')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                        <select id="status" name="status" required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                            <option value="pending" {{ old('status', $league->status) == 'pending' ? 'selected' : '' }}>
                                Pending</option>
                            <option value="active" {{ old('status', $league->status) == 'active' ? 'selected' : '' }}>
                                Active</option>
                            <option value="completed"
                                {{ old('status', $league->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled"
                                {{ old('status', $league->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                        <div class="relative">
                            <input type="text" name="start_date" id="start_date"
                                value="{{ old('start_date', $league->start_date->format('Y-m-d')) }}" required readonly
                                placeholder="Select start date"
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
                            <input type="text" name="end_date" id="end_date"
                                value="{{ old('end_date', $league->end_date->format('Y-m-d')) }}" required readonly
                                placeholder="Select end date"
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
            <div class="bg-white shadow-md rounded-2xl p-8 border border-gray-100">
                <h2 class="text-lg font-semibold mb-8 flex items-center gap-2">
                    <span class="text-red-500">🏟️</span> Ground & Venue Details
                </h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-6">
                    <div>
                        <label for="localbody_id" class="block text-sm font-medium text-gray-700 mb-1">Local Body</label>
                        <select id="localbody_id" name="localbody_id"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                            <option value="">Select Local Body</option>
                            @foreach ($localBodies as $localBody)
                                <option value="{{ $localBody->id }}"
                                    {{ old('localbody_id', $league->localbody_id) == $localBody->id ? 'selected' : '' }}>
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
                            value="{{ old('venue_details', $league->venue_details) }}"
                            placeholder="E.g., Near bus stand, Main entrance gate"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                        @error('venue_details')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Select Grounds for this League</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($grounds as $ground)
                            <div
                                class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <input type="checkbox" name="ground_ids[]" id="ground_{{ $ground->id }}"
                                    value="{{ $ground->id }}"
                                    {{ in_array($ground->id, old('ground_ids', $league->grounds->pluck('id')->toArray())) ? 'checked' : '' }}
                                class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded mt-0.5">
                                <label for="ground_{{ $ground->id }}" class="text-sm cursor-pointer">
                                    <span class="font-medium block text-gray-800">{{ $ground->name }}</span>
                                    <span class="text-gray-500 text-xs block">{{ $ground->localBody->name }},
                                        {{ $ground->district->name }}</span>
                                    @if ($ground->capacity)
                                        <span class="text-gray-500 text-xs block">Capacity:
                                            {{ number_format($ground->capacity) }}</span>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('ground_ids')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Team Settings --}}
            <div class="bg-white shadow-md rounded-2xl p-8 border border-gray-100">
                <h2 class="text-lg font-semibold mb-8 flex items-center gap-2">
                    <span class="text-blue-500">👥</span> Team Settings
                </h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <label for="max_teams" class="block text-sm font-medium text-gray-700 mb-1">Maximum Teams
                            *</label>
                        <input type="number" id="max_teams" name="max_teams"
                            value="{{ old('max_teams', $league->max_teams) }}" min="2" required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4"
                            onchange="updateTotalPlayers()">
                        @error('max_teams')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="max_team_players" class="block text-sm font-medium text-gray-700 mb-1">Players per
                            Team *</label>
                        <input type="number" id="max_team_players" name="max_team_players"
                            value="{{ old('max_team_players', $league->max_team_players) }}" min="1" required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4"
                            onchange="updateTotalPlayers()">
                        <p class="text-sm text-gray-500 mt-1">Total players capacity: <span id="total-players">{{ $league->max_teams * $league->max_team_players }}</span></p>
                        @error('max_team_players')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Fees --}}
            <div class="bg-white shadow-md rounded-2xl p-8 border border-gray-100">
                <h2 class="text-lg font-semibold mb-8 flex items-center gap-2">
                    <span class="text-green-500">💰</span> Registration & Wallet
                </h2>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div>
                        <label for="team_reg_fee" class="block text-sm font-medium text-gray-700 mb-1">Team Reg Fee (₹)
                            *</label>
                        <input type="number" name="team_reg_fee" id="team_reg_fee"
                            value="{{ old('team_reg_fee', $league->team_reg_fee) }}" min="0" step="0.01"
                            required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                        @error('team_reg_fee')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="player_reg_fee" class="block text-sm font-medium text-gray-700 mb-1">Player Reg Fee
                            (₹) *</label>
                        <input type="number" name="player_reg_fee" id="player_reg_fee"
                            value="{{ old('player_reg_fee', $league->player_reg_fee) }}" min="0" step="0.01"
                            required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                        @error('player_reg_fee')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="team_wallet_limit" class="block text-sm font-medium text-gray-700 mb-1">Team Wallet
                            Limit (₹) *</label>
                        <input type="number" name="team_wallet_limit" id="team_wallet_limit"
                            value="{{ old('team_wallet_limit', $league->team_wallet_limit) }}" min="0"
                            step="0.01" required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                        @error('team_wallet_limit')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Retention --}}
            <div class="bg-white shadow-md rounded-2xl p-8 border border-gray-100">
                <h2 class="text-lg font-semibold mb-8 flex items-center gap-2">
                    <span class="text-yellow-500">🔒</span> Player Retention
                </h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <div class="flex items-center gap-4">
                            <input type="checkbox" name="retention" id="retention" value="1"
                                {{ old('retention', $league->retention) ? 'checked' : '' }}
                                class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="retention" class="text-sm text-gray-700">Allow Player Retention</label>
                        </div>
                        @error('retention')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="retention_players" class="block text-sm font-medium text-gray-700 mb-1">Max Retention
                            Players</label>
                        <input type="number" id="retention_players" name="retention_players"
                            value="{{ old('retention_players', $league->retention_players) }}" min="0"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                        @error('retention_players')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Advanced Settings --}}
            <div class="bg-white shadow-md rounded-2xl p-8 border border-gray-100">
                <h2 class="text-lg font-semibold mb-8 flex items-center gap-2">
                    <span class="text-purple-500">⚙️</span> Advanced Settings
                </h2>
                <div class="flex items-center gap-4">
                    <input type="checkbox" name="is_default" id="is_default" value="1"
                        {{ old('is_default', $league->is_default) ? 'checked' : '' }}
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
                    Update League
                </button>
            </div>
        </form>
    </div>

    <!-- Datepicker Modal -->
    <div id="datepicker-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-auto">
                <!-- Datepicker Header -->
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Select Date</h3>
                    <button type="button" class="close-datepicker text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Datepicker Content -->
                <div class="p-4">
                    <div id="datepicker-calendar" class="space-y-4">
                        <!-- Month/Year Navigation -->
                        <div class="flex items-center justify-between">
                            <button type="button" id="prev-month" class="p-2 hover:bg-gray-100 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <h4 id="current-month" class="text-lg font-medium text-gray-900"></h4>
                            <button type="button" id="next-month" class="p-2 hover:bg-gray-100 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Week Days -->
                        <div class="grid grid-cols-7 gap-1">
                            <div class="text-center text-sm font-medium text-gray-500 py-2">Sun</div>
                            <div class="text-center text-sm font-medium text-gray-500 py-2">Mon</div>
                            <div class="text-center text-sm font-medium text-gray-500 py-2">Tue</div>
                            <div class="text-center text-sm font-medium text-gray-500 py-2">Wed</div>
                            <div class="text-center text-sm font-medium text-gray-500 py-2">Thu</div>
                            <div class="text-center text-sm font-medium text-gray-500 py-2">Fri</div>
                            <div class="text-center text-sm font-medium text-gray-500 py-2">Sat</div>
                        </div>

                        <!-- Calendar Days -->
                        <div id="calendar-days" class="grid grid-cols-7 gap-1">
                            <!-- Days will be populated by JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Datepicker Footer -->
                <div class="flex items-center justify-between p-4 border-t">
                    <button type="button" class="close-datepicker px-4 py-2 text-gray-600 hover:text-gray-800">
                        Cancel
                    </button>
                    <button type="button" id="confirm-date"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/flatpickr/flatpickr.css') }}" />
@endsection

@section('scripts')
    <script src="{{ asset('assets/flatpickr/flatpickr.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            flatpickr('.flatpickr', {
                dateFormat: "Y-m-d",
                allowInput: true,
                enableTime: false
            });
        });
        
        function updateTotalPlayers() {
            const maxTeams = document.getElementById('max_teams').value || 0;
            const maxTeamPlayers = document.getElementById('max_team_players').value || 0;
            const totalPlayers = maxTeams * maxTeamPlayers;
            document.getElementById('total-players').textContent = totalPlayers;
        }
        
        // Add event listener to max_teams as well
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('max_teams').addEventListener('change', updateTotalPlayers);
        });
    </script>
@endsection
