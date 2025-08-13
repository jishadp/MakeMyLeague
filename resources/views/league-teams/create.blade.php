@extends('layouts.app')

@section('title', 'Add Team - ' . $league->name)

@section('content')
    <div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('leagues.show', $league) }}" class="text-indigo-600 hover:text-indigo-700 font-semibold flex items-center gap-1">← Back to League</a>
        </div>
        
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-8 tracking-tight">Add Team to League</h2>
            <p class="text-gray-600 mb-8">Add a team to {{ $league->name }} for auction participation</p>
            
            <form action="{{ route('league-teams.store', $league) }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="team_id" class="block text-sm font-medium text-gray-700 mb-2">Select Team</label>
                    <select name="team_id" id="team_id" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg">
                        <option value="">Choose a team</option>
                        @foreach(App\Models\Team::all() as $team)
                            <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>
                                {{ $team->name }} ({{ $team->country }})
                            </option>
                        @endforeach
                    </select>
                    @error('team_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Team Name in League</label>
                    <input type="text" name="name" id="name" required 
                           value="{{ old('name') }}" placeholder="e.g., Mumbai Indians"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg">
                    <p class="text-sm text-gray-500 mt-1">Custom name for this team in the league</p>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="owner_name" class="block text-sm font-medium text-gray-700 mb-2">Owner Name (Optional)</label>
                    <input type="text" name="owner_name" id="owner_name" 
                           value="{{ old('owner_name') }}" placeholder="Team owner name"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg">
                    <p class="text-sm text-gray-500 mt-1">Name of the team owner (can be set later)</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-6">
                    <button type="submit" 
                            class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all text-lg font-medium w-full sm:w-auto">
                        Add Team
                    </button>
                    <a href="{{ route('leagues.show', $league) }}" 
                       class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 shadow-md hover:shadow-lg transition-all text-lg font-medium text-center w-full sm:w-auto">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection