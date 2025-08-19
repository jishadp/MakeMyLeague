@extends('layouts.app')

@section('title', 'Create Team | CricBid')

@section('content')
<div class="py-12 bg-gradient-to-br from-gray-50 via-white to-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 animate-fadeIn">
        
        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('teams.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            Teams
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Create Team</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                @if(request('league_id'))
                    Create New Team for League
                @else
                    Create New Team
                @endif
            </h1>
            <p class="mt-2 text-lg text-gray-600">
                @if(request('league_id'))
                    Add a new cricket team that will be automatically added to the league
                @else
                    Add a new cricket team to your collection
                @endif
            </p>
        </div>

        <!-- Create Team Form -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 animate-fadeInUp">
            <div class="p-8">
                <form action="{{ route('teams.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @if(request('league_id'))
                        <input type="hidden" name="league_id" value="{{ request('league_id') }}">
                    @endif

                    <!-- Team Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Team Name <span class="text-red-600">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4"
                               placeholder="Enter team name">
                        @error('name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Logo Upload -->
                    <div>
                        <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Team Logo</label>
                        <div class="mt-1 flex items-center">
                            <span class="inline-block h-16 w-16 rounded-full overflow-hidden bg-gray-100">
                                <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </span>
                            <input type="file" name="logo" id="logo"
                                   class="ml-5 bg-white py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        </div>
                        <p class="mt-2 text-sm text-gray-500">PNG, JPG, GIF up to 2MB</p>
                        @error('logo')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Home Ground -->
                        <div>
                            <label for="home_ground_id" class="block text-sm font-medium text-gray-700 mb-2">Home Ground <span class="text-red-600">*</span></label>
                            <select name="home_ground_id" id="home_ground_id" required
                                    class="select2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                                <option value="">Select Home Ground</option>
                                @foreach($grounds as $ground)
                                    <option value="{{ $ground->id }}" {{ old('home_ground_id') == $ground->id ? 'selected' : '' }}>
                                        {{ $ground->name }} ({{ $ground->localBody->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('home_ground_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Local Body -->
                        <div>
                            <label for="local_body_id" class="block text-sm font-medium text-gray-700 mb-2">Local Body <span class="text-red-600">*</span></label>
                            <select name="local_body_id" id="local_body_id" required
                                    class="select2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                                <option value="">Select Local Body</option>
                                @foreach($localBodies as $localBody)
                                    <option value="{{ $localBody->id }}" {{ old('local_body_id') == $localBody->id ? 'selected' : '' }}>
                                        {{ $localBody->name }} ({{ $localBody->district->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('local_body_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6">
                        <a href="{{ route('teams.index') }}" class="bg-gray-200 text-gray-700 py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 font-medium">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 font-medium">
                            Create Team
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
            width: '100%',
            placeholder: 'Select an option',
            allowClear: true
        });
    });
</script>
@endsection
