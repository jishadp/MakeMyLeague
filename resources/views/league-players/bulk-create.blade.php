@extends('layouts.app')

@section('title', 'Bulk Add Players - ' . $league->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        Bulk Add Players
                    </h1>
                    <p class="text-lg text-gray-600 mt-2 flex items-center">
                        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        {{ $league->name }}
                    </p>
                </div>
                <a href="{{ route('league-players.index', $league) }}" 
                   class="inline-flex items-center px-6 py-3 bg-white text-gray-700 font-medium rounded-xl hover:bg-gray-50 border border-gray-200 shadow-sm transition-all duration-200 hover:shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Players
                </a>
            </div>
        </div>

        <!-- Quick Import by Location -->
        <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-lg border border-white/40 mb-8">
            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <p class="text-sm uppercase tracking-wide text-indigo-600 font-semibold">Fast Import</p>
                    <h2 class="text-xl font-bold text-gray-900 mt-1">Paste players after picking a location</h2>
                    <p class="text-sm text-gray-600 mt-1">We will check mobile numbers, create missing player accounts, and add only those not already in this league.</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold">Name</div>
                    <div class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold">Mobile</div>
                    <div class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold">Role</div>
                </div>
            </div>
            <form action="{{ route('league-players.import-location', $league) }}" method="POST" class="p-6 space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="md:col-span-2">
                        <label for="local_body_id" class="block text-sm font-medium text-gray-700 mb-2">Location <span class="text-red-500">*</span></label>
                        <select id="local_body_id" name="local_body_id" required class="w-full bg-white border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('local_body_id') border-red-500 @enderror">
                            <option value="">Select location</option>
                            @foreach($localBodies as $localBody)
                                <option value="{{ $localBody->id }}" {{ old('local_body_id') == $localBody->id ? 'selected' : '' }}>
                                    {{ $localBody->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('local_body_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="base_price_import" class="block text-sm font-medium text-gray-700 mb-2">Base price for imported players</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-medium">₹</span>
                            </div>
                            <input type="number" name="import_base_price" id="base_price_import" min="0" step="0.01" value="{{ old('import_base_price', $league->team_wallet_limit * 0.01) }}" class="w-full pl-10 bg-white border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('import_base_price') border-red-500 @enderror">
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Defaults to 1% of wallet if left unchanged.</p>
                        @error('import_base_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Allowed roles</label>
                        <div class="flex flex-wrap gap-2">
                            @forelse($gamePositions as $position)
                                <span class="px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold">{{ $position->name }}</span>
                            @empty
                                <span class="text-xs text-gray-500">No roles configured</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div>
                    <label for="player_rows" class="block text-sm font-medium text-gray-700 mb-2">Players list <span class="text-red-500">*</span></label>
                    <textarea id="player_rows" name="player_rows" rows="7" required placeholder='Lines:&#10;John Doe, 9876543210, Batter&#10;Jane Doe, 9876501234, Bowler&#10;&#10;OR JSON:&#10;[{"name":"John Doe","mobile":"9876543210","role":"Batter"}]' class="w-full bg-white border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('player_rows') border-red-500 @enderror">{{ old('player_rows') }}</textarea>
                    <div class="mt-2 text-xs text-gray-600 space-y-2">
                        <p>Use either comma-separated lines or JSON array. Mobiles are deduplicated, and players already in this league are skipped automatically.</p>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                            <p class="font-semibold text-gray-700 mb-1">JSON example:</p>
                            <pre class="text-[11px] text-gray-800 whitespace-pre-wrap">[
  {"name":"John Doe","mobile":"9876543210","role":"Batter"},
  {"name":"Jane Doe","mobile":"9876501234","role":"Bowler"}
]</pre>
                        </div>
                    </div>
                    @error('player_rows')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="text-sm text-gray-600">
                        <p>Missing player accounts are auto-created with the selected location and role.</p>
                        <p>Slots left: {{ $remainingSlots }} / {{ $maxPlayers }}</p>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white font-semibold rounded-lg shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed" {{ $remainingSlots == 0 ? 'disabled' : '' }}>
                            Import & Add to League
                        </button>
                        <a href="{{ route('league-players.index', $league) }}" class="px-6 py-2.5 bg-white text-gray-700 font-medium rounded-lg border border-gray-200 hover:bg-gray-50">Back to Players</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Main Content Card -->
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600 p-6">
                <h2 class="text-xl font-semibold text-white flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Multiple Players to League
                </h2>
                <p class="text-indigo-100 mt-1">Select players and configure their settings for bulk addition</p>
            </div>

            <div class="p-8">
                <form action="{{ route('league-players.bulk-store', $league) }}" method="POST" class="space-y-8">
                    @csrf

                    <!-- Configuration Section -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Player Configuration
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Team Selection -->
                            <div>
                                <label for="league_team_id" class="block text-sm font-medium text-gray-700 mb-3">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    Team Assignment
                                    <span class="text-gray-500 font-normal">(optional)</span>
                                </label>
                                <select name="league_team_id" id="league_team_id"
                                        class="w-full bg-white border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('league_team_id') border-red-500 @enderror">
                                    <option value="">No team (available for auction)</option>
                                    @foreach($leagueTeams as $leagueTeam)
                                        <option value="{{ $leagueTeam->id }}" {{ old('league_team_id') == $leagueTeam->id ? 'selected' : '' }}>
                                            {{ $leagueTeam->team->name }} ({{ ucfirst($leagueTeam->status) }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-2 text-xs text-gray-500">
                                    If no team selected, players will be available for auction
                                </p>
                                @error('league_team_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Base Price -->
                            <div>
                                <label for="base_price" class="block text-sm font-medium text-gray-700 mb-3">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    Base Price
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-gray-500 font-medium">₹</span>
                                    </div>
                                    <input type="number" 
                                           name="base_price" 
                                           id="base_price" 
                                           min="0"
                                           step="0.01"
                                           value="{{ old('base_price', $league->team_wallet_limit * 0.01) }}"
                                           placeholder="{{ number_format($league->team_wallet_limit * 0.01, 2) }}"
                                           required
                                           class="w-full pl-10 bg-white border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('base_price') border-red-500 @enderror">
                                </div>
                                <p class="mt-2 text-xs text-gray-500">
                                    Applied to all selected players
                                </p>
                                @error('base_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-3">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Status
                                    <span class="text-red-500">*</span>
                                </label>
                                <select name="status" id="status" required
                                        class="w-full bg-white border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('status') border-red-500 @enderror">
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
                        </div>
                    </div>

                    <!-- Player Selection Section -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        Available Players
                                        <span class="ml-2 text-sm font-normal text-gray-500">({{ $availablePlayers->count() }} players)</span>
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Select players to add to {{ $league->name }}
                                        <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded text-xs font-medium {{ $remainingSlots > 0 ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $currentPlayerCount }}/{{ $maxPlayers }} players | {{ $remainingSlots }} slots remaining
                                        </span>
                                    </p>
                                </div>
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                        <input type="text" id="playerSearch" placeholder="Search players..." 
                                               class="pl-10 bg-white border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="button" id="selectAllBtn" class="px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed" {{ $remainingSlots == 0 ? 'disabled' : '' }}>
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Select {{ $remainingSlots > 0 && $remainingSlots < $availablePlayers->count() ? $remainingSlots : 'All' }}
                                        </button>
                                        <button type="button" id="clearAllBtn" class="px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Clear All
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="max-h-96 overflow-y-auto">
                                <div class="space-y-3" id="playerCheckboxContainer">
                                    @if($remainingSlots == 0)
                                        <div class="text-center py-12 bg-red-50 rounded-xl border-2 border-dashed border-red-200">
                                            <svg class="w-12 h-12 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                            <p class="text-red-600 font-medium">League is full!</p>
                                            <p class="text-sm text-red-500 mt-1">Maximum {{ $maxPlayers }} players reached. Cannot add more players.</p>
                                        </div>
                                    @elseif($availablePlayers->isEmpty())
                                        <div class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <p class="text-gray-500 font-medium">All players are already added to this league</p>
                                            <p class="text-sm text-gray-400 mt-1">No available players to add</p>
                                        </div>
                                    @else
                                        @foreach($availablePlayers as $player)
                                            <div class="group relative bg-white border border-gray-200 rounded-xl p-4 hover:border-green-300 hover:shadow-lg transition-all duration-200 player-item">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-4">
                                                        <!-- Custom Checkbox -->
                                                        <div class="relative">
                                                            <input type="checkbox" 
                                                                   name="user_ids[]" 
                                                                   id="player-{{ $player->id }}" 
                                                                   value="{{ $player->id }}"
                                                                   class="player-checkbox sr-only">
                                                            <label for="player-{{ $player->id }}" class="flex items-center justify-center w-6 h-6 border-2 border-gray-300 rounded-lg cursor-pointer transition-all duration-200 hover:border-green-400">
                                                                <svg class="w-4 h-4 text-white opacity-0 transition-opacity duration-200" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                </svg>
                                                            </label>
                                                        </div>
                                                        
                                                        <!-- Player Avatar -->
                                                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                                            {{ substr($player->name, 0, 1) }}
                                                        </div>
                                                        
                                                        <!-- Player Info -->
                                                        <div>
                                                            <h6 class="font-semibold text-gray-900 text-lg">{{ $player->name }}</h6>
                                                            <p class="text-sm text-gray-500 flex items-center">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                                                                </svg>
                                                                {{ $player->position->name ?? 'No Game Role' }}
                                                            </p>
                                                            <p class="text-xs text-gray-400 flex items-center mt-1">
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                                </svg>
                                                                {{ $player->email }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Status Badge -->
                                                    <div class="flex items-center">
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            Available
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Selection Summary -->
                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                            <span class="text-sm font-medium text-gray-700">
                                                Selected: <span id="selectedCount" class="font-bold text-green-600">0</span> players
                                            </span>
                                        </div>
                                        <div class="hidden sm:block w-px h-4 bg-gray-300"></div>
                                        <div class="text-xs text-gray-500">
                                            Ready to add to {{ $league->name }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @error('user_ids')
                                <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-indigo-500 rounded-full"></div>
                                    <span class="text-sm font-medium text-gray-700">
                                        Ready to add <span id="selectedCountFooter" class="font-bold text-indigo-600">0</span> players
                                    </span>
                                </div>
                                <div class="hidden sm:block w-px h-4 bg-gray-300"></div>
                                <div class="text-xs text-gray-500">
                                    Players will be added with the configured settings
                                </div>
                            </div>
                            <div class="flex space-x-3">
                                <a href="{{ route('league-players.index', $league) }}" 
                                   class="px-6 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                                        {{ $remainingSlots == 0 ? 'disabled' : '' }}>
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add Selected Players
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
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
        const selectedCountFooterElem = document.getElementById('selectedCountFooter');
        const remainingSlots = {{ $remainingSlots }};
        
        // Update selected count
        function updateSelectedCount() {
            const selectedCount = document.querySelectorAll('.player-checkbox:checked').length;
            selectedCountElem.textContent = selectedCount;
            selectedCountFooterElem.textContent = selectedCount;
            
            // Show warning if exceeding limit
            if (remainingSlots > 0 && selectedCount > remainingSlots) {
                selectedCountElem.classList.add('text-red-600');
                selectedCountFooterElem.classList.add('text-red-600');
            } else {
                selectedCountElem.classList.remove('text-red-600');
                selectedCountFooterElem.classList.remove('text-red-600');
            }
        }
        
        // Setup custom checkbox styling
        playerCheckboxes.forEach(checkbox => {
            const label = checkbox.nextElementSibling;
            
            // Handle checkbox change for custom styling
            checkbox.addEventListener('change', function() {
                const selectedCount = document.querySelectorAll('.player-checkbox:checked').length;
                
                // Prevent selecting more than remaining slots
                if (this.checked && remainingSlots > 0 && selectedCount > remainingSlots) {
                    this.checked = false;
                    alert(`You can only select ${remainingSlots} more player(s). League limit: ${remainingSlots} remaining slots.`);
                    return;
                }
                
                if (this.checked) {
                    label.classList.add('bg-green-600', 'border-green-600');
                    label.classList.remove('border-gray-300');
                    label.querySelector('svg').classList.remove('opacity-0');
                } else {
                    label.classList.remove('bg-green-600', 'border-green-600');
                    label.classList.add('border-gray-300');
                    label.querySelector('svg').classList.add('opacity-0');
                }
                updateSelectedCount();
            });
        });
        
        // Search functionality
        playerSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            playerItems.forEach(item => {
                const playerName = item.querySelector('h6').textContent.toLowerCase();
                const playerEmail = item.querySelector('p:last-child').textContent.toLowerCase();
                const playerRole = item.querySelector('p:nth-child(2)').textContent.toLowerCase();
                
                if (playerName.includes(searchTerm) || playerEmail.includes(searchTerm) || playerRole.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
        
        // Select all button
        selectAllBtn.addEventListener('click', function() {
            let selectedCount = 0;
            const maxToSelect = remainingSlots > 0 ? remainingSlots : 999999;
            
            playerCheckboxes.forEach(checkbox => {
                const item = checkbox.closest('.player-item');
                if (item.style.display !== 'none' && selectedCount < maxToSelect) {
                    checkbox.checked = true;
                    const label = checkbox.nextElementSibling;
                    label.classList.add('bg-green-600', 'border-green-600');
                    label.classList.remove('border-gray-300');
                    label.querySelector('svg').classList.remove('opacity-0');
                    selectedCount++;
                }
            });
            updateSelectedCount();
        });
        
        // Clear all button
        clearAllBtn.addEventListener('click', function() {
            playerCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
                const label = checkbox.nextElementSibling;
                label.classList.remove('bg-green-600', 'border-green-600');
                label.classList.add('border-gray-300');
                label.querySelector('svg').classList.add('opacity-0');
            });
            updateSelectedCount();
        });
        
        // Initial count update
        updateSelectedCount();
    });
</script>
@endsection

@endsection
