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

                <div>
                    <label for="league_team_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Team <span class="text-red-500">*</span>
                    </label>
                    <select name="league_team_id" id="league_team_id" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('league_team_id') border-red-500 @enderror">
                        <option value="">Choose a team...</option>
                        @foreach($leagueTeams as $leagueTeam)
                            <option value="{{ $leagueTeam->id }}" {{ old('league_team_id') == $leagueTeam->id ? 'selected' : '' }}>
                                {{ $leagueTeam->team->name }} ({{ ucfirst($leagueTeam->status) }})
                            </option>
                        @endforeach
                    </select>
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
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('user_id') border-red-500 @enderror">
                        <option value="">Choose a player...</option>
                        @foreach($availablePlayers as $player)
                            <option value="{{ $player->id }}" {{ old('user_id') == $player->id ? 'selected' : '' }}>
                                {{ $player->name }} ({{ $player->role->name ?? 'No Role' }}) - {{ $player->email }}
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

                <div>
                    <label for="base_price" class="block text-sm font-medium text-gray-700 mb-2">
                        Base Price <span class="text-red-500">*</span>
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
                        <option value="available" {{ old('status') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="sold" {{ old('status') === 'sold' ? 'selected' : '' }}>Sold</option>
                        <option value="unsold" {{ old('status') === 'unsold' ? 'selected' : '' }}>Unsold</option>
                        <option value="skip" {{ old('status') === 'skip' ? 'selected' : '' }}>Skip</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if($league->retention)
                <div>
                    <div class="flex items-center">
                        <input type="checkbox" 
                               name="retention" 
                               id="retention" 
                               value="1"
                               {{ old('retention') ? 'checked' : '' }}
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="retention" class="ml-2 block text-sm text-gray-900">
                            Retained Player
                        </label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        Mark this player as retained from previous season (max {{ $league->retention_players }} per team)
                    </p>
                    @error('retention')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @endif

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
@endsection
