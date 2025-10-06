@extends('layouts.app')

@section('title', 'Admin - Review Organizer Request - ' . config('app.name'))

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-purple-600 to-indigo-600 py-4 px-4 sm:px-6 lg:px-8 text-white animate-fadeIn">
    <div class="max-w-7xl mx-auto">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-4 drop-shadow">
                Review Organizer Request
            </h1>
            <p class="text-lg sm:text-xl text-white-100">
                Review and make a decision on this organizer request
            </p>
        </div>
    </div>
</section>

<!-- Content Section -->
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden animate-fadeInUp">
            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Request #{{ $organizerRequest->id }}
                    </h2>
                    <a href="{{ route('admin.organizer-requests.pending') }}" 
                       class="bg-white/20 hover:bg-white/30 text-black px-4 py-2 rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Pending
                    </a>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- Information Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- User Information -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                            User Information
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-600 w-20">Name:</span>
                                <span class="text-sm text-gray-900">{{ $organizerRequest->user->name }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-600 w-20">Email:</span>
                                <span class="text-sm text-gray-900">{{ $organizerRequest->user->email ?? 'Not provided' }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-600 w-20">Mobile:</span>
                                <span class="text-sm text-gray-900">{{ $organizerRequest->user->mobile }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-600 w-20">Position:</span>
                                <span class="text-sm text-gray-900">{{ $organizerRequest->user->position->name ?? 'Not specified' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- League Information -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            League Information
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-600 w-20">League:</span>
                                <a href="{{ route('leagues.show', $organizerRequest->league) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    {{ $organizerRequest->league->name }}
                                </a>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-600 w-20">Game:</span>
                                <span class="text-sm text-gray-900">{{ $organizerRequest->league->game->name }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-600 w-20">Season:</span>
                                <span class="text-sm text-gray-900">{{ $organizerRequest->league->season }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-600 w-20">Status:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $organizerRequest->league->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($organizerRequest->league->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Request Details -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Request Details -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            Request Details
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-600 w-24">Date:</span>
                                <span class="text-sm text-gray-900">{{ $organizerRequest->created_at->format('F d, Y \a\t g:i A') }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-600 w-24">Status:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ ucfirst($organizerRequest->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- User Message -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                            </svg>
                            User Message
                        </h3>
                        <div class="bg-white rounded-lg p-4">
                            @if($organizerRequest->message)
                                <p class="text-sm text-gray-800">{{ $organizerRequest->message }}</p>
                            @else
                                <p class="text-sm text-gray-500 italic">No message provided</p>
                            @endif
                        </div>
                    </div>
                </div>

                @if($organizerRequest->status === 'pending')
                    <!-- Action Forms -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Approve Form -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <svg class="w-6 h-6 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-green-800">Approve Request</h3>
                            </div>
                            <form method="POST" action="{{ route('admin.organizer-requests.approve', $organizerRequest) }}">
                                @csrf
                                <div class="mb-4">
                                    <label for="approve_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                        Admin Notes (Optional)
                                    </label>
                                    <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                              id="approve_notes" name="admin_notes" rows="3" 
                                              placeholder="Add any notes for the user...">{{ old('admin_notes') }}</textarea>
                                </div>
                                <button type="submit" 
                                        class="w-full bg-green-600 hover:bg-green-700 text-black px-6 py-3 rounded-lg font-medium active:scale-95 transition-all shadow-md hover:shadow-lg">
                                    <svg class="w-5 h-5 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Approve Request
                                </button>
                            </form>
                        </div>

                        <!-- Reject Form -->
                        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <svg class="w-6 h-6 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-red-800">Reject Request</h3>
                            </div>
                            <form method="POST" action="{{ route('admin.organizer-requests.reject', $organizerRequest) }}">
                                @csrf
                                <div class="mb-4">
                                    <label for="reject_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                        Rejection Reason <span class="text-red-500">*</span>
                                    </label>
                                    <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('admin_notes') border-red-500 @enderror" 
                                              id="reject_notes" name="admin_notes" rows="3" 
                                              placeholder="Please provide a reason for rejection..." required>{{ old('admin_notes') }}</textarea>
                                    @error('admin_notes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit" 
                                        class="w-full bg-red-600 hover:bg-red-700 text-black px-6 py-3 rounded-lg font-medium active:scale-95 transition-all shadow-md hover:shadow-lg">
                                    <svg class="w-5 h-5 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    Reject Request
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Already Processed -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <svg class="w-6 h-6 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-blue-800">Request Already Processed</h3>
                        </div>
                        <p class="text-blue-700 mb-4">This request has already been {{ $organizerRequest->status }}.</p>
                        
                        @if($organizerRequest->admin_notes)
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-blue-800 mb-2">Admin Notes:</h4>
                                <div class="bg-white rounded-lg p-3">
                                    <p class="text-sm text-gray-800">{{ $organizerRequest->admin_notes }}</p>
                                </div>
                            </div>
                        @endif
                        
                        @if($organizerRequest->reviewer)
                            <div class="text-sm text-blue-700">
                                <p><span class="font-medium">Reviewed by:</span> {{ $organizerRequest->reviewer->name }}</p>
                                <p><span class="font-medium">Reviewed on:</span> {{ $organizerRequest->reviewed_at->format('F d, Y \a\t g:i A') }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Admin Power: League Status Change -->
                <div class="mt-8 bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-purple-800">Admin Power: Change League Status</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Current Status -->
                        <div class="bg-white rounded-lg p-4 border border-purple-100">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Current League Status</h4>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $organizerRequest->league->status === 'active' ? 'bg-green-100 text-green-800' : 
                                       ($organizerRequest->league->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($organizerRequest->league->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                                    {{ ucfirst($organizerRequest->league->status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Status Change Form -->
                        <div class="bg-white rounded-lg p-4 border border-purple-100">
                            <form method="POST" action="{{ route('admin.organizer-requests.change-league-status', $organizerRequest) }}">
                                @csrf
                                <div class="mb-4">
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                        Change Status To
                                    </label>
                                    <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" 
                                            id="status" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="pending" {{ $organizerRequest->league->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="active" {{ $organizerRequest->league->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="completed" {{ $organizerRequest->league->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $organizerRequest->league->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="status_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                        Admin Notes (Optional)
                                    </label>
                                    <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" 
                                              id="status_notes" name="admin_notes" rows="2" 
                                              placeholder="Add notes about this status change...">{{ old('admin_notes') }}</textarea>
                                </div>
                                <button type="submit" 
                                        class="w-full bg-purple-600 hover:bg-purple-700 text-black px-6 py-3 rounded-lg font-medium active:scale-95 transition-all shadow-md hover:shadow-lg">
                                    <svg class="w-5 h-5 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                                    </svg>
                                    Update League Status
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection