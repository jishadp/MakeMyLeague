@extends('layouts.app')

@section('title', 'Organizer Request Details - ' . config('app.name'))

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-indigo-600 to-purple-600 py-4 px-4 sm:px-6 lg:px-8 text-white animate-fadeIn">
    <div class="max-w-7xl mx-auto">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-4 drop-shadow">
                Organizer Request Details
            </h1>
            <p class="text-lg sm:text-xl text-white-100">
                View your organizer request status and details
            </p>
        </div>
    </div>
</section>

<!-- Content Section -->
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden animate-fadeInUp">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        Request Information
                    </h2>
                    <a href="{{ route('organizer-requests.index') }}" 
                       class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Requests
                    </a>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- League Information -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">League Information</h3>
                        <a href="{{ route('leagues.show', $organizerRequest->league) }}" 
                           class="text-blue-600 hover:text-blue-800 font-medium">
                            View League →
                        </a>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-xl font-semibold text-gray-900 mb-2">{{ $organizerRequest->league->name }}</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-600">
                            <p><span class="font-medium">🎮 Game:</span> {{ $organizerRequest->league->game->name }}</p>
                            <p><span class="font-medium">📅 Season:</span> {{ $organizerRequest->league->season }}</p>
                            <p><span class="font-medium">📅 Start Date:</span> {{ $organizerRequest->league->start_date->format('M d, Y') }}</p>
                            <p><span class="font-medium">📅 End Date:</span> {{ $organizerRequest->league->end_date->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Request Details -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Status -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Request Status</h4>
                        <div class="flex items-center">
                            @switch($organizerRequest->status)
                                @case('pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        Pending Review
                                    </span>
                                    @break
                                @case('approved')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Approved
                                    </span>
                                    @break
                                @case('rejected')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        Rejected
                                    </span>
                                    @break
                            @endswitch
                        </div>
                    </div>

                    <!-- Request Date -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Request Date</h4>
                        <p class="text-gray-900">{{ $organizerRequest->created_at->format('F d, Y \a\t g:i A') }}</p>
                    </div>

                    <!-- Reviewer -->
                    @if($organizerRequest->reviewer)
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Reviewed By</h4>
                        <p class="text-gray-900">{{ $organizerRequest->reviewer->name }}</p>
                        <p class="text-sm text-gray-500">{{ $organizerRequest->reviewed_at->format('F d, Y \a\t g:i A') }}</p>
                    </div>
                    @endif
                </div>

                <!-- Message -->
                @if($organizerRequest->message)
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-900 mb-3">Your Message</h4>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-gray-800">{{ $organizerRequest->message }}</p>
                    </div>
                </div>
                @endif

                <!-- Admin Notes -->
                @if($organizerRequest->admin_notes)
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-900 mb-3">Admin Notes</h4>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <p class="text-gray-800">{{ $organizerRequest->admin_notes }}</p>
                    </div>
                </div>
                @endif

                <!-- Status Messages -->
                @if($organizerRequest->status === 'pending')
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-yellow-800">Under Review</h4>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Your request is currently under review. You will be notified once a decision is made.
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($organizerRequest->status === 'approved')
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-green-800">Request Approved</h4>
                                <p class="text-sm text-green-700 mt-1">
                                    Congratulations! Your request has been approved. You are now an organizer for this league.
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($organizerRequest->status === 'rejected')
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-red-800">Request Rejected</h4>
                                <p class="text-sm text-red-700 mt-1">
                                    Your request has been rejected. Please review the admin notes above for more information.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('organizer-requests.index') }}" 
                       class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium text-center transition-colors">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Requests
                    </a>
                    
                    @if($organizerRequest->status === 'pending')
                        <form method="POST" action="{{ route('organizer-requests.cancel', $organizerRequest) }}" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors"
                                    onclick="return confirm('Are you sure you want to cancel this request?')">
                                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel Request
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection