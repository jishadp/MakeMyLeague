@extends('layouts.app')

@section('title', 'Admin - Create Local Body - ' . config('app.name'))

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-green-600 to-emerald-600 py-4 px-4 sm:px-6 lg:px-8 text-white animate-fadeIn">
    <div class="max-w-7xl mx-auto">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-4 drop-shadow">
                Create New Local Body
            </h1>
            <p class="text-lg sm:text-xl text-white/90">
                Add a new local body to the location hierarchy
            </p>
        </div>
    </div>
</section>

<!-- Content Section -->
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-2xl mx-auto">
        
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

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 p-4 mb-6 rounded-xl shadow-md animate-fadeInUp">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-semibold">Please fix the following errors:</span>
                </div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-lg p-8 animate-fadeInUp">
            <form action="{{ route('admin.locations.store-local-body') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Local Body Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Local Body Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500 transition-colors @error('name') border-red-500 @enderror"
                           placeholder="Enter local body name (e.g., Municipality, Panchayat)"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- District Selection -->
                <div>
                    <label for="district_id" class="block text-sm font-medium text-gray-700 mb-2">
                        District <span class="text-red-500">*</span>
                    </label>
                    <select id="district_id" 
                            name="district_id" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500 transition-colors @error('district_id') border-red-500 @enderror"
                            required>
                        <option value="">Select a district</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}" 
                                    {{ old('district_id') == $district->id ? 'selected' : '' }}>
                                {{ $district->name }} - {{ $district->state->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('district_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6">
                    <button type="submit" 
                            class="flex-1 bg-green-600 hover:bg-green-700 text-black px-6 py-3 rounded-xl font-semibold transition-colors shadow-lg hover:shadow-xl">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Create Local Body
                    </button>
                    
                    <a href="{{ route('admin.locations.index') }}" 
                       class="flex-1 bg-gray-600 hover:bg-gray-700 text-black px-6 py-3 rounded-xl font-semibold transition-colors shadow-lg hover:shadow-xl text-center">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Back to Location Management -->
        <div class="text-center mt-8">
            <a href="{{ route('admin.locations.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-black rounded-xl font-medium transition-colors shadow-lg hover:shadow-xl">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Location Management
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
