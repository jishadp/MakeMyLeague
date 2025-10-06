@extends('layouts.app')

@section('title', 'Admin - Edit Ground - ' . config('app.name'))

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-orange-600 to-red-600 py-4 px-4 sm:px-6 lg:px-8 text-white animate-fadeIn">
    <div class="max-w-7xl mx-auto">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-4 drop-shadow">
                Edit Ground
            </h1>
            <p class="text-lg sm:text-xl text-white/90">
                Update ground information and details
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
            <form action="{{ route('admin.grounds.update', $ground) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Ground Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $ground->name) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-orange-500 focus:border-orange-500 transition-colors @error('name') border-red-500 @enderror"
                               placeholder="Enter ground name"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">
                            Capacity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="capacity" 
                               name="capacity" 
                               value="{{ old('capacity', $ground->capacity) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-orange-500 focus:border-orange-500 transition-colors @error('capacity') border-red-500 @enderror"
                               placeholder="Enter capacity"
                               min="1"
                               required>
                        @error('capacity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-orange-500 focus:border-orange-500 transition-colors @error('description') border-red-500 @enderror"
                              placeholder="Enter ground description">{{ old('description', $ground->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Address <span class="text-red-500">*</span>
                    </label>
                    <textarea id="address" 
                              name="address" 
                              rows="2"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-orange-500 focus:border-orange-500 transition-colors @error('address') border-red-500 @enderror"
                              placeholder="Enter ground address"
                              required>{{ old('address', $ground->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location Information -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div>
                        <label for="state_id" class="block text-sm font-medium text-gray-700 mb-2">
                            State <span class="text-red-500">*</span>
                        </label>
                        <select id="state_id" 
                                name="state_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-orange-500 focus:border-orange-500 transition-colors @error('state_id') border-red-500 @enderror"
                                required>
                            <option value="">Select a state</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}" 
                                        {{ old('state_id', $ground->state_id) == $state->id ? 'selected' : '' }}>
                                    {{ $state->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('state_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="district_id" class="block text-sm font-medium text-gray-700 mb-2">
                            District <span class="text-red-500">*</span>
                        </label>
                        <select id="district_id" 
                                name="district_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-orange-500 focus:border-orange-500 transition-colors @error('district_id') border-red-500 @enderror"
                                required>
                            <option value="">Select a district</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}" 
                                        {{ old('district_id', $ground->district_id) == $district->id ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('district_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="localbody_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Local Body <span class="text-red-500">*</span>
                        </label>
                        <select id="localbody_id" 
                                name="localbody_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-orange-500 focus:border-orange-500 transition-colors @error('localbody_id') border-red-500 @enderror"
                                required>
                            <option value="">Select a local body</option>
                            @foreach($localBodies as $localBody)
                                <option value="{{ $localBody->id }}" 
                                        {{ old('localbody_id', $ground->localbody_id) == $localBody->id ? 'selected' : '' }}>
                                    {{ $localBody->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('localbody_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div>
                        <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">
                            Contact Person
                        </label>
                        <input type="text" 
                               id="contact_person" 
                               name="contact_person" 
                               value="{{ old('contact_person', $ground->contact_person) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-orange-500 focus:border-orange-500 transition-colors @error('contact_person') border-red-500 @enderror"
                               placeholder="Enter contact person name">
                        @error('contact_person')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Contact Phone
                        </label>
                        <input type="text" 
                               id="contact_phone" 
                               name="contact_phone" 
                               value="{{ old('contact_phone', $ground->contact_phone) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-orange-500 focus:border-orange-500 transition-colors @error('contact_phone') border-red-500 @enderror"
                               placeholder="Enter contact phone">
                        @error('contact_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Contact Email
                        </label>
                        <input type="email" 
                               id="contact_email" 
                               name="contact_email" 
                               value="{{ old('contact_email', $ground->contact_email) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-orange-500 focus:border-orange-500 transition-colors @error('contact_email') border-red-500 @enderror"
                               placeholder="Enter contact email">
                        @error('contact_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Facilities -->
                <div>
                    <label for="facilities" class="block text-sm font-medium text-gray-700 mb-2">
                        Facilities
                    </label>
                    <textarea id="facilities" 
                              name="facilities" 
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-orange-500 focus:border-orange-500 transition-colors @error('facilities') border-red-500 @enderror"
                              placeholder="Enter available facilities">{{ old('facilities', $ground->facilities) }}</textarea>
                    @error('facilities')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Availability -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_available" 
                               value="1"
                               {{ old('is_available', $ground->is_available) ? 'checked' : '' }}
                               class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Ground is available for booking</span>
                    </label>
                </div>

                <!-- Images -->
                <div>
                    <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                        Ground Images
                    </label>
                    <input type="file" 
                           id="images" 
                           name="images[]" 
                           multiple
                           accept="image/*"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-orange-500 focus:border-orange-500 transition-colors @error('images') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-gray-500">You can select multiple images. Supported formats: JPG, PNG, GIF. Max size: 2MB per image.</p>
                    @error('images')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Images -->
                @if($ground->images && count($ground->images) > 0)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Images</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($ground->images as $image)
                        <div class="relative">
                            <img src="{{ Storage::url($image) }}" alt="Ground Image" class="w-full h-32 object-cover rounded-lg shadow-md">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6">
                    <button type="submit" 
                            class="flex-1 bg-orange-600 hover:bg-orange-700 text-black px-6 py-3 rounded-xl font-semibold transition-colors shadow-lg hover:shadow-xl">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Ground
                    </button>
                    
                    <a href="{{ route('admin.grounds.index') }}" 
                       class="flex-1 bg-gray-600 hover:bg-gray-700 text-black px-6 py-3 rounded-xl font-semibold transition-colors shadow-lg hover:shadow-xl text-center">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Back to Grounds -->
        <div class="text-center mt-8">
            <a href="{{ route('admin.grounds.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-black rounded-xl font-medium transition-colors shadow-lg hover:shadow-xl">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Grounds Management
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
