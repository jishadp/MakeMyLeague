@extends('layouts.app')

@section('title', 'Create League - Cricket League Manager')

@section('content')
    <div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('leagues.index') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold flex items-center gap-1">← Back to Leagues</a>
        </div>
        
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-8 tracking-tight">Create New League</h2>
            
            <form action="{{ route('leagues.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">League Name</label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg"
                           placeholder="Enter league name">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" id="start_date" required value="{{ old('start_date') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" name="end_date" id="end_date" required value="{{ old('end_date') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg">
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-6">
                    <button type="submit" 
                            class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all text-lg font-medium w-full sm:w-auto">
                        Create League
                    </button>
                    <a href="{{ route('leagues.index') }}" 
                       class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 shadow-md hover:shadow-lg transition-all text-lg font-medium text-center w-full sm:w-auto">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection