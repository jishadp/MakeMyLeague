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

            {{-- Logo & Banner --}}
            <div class="bg-white shadow-md rounded-2xl p-8 border border-gray-100">
                <h2 class="text-lg font-semibold mb-8 flex items-center gap-2">
                    <span class="text-purple-500">🖼️</span> Logo & Banner
                </h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- Logo Upload --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">League Logo</label>
                        <div class="space-y-4">
                            @if($league->logo)
                                <div class="flex items-center space-x-4">
                                    <img src="{{ Storage::url($league->logo) }}" alt="Current Logo" class="w-16 h-16 object-cover rounded-lg border">
                                    <div>
                                        <p class="text-sm text-gray-600">Current Logo</p>
                                        <button type="button" onclick="removeLogo()" class="text-red-600 text-sm hover:text-red-800">Remove</button>
                                    </div>
                                </div>
                            @endif
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                                <input type="file" id="logo-upload" accept="image/*" class="hidden">
                                <button type="button" onclick="document.getElementById('logo-upload').click()" class="text-indigo-600 hover:text-indigo-800">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600">Click to upload logo</p>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB (1:1 ratio recommended)</p>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Banner Upload --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">League Banner</label>
                        <div class="space-y-4">
                            @if($league->banner)
                                <div class="flex items-center space-x-4">
                                    <img src="{{ Storage::url($league->banner) }}" alt="Current Banner" class="w-24 h-16 object-cover rounded-lg border">
                                    <div>
                                        <p class="text-sm text-gray-600">Current Banner</p>
                                        <button type="button" onclick="removeBanner()" class="text-red-600 text-sm hover:text-red-800">Remove</button>
                                    </div>
                                </div>
                            @endif
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                                <input type="file" id="banner-upload" accept="image/*" class="hidden">
                                <button type="button" onclick="document.getElementById('banner-upload').click()" class="text-indigo-600 hover:text-indigo-800">
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
            </div>

            {{-- Prize Information --}}
            <div class="bg-white shadow-md rounded-2xl p-8 border border-gray-100">
                <h2 class="text-lg font-semibold mb-8 flex items-center gap-2">
                    <span class="text-yellow-500">🏆</span> Prize Information
                </h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <label for="winner_prize" class="block text-sm font-medium text-gray-700 mb-1">Winner Prize (₹)</label>
                        <input type="number" name="winner_prize" id="winner_prize"
                            value="{{ old('winner_prize', $league->winner_prize) }}" min="0" step="0.01"
                            placeholder="Enter winner prize amount"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                        @error('winner_prize')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="runner_prize" class="block text-sm font-medium text-gray-700 mb-1">Runner-up Prize (₹)</label>
                        <input type="number" name="runner_prize" id="runner_prize"
                            value="{{ old('runner_prize', $league->runner_prize) }}" min="0" step="0.01"
                            placeholder="Enter runner-up prize amount"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3 px-4">
                        @error('runner_prize')
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


@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/flatpickr/flatpickr.css') }}" />
    <style>
        /* Flatpickr responsive improvements */
        .flatpickr-calendar {
            max-width: 100vw !important;
            width: 100% !important;
            font-size: 14px !important;
        }
        
        .flatpickr-months {
            background: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }
        
        .flatpickr-month {
            background: transparent !important;
            color: #1f2937 !important;
            font-weight: 600 !important;
        }
        
        .flatpickr-prev-month,
        .flatpickr-next-month {
            background: #ffffff !important;
            border: 1px solid #d1d5db !important;
            border-radius: 6px !important;
            padding: 8px !important;
            transition: all 0.2s ease !important;
        }
        
        .flatpickr-prev-month:hover,
        .flatpickr-next-month:hover {
            background: #f3f4f6 !important;
            border-color: #9ca3af !important;
        }
        
        .flatpickr-weekdays {
            background: #f9fafb !important;
        }
        
        .flatpickr-weekday {
            color: #6b7280 !important;
            font-weight: 500 !important;
        }
        
        .flatpickr-day {
            border-radius: 6px !important;
            margin: 1px !important;
            transition: all 0.2s ease !important;
        }
        
        .flatpickr-day:hover {
            background: #dbeafe !important;
            border-color: #3b82f6 !important;
        }
        
        .flatpickr-day.selected {
            background: #3b82f6 !important;
            border-color: #3b82f6 !important;
        }
        
        .flatpickr-day.today {
            border-color: #3b82f6 !important;
            color: #3b82f6 !important;
        }
        
        /* Mobile responsiveness */
        @media (max-width: 640px) {
            .flatpickr-calendar {
                font-size: 12px !important;
            }
            
            .flatpickr-day {
                height: 32px !important;
                line-height: 32px !important;
            }
            
            .flatpickr-prev-month,
            .flatpickr-next-month {
                padding: 6px !important;
            }
        }
        
        /* Ensure proper z-index */
        .flatpickr-calendar.open {
            z-index: 9999 !important;
        }
    </style>
@endsection

@section('scripts')
    <script src="{{ asset('assets/flatpickr/flatpickr.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize Flatpickr with better options
            flatpickr('.flatpickr', {
                dateFormat: "Y-m-d",
                allowInput: true,
                enableTime: false,
                minDate: "today",
                maxDate: new Date().fp_incr(365), // 1 year from today
                disableMobile: false, // Enable on mobile devices
                clickOpens: true,
                static: false, // Allow positioning adjustments
                monthSelectorType: "static", // Better month navigation
                prevArrow: "<svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 19l-7-7 7-7'></path></svg>",
                nextArrow: "<svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5l7 7-7 7'></path></svg>",
                locale: {
                    firstDayOfWeek: 1, // Start week on Monday
                    weekdays: {
                        shorthand: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                        longhand: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]
                    },
                    months: {
                        shorthand: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                        longhand: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]
                    }
                }
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

        // Image Upload and Cropper.js functionality
        let logoCropper, bannerCropper;
        let currentUploadType = '';

        // Logo upload handler
        document.getElementById('logo-upload').addEventListener('change', function(e) {
            handleImageUpload(e, 'logo');
        });

        // Banner upload handler
        document.getElementById('banner-upload').addEventListener('change', function(e) {
            handleImageUpload(e, 'banner');
        });

        function handleImageUpload(event, type) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file size
            const maxSize = type === 'logo' ? 2 * 1024 * 1024 : 5 * 1024 * 1024; // 2MB for logo, 5MB for banner
            if (file.size > maxSize) {
                alert(`File size must be less than ${type === 'logo' ? '2MB' : '5MB'}`);
                return;
            }

            currentUploadType = type;
            const reader = new FileReader();
            reader.onload = function(e) {
                showCropperModal(e.target.result, type);
            };
            reader.readAsDataURL(file);
        }

        function showCropperModal(imageSrc, type) {
            // Create modal HTML
            const modalHtml = `
                <div id="cropper-modal" class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                        <div class="flex items-center justify-between p-4 border-b">
                            <h3 class="text-lg font-semibold text-gray-900">Crop ${type === 'logo' ? 'Logo' : 'Banner'}</h3>
                            <button type="button" onclick="closeCropperModal()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <div class="mb-4">
                                <img id="cropper-image" src="${imageSrc}" style="max-width: 100%; max-height: 400px;">
                            </div>
                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="closeCropperModal()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Cancel
                                </button>
                                <button type="button" onclick="cropAndUpload()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                    Crop & Upload
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // Initialize cropper
            const image = document.getElementById('cropper-image');
            const aspectRatio = type === 'logo' ? 1 : 16/9; // 1:1 for logo, 16:9 for banner
            
            if (type === 'logo') {
                logoCropper = new Cropper(image, {
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
                bannerCropper = new Cropper(image, {
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

        function closeCropperModal() {
            const modal = document.getElementById('cropper-modal');
            if (modal) {
                modal.remove();
            }
            if (logoCropper) {
                logoCropper.destroy();
                logoCropper = null;
            }
            if (bannerCropper) {
                bannerCropper.destroy();
                bannerCropper = null;
            }
            currentUploadType = '';
        }

        function cropAndUpload() {
            const cropper = currentUploadType === 'logo' ? logoCropper : bannerCropper;
            if (!cropper) return;

            // Get cropped canvas
            const canvas = cropper.getCroppedCanvas({
                width: currentUploadType === 'logo' ? 300 : 800,
                height: currentUploadType === 'logo' ? 300 : 450,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            // Convert to blob
            canvas.toBlob(function(blob) {
                const formData = new FormData();
                formData.append(currentUploadType, blob, `${currentUploadType}.jpg`);

                // Show loading
                const uploadBtn = document.querySelector('#cropper-modal button:last-child');
                const originalText = uploadBtn.textContent;
                uploadBtn.textContent = 'Uploading...';
                uploadBtn.disabled = true;

                // Upload to server
                fetch(`/leagues/{{ $league->slug }}/upload-${currentUploadType}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('Response is not JSON:', text);
                            throw new Error('Server returned non-JSON response');
                        }
                    });
                })
                .then(data => {
                    if (data.success) {
                        // Reload page to show new image
                        location.reload();
                    } else {
                        alert('Upload failed: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Upload failed: ' + error.message);
                })
                .finally(() => {
                    uploadBtn.textContent = originalText;
                    uploadBtn.disabled = false;
                });
            }, 'image/jpeg', 0.8);
        }

        function removeLogo() {
            if (confirm('Are you sure you want to remove the logo?')) {
                // Add logic to remove logo from server
                fetch(`/leagues/{{ $league->slug }}/remove-logo`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to remove logo');
                    }
                });
            }
        }

        function removeBanner() {
            if (confirm('Are you sure you want to remove the banner?')) {
                // Add logic to remove banner from server
                fetch(`/leagues/{{ $league->slug }}/remove-banner`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to remove banner');
                    }
                });
            }
        }
    </script>

    <!-- Include Cropper.js CSS and JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
@endsection
