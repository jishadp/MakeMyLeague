@extends('layouts.app')

@section('title', 'My Organizer Requests - ' . config('app.name'))

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-indigo-600 to-purple-600 py-4 px-4 sm:px-6 lg:px-8 text-white animate-fadeIn">
    <div class="max-w-7xl mx-auto">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-4 drop-shadow">
                My Organizer Requests
            </h1>
            <p class="text-lg sm:text-xl text-white-100">
                Track your league organizer requests and approvals
            </p>
        </div>
    </div>
</section>

<!-- Content Section -->
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 p-4 mb-6 rounded-xl shadow-md animate-fadeInUp">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 p-4 mb-6 rounded-xl shadow-md animate-fadeInUp">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Header with Create Button -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <svg class="w-6 h-6 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Organizer Requests
            </h2>
            <div class="flex flex-col sm:flex-row gap-3">
                @if(auth()->user()->isAdmin())
                    <div class="flex gap-2">
                        <a href="{{ route('admin.organizer-requests.index') }}" 
                           class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <svg class="w-5 h-5 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Admin Dashboard
                        </a>
                        <a href="{{ route('admin.organizer-requests.pending') }}" 
                           class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <svg class="w-5 h-5 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            Pending Requests
                        </a>
                    </div>
                @endif
                <a href="{{ route('organizer-requests.create') }}" 
                   class="bg-blue-700 hover:bg-blue-800 active:scale-95 transition-all duration-200 text-white px-5 py-2 rounded-xl shadow-md hover:shadow-lg w-full sm:w-auto text-center font-medium">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Request to Organize League
                </a>
            </div>
        </div>

        @if($requests->count() > 0)
            <!-- Requests Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($requests as $request)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                        <!-- League Header -->
                        <div class="h-32 overflow-hidden relative">
                            <img src="{{ asset('images/league.jpg') }}" alt="{{ $request->league->name }}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end">
                                <h3 class="text-lg font-semibold text-white p-4">{{ $request->league->name }}</h3>
                            </div>
                            <!-- Status Badge -->
                            <div class="absolute top-3 right-3">
                                @switch($request->status)
                                    @case('pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                            Pending
                                        </span>
                                        @break
                                    @case('approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Approved
                                        </span>
                                        @break
                                    @case('rejected')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                            Rejected
                                        </span>
                                        @break
                                @endswitch
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <!-- League Details -->
                            <div class="space-y-3 text-sm text-gray-600 mb-4">
                                <p><span class="font-medium">🎮 Game:</span> {{ $request->league->game->name }}</p>
                                <p><span class="font-medium">📅 Season:</span> {{ $request->league->season }}</p>
                                <p><span class="font-medium">📅 Requested:</span> {{ $request->created_at->format('M d, Y') }}</p>
                                @if($request->reviewer)
                                    <p><span class="font-medium">👤 Reviewed by:</span> {{ $request->reviewer->name }}</p>
                                    <p><span class="font-medium">📅 Reviewed:</span> {{ $request->reviewed_at->format('M d, Y') }}</p>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <a href="{{ route('organizer-requests.show', $request) }}" 
                                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium text-center text-sm transition-colors">
                                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Details
                                </a>
                                @if($request->status === 'pending')
                                    <form method="POST" action="{{ route('organizer-requests.cancel', $request) }}" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors"
                                                onclick="return confirm('Are you sure you want to cancel this request?')">
                                            <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Cancel
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($requests->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $requests->links() }}
                </div>
            @endif

        @else
            <!-- Empty State -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-md p-8 text-center animate-fadeInUp">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Organizer Requests</h3>
                <p class="text-gray-600 mb-6">You haven't made any organizer requests yet.</p>
                <a href="{{ route('organizer-requests.create') }}" 
                   class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-3 rounded-xl font-medium active:scale-95 transition-all shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Request to Organize League
                </a>
            </div>
        @endif
    </div>
</section>
@endsection