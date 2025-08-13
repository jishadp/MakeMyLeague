@extends('layouts.app')

@section('title', 'Add Player - Cricket League Manager')

@section('content')
    <div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('players.index') }}" 
               class="inline-flex items-center text-indigo-600 hover:text-indigo-500 font-medium transition-colors duration-200">
                ← Back to Players
            </a>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 border border-gray-100">
            <div class="p-6 sm:p-8">
                
                <!-- Title -->
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">
                    Add New Player
                </h2>

                <!-- Form -->
                <form action="{{ route('players.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Player Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Player Name</label>
                        <input type="text" name="name" id="name" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-base sm:text-lg"
                               placeholder="Enter player name">
                    </div>

                    <!-- Position -->
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                        <select name="position" id="position" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-base sm:text-lg">
                            <option value="">Select position</option>
                            <option value="Batsman">Batsman</option>
                            <option value="Bowler">Bowler</option>
                            <option value="All-rounder">All-rounder</option>
                            <option value="Wicket-keeper">Wicket-keeper</option>
                        </select>
                    </div>

                    <!-- Age -->
                    <div>
                        <label for="age" class="block text-sm font-medium text-gray-700 mb-2">Age</label>
                        <input type="number" name="age" id="age" min="16" max="50"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-base sm:text-lg"
                               placeholder="Enter age">
                    </div>

                    <!-- Team -->
                    <div>
                        <label for="team_id" class="block text-sm font-medium text-gray-700 mb-2">Team</label>
                        <select name="team_id" id="team_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-base sm:text-lg">
                            <option value="">Select team</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <button type="submit" 
                                class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all duration-300 text-base sm:text-lg font-medium w-full sm:w-auto">
                            Add Player
                        </button>
                        <a href="{{ route('players.index') }}" 
                           class="bg-gray-200 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-300 shadow-sm hover:shadow-md transition-all duration-300 text-base sm:text-lg font-medium text-center w-full sm:w-auto">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
