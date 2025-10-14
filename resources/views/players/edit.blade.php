@extends('layouts.app')

@section('title', 'Cricket Players | ' . config('app.name'))

@section('content')
<div class="py-12 bg-gradient-to-br from-gray-50 via-white to-gray-100 min-h-screen">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 animate-fadeIn">
        
        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('players.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            Players
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('players.show', $player) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 md:ml-2">
                                {{ $player->name }}
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                Edit Player
            </h1>
            <p class="mt-2 text-lg text-gray-600">
                Update player information
            </p>
        </div>
        
        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 animate-fadeInUp">
            <div class="p-6">
                <form action="{{ route('players.update', $player) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Error Display -->
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">
                                        There were errors with your submission:
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $player->name) }}" required
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4">
                        </div>

                        <!-- Mobile -->
                        <div>
                            <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number <span class="text-red-500">*</span></label>
                            <input type="tel" name="mobile" id="mobile" value="{{ old('mobile', $player->mobile) }}" required
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4">
                        </div>

                        <!-- PIN -->
                        <div>
                            <label for="pin" class="block text-sm font-medium text-gray-700 mb-2">PIN (4-6 digits) <span class="text-red-500">*</span></label>
                            <input type="password" name="pin" id="pin" value="{{ old('pin', $player->pin) }}" required
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4"
                                   minlength="4" maxlength="6">
                        </div>

                        <!-- Game Role -->
                        <div>
                            <label for="position_id" class="block text-sm font-medium text-gray-700 mb-2">Game Role</label>
                            <select name="position_id" id="position_id" {{ auth()->user()->isOrganizer() ? '' : 'disabled' }}
                                    class="select2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4 {{ !auth()->user()->isOrganizer() ? 'bg-gray-100' : '' }}">
                                <option value="">Select Game Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('position_id', $player->position_id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if(!auth()->user()->isOrganizer())
                                <p class="mt-1 text-sm text-gray-500">Only administrators can change game roles.</p>
                                <input type="hidden" name="position_id" value="{{ $player->position_id }}">
                            @endif
                        </div>

                        <!-- Email (Optional) -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email (Optional)</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $player->email) }}"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4">
                        </div>

                        <!-- Local Body (Optional) -->
                        <div>
                            <label for="local_body_id" class="block text-sm font-medium text-gray-700 mb-2">Location (Optional)</label>
                            <select name="local_body_id" id="local_body_id"
                                    class="select2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4">
                                <option value="">Select Location</option>
                                @foreach($localBodies as $localBody)
                                    <option value="{{ $localBody->id }}" {{ old('local_body_id', $player->local_body_id) == $localBody->id ? 'selected' : '' }}>
                                        {{ $localBody->name }} ({{ $localBody->district->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Profile Photo (Optional) -->
                    <div class="mt-6">
                        <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">Profile Photo (Optional)</label>
                        
                        <!-- Current Photo Preview -->
                        @if($player->photo)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">Current Photo:</p>
                                <img src="{{ asset('storage/' . $player->photo) }}" alt="{{ $player->name }}" class="h-24 w-24 object-cover rounded-lg border border-gray-200">
                            </div>
                        @endif
                        
                        <input type="file" name="photo" id="photo"
                               class="block w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <p class="mt-2 text-sm text-gray-500">Accepted formats: JPEG, PNG, JPG, GIF. Max size: 2MB.</p>
                    </div>
                    
                    <!-- Submit Buttons -->
                    <div class="mt-8 flex justify-end space-x-4">
                        <a href="{{ route('players.show', $player) }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Back to Player
                        </a>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Update Player
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Delete Player Section -->
        @if(auth()->user()->isOrganizer())
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 animate-fadeInUp">
                <div class="p-6 border-t border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Danger Zone</h2>
                    <p class="text-gray-600 mb-4">Once deleted, this player and all associated data will be permanently removed.</p>
                    
                    <form action="{{ route('players.destroy', $player) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this player? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Delete Player
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Animations -->
<style>
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fadeIn { animation: fadeIn 0.5s ease-in-out; }
.animate-fadeInUp { animation: fadeInUp 0.4s ease-in-out; }
</style>

<!-- Select2 Initialization -->
<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'classic',
        width: '100%'
    });
});
</script>
@endsection
