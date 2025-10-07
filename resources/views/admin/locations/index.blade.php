@extends('layouts.app')

@section('title', 'Admin - Location Management - ' . config('app.name'))

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-blue-600 to-indigo-600 py-4 px-4 sm:px-6 lg:px-8 text-white animate-fadeIn">
    <div class="max-w-7xl mx-auto">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-4 drop-shadow">
                Location Management
            </h1>
            <p class="text-lg sm:text-xl text-white/90">
                Manage states, districts, and local bodies
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

        <!-- Admin Navigation -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 animate-fadeInUp">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-gray-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                </svg>
                Admin Management
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('admin.organizer-requests.index') }}" 
                   class="flex items-center p-4 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 transition-colors {{ request()->routeIs('admin.organizer-requests.*') ? 'ring-2 ring-red-500' : '' }}">
                    <div class="bg-red-100 rounded-full p-2 mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-red-900">Organizer Requests</h4>
                        <p class="text-sm text-red-700">Manage organizer applications</p>
                    </div>
                </a>

                <a href="{{ route('admin.locations.index') }}" 
                   class="flex items-center p-4 bg-blue-50 border border-blue-200 rounded-xl hover:bg-blue-100 transition-colors {{ request()->routeIs('admin.locations.*') ? 'ring-2 ring-blue-500' : '' }}">
                    <div class="bg-blue-100 rounded-full p-2 mr-3">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-blue-900">Location Management</h4>
                        <p class="text-sm text-blue-700">Manage states, districts & local bodies</p>
                    </div>
                </a>

                <a href="{{ route('admin.grounds.index') }}" 
                   class="flex items-center p-4 bg-green-50 border border-green-200 rounded-xl hover:bg-green-100 transition-colors {{ request()->routeIs('admin.grounds.*') ? 'ring-2 ring-green-500' : '' }}">
                    <div class="bg-green-100 rounded-full p-2 mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h4a2 2 0 012 2v2H6v-2z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-green-900">Ground Management</h4>
                        <p class="text-sm text-green-700">Manage sports grounds & venues</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Header with Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <svg class="w-6 h-6 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                </svg>
                Location Hierarchy
            </h2>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="{{ route('admin.locations.create-state') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-black px-4 py-2 rounded-xl font-medium transition-colors shadow-lg hover:shadow-xl">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add State
                </a>
                <a href="{{ route('admin.locations.create-district') }}" 
                   class="bg-green-600 hover:bg-green-700 text-black px-4 py-2 rounded-xl font-medium transition-colors shadow-lg hover:shadow-xl">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add District
                </a>
                <a href="{{ route('admin.locations.create-local-body') }}" 
                   class="bg-purple-600 hover:bg-purple-700 text-black px-4 py-2 rounded-xl font-medium transition-colors shadow-lg hover:shadow-xl">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Local Body
                </a>
            </div>
        </div>

        <!-- States Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 animate-fadeInUp">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    States ({{ $states->count() }})
                </h3>
            </div>
            
            @if($states->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($states as $state)
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-semibold text-blue-900">{{ $state->name }}</h4>
                                    <p class="text-sm text-blue-700">
                                        {{ $state->districts_count }} Districts, {{ $state->local_bodies_count }} Local Bodies
                                    </p>
                                </div>
                                <div class="flex gap-1">
                                    <a href="{{ route('admin.locations.edit-state', $state) }}" 
                                       class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.locations.destroy-state', $state) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this state? This will also delete all associated districts and local bodies.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"></path>
                    </svg>
                    <p>No states found. Add your first state to get started.</p>
                </div>
            @endif
            
            <!-- States Pagination -->
            @if($states->hasPages())
                <div class="mt-6">
                    {{ $states->links() }}
                </div>
            @endif
        </div>

        <!-- Districts Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 animate-fadeInUp">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                    </svg>
                    Districts ({{ $districts->count() }})
                </h3>
            </div>
            
            @if($districts->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($districts as $district)
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-semibold text-green-900">{{ $district->name }}</h4>
                                    <p class="text-sm text-green-700">
                                        {{ $district->state->name }} • {{ $district->local_bodies_count }} Local Bodies
                                    </p>
                                </div>
                                <div class="flex gap-1">
                                    <a href="{{ route('admin.locations.edit-district', $district) }}" 
                                       class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.locations.destroy-district', $district) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this district? This will also delete all associated local bodies.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"></path>
                    </svg>
                    <p>No districts found. Add your first district to get started.</p>
                </div>
            @endif
            
            <!-- Districts Pagination -->
            @if($districts->hasPages())
                <div class="mt-6">
                    {{ $districts->links() }}
                </div>
            @endif
        </div>

        <!-- Local Bodies Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 animate-fadeInUp">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h4a2 2 0 012 2v2H6v-2z" clip-rule="evenodd"></path>
                    </svg>
                    Local Bodies ({{ $localBodies->count() }})
                </h3>
            </div>
            
            @if($localBodies->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($localBodies as $localBody)
                        <div class="bg-purple-50 border border-purple-200 rounded-xl p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-semibold text-purple-900">{{ $localBody->name }}</h4>
                                    <p class="text-sm text-purple-700">
                                        {{ $localBody->district->name }}, {{ $localBody->district->state->name }}
                                    </p>
                                </div>
                                <div class="flex gap-1">
                                    <a href="{{ route('admin.locations.edit-local-body', $localBody) }}" 
                                       class="p-2 text-purple-600 hover:bg-purple-100 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.locations.destroy-local-body', $localBody) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this local body? This will affect users and leagues associated with it.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h4a2 2 0 012 2v2H6v-2z"></path>
                    </svg>
                    <p>No local bodies found. Add your first local body to get started.</p>
                </div>
            @endif
            
            <!-- Local Bodies Pagination -->
            @if($localBodies->hasPages())
                <div class="mt-6">
                    {{ $localBodies->links() }}
                </div>
            @endif
        </div>

        <!-- Back to Admin Dashboard -->
        <div class="text-center">
            <a href="{{ route('admin.organizer-requests.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-black rounded-xl font-medium transition-colors shadow-lg hover:shadow-xl">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Admin Dashboard
            </a>
        </div>
    </div>
</section>

<!-- Animations -->
<style>
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fadeIn {
        animation: fadeIn 0.5s ease-in-out;
    }

    .animate-fadeInUp {
        animation: fadeInUp 0.4s ease-in-out;
    }
</style>
@endsection
