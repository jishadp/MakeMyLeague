@extends('layouts.app')

@section('title', 'Edit Team - Cricket League Manager')

@section('content')
    <div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('teams.index') }}" class="text-indigo-600 hover:text-indigo-500 font-medium">← Back to Teams</a>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Team</h2>
            
            <form action="{{ route('teams.update', $team) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Team Name</label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $team->name) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg"
                           placeholder="Enter team name">
                </div>

                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                    <input type="text" name="country" id="country" value="{{ old('country', $team->country) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg"
                           placeholder="Enter country">
                </div>

                <div>
                    <label for="logo_url" class="block text-sm font-medium text-gray-700 mb-2">Logo URL</label>
                    <input type="url" name="logo_url" id="logo_url" value="{{ old('logo_url', $team->logo_url) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg"
                           placeholder="Enter logo URL (optional)">
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <button type="submit" 
                            class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors text-lg font-medium w-full sm:w-auto">
                        Update Team
                    </button>
                    <a href="{{ route('teams.index') }}" 
                       class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition-colors text-lg font-medium text-center w-full sm:w-auto">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection