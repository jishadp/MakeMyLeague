@extends('layouts.app')

@section('title', config('app.name') . ' - My Leagues')

@section('content')
    <div class="py-2 bg-gradient-to-br from-gray-50 via-white to-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 animate-fadeIn">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                    My Leagues
                </h1>
                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    @if (auth()->user()->isPlayer())
                        <button onclick="openPlayerRegistrationModal()"
                            class="bg-green-600 hover:bg-green-700 active:scale-95 transition-all duration-200
                              text-black px-5 py-2 rounded-xl shadow-md hover:shadow-lg w-full sm:w-auto text-center font-medium">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Register as Player
                        </button>
                    @endif
                    <a href="{{ route('leagues.create') }}"
                        class="bg-blue-700 hover:bg-blue-800 active:scale-95 transition-all duration-200
                          text-black px-5 py-2 rounded-xl shadow-md hover:shadow-lg w-full sm:w-auto text-center font-medium">
                        + Create New League
                    </a>
                </div>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div
                    class="bg-green-50 border border-green-200 text-green-800 p-4 mb-6 rounded-xl shadow-md animate-fadeInUp">
                    {{ session('success') }}
                </div>
            @endif

            <!-- No Leagues -->
            @if ($leagues->isEmpty())
                <div class="bg-white border border-gray-200 rounded-xl shadow-md p-8 text-center animate-fadeInUp">
                    <p class="text-gray-600 mb-4">No approved leagues available at the moment.</p>
                    <a href="{{ route('leagues.create') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                        Create your first league
                    </a>
                </div>
            @else
                <!-- League Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($leagues as $league)
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                            
                            <!-- Hero Image Section -->
                            <div class="relative h-48 overflow-hidden">
                                @if($league->banner)
                                    <img src="{{ Storage::url($league->banner) }}" alt="{{ $league->name }} Banner" 
                                         class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 flex items-center justify-center">
                                        <div class="text-center text-white">
                                            @if($league->logo)
                                                <img src="{{ Storage::url($league->logo) }}" alt="{{ $league->name }} Logo" 
                                                     class="w-16 h-16 rounded-full object-cover border-4 border-white/30 mx-auto mb-3">
                                            @else
                                                <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3">
                                                    <span class="text-white font-bold text-2xl">{{ substr($league->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                            <h3 class="text-2xl font-bold drop-shadow-lg">{{ $league->name }}</h3>
                                            <p class="text-sm opacity-90 drop-shadow">{{ $league->game->name }}</p>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Status Badge -->
                                <div class="absolute top-4 left-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium shadow-lg
                                        {{ $league->status === 'active'
                                            ? 'bg-green-500 text-white'
                                            : ($league->status === 'pending'
                                                ? 'bg-yellow-500 text-white'
                                                : ($league->status === 'completed'
                                                    ? 'bg-blue-500 text-white'
                                                    : 'bg-red-500 text-white')) }}">
                                        {{ ucfirst($league->status) }}
                                    </span>
                                </div>
                                
                                <!-- Default Badge -->
                                @if ($league->is_default)
                                    <div class="absolute top-4 right-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/90 text-green-700 shadow-lg">
                                            ⭐ Default
                                        </span>
                                    </div>
                                @endif
                                
                                <!-- League Name Overlay (if banner exists) -->
                                @if($league->banner)
                                    <div class="absolute bottom-4 left-4 right-4">
                                        <div class="flex items-center space-x-3">
                                            @if($league->logo)
                                                <img src="{{ Storage::url($league->logo) }}" alt="{{ $league->name }} Logo" 
                                                     class="w-12 h-12 rounded-full object-cover border-2 border-white/80 shadow-lg">
                                            @endif
                                            <div>
                                                <h3 class="text-xl font-bold text-white drop-shadow-lg">{{ $league->name }}</h3>
                                                <p class="text-sm text-white/90 drop-shadow">{{ $league->game->name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Content Section -->
                            <div class="p-6">
                                <!-- Quick Stats -->
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div class="text-center p-3 bg-gray-50 rounded-xl">
                                        <div class="text-2xl font-bold text-indigo-700">{{ $league->max_teams }}</div>
                                        <div class="text-xs text-gray-700">Max Teams</div>
                                    </div>
                                    <div class="text-center p-3 bg-gray-50 rounded-xl">
                                        <div class="text-2xl font-bold text-purple-700">{{ $league->max_team_players }}</div>
                                        <div class="text-xs text-gray-700">Players/Team</div>
                                    </div>
                                </div>
                                
                                <!-- Season & Duration -->
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center text-sm text-gray-700">
                                        <svg class="w-4 h-4 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-medium">Season {{ $league->season }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-700">
                                        <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $league->start_date->format('M d') }} - {{ $league->end_date->format('M d, Y') }}</span>
                                    </div>
                                </div>
                                
                                <!-- Venue Info -->
                                @if ($league->localBody)
                                    <div class="flex items-center text-sm text-gray-700 mb-4">
                                        <svg class="w-4 h-4 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $league->localBody->name }}, {{ $league->localBody->district->name }}</span>
                                    </div>
                                @endif
                                
                                <!-- Prize Pool (Highlighted) -->
                                @if ($league->winner_prize || $league->runner_prize)
                                    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-xl p-4 mb-4 text-white">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5zM8 15a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="font-bold text-lg">Prize Pool</span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            @if ($league->winner_prize)
                                                <div class="bg-white/20 rounded-lg p-2 text-center">
                                                    <div class="text-xs opacity-90">🥇 Winner</div>
                                                    <div class="font-bold">₹{{ number_format($league->winner_prize/1000, 0) }}K</div>
                                                </div>
                                            @endif
                                            @if ($league->runner_prize)
                                                <div class="bg-white/20 rounded-lg p-2 text-center">
                                                    <div class="text-xs opacity-90">🥈 Runner-up</div>
                                                    <div class="font-bold">₹{{ number_format($league->runner_prize/1000, 0) }}K</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Registration Fees -->
                                <div class="flex items-center justify-between text-sm mb-4 p-3 bg-blue-50 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-gray-800">Registration</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-gray-900">₹{{ number_format($league->team_reg_fee, 0) }}</div>
                                        <div class="text-xs text-gray-600">Team Fee</div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex gap-2">
                                    <a href="{{ route('leagues.show', $league) }}"
                                        class="flex-1 bg-indigo-600 text-black text-center py-3 px-4 rounded-xl font-semibold hover:bg-indigo-700 transition-colors shadow-lg hover:shadow-xl">
                                        View Details
                                    </a>
                                    
                                    <!-- Player Register Button -->
                                    @if (auth()->user()->isPlayer())
                                        @php
                                            $existingPlayer = \App\Models\LeaguePlayer::where('user_id', auth()->id())
                                                ->where('league_id', $league->id)
                                                ->first();
                                        @endphp
                                        
                                        @if (!$existingPlayer && in_array($league->status, ['active', 'pending']))
                                            <button onclick="openPaymentConfirmationModal({{ $league->id }}, '{{ addslashes($league->name) }}', {{ $league->player_reg_fee ?? 0 }}, '{{ addslashes($league->game->name) }}')"
                                                class="bg-green-600 text-black px-4 py-3 rounded-xl font-semibold hover:bg-green-700 transition-colors shadow-lg hover:shadow-xl">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Register
                                            </button>
                                        @elseif($existingPlayer && $existingPlayer->status === 'pending')
                                            <button disabled
                                                class="bg-yellow-600 text-black px-4 py-3 rounded-xl font-semibold cursor-not-allowed shadow-lg">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Pending
                                            </button>
                                        @elseif($existingPlayer && in_array($existingPlayer->status, ['approved', 'available', 'sold', 'active']))
                                            <span class="bg-green-600 text-black px-4 py-3 rounded-xl font-semibold shadow-lg">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Approved
                                            </span>
                                        @elseif(!in_array($league->status, ['active', 'pending']))
                                            <button disabled
                                                class="bg-gray-400 text-black px-4 py-3 rounded-xl font-semibold cursor-not-allowed shadow-lg">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Closed
                                            </button>
                                        @endif
                                    @endif
                                    
                                    <div class="flex gap-1">
                                        <!-- Edit -->
                                        <a href="{{ route('leagues.edit', $league) }}"
                                            class="p-3 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <!-- Set Default -->
                                        @if (!$league->is_default)
                                            <form action="{{ route('leagues.setDefault', $league) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="p-3 bg-yellow-100 text-yellow-600 rounded-xl hover:bg-yellow-200 transition-colors"
                                                    title="Set as Default">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                        <!-- Delete -->
                                        <form action="{{ route('leagues.destroy', $league) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Are you sure you want to delete this league?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-3 bg-red-100 text-red-600 rounded-xl hover:bg-red-200 transition-colors"
                                                title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination Links -->
                @if(method_exists($leagues, 'links'))
                    <div class="mt-8 flex justify-center">
                        {{ $leagues->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Player Registration Modal -->
    <div id="playerRegistrationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-4 sm:top-20 mx-auto p-4 sm:p-6 border w-11/12 max-w-md shadow-lg rounded-lg bg-white mb-20 sm:mb-24 lg:mb-32">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900">Register as Player</h3>
                    <button onclick="closePlayerRegistrationModal()" class="text-gray-400 hover:text-gray-600 p-1">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Registration Content -->
                <div class="space-y-4 sm:space-y-6">
                    <!-- League Selection -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <div class="bg-blue-100 rounded-full p-2 mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-blue-900">Select League</h4>
                        </div>
                        <div>
                            <label for="league_id" class="block text-sm font-medium text-blue-700 mb-2">Available Leagues *</label>
                            <select id="league_id" name="league_id" class="w-full border border-blue-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select a league</option>
                                @foreach ($leagues as $league)
                                    @if (in_array($league->status, ['active', 'pending']))
                                        @php
                                            $existingPlayer = \App\Models\LeaguePlayer::where('user_id', auth()->id())
                                                ->where('league_id', $league->id)
                                                ->first();
                                        @endphp
                                        @if (!$existingPlayer)
                                            <option value="{{ $league->id }}" data-reg-fee="{{ $league->player_reg_fee ?? 0 }}" data-game="{{ $league->game->name }}">
                                                {{ $league->name }} ({{ $league->game->name }}) - ₹{{ number_format($league->player_reg_fee ?? 0, 2) }}
                                            </option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <!-- Registration Info -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-green-100 rounded-full p-2 mr-3">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-green-900">Registration Details</h4>
                                    <p class="text-sm text-green-700">Registration fee will be displayed after selecting a league</p>
                                </div>
                            </div>
                            <button onclick="submitPlayerRegistration()" class="bg-green-600 text-black px-6 py-2 rounded-lg hover:bg-green-700 transition-colors font-medium shadow-lg">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Register
                            </button>
                        </div>
                    </div>

                    <!-- Registration Notice -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="bg-yellow-100 rounded-full p-2 mr-3">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-yellow-900">Important Notice</h4>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Your registration request will be reviewed by the league organizer. You'll be notified once approved.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Confirmation Modal -->
    <div id="paymentConfirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-4 sm:top-20 mx-auto p-4 sm:p-6 border w-11/12 max-w-md shadow-lg rounded-lg bg-white mb-20 sm:mb-24 lg:mb-32">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900">Payment Confirmation</h3>
                    <button onclick="closePaymentConfirmationModal()" class="text-gray-400 hover:text-gray-600 p-1">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="space-y-4 sm:space-y-6">
                    <!-- Payment Question -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <div class="bg-blue-100 rounded-full p-2 mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0-2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-blue-900">Registration Fee Payment</h4>
                        </div>
                        <p class="text-sm text-blue-700 mb-3">
                            Registration Fee: <span class="font-bold" id="paymentModalFee">₹0.00</span>
                        </p>
                        <p class="text-sm text-blue-700">
                            Have you paid the registration fee for this league?
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3">
                        <button onclick="confirmPaymentAndRegister()" 
                                class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium shadow-lg">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Yes, I Paid
                        </button>
                        <button onclick="closePaymentConfirmationModal()" 
                                class="flex-1 bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors font-medium shadow-lg">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </button>
                    </div>

                    <!-- Notice -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="bg-yellow-100 rounded-full p-2 mr-3">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-yellow-900">Important</h4>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Only proceed if you have already paid the registration fee. Your request will be reviewed by the league organizer.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Individual League Registration Modal -->
    <div id="leagueRegistrationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-4 sm:top-20 mx-auto p-4 sm:p-6 border w-11/12 max-w-md shadow-lg rounded-lg bg-white mb-20 sm:mb-24 lg:mb-32">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900">Register for League</h3>
                    <button onclick="closeLeagueRegistrationModal()" class="text-gray-400 hover:text-gray-600 p-1">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Registration Content -->
                <div class="space-y-4 sm:space-y-6">
                    <!-- League Information -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="bg-blue-100 rounded-full p-2 mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-blue-900" id="leagueModalName">League Name</h4>
                                    <p class="text-sm text-blue-700" id="leagueModalGame">Game Type</p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-blue-700">Registration Fee:</span>
                                <span class="text-sm font-medium text-blue-900" id="leagueModalFee">₹0.00</span>
                            </div>
                        </div>
                    </div>


                    <!-- Registration Action -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-green-100 rounded-full p-2 mr-3">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-green-900">Ready to Register?</h4>
                                    <p class="text-sm text-green-700">Click register to submit your request</p>
                                </div>
                            </div>
                            <button onclick="submitLeagueRegistration()" class="bg-green-600 text-black px-6 py-2 rounded-lg hover:bg-green-700 transition-colors font-medium shadow-lg">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Register
                            </button>
                        </div>
                    </div>

                    <!-- Registration Notice -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="bg-yellow-100 rounded-full p-2 mr-3">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-yellow-900">Important Notice</h4>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Your registration request will be reviewed by the league organizer. You'll be notified once approved.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="broadcastModal" tabindex="-1" aria-labelledby="dashboardBroadcastModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="dashboardBroadcastModalLabel">📢 Broadcast Alert</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="dashboardBroadcastMessage">
                    @include('auction.partials.player-bidding')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Animations -->
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>

    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script>    
        // Pusher logging disabled for production
        Pusher.logToConsole = false;

        var pusher = new Pusher('323027c72d05c6476b51', {
            cluster: 'ap2'
        });

        var channel = pusher.subscribe('order');
        channel.bind('player-binding', function(data) {
            $('#broadcastModal').modal('show');
        });

        // Success message function
        function showSuccessMessage(message) {
            // Create success message element
            const successDiv = document.createElement('div');
            successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 ease-in-out';
            successDiv.style.transform = 'translateX(100%)';
            successDiv.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium">${message}</span>
                </div>
            `;
            
            // Add to page
            document.body.appendChild(successDiv);
            
            // Animate in
            setTimeout(() => {
                successDiv.style.transform = 'translateX(0)';
            }, 100);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                successDiv.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (successDiv.parentNode) {
                        successDiv.parentNode.removeChild(successDiv);
                    }
                }, 300);
            }, 3000);
        }

        // Player Registration Modal Functions
        function openPlayerRegistrationModal() {
            document.getElementById('playerRegistrationModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closePlayerRegistrationModal() {
            document.getElementById('playerRegistrationModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            // Reset form
            document.getElementById('league_id').value = '';
        }


        // Submit player registration
        function submitPlayerRegistration() {
            const leagueId = document.getElementById('league_id').value;

            // Validate selections
            if (!leagueId) {
                alert('Please select a league');
                return;
            }

            // Show loading state
            const submitBtn = document.querySelector('button[onclick="submitPlayerRegistration()"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = `
                <svg class="w-4 h-4 inline mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Submitting...
            `;
            submitBtn.disabled = true;

            // Prepare data
            const formData = {};

            // Send AJAX request
            fetch(`/register-player/${leagueId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Close modal immediately
                        closePlayerRegistrationModal();
                        
                        // Show success message
                        showSuccessMessage('Registration request submitted successfully! You will be notified once approved.');
                        
                        // Reload page to update the UI
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        alert('Error: ' + (data.message || 'Unknown error occurred'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error: ' + error.message);
                })
                .finally(() => {
                    // Restore button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }

        // Close modal when clicking outside
        document.getElementById('playerRegistrationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePlayerRegistrationModal();
            }
        });

        // Payment confirmation modal functions
        let currentLeagueId = null;
        let currentLeagueName = '';
        let currentRegFee = 0;

        function openPaymentConfirmationModal(leagueId, leagueName, regFee, gameName) {
            currentLeagueId = leagueId;
            currentLeagueName = leagueName;
            currentRegFee = regFee;
            
            // Update modal content
            document.getElementById('paymentModalFee').textContent = `₹${parseFloat(regFee).toFixed(2)}`;
            
            // Show modal
            document.getElementById('paymentConfirmationModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closePaymentConfirmationModal() {
            document.getElementById('paymentConfirmationModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            currentLeagueId = null;
            currentLeagueName = '';
            currentRegFee = 0;
        }

        function confirmPaymentAndRegister() {
            if (!currentLeagueId) {
                alert('League not selected');
                return;
            }

            // Show loading state
            const submitBtn = document.querySelector('button[onclick="confirmPaymentAndRegister()"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = `
                <svg class="w-4 h-4 inline mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Submitting...
            `;
            submitBtn.disabled = true;

            // Prepare data
            const formData = {};

            // Send AJAX request
            fetch(`/register-player/${currentLeagueId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Close modal immediately
                        closePaymentConfirmationModal();
                        
                        // Show success message
                        showSuccessMessage('Registration request submitted successfully! You will be notified once approved.');
                        
                        // Reload page to update the UI
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        alert('Error: ' + (data.message || 'Unknown error occurred'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error: ' + error.message);
                })
                .finally(() => {
                    // Restore button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }

        // Close payment modal when clicking outside
        document.getElementById('paymentConfirmationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePaymentConfirmationModal();
            }
        });

        // League-specific registration modal functions (kept for compatibility)
        function openLeagueRegistrationModal(leagueId, leagueName, regFee, gameName) {
            // Redirect to payment confirmation modal
            openPaymentConfirmationModal(leagueId, leagueName, regFee, gameName);
        }

        function closeLeagueRegistrationModal() {
            closePaymentConfirmationModal();
        }

        function submitLeagueRegistration() {
            confirmPaymentAndRegister();
        }

        // Close league modal when clicking outside
        document.getElementById('leagueRegistrationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLeagueRegistrationModal();
            }
        });
    </script>
@endsection
