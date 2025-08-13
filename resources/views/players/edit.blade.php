@extends('layouts.app')

@section('title', 'Edit Player - Cricket League Manager')

@section('content')
    <div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('players.index') }}" class="text-indigo-600 hover:text-indigo-500 font-medium">← Back to Players</a>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Player</h2>
            
            <form action="{{ route('players.update', $player) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Player Name</label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $player->name) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg"
                           placeholder="Enter player name">
                </div>

                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                    <select name="position" id="position" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg">
                        <option value="">Select position</option>
                        <option value="Batsman" {{ old('position', $player->position) == 'Batsman' ? 'selected' : '' }}>Batsman</option>
                        <option value="Bowler" {{ old('position', $player->position) == 'Bowler' ? 'selected' : '' }}>Bowler</option>
                        <option value="All-rounder" {{ old('position', $player->position) == 'All-rounder' ? 'selected' : '' }}>All-rounder</option>
                        <option value="Wicket-keeper" {{ old('position', $player->position) == 'Wicket-keeper' ? 'selected' : '' }}>Wicket-keeper</option>
                    </select>
                </div>

                <div>
                    <label for="age" class="block text-sm font-medium text-gray-700 mb-2">Age</label>
                    <input type="number" name="age" id="age" min="16" max="50" value="{{ old('age', $player->age) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg"
                           placeholder="Enter age">
                </div>

                <div>
                    <label for="team_id" class="block text-sm font-medium text-gray-700 mb-2">Team</label>
                    <select name="team_id" id="team_id" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg">
                        <option value="">Select team</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ old('team_id', $player->team_id) == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <button type="submit" 
                            class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors text-lg font-medium w-full sm:w-auto">
                        Update Player
                    </button>
                    <a href="{{ route('players.index') }}" 
                       class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition-colors text-lg font-medium text-center w-full sm:w-auto">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection