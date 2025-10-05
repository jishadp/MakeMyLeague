@extends('layouts.app')

@section('title', 'Request to Organize League - ' . config('app.name'))

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-indigo-600 to-purple-600 py-4 px-4 sm:px-6 lg:px-8 text-white animate-fadeIn">
    <div class="max-w-7xl mx-auto">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-4 drop-shadow">
                Request to Organize League
            </h1>
            <p class="text-lg sm:text-xl text-white-100">
                Apply to become an organizer for a league
            </p>
        </div>
    </div>
</section>

<!-- Content Section -->
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden animate-fadeInUp">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                <h2 class="text-xl font-semibold text-white flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Organizer Request Form
                </h2>
            </div>

            <!-- Form -->
            <div class="p-6">
                <form method="POST" action="{{ route('organizer-requests.store') }}">
                    @csrf
                    
                    <!-- League Selection -->
                    <div class="mb-6">
                        <label for="league_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Select League <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('league_id') border-red-500 @enderror" 
                                id="league_id" name="league_id" required>
                            <option value="">Choose a league...</option>
                            @foreach($leagues as $league)
                                <option value="{{ $league->id }}" {{ old('league_id') == $league->id ? 'selected' : '' }}>
                                    {{ $league->name }} - {{ $league->game->name }} (Season {{ $league->season }})
                                </option>
                            @endforeach
                        </select>
                        @error('league_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message -->
                    <div class="mb-6">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                            Message (Optional)
                        </label>
                        <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('message') border-red-500 @enderror" 
                                  id="message" name="message" rows="4" 
                                  placeholder="Tell us why you want to organize this league...">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Maximum 1000 characters</p>
                    </div>

                    <!-- Info Notice -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-blue-800">Important Note</h4>
                                <p class="text-sm text-blue-700 mt-1">
                                    Your request will be reviewed by an administrator. You will be notified once a decision is made.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('organizer-requests.index') }}" 
                           class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium text-center transition-colors">
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Requests
                        </a>
                        <button type="submit" 
                                class="flex-1 bg-blue-700 hover:bg-blue-800 text-white px-6 py-3 rounded-lg font-medium active:scale-95 transition-all shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection