@extends('layouts.app')

@section('title', 'Add Team - Cricket League Manager')

@section('content')
    <div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('teams.index') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold flex items-center gap-1">← Back to Teams</a>
        </div>
        
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-8 tracking-tight">Add New Team</h2>
                
                <form action="{{ route('teams.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Team Name</label>
                        <input type="text" name="name" id="name" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg"
                               placeholder="Enter team name">
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                        <input type="text" name="country" id="country" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg"
                               placeholder="Enter country" value="India">
                    </div>

                    <div>
                        <label for="logo_url" class="block text-sm font-medium text-gray-700 mb-2">Logo URL</label>
                        <input type="url" name="logo_url" id="logo_url" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg"
                               placeholder="Enter logo URL (optional)">
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 pt-6">
                        <button type="submit" 
                                class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all text-lg font-medium w-full sm:w-auto">
                            Add Team
                        </button>
                        <a href="{{ route('teams.index') }}" 
                           class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 shadow-md hover:shadow-lg transition-all text-lg font-medium text-center w-full sm:w-auto">
                            Cancel
                        </a>
                    </div>
                </form>
        </div>
    </div>
@endsection