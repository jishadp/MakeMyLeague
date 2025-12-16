@extends('layouts.app')

@section('title', 'Replace Team - ' . $league->name)

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <div>
                        <a href="{{ route('leagues.show', $league) }}" class="text-gray-400 hover:text-gray-500">
                            <svg class="flex-shrink-0 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                            <span class="sr-only">League</span>
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                        </svg>
                        <a href="{{ route('league-teams.index', $league) }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Teams</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                        </svg>
                        <a href="#" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700 current" aria-current="page">Replace Team</a>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">
                    Replace Team: <span class="text-orange-600">{{ $leagueTeam->team->name }}</span>
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Select a new team to replace the existing one. All players and stats will be transferred to the new team.
                </p>
            </div>
            
            <div class="p-6">
                <form action="{{ route('league-teams.replace', [$league, $leagueTeam]) }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <label for="new_team_id" class="block text-sm font-medium text-gray-700 mb-2">New Team</label>
                        <select name="new_team_id" id="new_team_id" required 
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            <option value="">Select a team...</option>
                            @foreach($availableTeams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                        @error('new_team_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex items-center justify-end space-x-3">
                        <a href="{{ route('league-teams.index', $league) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors shadow-sm">
                            Replace Team
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
