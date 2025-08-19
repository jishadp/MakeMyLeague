@extends('layouts.app')

@section('title', 'Edit Player - ' . $leaguePlayer->user->name)

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Player</h1>
                    <p class="text-gray-600 mt-2">{{ $leaguePlayer->user->name }} - {{ $league->name }}</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('league-players.show', [$league, $leaguePlayer]) }}" 
                       class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </a>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form action="{{ route('league-players.update', [$league, $leaguePlayer]) }}" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Player Info (Read Only) -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Player Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Player Name</label>
                                <p class="text-gray-900">{{ $leaguePlayer->user->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <p class="text-gray-900">{{ $leaguePlayer->user->email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Current Team</label>
                                <p class="text-gray-900">{{ $leaguePlayer->leagueTeam->team->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                <p class="text-gray-900">{{ $leaguePlayer->user->position->name ?? 'Not Set' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Team Selection -->
                    <div>
                        <label for="league_team_id" class="block text-sm font-medium text-gray-700 mb-2">
                            League Team <span class="text-red-500">*</span>
                        </label>
                        <select name="league_team_id" id="league_team_id" required
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('league_team_id') border-red-300 @enderror">
                            <option value="">Select Team</option>
                            @foreach($leagueTeams as $team)
                                <option value="{{ $team->id }}" {{ old('league_team_id', $leaguePlayer->league_team_id) == $team->id ? 'selected' : '' }}>
                                    {{ $team->team->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('league_team_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Base Price -->
                    <div>
                        <label for="base_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Base Price (₹) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="base_price" 
                               id="base_price" 
                               value="{{ old('base_price', $leaguePlayer->base_price) }}"
                               required
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('base_price') border-red-300 @enderror"
                               placeholder="Enter base price">
                        @error('base_price')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-300 @enderror">
                            <option value="pending" {{ old('status', $leaguePlayer->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="available" {{ old('status', $leaguePlayer->status) === 'available' ? 'selected' : '' }}>Available</option>
                            <option value="sold" {{ old('status', $leaguePlayer->status) === 'sold' ? 'selected' : '' }}>Sold</option>
                            <option value="unsold" {{ old('status', $leaguePlayer->status) === 'unsold' ? 'selected' : '' }}>Unsold</option>
                            <option value="skip" {{ old('status', $leaguePlayer->status) === 'skip' ? 'selected' : '' }}>Skip</option>
                        </select>
                        @error('status')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Retention -->
                    <div>
                        <label for="retention" class="block text-sm font-medium text-gray-700 mb-2">Retention Status</label>
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="retention" 
                                       value="1" 
                                       {{ old('retention', $leaguePlayer->retention) ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Retained</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="retention" 
                                       value="0" 
                                       {{ !old('retention', $leaguePlayer->retention) ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Not Retained</span>
                            </label>
                        </div>
                        @error('retention')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-end">
                    <a href="{{ route('league-players.show', [$league, $leaguePlayer]) }}" 
                       class="px-6 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 text-center">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
                        Update Player
                    </button>
                </div>
            </form>
        </div>
        
    </div>
</div>
@endsection
