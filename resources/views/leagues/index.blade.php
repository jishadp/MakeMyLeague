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
                <a href="{{ route('leagues.create') }}"
                    class="bg-blue-700 hover:bg-blue-800 active:scale-95 transition-all duration-200
                      text-white px-5 py-2 rounded-xl shadow-md hover:shadow-lg w-full sm:w-auto text-center font-medium">
                    + Create New League
                </a>
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
                    <p class="text-gray-600 mb-4">You haven't created any leagues yet.</p>
                    <a href="{{ route('leagues.create') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                        Register by creating your first league
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
                                        <div class="text-2xl font-bold text-indigo-600">{{ $league->max_teams }}</div>
                                        <div class="text-xs text-gray-600">Max Teams</div>
                                    </div>
                                    <div class="text-center p-3 bg-gray-50 rounded-xl">
                                        <div class="text-2xl font-bold text-purple-600">{{ $league->max_team_players }}</div>
                                        <div class="text-xs text-gray-600">Players/Team</div>
                                    </div>
                                </div>
                                
                                <!-- Season & Duration -->
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-medium">Season {{ $league->season }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $league->start_date->format('M d') }} - {{ $league->end_date->format('M d, Y') }}</span>
                                    </div>
                                </div>
                                
                                <!-- Venue Info -->
                                @if ($league->localBody)
                                    <div class="flex items-center text-sm text-gray-600 mb-4">
                                        <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
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
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-gray-700">Registration</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-gray-900">₹{{ number_format($league->team_reg_fee, 0) }}</div>
                                        <div class="text-xs text-gray-500">Team Fee</div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex gap-2">
                                    <a href="{{ route('leagues.show', $league) }}"
                                        class="flex-1 bg-indigo-600 text-black text-center py-3 px-4 rounded-xl font-semibold hover:bg-indigo-700 transition-colors shadow-lg hover:shadow-xl">
                                        View Details
                                    </a>
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
            @endif
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
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('323027c72d05c6476b51', {
            cluster: 'ap2'
        });

        var channel = pusher.subscribe('order');
        channel.bind('player-binding', function(data) {
            $('#broadcastModal').modal('show');
        });
    </script>
@endsection
