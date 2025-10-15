@extends('layouts.app')

@section('title', 'Admin - Players Management | ' . config('app.name'))

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Users Management</h1>
                    <p class="text-gray-600 mt-2">Manage all users in the system</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.organizer-requests.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Admin
                    </a>
                </div>
            </div>
        </div>

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

                <a href="{{ route('admin.players.index') }}" 
                   class="flex items-center p-4 bg-purple-50 border border-purple-200 rounded-xl hover:bg-purple-100 transition-colors {{ request()->routeIs('admin.players.*') ? 'ring-2 ring-purple-500' : '' }}">
                    <div class="bg-purple-100 rounded-full p-2 mr-3">
                        <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-purple-900">Users Management</h4>
                        <p class="text-sm text-purple-700">Manage all users & reset PINs</p>
                    </div>
                </a>

                <a href="{{ route('admin.analytics.index') }}" 
                   class="flex items-center p-4 bg-indigo-50 border border-indigo-200 rounded-xl hover:bg-indigo-100 transition-colors {{ request()->routeIs('admin.analytics.*') ? 'ring-2 ring-indigo-500' : '' }}">
                    <div class="bg-indigo-100 rounded-full p-2 mr-3">
                        <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-indigo-900">Analytics Dashboard</h4>
                        <p class="text-sm text-indigo-700">View platform statistics & reports</p>
                    </div>
                </a>

                <a href="{{ route('admin.admin-users.index') }}" 
                   class="flex items-center p-4 bg-orange-50 border border-orange-200 rounded-xl hover:bg-orange-100 transition-colors {{ request()->routeIs('admin.admin-users.*') ? 'ring-2 ring-orange-500' : '' }}">
                    <div class="bg-orange-100 rounded-full p-2 mr-3">
                        <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-orange-900">Admin Users</h4>
                        <p class="text-sm text-orange-700">Manage admin accounts</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <form action="{{ route('admin.players.index') }}" method="GET">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/>
                    </svg>
                    Filter Players
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               placeholder="Name or Mobile"
                               class="w-full h-12 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3">
                    </div>

                    <!-- Role Filter -->
                    <div>
                        <label for="position_id" class="block text-sm font-medium text-gray-700 mb-2">Playing Role</label>
                        <select name="position_id" id="position_id" class="w-full h-12 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3">
                            <option value="">All Roles</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                                    {{ $position->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Location Filter -->
                    <div>
                        <label for="local_body_id" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                        <select name="local_body_id" id="local_body_id" class="w-full h-12 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3">
                            <option value="">All Locations</option>
                            @foreach($localBodies as $localBody)
                                <option value="{{ $localBody->id }}" {{ request('local_body_id') == $localBody->id ? 'selected' : '' }}>
                                    {{ $localBody->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort Filter -->
                    <div>
                        <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                        <select name="sort_by" id="sort_by" class="w-full h-12 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3">
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Recently Added</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-between items-center">
                    <!-- Reset Filters -->
                    <a href="{{ route('admin.players.index') }}" class="inline-flex items-center px-4 py-2 text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset Filters
                    </a>
                    
                    <!-- Apply Filters -->
                    <button type="submit" class="inline-flex items-center px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Players Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Players List ({{ $players->total() }} total)</h2>
            </div>
            
            @if($players->isEmpty())
                <div class="p-12 text-center">
                    <div class="mb-6">
                        <svg class="w-20 h-20 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Users Found</h3>
                <p class="text-gray-600 mb-6">No users found matching your criteria.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($players as $player)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12">
                                                @if($player->photo)
                                                    <img class="h-12 w-12 rounded-full object-cover" src="{{ asset('storage/' . $player->photo) }}" alt="{{ $player->name }}">
                                                @else
                                                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center">
                                                        <span class="text-white font-bold text-lg">{{ strtoupper(substr($player->name, 0, 1)) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $player->name }}</div>
                                                <div class="text-sm text-gray-500">ID: {{ $player->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $player->mobile }}</div>
                                        <div class="text-sm text-gray-500">{{ $player->email ?? 'No email' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ $player->position->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $player->localBody->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $player->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('players.show', $player->slug) }}" 
                                               class="text-indigo-600 hover:text-indigo-900 transition-colors"
                                               title="View Profile">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <button onclick="document.getElementById('photo-input-{{ $player->id }}').click()" 
                                                    class="text-purple-600 hover:text-purple-900 transition-colors"
                                                    title="Change Photo">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </button>
                                            <input type="file" id="photo-input-{{ $player->id }}" class="hidden" accept="image/*" 
                                                   onchange="showCropModal(this, '{{ $player->slug }}', '{{ $player->name }}')">
                                            <button onclick="resetPlayerPin('{{ $player->slug }}', '{{ $player->name }}')" 
                                                    class="text-red-600 hover:text-red-900 transition-colors"
                                                    title="Reset PIN">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($players->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $players->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Reset PIN Modal -->
<div id="resetPinModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Reset Player PIN</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to reset the PIN for <span id="playerName" class="font-semibold"></span>?
                </p>
                <p class="text-sm text-gray-500 mt-2">
                    A new random 4-digit PIN will be generated and displayed.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="confirmReset" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    Reset PIN
                </button>
                <button onclick="closeResetPinModal()" class="mt-3 px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">PIN Reset Successful</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    The PIN for <span id="successPlayerName" class="font-semibold"></span> has been reset successfully.
                </p>
                <div class="mt-4 p-3 bg-gray-100 rounded-lg">
                    <p class="text-sm text-gray-600">New PIN:</p>
                    <p id="newPin" class="text-lg font-mono font-bold text-indigo-600"></p>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    Please inform the player of their new PIN.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeSuccessModal()" class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Crop Modal -->
<div id="crop-modal" class="fixed inset-0 bg-black/50 z-50 hidden backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-8 shadow-2xl">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" clip-rule="evenodd"/>
                </svg>
                Crop Profile Photo for <span id="cropPlayerName" class="text-purple-600"></span>
            </h3>
            <div class="mb-6">
                <div id="crop-container" class="w-full h-64 bg-gray-100 rounded-xl overflow-hidden border-2 border-gray-200"></div>
            </div>
            <div class="flex gap-4">
                <button onclick="closeCropModal()" class="flex-1 px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 font-medium transition-colors">Cancel</button>
                <button onclick="cropAndUpload()" class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 font-medium transition-all duration-200 shadow-lg hover:shadow-xl">Upload</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

<script>
let currentPlayerSlug = null;
let cropper;
let selectedFile;
let currentUserSlug;
let currentInputElement;

function resetPlayerPin(playerSlug, playerName) {
    currentPlayerSlug = playerSlug;
    document.getElementById('playerName').textContent = playerName;
    document.getElementById('resetPinModal').classList.remove('hidden');
}

function closeResetPinModal() {
    document.getElementById('resetPinModal').classList.add('hidden');
    currentPlayerSlug = null;
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
}

document.getElementById('confirmReset').addEventListener('click', function() {
    if (!currentPlayerSlug) return;
    
    // Show loading state
    this.textContent = 'Resetting...';
    this.disabled = true;
    
    // Debug logging removed for production
    
    fetch(`/admin/players/${currentPlayerSlug}/reset-pin`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Close reset modal
            closeResetPinModal();
            
            // Show success modal
            document.getElementById('successPlayerName').textContent = data.player_name;
            document.getElementById('newPin').textContent = data.new_pin;
            document.getElementById('successModal').classList.remove('hidden');
        } else {
            alert('Error resetting PIN. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error resetting PIN. Please try again.');
    })
    .finally(() => {
        // Reset button state
        this.textContent = 'Reset PIN';
        this.disabled = false;
    });
});

// Photo cropping and upload functions
function showCropModal(input, playerSlug, playerName) {
    if (input.files && input.files[0]) {
        selectedFile = input.files[0];
        currentUserSlug = playerSlug;
        currentInputElement = input;
        
        document.getElementById('cropPlayerName').textContent = playerName;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '100%';
            
            const container = document.getElementById('crop-container');
            container.innerHTML = '';
            container.appendChild(img);
            
            cropper = new Cropper(img, {
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 1,
                responsive: true,
                cropBoxResizable: true,
                cropBoxMovable: true,
                guides: false,
                center: false,
                highlight: false,
                background: false,
                modal: true
            });
            
            document.getElementById('crop-modal').classList.remove('hidden');
        };
        reader.readAsDataURL(selectedFile);
    }
}

function closeCropModal() {
    document.getElementById('crop-modal').classList.add('hidden');
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    if (currentInputElement) {
        currentInputElement.value = '';
    }
    selectedFile = null;
    currentUserSlug = null;
}

function cropAndUpload() {
    if (cropper && selectedFile && currentUserSlug) {
        const canvas = cropper.getCroppedCanvas({
            width: 300,
            height: 300,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
        });
        
        canvas.toBlob(function(blob) {
            const formData = new FormData();
            formData.append('photo', blob, 'profile.jpg');
            formData.append('_token', '{{ csrf_token() }}');
            
            fetch(`/admin/players/${currentUserSlug}/update-photo`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeCropModal();
                    location.reload();
                } else {
                    console.error('Upload failed:', data.message);
                    alert('Upload failed: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Network error: ' + error.message);
            });
        }, 'image/jpeg', 0.85);
    }
}
</script>
@endsection
