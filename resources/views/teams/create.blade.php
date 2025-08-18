@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Create New Team</h1>
        <p class="mt-2 text-gray-600">Add a new cricket team to your collection</p>
    </div>

    <!-- Create Team Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('teams.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Team Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Team Name <span class="text-red-600">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#5c9c85] focus:ring focus:ring-[#5c9c85] focus:ring-opacity-50"
                       placeholder="Enter team name">
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Logo Upload -->
            <div>
                <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Team Logo</label>
                <div class="mt-1 flex items-center">
                    <span class="inline-block h-12 w-12 rounded-full overflow-hidden bg-gray-100">
                        <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </span>
                    <input type="file" name="logo" id="logo"
                           class="ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#5c9c85]">
                </div>
                <p class="mt-1 text-sm text-gray-500">PNG, JPG, GIF up to 2MB</p>
                @error('logo')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-6">
                <!-- Home Ground -->
                <div>
                    <label for="home_ground_id" class="block text-sm font-medium text-gray-700 mb-1">Home Ground <span class="text-red-600">*</span></label>
                    <select name="home_ground_id" id="home_ground_id" required
                            class="select2 w-full rounded-md border-gray-300 shadow-sm focus:border-[#5c9c85] focus:ring focus:ring-[#5c9c85] focus:ring-opacity-50">
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
                    <label for="local_body_id" class="block text-sm font-medium text-gray-700 mb-1">Local Body <span class="text-red-600">*</span></label>
                    <select name="local_body_id" id="local_body_id" required
                            class="select2 w-full rounded-md border-gray-300 shadow-sm focus:border-[#5c9c85] focus:ring focus:ring-[#5c9c85] focus:ring-opacity-50">
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
            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('teams.index') }}" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                    Cancel
                </a>
                <button type="submit" class="bg-gradient-to-r from-[#89a894] to-[#5c9c85] text-white py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                    Create Team
                </button>
            </div>
        </form>
    </div>
</div>
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
