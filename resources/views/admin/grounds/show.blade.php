@extends('layouts.app')

@section('title', 'Admin - Ground Details - ' . config('app.name'))

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-orange-600 to-red-600 py-4 px-4 sm:px-6 lg:px-8 text-white animate-fadeIn">
    <div class="max-w-7xl mx-auto">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-4 drop-shadow">
                Ground Details
            </h1>
            <p class="text-lg sm:text-xl text-white/90">
                View ground information and details
            </p>
        </div>
    </div>
</section>

<!-- Content Section -->
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-4xl mx-auto">
        
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

        <!-- Ground Details Card -->
        <div class="bg-white rounded-2xl shadow-lg p-8 animate-fadeInUp">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Basic Information -->
                <div class="space-y-6">
                    <div class="flex items-center mb-6">
                        <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <h2 class="text-2xl font-bold text-gray-800">Basic Information</h2>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Ground Name</label>
                        <p class="text-lg font-semibold text-gray-800">{{ $ground->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Capacity</label>
                        <p class="text-lg font-semibold text-gray-800">{{ number_format($ground->capacity) }} people</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $ground->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $ground->is_available ? 'Available' : 'Not Available' }}
                        </span>
                    </div>

                    @if($ground->description)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Description</label>
                        <p class="text-gray-700">{{ $ground->description }}</p>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Address</label>
                        <p class="text-gray-700">{{ $ground->address }}</p>
                    </div>
                </div>

                <!-- Location Information -->
                <div class="space-y-6">
                    <div class="flex items-center mb-6">
                        <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <h2 class="text-2xl font-bold text-gray-800">Location</h2>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">State</label>
                        <p class="text-lg font-semibold text-gray-800">{{ $ground->state->name ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">District</label>
                        <p class="text-lg font-semibold text-gray-800">{{ $ground->district->name ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Local Body</label>
                        <p class="text-lg font-semibold text-gray-800">{{ $ground->localBody->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            @if($ground->contact_person || $ground->contact_phone || $ground->contact_email)
            <div class="mt-8 pt-8 border-t border-gray-200">
                <div class="flex items-center mb-6">
                    <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-800">Contact Information</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @if($ground->contact_person)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Contact Person</label>
                        <p class="text-gray-700">{{ $ground->contact_person }}</p>
                    </div>
                    @endif

                    @if($ground->contact_phone)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                        <p class="text-gray-700">{{ $ground->contact_phone }}</p>
                    </div>
                    @endif

                    @if($ground->contact_email)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                        <p class="text-gray-700">{{ $ground->contact_email }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Facilities -->
            @if($ground->facilities)
            <div class="mt-8 pt-8 border-t border-gray-200">
                <div class="flex items-center mb-6">
                    <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-800">Facilities</h2>
                </div>
                <p class="text-gray-700">{{ $ground->facilities }}</p>
            </div>
            @endif

            <!-- Images -->
            @if($ground->images && count($ground->images) > 0)
            <div class="mt-8 pt-8 border-t border-gray-200">
                <div class="flex items-center mb-6">
                    <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-800">Images</h2>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($ground->images as $image)
                    <div class="relative">
                        <img src="{{ Storage::url($image) }}" alt="Ground Image" class="w-full h-32 object-cover rounded-lg shadow-md">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="mt-8 pt-8 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('admin.grounds.edit', $ground) }}" 
                       class="flex-1 bg-orange-600 hover:bg-orange-700 text-black px-6 py-3 rounded-xl font-semibold transition-colors shadow-lg hover:shadow-xl text-center">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Ground
                    </a>
                    
                    <a href="{{ route('admin.grounds.index') }}" 
                       class="flex-1 bg-gray-600 hover:bg-gray-700 text-black px-6 py-3 rounded-xl font-semibold transition-colors shadow-lg hover:shadow-xl text-center">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Grounds
                    </a>
                </div>
            </div>
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
