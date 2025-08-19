@extends('layouts.app')

@section('title', 'Create Player | CricBid')

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
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Create Player</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                @if(request('league_slug'))
                    Create Player for League
                @else
                    Create Player
                @endif
            </h1>
            <p class="mt-2 text-lg text-gray-600">
                @if(request('league_slug'))
                    Add a new cricket player that will be automatically added to the league
                @else
                    Add a new cricket player to the system
                @endif
            </p>
        </div>
        
        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 animate-fadeInUp">
            <div class="p-8">
                @if ($errors->any())
                <div class="mb-6 bg-red-50 text-red-600 p-4 rounded-lg">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('players.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(request('league_slug'))
                        <input type="hidden" name="league_slug" value="{{ request('league_slug') }}">
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4">
                        </div>

                        <!-- Mobile -->
                        <div>
                            <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number <span class="text-red-500">*</span></label>
                            <input type="tel" name="mobile" id="mobile" value="{{ old('mobile') }}" required
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4">
                        </div>

                        <!-- PIN -->
                        <div>
                            <label for="pin" class="block text-sm font-medium text-gray-700 mb-2">PIN (4-6 digits) <span class="text-red-500">*</span></label>
                            <input type="password" name="pin" id="pin" value="{{ old('pin') }}" required
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4"
                                   minlength="4" maxlength="6">
                        </div>

                        <!-- Player Role -->
                        <div>
                            <label for="position_id" class="block text-sm font-medium text-gray-700 mb-2">Player Role <span class="text-red-500">*</span></label>
                            <select name="position_id" id="position_id" required
                                    class="select2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('position_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Email (Optional) -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email (Optional)</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4">
                        </div>

                        <!-- Local Body (Optional) -->
                        <div>
                            <label for="local_body_id" class="block text-sm font-medium text-gray-700 mb-2">Location (Optional)</label>
                            <select name="local_body_id" id="local_body_id"
                                    class="select2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4">
                                <option value="">Select Location</option>
                                @foreach($localBodies as $localBody)
                                    <option value="{{ $localBody->id }}" {{ old('local_body_id') == $localBody->id ? 'selected' : '' }}>
                                        {{ $localBody->name }} ({{ $localBody->district->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Profile Photo -->
                    <div class="mt-6">
                        <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">Profile Photo (Optional)</label>
                        <input type="file" name="photo" id="photo"
                               class="block w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <p class="mt-2 text-sm text-gray-500">Accepted formats: JPEG, PNG, JPG, GIF. Max size: 2MB.</p>
                    </div>
                    
                    <!-- Submit Buttons -->
                    <div class="mt-8 flex flex-col sm:flex-row items-center justify-between">
                        <a href="{{ route('players.index') }}" 
                           class="underline text-sm text-gray-600 hover:text-gray-900 mb-4 sm:mb-0">
                            Back to Players
                        </a>
                        <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            Create Player
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Animations -->
<style>
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fadeIn { animation: fadeIn 0.5s ease-in-out; }
.animate-fadeInUp { animation: fadeInUp 0.4s ease-in-out; }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'classic',
            width: '100%',
            placeholder: 'Select an option',
            allowClear: true
        });
    });
</script>
@endsection
