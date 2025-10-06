@extends('layouts.app')

@section('title', 'Admin - Ground Management - ' . config('app.name'))

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-green-600 to-teal-600 py-4 px-4 sm:px-6 lg:px-8 text-white animate-fadeIn">
    <div class="max-w-7xl mx-auto">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-4 drop-shadow">
                Ground Management
            </h1>
            <p class="text-lg sm:text-xl text-white/90">
                Manage sports grounds and venues
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
                <svg class="w-6 h-6 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h4a2 2 0 012 2v2H6v-2z" clip-rule="evenodd"></path>
                </svg>
                Sports Grounds ({{ $grounds->total() }})
            </h2>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="{{ route('admin.grounds.create') }}" 
                   class="bg-green-600 hover:bg-green-700 text-black px-4 py-2 rounded-xl font-medium transition-colors shadow-lg hover:shadow-xl">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add New Ground
                </a>
            </div>
        </div>

        <!-- Grounds Grid -->
        @if($grounds->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($grounds as $ground)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                        
                        <!-- Ground Image Section -->
                        <div class="relative h-48 overflow-hidden">
                            @if($ground->images && count($ground->images) > 0)
                                <img src="{{ Storage::url($ground->images[0]) }}" alt="{{ $ground->name }}" 
                                     class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-green-600 via-teal-600 to-blue-600 flex items-center justify-center">
                                    <div class="text-center text-white">
                                        <svg class="w-16 h-16 mx-auto mb-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h4a2 2 0 012 2v2H6v-2z" clip-rule="evenodd"></path>
                                        </svg>
                                        <h3 class="text-xl font-bold drop-shadow-lg">{{ $ground->name }}</h3>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Availability Badge -->
                            <div class="absolute top-4 left-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium shadow-lg
                                    {{ $ground->is_available ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                    {{ $ground->is_available ? 'Available' : 'Unavailable' }}
                                </span>
                            </div>
                            
                            <!-- Ground Name Overlay -->
                            <div class="absolute bottom-4 left-4 right-4">
                                <h3 class="text-xl font-bold text-white drop-shadow-lg">{{ $ground->name }}</h3>
                                <p class="text-sm text-white/90 drop-shadow">{{ $ground->localBody->name }}, {{ $ground->district->name }}</p>
                            </div>
                        </div>
                        
                        <!-- Content Section -->
                        <div class="p-6">
                            <!-- Quick Stats -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="text-center p-3 bg-gray-50 rounded-xl">
                                    <div class="text-2xl font-bold text-green-600">{{ number_format($ground->capacity) }}</div>
                                    <div class="text-xs text-gray-600">Capacity</div>
                                </div>
                                <div class="text-center p-3 bg-gray-50 rounded-xl">
                                    <div class="text-2xl font-bold text-blue-600">{{ $ground->images ? count($ground->images) : 0 }}</div>
                                    <div class="text-xs text-gray-600">Images</div>
                                </div>
                            </div>
                            
                            <!-- Location Info -->
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>{{ $ground->localBody->name }}, {{ $ground->district->name }}, {{ $ground->state->name }}</span>
                                </div>
                                @if($ground->contact_person)
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                        </svg>
                                        <span>{{ $ground->contact_person }}</span>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Description -->
                            @if($ground->description)
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 line-clamp-2">{{ $ground->description }}</p>
                                </div>
                            @endif
                            
                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <a href="{{ route('admin.grounds.show', $ground) }}"
                                    class="flex-1 bg-blue-600 text-black text-center py-2 px-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl">
                                    View Details
                                </a>
                                
                                <a href="{{ route('admin.grounds.edit', $ground) }}"
                                    class="p-2 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                
                                <form action="{{ route('admin.grounds.toggle-availability', $ground) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="p-2 {{ $ground->is_available ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-green-100 text-green-600 hover:bg-green-200' }} rounded-xl transition-colors"
                                        title="{{ $ground->is_available ? 'Mark Unavailable' : 'Mark Available' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($ground->is_available)
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            @endif
                                        </svg>
                                    </button>
                                </form>
                                
                                <form action="{{ route('admin.grounds.destroy', $ground) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this ground? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-100 text-red-600 rounded-xl hover:bg-red-200 transition-colors"
                                        title="Delete">
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

            <!-- Pagination -->
            @if($grounds->hasPages())
                <div class="flex justify-center">
                    {{ $grounds->links() }}
                </div>
            @endif
        @else
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center animate-fadeInUp">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h4a2 2 0 012 2v2H6v-2z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Grounds Found</h3>
                <p class="text-gray-600 mb-6">Start by adding your first sports ground to the system.</p>
                <a href="{{ route('admin.grounds.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-black rounded-xl font-medium transition-colors shadow-lg hover:shadow-xl">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add First Ground
                </a>
            </div>
        @endif

        <!-- Back to Admin Dashboard -->
        <div class="text-center mt-8">
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

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
