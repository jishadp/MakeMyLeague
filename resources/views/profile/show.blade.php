@extends('layouts.app')

@section('title', 'My Profile - ' . config('app.name'))

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-indigo-600 to-purple-600 py-6 sm:py-8 px-4 sm:px-6 lg:px-8 text-white animate-fadeIn">
    <div class="max-w-7xl mx-auto">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-4 drop-shadow">
                My Profile
            </h1>
            <p class="text-lg sm:text-xl text-white/90">
                Manage your account details and preferences
            </p>
        </div>
    </div>
</section>

<!-- Profile Content -->
<section class="py-6 sm:py-8 lg:py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 p-4 mb-4 sm:mb-6 lg:mb-8 rounded-xl shadow-md animate-fadeInUp">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
            {{ session('success') }}
                </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 lg:gap-12">
            <!-- Profile Photo Section -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden animate-fadeInUp">
                <div class="bg-gradient-to-br from-indigo-600 to-purple-600 px-6 py-8 text-center">
                    <div class="relative inline-block">
                        <div class="w-28 h-28 rounded-full overflow-hidden border-4 border-white shadow-xl mx-auto">
                            @if($user->photo)
                                <img id="profile-image" src="{{ Storage::url($user->photo) }}" alt="Profile" class="w-full h-full object-cover">
                            @else
                                <div id="profile-placeholder" class="w-full h-full bg-white/20 flex items-center justify-center">
                                    <svg class="w-14 h-14 text-white/80" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <button type="button" onclick="document.getElementById('photo-input').click()" 
                                class="absolute -bottom-2 -right-2 bg-white rounded-full p-3 shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                        <input type="file" id="photo-input" accept="image/*" class="hidden" onchange="showCropModal(this)">
                    </div>
                    <h2 class="text-2xl font-bold text-white mt-6">{{ $user->name }}</h2>
                    <p class="text-white/80 text-sm mt-1">{{ $user->email }}</p>
                </div>

                <!-- Account Information -->
                <div class="p-4 sm:p-6 lg:p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 text-indigo-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        Account Information
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-gray-600 font-medium">Member Since</span>
                            <span class="font-semibold text-gray-900">{{ $user->created_at->format('M Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-gray-600 font-medium">Account Status</span>
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">Active</span>
                        </div>
                        @if($user->roles->isNotEmpty())
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-gray-600 font-medium">Roles</span>
                            <div class="flex flex-wrap gap-2">
                                @if($user->isAdmin()) 
                                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">Admin</span>
                                @endif
                                @if($user->isOrganizer()) 
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">Organizer</span>
                                @endif
                                @if($user->isTeamOwner()) 
                                    <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">Team Owner</span>
                                @endif
                                @if($user->isPlayer()) 
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">Player</span>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        @if($user->isPlayer())
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-gray-600 font-medium">Player Profile</span>
                            <a href="{{ route('players.show', $user) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200 shadow-md hover:shadow-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                View Player Profile
                            </a>
                        </div>
                        @endif
                        @if($user->gameRoles->isNotEmpty())
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-gray-600 font-medium">Game Roles</span>
                            <div class="text-right">
                                @foreach($user->gameRoles as $gameRole)
                                    <div class="mb-1">
                                        <span class="font-semibold text-gray-900">{{ $gameRole->game->name }}</span>
                                        @if($gameRole->is_primary)
                                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium ml-2">Primary</span>
                                        @endif
                                        <br>
                                        <span class="text-sm text-gray-600">{{ $gameRole->gamePosition->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @if($user->localBody)
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-gray-600 font-medium">Location</span>
                            <span class="font-semibold text-gray-900">{{ $user->localBody->name }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between items-center py-3">
                            <span class="text-gray-600 font-medium">Phone</span>
                            <span class="font-semibold text-gray-900">{{ $user->country_code }} {{ $user->mobile }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="lg:col-span-2 space-y-6 sm:space-y-8 lg:space-y-12">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic Information -->
                    <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 lg:p-10 mb-6 sm:mb-8 animate-fadeInUp">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                            Basic Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Full Name</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                @error('name')
                                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                       placeholder="Enter your email address">
                                @error('email')
                                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Phone Number</label>
                                <div class="flex gap-3">
                                    <select name="country_code" class="px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <option value="+91" {{ old('country_code', $user->country_code) == '+91' ? 'selected' : '' }}>+91</option>
                                        <option value="+1" {{ old('country_code', $user->country_code) == '+1' ? 'selected' : '' }}>+1</option>
                                        <option value="+44" {{ old('country_code', $user->country_code) == '+44' ? 'selected' : '' }}>+44</option>
                                    </select>
                                    <input type="text" name="mobile" value="{{ old('mobile', $user->mobile) }}" 
                                           class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                                @error('mobile')
                                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Game Information -->
                    <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 lg:p-10 mb-6 sm:mb-8 animate-fadeInUp">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                            <svg class="w-6 h-6 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Game Information
                        </h3>
                            <button type="button" id="toggleGameRoles" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span id="toggleButtonText">Edit Roles</span>
                            </button>
                        </div>
                        
                        <!-- View Mode - Display Current Roles -->
                        <div id="gameRolesView" class="space-y-4">
                            @if($user->gameRoles->isNotEmpty())
                                @foreach($user->gameRoles as $gameRole)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 border border-gray-200 rounded-xl">
                                        <div class="flex items-center">
                                            @if($gameRole->game->name === 'Cricket')
                                                <svg class="w-6 h-6 text-orange-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            @elseif($gameRole->game->name === 'Football')
                                                <svg class="w-6 h-6 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            @elseif($gameRole->game->name === 'Badminton')
                                                <svg class="w-6 h-6 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            @elseif($gameRole->game->name === 'Table Tennis')
                                                <svg class="w-6 h-6 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                        <div>
                                                <h4 class="text-lg font-semibold text-gray-900">{{ $gameRole->game->name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $gameRole->gamePosition->name }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            @if($gameRole->is_primary)
                                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium mr-3">
                                                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Primary
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-8 bg-gray-50 border border-gray-200 rounded-xl">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    <p class="text-gray-500 text-lg font-medium">No Game Roles Selected</p>
                                    <p class="text-gray-400 text-sm mt-1">Click "Edit Roles" to add your game positions</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Edit Mode - Form for Editing Roles -->
                        <div id="gameRolesEdit" class="space-y-6 hidden">
                            @foreach($games as $game)
                                <div class="border border-gray-200 rounded-xl p-4">
                                    <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                                        @if($game->name === 'Cricket')
                                            <svg class="w-5 h-5 text-orange-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        @elseif($game->name === 'Football')
                                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        @elseif($game->name === 'Badminton')
                                            <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        @elseif($game->name === 'Table Tennis')
                                            <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                        {{ $game->name }}
                                    </h4>
                                    <p class="text-sm text-gray-600 mb-3">{{ $game->description }}</p>
                                    
                                    @php
                                        $userGameRole = $user->gameRoles->where('game_id', $game->id)->first();
                                    @endphp
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($game->roles as $position)
                                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 cursor-pointer transition-all duration-200 {{ $userGameRole && $userGameRole->game_position_id == $position->id ? 'bg-green-50 border-green-400 shadow-md' : '' }}">
                                                <input type="radio" name="game_roles[{{ $game->id }}][position_id]" value="{{ $position->id }}" 
                                                       class="mr-3 text-green-600 focus:ring-green-500 h-4 w-4" 
                                                       {{ $userGameRole && $userGameRole->game_position_id == $position->id ? 'checked' : '' }}>
                                                <span class="text-sm font-semibold text-gray-800">{{ $position->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    
                                    <!-- Hidden input for game_id -->
                                    <input type="hidden" name="game_roles[{{ $game->id }}][game_id]" value="{{ $game->id }}">
                                    
                                    <!-- Primary game selection -->
                                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="primary_game_id" value="{{ $game->id }}" 
                                                   class="mr-3 text-yellow-600 focus:ring-yellow-500 h-4 w-4"
                                                   {{ $userGameRole && $userGameRole->is_primary ? 'checked' : '' }}>
                                            <span class="text-sm font-semibold text-yellow-800 flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" clip-rule="evenodd"/>
                                                </svg>
                                                Set as Primary Game
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @error('game_roles')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                        @error('primary_game_id')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location Information -->
                    <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 lg:p-10 mb-6 sm:mb-8 animate-fadeInUp">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            Location Information
                        </h3>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Local Body</label>
                            <select name="local_body_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                                <option value="">Select Local Body</option>
                                @foreach($localBodies as $localBody)
                                    <option value="{{ $localBody->id }}" {{ old('local_body_id', $user->local_body_id) == $localBody->id ? 'selected' : '' }}>
                                        {{ $localBody->name }} - {{ $localBody->district->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('local_body_id')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Security -->
                    <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 lg:p-10 mb-6 sm:mb-8 animate-fadeInUp">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            Security
                        </h3>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">PIN (Leave blank to keep current)</label>
                            <input type="password" name="pin" placeholder="Enter new PIN" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                            @error('pin')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 sm:py-4 px-6 rounded-xl font-semibold hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl active:scale-95">
                        <svg class="w-5 h-5 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Update Profile
                    </button>
                    
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Crop Modal -->
<div id="crop-modal" class="fixed inset-0 bg-black/50 z-50 hidden backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-8 shadow-2xl animate-fadeInUp">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 text-indigo-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" clip-rule="evenodd"/>
                </svg>
                Crop Profile Photo
            </h3>
            <div class="mb-6">
                <div id="crop-container" class="w-full h-64 bg-gray-100 rounded-xl overflow-hidden border-2 border-gray-200"></div>
            </div>
            <div class="flex gap-4">
                <button onclick="closeCropModal()" class="flex-1 px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 font-medium transition-colors">Cancel</button>
                <button onclick="cropAndUpload()" class="flex-1 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 font-medium transition-all duration-200 shadow-lg hover:shadow-xl">Upload</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">

<script>
// Wait for jQuery and Select2 to load
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit more for deferred scripts to load
    setTimeout(function() {
        if (typeof $ !== 'undefined' && $.fn.select2) {
    $('select').select2({
        theme: 'default',
        width: '100%',
        placeholder: function() {
            return $(this).find('option:first').text();
        }
    });
        }
    }, 100);
    
    // Toggle between view and edit modes for game roles
    const toggleButton = document.getElementById('toggleGameRoles');
    const toggleButtonText = document.getElementById('toggleButtonText');
    const gameRolesView = document.getElementById('gameRolesView');
    const gameRolesEdit = document.getElementById('gameRolesEdit');
    
    if (toggleButton && gameRolesView && gameRolesEdit) {
        toggleButton.addEventListener('click', function() {
            if (gameRolesView.classList.contains('hidden')) {
                // Switch to view mode
                gameRolesView.classList.remove('hidden');
                gameRolesEdit.classList.add('hidden');
                toggleButtonText.textContent = 'Edit Roles';
                toggleButton.classList.remove('bg-green-600', 'hover:bg-green-700');
                toggleButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
            } else {
                // Switch to edit mode
                gameRolesView.classList.add('hidden');
                gameRolesEdit.classList.remove('hidden');
                toggleButtonText.textContent = 'View Roles';
                toggleButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                toggleButton.classList.add('bg-green-600', 'hover:bg-green-700');
            }
        });
    }
    
    // Handle form submission for game roles
    const form = document.querySelector('form[action="{{ route('profile.update') }}"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Remove any existing hidden inputs to avoid duplicates
            document.querySelectorAll('input[name*="game_roles"][type="hidden"]').forEach(function(input) {
                if (input.name.includes('[position_id]')) {
                    input.remove();
                }
            });
            
            // Collect game roles data
            const gameRoles = {};
            
            document.querySelectorAll('input[name*="game_roles"]').forEach(function(input) {
                if (input.type === 'radio' && input.checked) {
                    const nameParts = input.name.match(/game_roles\[(\d+)\]\[position_id\]/);
                    if (nameParts) {
                        const gameId = nameParts[1];
                        gameRoles[gameId] = {
                            position_id: input.value
                        };
                    }
                }
            });
            
            // Add hidden inputs for game roles in the correct format
            Object.keys(gameRoles).forEach(function(gameId) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'game_roles[' + gameId + '][position_id]';
                hiddenInput.value = gameRoles[gameId].position_id;
                form.appendChild(hiddenInput);
            });
        });
    }
});


let cropper;
let selectedFile;

function showCropModal(input) {
    if (input.files && input.files[0]) {
        selectedFile = input.files[0];
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
    document.getElementById('photo-input').value = '';
}

function cropAndUpload() {
    if (cropper && selectedFile) {
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
            formData.append('_method', 'PUT');
            
            fetch('{{ route("profile.update") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                // Debug logging removed for production
                return response.json();
            })
            .then(data => {
                // Debug logging removed for production
                if (data.success) {
                    location.reload();
                } else {
                    console.error('Upload failed:', data.error);
                    alert('Upload failed: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Network error: ' + error.message);
            });
        }, 'image/jpeg', 0.85);
        
        closeCropModal();
    }
}
</script>

<style>
@keyframes fadeIn { 
    from { opacity: 0; transform: translateY(20px); } 
    to { opacity: 1; transform: translateY(0); } 
}
@keyframes fadeInUp { 
    from { opacity: 0; transform: translateY(30px); } 
    to { opacity: 1; transform: translateY(0); } 
}
.animate-fadeIn { animation: fadeIn 0.6s ease-out; }
.animate-fadeInUp { animation: fadeInUp 0.6s ease-out; }

/* Select2 Custom Styling to Match Theme */
.select2-container--default .select2-selection--single {
    background-color: white !important;
    border: 1px solid #d1d5db !important;
    border-radius: 0.75rem !important;
    height: 48px !important;
    padding: 0 !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #374151 !important;
    line-height: 46px !important;
    padding-left: 16px !important;
    padding-right: 20px !important;
}

.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #9ca3af !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 46px !important;
    right: 8px !important;
    top: 1px !important;
}

.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    outline: none !important;
}

.select2-container--default .select2-selection--single:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
}

.select2-dropdown {
    border: 1px solid #d1d5db !important;
    border-radius: 0.75rem !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
}

.select2-container--default .select2-results__option {
    padding: 12px 16px !important;
    color: #374151 !important;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #3b82f6 !important;
    color: white !important;
}

.select2-container--default .select2-results__option[aria-selected=true] {
    background-color: #e5e7eb !important;
    color: #374151 !important;
}

.select2-container--default .select2-search--dropdown .select2-search__field {
    border: 1px solid #d1d5db !important;
    border-radius: 0.5rem !important;
    padding: 8px 12px !important;
    margin: 8px !important;
}

/* Position-specific styling */
select[name="position_id"] + .select2-container--default .select2-selection--single {
    border-color: #10b981 !important;
}

select[name="position_id"] + .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #10b981 !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
}

/* Local body-specific styling */
select[name="local_body_id"] + .select2-container--default .select2-selection--single {
    border-color: #8b5cf6 !important;
}

select[name="local_body_id"] + .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #8b5cf6 !important;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1) !important;
}
</style>
@endsection