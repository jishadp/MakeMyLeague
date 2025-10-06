@extends('layouts.app')

@section('title', config('app.name') . ' - ' . $league->name)

@section('content')
    <!-- Notification System -->
    <div id="notification" class="fixed top-4 right-4 z-50 hidden">
        <div class="bg-white border-l-4 border-gray-400 shadow-lg rounded-lg p-4 max-w-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0 mr-3">
                    <svg id="notification-icon" class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <p id="notification-message" class="text-sm font-medium text-gray-900"></p>
                </div>
                <button onclick="hideNotification()" class="ml-3 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="py-2 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Main Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="p-4 sm:p-6 lg:p-10">

                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row justify-between items-start mb-8 sm:mb-10 gap-4 sm:gap-6">
                        <div>
                            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-2">{{ $league->name }}
                            </h1>
                            <div class="flex items-center gap-3 mb-4">
                                <span
                                    class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    Season {{ $league->season }}
                                </span>
                                <span
                                    class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold
                                {{ $league->status === 'active'
                                    ? 'bg-green-100 text-green-800'
                                    : ($league->status === 'pending'
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : ($league->status === 'completed'
                                            ? 'bg-blue-100 text-blue-800'
                                            : 'bg-red-100 text-red-800')) }}">
                                    {{ ucfirst($league->status) }}
                                </span>
                                @if ($league->is_default)
                                    <span
                                        class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold bg-green-50 text-green-700 border border-green-200">
                                        Default
                                    </span>
                                @endif
                            </div>


                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                            <!-- Mobile: 2 buttons per row -->
                            <div class="grid grid-cols-2 gap-3 sm:hidden">
                                @if (auth()->user()->isOrganizer())
                                    <a href="{{ route('leagues.edit', $league) }}"
                                        class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 text-white font-medium rounded-lg shadow-sm hover:bg-indigo-700 transition-colors text-center text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                        Edit
                                    </a>
                                @endif
                                @if (auth()->user()->isTeamOwner())
                                    <a href="{{ route('teams.create') }}?league_slug={{ $league->slug }}"
                                        class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white font-medium rounded-lg shadow-sm hover:bg-blue-700 transition-colors text-center text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Add Team
                                    </a>
                                    <button onclick="openOwnershipModal()"
                                        class="inline-flex items-center justify-center px-4 py-2.5 bg-purple-600 text-white font-medium rounded-lg shadow-sm hover:bg-purple-700 transition-colors text-center text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                            </path>
                                        </svg>
                                        Ownership
                                    </button>
                                @endif

                                @if (auth()->user()->isPlayer())
                                    @php
                                        $existingPlayer = \App\Models\LeaguePlayer::where('user_id', auth()->id())
                                            ->where('league_id', $league->id)
                                            ->first();
                                    @endphp

                                    @if (!$existingPlayer && in_array($league->status, ['active', 'pending']))
                                        <button onclick="openRegistrationModal()"
                                            class="inline-flex items-center justify-center px-4 py-2.5 bg-green-600 text-white font-medium rounded-lg shadow-sm hover:bg-green-700 transition-colors text-center text-sm">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Register
                                        </button>
                                    @elseif(!in_array($league->status, ['active', 'pending']))
                                        <button disabled
                                            class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-400 text-white font-medium rounded-lg shadow-sm cursor-not-allowed text-center text-sm">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Registration Closed
                                        </button>
                                    @elseif($existingPlayer->status === 'pending')
                                        <button disabled
                                            class="inline-flex items-center justify-center px-4 py-2.5 bg-yellow-600 text-white font-medium rounded-lg shadow-sm cursor-not-allowed text-center text-sm">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Pending
                                        </button>
                                    @elseif(in_array($existingPlayer->status, ['approved', 'available', 'sold', 'active']))
                                        <span
                                            class="inline-flex items-center justify-center px-4 py-2.5 bg-green-600 text-white font-medium rounded-lg text-center text-sm">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Approved
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-600 text-white font-medium rounded-lg text-center text-sm">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ ucfirst($existingPlayer->status) }}
                                        </span>
                                    @endif
                                @endif
                                <a href="{{ route('leagues.index') }}"
                                    class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 text-gray-800 font-medium rounded-lg shadow-sm hover:bg-gray-200 transition-colors text-center text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Back
                                </a>
                            </div>

                            <!-- Mobile: Add Player and Team buttons -->
                            @if (auth()->user()->isOrganizer())
                                <div class="grid grid-cols-2 gap-3 sm:hidden">
                                    <a href="{{ route('players.create') }}?league_slug={{ $league->slug }}"
                                        class="inline-flex items-center justify-center px-4 py-2.5 bg-green-600 text-white font-medium rounded-lg shadow-sm hover:bg-green-700 transition-colors text-center text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Add Player
                                    </a>
                                    <a href="{{ route('teams.create') }}?league_slug={{ $league->slug }}"
                                        class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white font-medium rounded-lg shadow-sm hover:bg-blue-700 transition-colors text-center text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Add Team
                                    </a>
                                </div>
                            @endif

                            <!-- Desktop: Original layout -->
                            <div class="hidden sm:flex flex-col gap-3">
                                <div class="flex gap-3">
                                    @if (auth()->user()->isOrganizer())
                                        <a href="{{ route('leagues.edit', $league) }}"
                                            class="inline-flex items-center justify-center px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg shadow-sm hover:bg-indigo-700 transition-colors text-base">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            Edit League
                                        </a>
                                    @endif
                                    @if (auth()->user()->isTeamOwner())
                                        <button onclick="openOwnershipModal()"
                                            class="inline-flex items-center justify-center px-6 py-2.5 bg-purple-600 text-white font-medium rounded-lg shadow-sm hover:bg-purple-700 transition-colors text-base">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                                </path>
                                            </svg>
                                            Ownership
                                        </button>
                                    @endif
                                    <a href="{{ route('leagues.index') }}"
                                        class="inline-flex items-center justify-center px-6 py-2.5 bg-gray-100 text-gray-800 font-medium rounded-lg shadow-sm hover:bg-gray-200 transition-colors text-base">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                        </svg>
                                        Back to Leagues
                                    </a>
                                </div>
                                @if (auth()->user()->isOrganizer() || auth()->user()->isTeamOwner())
                                    <div class="flex gap-3">
                                        <a href="{{ route('players.create') }}?league_slug={{ $league->slug }}"
                                            class="inline-flex items-center justify-center px-6 py-2.5 bg-green-600 text-white font-medium rounded-lg shadow-sm hover:bg-green-700 transition-colors text-base">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Create Player
                                        </a>
                                        <a href="{{ route('teams.create') }}?league_slug={{ $league->slug }}"
                                            class="inline-flex items-center justify-center px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg shadow-sm hover:bg-blue-700 transition-colors text-base">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Create Team
                                        </a>
                                    </div>
                                @endif
                                @if (auth()->user()->isPlayer())
                                    <div class="flex gap-3">
                                        @php
                                            $existingPlayer = \App\Models\LeaguePlayer::where('user_id', auth()->id())
                                                ->where('league_id', $league->id)
                                                ->first();
                                        @endphp

                                        @if (!$existingPlayer && in_array($league->status, ['active', 'pending']))
                                            <button onclick="openRegistrationModal()"
                                                class="inline-flex items-center justify-center px-6 py-2.5 bg-green-600 text-white font-medium rounded-lg shadow-sm hover:bg-green-700 transition-colors text-base">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Register
                                            </button>
                                        @elseif(!in_array($league->status, ['active', 'pending']))
                                            <button disabled
                                                class="inline-flex items-center justify-center px-6 py-2.5 bg-gray-400 text-white font-medium rounded-lg shadow-sm cursor-not-allowed text-base">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Registration Closed
                                            </button>
                                        @elseif($existingPlayer->status === 'pending')
                                            <button disabled
                                                class="inline-flex items-center justify-center px-6 py-2.5 bg-yellow-600 text-white font-medium rounded-lg shadow-sm cursor-not-allowed text-base">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Pending
                                            </button>
                                        @elseif(in_array($existingPlayer->status, ['approved', 'available', 'sold', 'active']))
                                            <span
                                                class="inline-flex items-center justify-center px-6 py-2.5 bg-green-600 text-white font-medium rounded-lg text-base">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Approved
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center justify-center px-6 py-2.5 bg-gray-600 text-white font-medium rounded-lg text-base">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ ucfirst($existingPlayer->status) }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @if (auth()->user()->isOrganizer() || auth()->user()->isTeamOwner())
                        <!-- Quick Actions -->
                        <div class="mb-8 sm:mb-12">
                            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                                <!-- Teams Card -->
                                <a href="{{ route('league-teams.index', $league) }}"
                                    class="group relative flex flex-col items-center justify-center p-4 sm:p-6 bg-gradient-to-br from-indigo-50 via-indigo-50 to-indigo-100 text-indigo-700 rounded-xl hover:from-indigo-100 hover:to-indigo-200 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 border border-indigo-100 hover:border-indigo-200 overflow-hidden">

                                    <!-- Background Pattern -->
                                    <div class="absolute inset-0 opacity-5">
                                        <div
                                            class="absolute top-0 right-0 w-16 h-16 bg-indigo-600 rounded-full transform translate-x-8 -translate-y-8">
                                        </div>
                                        <div
                                            class="absolute bottom-0 left-0 w-12 h-12 bg-indigo-400 rounded-full transform -translate-x-6 translate-y-6">
                                        </div>
                                    </div>

                                    <!-- Icon Container -->
                                    <div
                                        class="relative z-10 p-3 bg-white/50 backdrop-blur-sm rounded-full mb-3 group-hover:scale-110 transition-transform duration-300 group-hover:rotate-6">
                                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                            </path>
                                        </svg>
                                    </div>

                                    <!-- Content -->
                                    <div class="relative z-10 text-center">
                                        <h3 class="text-sm sm:text-base font-semibold text-indigo-800 mb-1">Teams</h3>
                                        <div class="flex items-center justify-center space-x-2">
                                            <span
                                                class="text-xs text-indigo-600 font-medium bg-white/60 px-2 py-1 rounded-full">
                                                {{ $leagueTeamsCount }} {{ Str::plural('Team', $leagueTeamsCount) }}
                                            </span>
                                        </div>
                                    </div>
                                </a>

                                <!-- Players Card -->
                                <a href="{{ route('league-players.index', $league) }}"
                                    class="group relative flex flex-col items-center justify-center p-4 sm:p-6 bg-gradient-to-br from-green-50 via-green-50 to-green-100 text-green-700 rounded-xl hover:from-green-100 hover:to-green-200 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 border border-green-100 hover:border-green-200 overflow-hidden">

                                    <!-- Background Pattern -->
                                    <div class="absolute inset-0 opacity-5">
                                        <div
                                            class="absolute top-0 left-0 w-14 h-14 bg-green-600 rounded-full transform -translate-x-7 -translate-y-7">
                                        </div>
                                        <div
                                            class="absolute bottom-0 right-0 w-10 h-10 bg-green-400 rounded-full transform translate-x-5 translate-y-5">
                                        </div>
                                    </div>

                                    <!-- Icon Container -->
                                    <div
                                        class="relative z-10 p-3 bg-white/50 backdrop-blur-sm rounded-full mb-3 group-hover:scale-110 transition-transform duration-300 group-hover:-rotate-6">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                    </div>

                                    <!-- Content -->
                                    <div class="relative z-10 text-center">
                                        <h3 class="text-sm sm:text-base font-semibold text-green-800 mb-1">Players</h3>
                                        <div class="flex items-center justify-center space-x-2">
                                            <span
                                                class="text-xs text-green-600 font-medium bg-white/60 px-2 py-1 rounded-full">
                                                {{ $leaguePlayersCount }} {{ Str::plural('Player', $leaguePlayersCount) }}
                                            </span>
                                        </div>
                                    </div>
                                </a>

                                <!-- League Match Setup / Auction Card -->
                                @if($league->status === 'auction_completed')
                                <a href="{{ route('leagues.league-match', $league->slug) }}"
                                    class="group relative flex flex-col items-center justify-center p-4 sm:p-6 bg-gradient-to-br from-green-50 via-emerald-50 to-green-100 text-green-700 rounded-xl hover:from-green-100 hover:to-emerald-200 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 border border-green-100 hover:border-green-200 overflow-hidden">

                                    <!-- Background Pattern -->
                                    <div class="absolute inset-0 opacity-5">
                                        <div
                                            class="absolute top-2 right-2 w-12 h-12 bg-green-600 rounded-full transform translate-x-6 -translate-y-6">
                                        </div>
                                        <div
                                            class="absolute bottom-2 left-2 w-8 h-8 bg-emerald-400 rounded-full transform -translate-x-4 translate-y-4">
                                        </div>
                                    </div>

                                    <!-- Icon Container -->
                                    <div
                                        class="relative z-10 p-3 bg-white/50 backdrop-blur-sm rounded-full mb-3 group-hover:scale-110 transition-transform duration-300 group-hover:rotate-12">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                            </path>
                                        </svg>
                                    </div>

                                    <!-- Content -->
                                    <div class="relative z-10 text-center">
                                        <h3 class="text-sm sm:text-base font-semibold text-green-800 mb-1">League Match</h3>
                                        <div class="flex items-center justify-center">
                                            <span
                                                class="text-xs text-green-600 font-medium bg-white/60 px-2 py-1 rounded-full">
                                                Setup Groups
                                            </span>
                                        </div>
                                    </div>
                                </a>
                                @else
                                <a href="{{ route('auction.index', $league->slug) }}"
                                    class="group relative flex flex-col items-center justify-center p-4 sm:p-6 bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-100 text-amber-700 rounded-xl hover:from-amber-100 hover:to-orange-200 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 border border-amber-100 hover:border-amber-200 overflow-hidden">

                                    <!-- Background Pattern -->
                                    <div class="absolute inset-0 opacity-5">
                                        <div
                                            class="absolute top-2 right-2 w-12 h-12 bg-amber-600 rounded-full transform translate-x-6 -translate-y-6">
                                        </div>
                                        <div
                                            class="absolute bottom-2 left-2 w-8 h-8 bg-orange-400 rounded-full transform -translate-x-4 translate-y-4">
                                        </div>
                                    </div>

                                    <!-- Icon Container -->
                                    <div
                                        class="relative z-10 p-3 bg-white/50 backdrop-blur-sm rounded-full mb-3 group-hover:scale-110 transition-transform duration-300 group-hover:rotate-12">
                                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                                            </path>
                                        </svg>
                                    </div>

                                    <!-- Content -->
                                    <div class="relative z-10 text-center">
                                        <h3 class="text-sm sm:text-base font-semibold text-amber-800 mb-1">Auction</h3>
                                        <div class="flex items-center justify-center">
                                            <span
                                                class="text-xs text-amber-600 font-medium bg-white/60 px-2 py-1 rounded-full">
                                                Live Bidding
                                            </span>
                                        </div>
                                    </div>
                                </a>
                                @endif


                                @if($fixturesCount > 0)
                                <!-- Fixtures Card -->
                                <a href="{{ route('leagues.fixtures', $league) }}"
                                    class="group relative flex flex-col items-center justify-center p-4 sm:p-6 bg-gradient-to-br from-orange-50 via-amber-50 to-orange-100 text-orange-700 rounded-xl hover:from-orange-100 hover:to-amber-200 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 border border-orange-100 hover:border-orange-200 overflow-hidden">

                                    <!-- Background Pattern -->
                                    <div class="absolute inset-0 opacity-5">
                                        <div
                                            class="absolute top-1 left-1 w-16 h-16 bg-orange-600 rounded-full transform -translate-x-8 -translate-y-8">
                                        </div>
                                        <div
                                            class="absolute bottom-1 right-1 w-10 h-10 bg-amber-400 rounded-full transform translate-x-5 translate-y-5">
                                        </div>
                                    </div>

                                    <!-- Icon Container -->
                                    <div
                                        class="relative z-10 p-3 bg-white/50 backdrop-blur-sm rounded-full mb-3 group-hover:scale-110 transition-transform duration-300 group-hover:-rotate-12">
                                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>

                                    <!-- Content -->
                                    <div class="relative z-10 text-center">
                                        <h3 class="text-sm sm:text-base font-semibold text-orange-800 mb-1">Fixtures</h3>
                                        <div class="flex items-center justify-center">
                                            <span
                                                class="text-xs text-orange-600 font-medium bg-white/60 px-2 py-1 rounded-full">
                                                {{ $fixturesCount }} {{ Str::plural('Match', $fixturesCount) }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                                @else
                                <!-- Auction Rules Card -->
                                <button onclick="openAuctionRulesModal()"
                                    class="group relative flex flex-col items-center justify-center p-4 sm:p-6 bg-gradient-to-br from-purple-50 via-violet-50 to-purple-100 text-purple-700 rounded-xl hover:from-purple-100 hover:to-violet-200 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 border border-purple-100 hover:border-purple-200 cursor-pointer overflow-hidden">

                                    <!-- Background Pattern -->
                                    <div class="absolute inset-0 opacity-5">
                                        <div
                                            class="absolute top-1 left-1 w-16 h-16 bg-purple-600 rounded-full transform -translate-x-8 -translate-y-8">
                                        </div>
                                        <div
                                            class="absolute bottom-1 right-1 w-10 h-10 bg-violet-400 rounded-full transform translate-x-5 translate-y-5">
                                        </div>
                                    </div>

                                    <!-- Icon Container -->
                                    <div
                                        class="relative z-10 p-3 bg-white/50 backdrop-blur-sm rounded-full mb-3 group-hover:scale-110 transition-transform duration-300 group-hover:-rotate-12">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>

                                    <!-- Content -->
                                    <div class="relative z-10 text-center">
                                        <h3 class="text-sm sm:text-base font-semibold text-purple-800 mb-1">Rules</h3>
                                        <div class="flex items-center justify-center">
                                            <span
                                                class="text-xs text-purple-600 font-medium bg-white/60 px-2 py-1 rounded-full">
                                                Guidelines
                                            </span>
                                        </div>
                                    </div>
                                </button>
                                @endif
                            </div>
                        </div>
                    @endif
                    <!-- League Summary -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 mb-8 sm:mb-12">
                        <!-- Card -->
                        <div class="bg-gray-50 p-6 rounded-xl shadow-sm border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Info</h3>
                            <dl class="space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Game:</dt>
                                    <dd class="font-medium">{{ $league->game->name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Season:</dt>
                                    <dd class="font-medium">{{ $league->season }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Organizers:</dt>
                                    <dd class="font-medium">
                                        @if($league->approvedOrganizers->count() > 0)
                                            @foreach($league->approvedOrganizers as $organizer)
                                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">
                                                    {{ $organizer->name }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-gray-500">No organizers assigned</span>
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Created by:</dt>
                                    <dd class="font-medium">
                                        @php
                                            // Get the creator - first organizer (regardless of status)
                                            $creator = $league->organizers->first();
                                        @endphp
                                        @if($creator)
                                            <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">
                                                {{ $creator->name }}
                                                @if($creator->pivot->status === 'pending')
                                                    <span class="text-yellow-600 ml-1">(Pending Approval)</span>
                                                @elseif($creator->pivot->status === 'approved')
                                                    <span class="text-green-600 ml-1">(Approved)</span>
                                                @else
                                                    <span class="text-red-600 ml-1">({{ ucfirst($creator->pivot->status) }})</span>
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-gray-500">Unknown</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-xl shadow-sm border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Schedule</h3>
                            <dl class="space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Start Date:</dt>
                                    <dd class="font-medium">{{ $league->start_date->format('M d, Y') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">End Date:</dt>
                                    <dd class="font-medium">{{ $league->end_date->format('M d, Y') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Duration:</dt>
                                    <dd class="font-medium">{{ $league->start_date->diffInDays($league->end_date) + 1 }}
                                        days</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-xl shadow-sm border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Team Configuration</h3>
                            <dl class="space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Max Teams:</dt>
                                    <dd class="font-medium">{{ $league->max_teams }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Players per Team:</dt>
                                    <dd class="font-medium">{{ $league->max_team_players }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Wallet Limit:</dt>
                                    <dd class="font-medium">₹{{ number_format($league->team_wallet_limit, 2) }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Registration Information -->
                    <div class="mb-8 sm:mb-12">
                        <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-4 sm:mb-6">Registration Information
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 sm:gap-8">
                            <div class="bg-gray-50 p-6 rounded-xl shadow-sm border border-gray-200">
                                <h4 class="text-lg font-medium text-gray-800 mb-4">Team Registration</h4>
                                <dl class="space-y-3">
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600">Fee:</dt>
                                        <dd class="font-medium">₹{{ number_format($league->team_reg_fee, 2) }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600">Teams Registered:</dt>
                                        <dd class="font-medium">0 / {{ $league->max_teams }}</dd>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <dt class="text-gray-600">Status:</dt>
                                        <dd class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            Open
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="bg-gray-50 p-6 rounded-xl shadow-sm border border-gray-200">
                                <h4 class="text-lg font-medium text-gray-800 mb-4">Player Registration</h4>
                                <dl class="space-y-3">
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600">Fee:</dt>
                                        <dd class="font-medium">₹{{ number_format($league->player_reg_fee, 2) }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600">Retention:</dt>
                                        <dd class="font-medium">{{ $league->retention ? 'Enabled' : 'Disabled' }}</dd>
                                    </div>
                                    @if ($league->retention)
                                        <div class="flex justify-between">
                                            <dt class="text-gray-600">Max Retained Players:</dt>
                                            <dd class="font-medium">{{ $league->retention_players }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- Ground & Venue Information -->
                    <div class="mb-8 sm:mb-12">
                        <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-4 sm:mb-6">Ground & Venue Information
                        </h3>

                        <!-- Venue Details -->
                        @if ($league->localbody_id || $league->venue_details)
                            <div class="bg-gray-50 p-6 rounded-xl shadow-sm border border-gray-200 mb-6">
                                <h4 class="text-lg font-medium text-gray-800 mb-4">Venue Details</h4>
                                <dl class="space-y-3">
                                    @if ($league->localbody_id)
                                        <div class="flex justify-between">
                                            <dt class="text-gray-600">Local Body:</dt>
                                            <dd class="font-medium">{{ $league->localBody->name }},
                                                {{ $league->localBody->district->name }}</dd>
                                        </div>
                                    @endif

                                    @if ($league->venue_details)
                                        <div class="flex justify-between">
                                            <dt class="text-gray-600">Additional Details:</dt>
                                            <dd class="font-medium">{{ $league->venue_details }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>
                        @endif

                        <!-- Associated Grounds -->
                        @if ($league->grounds()->count())
                            <div>
                                <h4 class="text-lg font-medium text-gray-800 mb-4">Associated Grounds</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach ($league->grounds as $ground)
                                        <div
                                            class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                                            <h5 class="font-semibold text-gray-900 mb-2">{{ $ground->name }}</h5>
                                            <div class="text-sm text-gray-600 space-y-1">
                                                <p>{{ $ground->localBody->name }}, {{ $ground->district->name }}</p>
                                                @if ($ground->capacity)
                                                    <p>Capacity: {{ number_format($ground->capacity) }}</p>
                                                @endif
                                                @if ($ground->details)
                                                    <p class="text-gray-500 truncate">{{ $ground->details }}</p>
                                                @endif
                                            </div>
                                            <a href="{{ route('grounds.show', $ground) }}"
                                                class="mt-3 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                                View Details
                                                <svg class="ml-1 w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="text-gray-500">No ground is associated with this league.</p>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            <!-- Join Link Section - Only for League Organizers -->
            @if (auth()->user()->isOrganizer() && $league->organizers()->where('user_id', auth()->id())->exists())
                <div class="mt-6 sm:mt-8">
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="p-4 sm:p-6 lg:p-8">
                            <!-- Join Link Header -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 sm:mb-8 gap-4">
                                <div class="flex items-center">
                                    <div class="p-3 bg-blue-100 rounded-xl mr-4">
                                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900">Share Join Link</h3>
                                        <p class="text-sm sm:text-base text-gray-600">Share this link to invite players to join {{ $league->name }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Join Link Card -->
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
                                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-2">League Join Link</h4>
                                        <p class="text-gray-600 mb-4">Share this link with players to allow them to join your league. They can register and join in one click!</p>
                                        
                                        <!-- Join Link Display -->
                                        <div class="bg-white rounded-lg p-4 border border-gray-200 mb-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-500 mb-1">Join Link</p>
                                                    <p class="text-sm text-gray-900 break-all" id="joinLinkText">{{ route('leagues.join-link', $league) }}</p>
                                                </div>
                                                <button onclick="copyJoinLink()" id="copyButton"
                                                        class="ml-4 inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                    </svg>
                                                    <span id="copyButtonText">Copy</span>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Share Options -->
                                        <div class="flex flex-wrap gap-3">
                                            <button onclick="shareToWhatsApp()" 
                                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                                </svg>
                                                WhatsApp
                                            </button>
                                            
                                            <button onclick="shareToTelegram()" 
                                                    class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                                </svg>
                                                Telegram
                                            </button>
                                            
                                            <button onclick="shareToEmail()" 
                                                    class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                                Email
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Player Status Summary -->
                                    <div class="lg:ml-8 bg-white rounded-lg p-4 border border-gray-200 min-w-0 lg:min-w-[200px]">
                                        <h5 class="font-semibold text-gray-900 mb-3">Player Status</h5>
                                        <div class="space-y-2 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Total Players:</span>
                                                <span class="font-medium">{{ $playerStatusCounts['total'] }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Available:</span>
                                                <span class="font-medium text-green-600">{{ $playerStatusCounts['available'] }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Sold:</span>
                                                <span class="font-medium text-blue-600">{{ $playerStatusCounts['sold'] }}</span>
                                            </div>
                                            @if($playerStatusCounts['pending'] > 0)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Pending:</span>
                                                <span class="font-medium text-yellow-600">{{ $playerStatusCounts['pending'] }}</span>
                                            </div>
                                            @endif
                                            @if($playerStatusCounts['unsold'] > 0)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Unsold:</span>
                                                <span class="font-medium text-red-600">{{ $playerStatusCounts['unsold'] }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Finance Section - Only for League Organizers -->
            @if (auth()->user()->isOrganizer() && $league->organizers()->where('user_id', auth()->id())->exists())
                <div class="mt-6 sm:mt-8">
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="p-4 sm:p-6 lg:p-8">
                            <!-- Finance Header -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 sm:mb-8 gap-4">
                                <div class="flex items-center">
                                    <div class="p-3 bg-emerald-100 rounded-xl mr-4">
                                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900">League Finances</h3>
                                        <p class="text-sm sm:text-base text-gray-600">Manage income and expenses for {{ $league->name }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('league-finances.index', $league) }}" 
                                   class="w-full sm:w-auto bg-emerald-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg hover:bg-emerald-700 transition-colors flex items-center justify-center">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    <span class="text-sm sm:text-base">Manage Finances</span>
                                </a>
                            </div>
                            
                            <!-- Financial Summary -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                                <!-- Total Income Card -->
                                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-4 sm:p-6 border border-green-200">
                                    <div class="flex items-center">
                                        <div class="p-2 sm:p-3 bg-green-100 rounded-lg mr-3 sm:mr-4">
                                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wide">Total Income</p>
                                            <p class="text-lg sm:text-xl lg:text-2xl font-bold text-green-600 truncate">₹{{ number_format($league->total_income ?? 0, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Total Expenses Card -->
                                <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-xl p-4 sm:p-6 border border-red-200">
                                    <div class="flex items-center">
                                        <div class="p-2 sm:p-3 bg-red-100 rounded-lg mr-3 sm:mr-4">
                                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wide">Total Expenses</p>
                                            <p class="text-lg sm:text-xl lg:text-2xl font-bold text-red-600 truncate">₹{{ number_format($league->total_expenses ?? 0, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Net Profit/Loss Card -->
                                <div class="bg-gradient-to-br {{ ($league->net_profit ?? 0) >= 0 ? 'from-blue-50 to-cyan-50 border-blue-200' : 'from-orange-50 to-yellow-50 border-orange-200' }} rounded-xl p-4 sm:p-6 border sm:col-span-2 lg:col-span-1">
                                    <div class="flex items-center">
                                        <div class="p-2 sm:p-3 {{ ($league->net_profit ?? 0) >= 0 ? 'bg-blue-100' : 'bg-orange-100' }} rounded-lg mr-3 sm:mr-4">
                                            <svg class="w-5 h-5 sm:w-6 sm:h-6 {{ ($league->net_profit ?? 0) >= 0 ? 'text-blue-600' : 'text-orange-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wide">{{ ($league->net_profit ?? 0) >= 0 ? 'Net Profit' : 'Net Loss' }}</p>
                                            <p class="text-lg sm:text-xl lg:text-2xl font-bold {{ ($league->net_profit ?? 0) >= 0 ? 'text-blue-600' : 'text-orange-600' }} truncate">
                                                ₹{{ number_format(abs($league->net_profit ?? 0), 2) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="mt-6 sm:mt-8">
                                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                                    <a href="{{ route('league-finances.create', $league) }}" 
                                       class="flex-1 sm:flex-none bg-emerald-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg hover:bg-emerald-700 transition-colors text-sm sm:text-base flex items-center justify-center">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        <span class="text-sm sm:text-base">Add Transaction</span>
                                    </a>
                                    <a href="{{ route('league-finances.report', $league) }}" 
                                       class="flex-1 sm:flex-none bg-blue-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg hover:bg-blue-700 transition-colors text-sm sm:text-base flex items-center justify-center">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="text-sm sm:text-base">Generate Report</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <!-- Player Registration Modal -->
    <div id="registrationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div
            class="relative top-4 sm:top-20 mx-auto p-4 sm:p-6 border w-11/12 max-w-md shadow-lg rounded-lg bg-white mb-20 sm:mb-24 lg:mb-32">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900">League Registration</h3>
                    <button onclick="closeRegistrationModal()" class="text-gray-400 hover:text-gray-600 p-1">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Registration Content -->
                <div class="space-y-4 sm:space-y-6">
                    <!-- League Information with Submit Button -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="bg-blue-100 rounded-full p-2 mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-blue-900">{{ $league->name }}</h4>
                            </div>
                            <button onclick="submitRegistration()"
                                class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors font-medium shadow-lg">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Request
                            </button>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-blue-700">Season:</span>
                                <span class="text-sm font-medium text-blue-900">{{ $league->season }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-blue-700">Registration Fee:</span>
                                <span
                                    class="text-sm font-medium text-blue-900">₹{{ number_format($league->player_reg_fee ?? 0, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-blue-700">Game:</span>
                                <span class="text-sm font-medium text-blue-900">{{ $league->game->name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Position Selection -->
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <div class="bg-purple-100 rounded-full p-2 mr-3">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-purple-900">Select Your Position</h4>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <label for="position_id" class="block text-sm font-medium text-purple-700 mb-2">Game
                                    Position *</label>
                                <select id="position_id" name="position_id"
                                    class="w-full border border-purple-300 rounded-lg px-3 py-2 focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">Select a position</option>
                                    @foreach ($league->game->roles as $position)
                                        <option value="{{ $position->id }}"
                                            {{ auth()->user()->position_id == $position->id ? 'selected' : '' }}>
                                            {{ $position->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="text-sm text-purple-600">
                                <p>Please select your preferred position for this league. This helps organizers assign you
                                    to appropriate teams.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Registration Notice -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="bg-yellow-100 rounded-full p-2 mr-3">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-yellow-900">Important Notice</h4>
                                <p class="text-sm text-yellow-700 mt-1">Your registration request will be reviewed by the
                                    league organizers. You will be notified once your request is approved or rejected.</p>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <!-- Ownership Modal -->
    <div id="ownershipModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-2 sm:top-8 lg:top-20 mx-auto p-3 sm:p-4 lg:p-6 border w-11/12 sm:w-10/12 lg:w-4/5 xl:w-3/4 max-w-6xl shadow-lg rounded-lg bg-white mb-20 sm:mb-24 lg:mb-32">
            <div class="mt-2 sm:mt-3">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="bg-purple-100 rounded-lg p-2">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900">Team Ownership Request</h3>
                    </div>
                    <button onclick="closeOwnershipModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-2 transition-colors">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Ownership Content -->
                <div class="space-y-4 sm:space-y-6">
                    <!-- League Information -->
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 sm:p-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-purple-100 rounded-lg p-3 mr-4">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg sm:text-xl font-bold text-purple-900">{{ $league->name }}</h4>
                                <p class="text-sm sm:text-base text-purple-700">Team Ownership Request</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="bg-white rounded-lg p-3 border border-purple-100">
                                <div class="flex items-center mb-2">
                                    <svg class="w-4 h-4 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-xs font-medium text-purple-700 uppercase tracking-wide">Season</span>
                                </div>
                                <p class="text-sm font-semibold text-purple-900">{{ $league->season }}</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-purple-100">
                                <div class="flex items-center mb-2">
                                    <svg class="w-4 h-4 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                    </svg>
                                    <span class="text-xs font-medium text-purple-700 uppercase tracking-wide">Teams</span>
                                </div>
                                <p class="text-sm font-semibold text-purple-900">{{ $leagueTeamsCount }} / {{ $league->max_teams }}</p>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-purple-100">
                                <div class="flex items-center mb-2">
                                    <svg class="w-4 h-4 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-xs font-medium text-purple-700 uppercase tracking-wide">Game</span>
                                </div>
                                <p class="text-sm font-semibold text-purple-900">{{ $league->game->name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Teams List -->
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="px-4 sm:px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h4 class="text-lg sm:text-xl font-bold text-gray-900">Available Teams</h4>
                            <p class="text-sm text-gray-600 mt-1">Select a team to request ownership</p>
                        </div>

                        <div class="divide-y divide-gray-200">
                            @forelse($availableTeams as $team)
                            <div class="p-4 sm:p-6 hover:bg-gray-50 transition-colors">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                    <div class="flex items-center space-x-4 w-full sm:w-auto">
                                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-blue-100 rounded-lg flex items-center justify-center">
                                            @if($team->logo)
                                                <img src="{{ asset($team->logo) }}" alt="{{ $team->name }}" class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg object-cover">
                                            @else
                                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h5 class="text-base sm:text-lg font-bold text-gray-900 truncate">{{ $team->name }}</h5>
                                            <p class="text-sm text-gray-600 truncate">{{ $team->localBody->name ?? 'Location not specified' }}</p>
                                            <div class="flex flex-wrap items-center mt-2 gap-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Available
                                                </span>
                                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">Wallet: ₹{{ number_format($league->team_wallet_limit, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button onclick="requestOwnership('{{ $team->name }}', {{ $team->id }})"
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Request Ownership
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No teams available</h3>
                                <p class="mt-1 text-sm text-gray-500">All teams are already participating in this league.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Information Notice -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 sm:p-6">
                        <div class="flex items-start">
                            <div class="bg-blue-100 rounded-lg p-3 mr-4 flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-base sm:text-lg font-bold text-blue-900 mb-2">Ownership Request Process</h4>
                                <p class="text-sm sm:text-base text-blue-700 leading-relaxed">Your ownership request will be reviewed by the league organizers. You will be notified once your request is approved or rejected.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Confirmation Modal -->
    <div id="confirmModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-semibold mb-4">Confirm Registration</h2>
            <p class="text-gray-600 mb-6">Do you want to register for this league?</p>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeConfirmModal()"
                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    Cancel
                </button>
                <form method="POST"  action="{{ route('league-players.register', ['league' => $league->slug]) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Yes, Register
                    </button>
                </form>
            </div>
        </div>
    </div>


    <!-- Bid Increment Modal -->
    <div id="auctionRulesModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div
            class="relative top-4 sm:top-20 mx-auto p-4 sm:p-6 border w-11/12 max-w-md shadow-lg rounded-lg bg-white mb-20 sm:mb-24 lg:mb-32">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900">Bid Increment</h3>
                    <button onclick="closeAuctionRulesModal()" class="text-gray-400 hover:text-gray-600 p-1">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Bid Increment Content -->
                <div class="space-y-4 sm:space-y-6">
                    @if ($league->bid_increment_type === 'custom')
                        <!-- Custom Increment Section -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center mb-3">
                                <div class="bg-blue-100 rounded-full p-2 mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                        </path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-blue-900">Custom Increment</h4>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-blue-800 mb-1">Increment Amount
                                        (₹)</label>
                                    <input type="number" id="customIncrementValue"
                                        value="{{ $league->custom_bid_increment ?? 10 }}" min="1" step="0.01"
                                        class="w-full border border-blue-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-blue-800">₹<span
                                            id="customIncrementDisplay">{{ number_format($league->custom_bid_increment ?? 10, 2) }}</span>
                                    </p>
                                    <p class="text-sm text-blue-600">Fixed increment for all bids</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Predefined Increments Section -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center mb-3">
                                <div class="bg-green-100 rounded-full p-2 mr-3">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-green-900">Predefined Increments</h4>
                            </div>
                            <div class="space-y-3">
                                @php
                                    $predefinedIncrements = $league->predefined_increments ?? [
                                        ['min' => 0, 'max' => 100, 'increment' => 5],
                                        ['min' => 101, 'max' => 500, 'increment' => 10],
                                        ['min' => 501, 'max' => 1000, 'increment' => 25],
                                        ['min' => 1001, 'max' => null, 'increment' => 50],
                                    ];
                                @endphp
                                @foreach ($predefinedIncrements as $index => $rule)
                                    <div class="bg-white bg-opacity-50 rounded-lg p-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-green-800">
                                                ₹{{ $rule['min'] }}{{ $rule['max'] ? '-' . $rule['max'] : '+' }}
                                            </span>
                                            <span class="text-xs text-green-600">Increment</span>
                                        </div>
                                        <input type="number" id="increment_{{ $index }}"
                                            value="{{ $rule['increment'] }}" min="1" step="1"
                                            class="w-full border border-green-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500 text-lg font-bold text-green-900">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Auction Status -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-gray-100 rounded-full p-2 mr-3">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Auction Status</span>
                            </div>
                            <span
                                class="px-3 py-1 rounded-full text-xs font-medium 
                            @if ($league->auction_active) bg-green-100 text-green-800
                            @elseif($league->auction_ended_at) bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                                @if ($league->auction_active)
                                    Active
                                @elseif($league->auction_ended_at)
                                    Ended
                                @else
                                    Ready
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-center gap-3 mt-6 pb-4">
                    <button onclick="updateBidIncrements()"
                        class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 transition-colors font-medium shadow-lg">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification System -->
    <div id="notification" class="fixed top-4 right-4 z-50 hidden">
        <div
            class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg id="notification-icon" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p id="notification-message" class="text-sm font-medium text-gray-900"></p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button onclick="hideNotification()"
                            class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Approval Signature Section -->
            @if($league->approvedOrganizers->isNotEmpty())
                <div class="mt-8 bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-xl p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="bg-green-100 rounded-full p-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Approved by</h3>
                                <p class="text-sm text-gray-600">League Organizer Authorization</p>
                            </div>
                        </div>
                        <div class="text-right">
                            @foreach($league->approvedOrganizers as $organizer)
                                <div class="mb-2">
                                    <p class="font-medium text-gray-900">{{ $organizer->name }}</p>
                                    <p class="text-sm text-gray-600">Organizer</p>
                                </div>
                            @endforeach
                            <p class="text-xs text-gray-500 mt-2">
                                Approved on {{ $league->updated_at->format('M d, Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Debug: Show why approval signature is not showing -->
                <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-xl p-6 shadow-sm">
                    <div class="flex items-center space-x-4">
                        <div class="bg-yellow-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-yellow-800">Debug: Approval Signature</h3>
                            <p class="text-sm text-yellow-700">
                                Total organizers: {{ $league->organizers->count() }} | 
                                Approved organizers: {{ $league->approvedOrganizers->count() }} | 
                                Has approved organizers: {{ $league->approvedOrganizers->isNotEmpty() ? 'Yes' : 'No' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Auction Management Section -->
    @if((Auth::user()->isOrganizerForLeague($league->id) || Auth::user()->isAdmin()))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Complete Auction Section (only show if auction is active) -->
        @if($league->status === 'active')
        <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-green-800">Complete Auction</h3>
                        <p class="text-sm text-green-600 mt-1">
                            Finalize the auction and mark all unsold players as unsold. This will change the league status to "Auction Completed".
                        </p>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <form action="{{ route('auction.complete', $league) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Are you sure you want to complete the auction? This action cannot be undone.')"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Complete Auction
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <!-- Completed Auction Information (only show if auction is completed) -->
        @if($league->status === 'auction_completed')
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-blue-800">Auction Completed</h3>
                        <p class="text-sm text-blue-600 mt-1">
                            @if($league->auction_ended_at)
                                Auction completed on {{ $league->auction_ended_at->format('F j, Y \a\t g:i A') }}
                            @else
                                Auction has been completed successfully
                            @endif
                        </p>
                        <div class="mt-2 text-sm text-blue-700">
                            <span class="font-medium">Status:</span> All players have been assigned to teams or marked as unsold
                        </div>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Completed
                    </span>
                </div>
            </div>
        </div>
        @endif

        <!-- Reset Auction Section (only show when auction is completed or active) -->
        @if(in_array($league->status, ['auction_completed', 'active']))
        <div class="bg-red-50 border border-red-200 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-red-800">Reset Auction</h3>
                        <p class="text-sm text-red-600 mt-1">
                            This will delete all auction data, fixtures, groups, and reset player assignments. 
                            Teams will get their wallet balance restored. This action cannot be undone.
                        </p>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <form action="{{ route('auction.reset', $league) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('⚠️ WARNING: This will permanently delete ALL auction data, fixtures, groups, and reset all player assignments. Teams will get their wallet balance restored. This action CANNOT be undone!\n\nAre you absolutely sure you want to reset the auction?')"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset Auction
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <script>
        function openRegistrationModal() {
            document.getElementById('registrationModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeRegistrationModal() {
            document.getElementById('registrationModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openOwnershipModal() {
            document.getElementById('ownershipModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeOwnershipModal() {
            document.getElementById('ownershipModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openAuctionRulesModal() {
            document.getElementById('auctionRulesModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Initialize modal events after a short delay to ensure DOM is ready
            setTimeout(initializeModalEvents, 100);
        }

        function closeAuctionRulesModal() {
            document.getElementById('auctionRulesModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function requestOwnership(teamName, teamId) {
            // Show loading state on the button
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = `
        <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        Requesting...
    `;
            button.disabled = true;

            // Simulate API call delay
            setTimeout(() => {
                // Show success message
                showNotification(
                    `Ownership request submitted successfully for ${teamName}! Please wait for approval.`,
                    'success');

                // Change button to show requested state
                button.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Requested
        `;
                button.classList.remove('bg-purple-600', 'hover:bg-purple-700', 'focus:ring-purple-500');
                button.classList.add('bg-yellow-600', 'cursor-not-allowed');
                button.disabled = true;

                // Update team status
                const teamDiv = button.closest('.p-6');
                const statusBadge = teamDiv.querySelector('.bg-green-100');
                if (statusBadge) {
                    statusBadge.className =
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800';
                    statusBadge.textContent = 'Requested';
                }

                // Update description
                const description = teamDiv.querySelector('.text-gray-600');
                if (description) {
                    description.textContent = 'Ownership request pending approval';
                }
            }, 1500);
        }



        // Update custom increment display
        function updateCustomIncrementDisplay() {
            const value = document.getElementById('customIncrementValue').value;
            const display = document.getElementById('customIncrementDisplay');
            display.textContent = parseFloat(value || 0).toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Update bid increments
        function updateBidIncrements() {
            const currentType = '{{ $league->bid_increment_type }}';
            let data = {
                bid_increment_type: currentType
            };

            if (currentType === 'custom') {
                const customValue = document.getElementById('customIncrementValue').value;
                if (!customValue || customValue <= 0) {
                    showNotification('Please enter a valid custom increment amount', 'error');
                    return;
                }
                data.custom_bid_increment = parseFloat(customValue);
            } else {
                // Collect predefined increments
                const predefinedIncrements = [];
                const ranges = [{
                        min: 0,
                        max: 100
                    },
                    {
                        min: 101,
                        max: 500
                    },
                    {
                        min: 501,
                        max: 1000
                    },
                    {
                        min: 1001,
                        max: null
                    }
                ];

                for (let i = 0; i < 4; i++) {
                    const value = document.getElementById(`increment_${i}`).value;
                    if (!value || value <= 0) {
                        showNotification('Please enter valid increment values for all ranges', 'error');
                        return;
                    }
                    predefinedIncrements.push({
                        min: ranges[i].min,
                        max: ranges[i].max,
                        increment: parseInt(value)
                    });
                }
                data.predefined_increments = predefinedIncrements;
            }

            // Show loading state
            const updateBtn = document.querySelector('button[onclick="updateBidIncrements()"]');
            const originalText = updateBtn.innerHTML;
            updateBtn.innerHTML = `
        <svg class="w-5 h-5 inline mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        Updating...
    `;
            updateBtn.disabled = true;

            // Log the data being sent
            console.log('Sending bid increment data:', data);

            // Send AJAX request
            fetch('{{ route('leagues.update-bid-increments', $league) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        showNotification('Bid increments updated successfully!', 'success');
                        closeAuctionRulesModal();
                        // Reload page to reflect changes
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showNotification(data.message || 'Failed to update bid increments', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification(error.message || 'Error updating bid increments. Please try again.', 'error');
                })
                .finally(() => {
                    // Restore button state
                    updateBtn.innerHTML = originalText;
                    updateBtn.disabled = false;
                });
        }



        // Submit registration request
        function submitRegistration() {
            // Get position value
            const positionId = document.getElementById('position_id').value;

            // Validate position selection
            if (!positionId) {
                showNotification('Please select a position before submitting your registration request.', 'error');
                return;
            }

            // Show loading state
            const submitBtn = document.querySelector('button[onclick="submitRegistration()"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = `
        <svg class="w-5 h-5 inline mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        Submitting...
    `;
            submitBtn.disabled = true;

            // Prepare data
            const formData = {
                position_id: positionId
            };

            // Send AJAX request
            fetch('{{ route('league-players.request-registration', $league) }}', {
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
                        showNotification(data.message, 'success');
                        closeRegistrationModal();
                        // Reload page to reflect changes
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showNotification(data.message || 'Failed to submit registration request', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification(error.message || 'Error submitting registration request. Please try again.',
                        'error');
                })
                .finally(() => {
                    // Restore button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }

        // Close modal when clicking outside
        document.getElementById('registrationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRegistrationModal();
            }
        });

        document.getElementById('ownershipModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeOwnershipModal();
            }
        });

        document.getElementById('auctionRulesModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAuctionRulesModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRegistrationModal();
                closeOwnershipModal();
                closeAuctionRulesModal();
            }
        });

        // Show notification
        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            const messageEl = document.getElementById('notification-message');
            const icon = document.getElementById('notification-icon');

            // Set message
            messageEl.textContent = message;

            // Set icon and colors based on type
            if (type === 'success') {
                icon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                icon.classList.remove('text-red-600', 'text-yellow-600', 'text-blue-600');
                icon.classList.add('text-green-600');
                notification.querySelector('.bg-white').classList.add('border-l-4', 'border-green-500');
            } else if (type === 'error') {
                icon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                icon.classList.remove('text-green-600', 'text-yellow-600', 'text-blue-600');
                icon.classList.add('text-red-600');
                notification.querySelector('.bg-white').classList.add('border-l-4', 'border-red-500');
            } else {
                icon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                icon.classList.remove('text-green-600', 'text-red-600', 'text-yellow-600');
                icon.classList.add('text-blue-600');
                notification.querySelector('.bg-white').classList.add('border-l-4', 'border-blue-500');
            }

            // Show notification
            notification.classList.remove('hidden');

            // Auto hide after 5 seconds
            setTimeout(() => {
                hideNotification();
            }, 5000);
        }

        // Hide notification
        function hideNotification() {
            const notification = document.getElementById('notification');
            notification.classList.add('hidden');
            // Remove border classes
            notification.querySelector('.bg-white').classList.remove('border-l-4', 'border-green-500', 'border-red-500',
                'border-blue-500');
        }

        // Initialize modal event listeners when modal opens
        function initializeModalEvents() {
            // Custom increment value change
            const customInput = document.getElementById('customIncrementValue');
            if (customInput) {
                customInput.addEventListener('input', updateCustomIncrementDisplay);
            }

            // Predefined increment inputs
            for (let i = 0; i < 4; i++) {
                const input = document.getElementById(`increment_${i}`);
                if (input) {
                    input.addEventListener('input', function() {
                        // Ensure positive values
                        if (this.value < 0) this.value = 0;
                    });
                }
            }
        }
    </script>
    <script>
        function openConfirmModal() {
            document.getElementById('confirmModal').classList.remove('hidden');
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
        }

        // Join Link Functions
        function copyJoinLink() {
            const joinLinkElement = document.getElementById('joinLinkText');
            const copyButton = document.getElementById('copyButton');
            const copyButtonText = document.getElementById('copyButtonText');
            
            if (!joinLinkElement) {
                console.error('Join link element not found');
                showNotification('Error: Could not find join link', 'error');
                return;
            }
            
            const joinLink = joinLinkElement.textContent.trim();
            if (!joinLink) {
                console.error('Join link is empty');
                showNotification('Error: Join link is empty', 'error');
                return;
            }
            
            // Update button state
            if (copyButton && copyButtonText) {
                copyButton.disabled = true;
                copyButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                copyButton.classList.add('bg-green-600');
                copyButtonText.textContent = 'Copied!';
            }
            
            // Try modern clipboard API first
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(joinLink).then(function() {
                    showNotification('Join link copied to clipboard!', 'success');
                    resetCopyButton();
                }).catch(function(err) {
                    console.error('Clipboard API failed:', err);
                    // Fallback to execCommand
                    fallbackCopy(joinLink);
                });
            } else {
                // Fallback for older browsers or non-HTTPS
                fallbackCopy(joinLink);
            }
        }
        
        function resetCopyButton() {
            setTimeout(function() {
                const copyButton = document.getElementById('copyButton');
                const copyButtonText = document.getElementById('copyButtonText');
                
                if (copyButton && copyButtonText) {
                    copyButton.disabled = false;
                    copyButton.classList.remove('bg-green-600');
                    copyButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    copyButtonText.textContent = 'Copy';
                }
            }, 2000);
        }
        
        function fallbackCopy(text) {
            try {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                
                const successful = document.execCommand('copy');
                document.body.removeChild(textArea);
                
                if (successful) {
                    showNotification('Join link copied to clipboard!', 'success');
                    resetCopyButton();
                } else {
                    showNotification('Failed to copy link. Please copy manually.', 'error');
                    resetCopyButton();
                }
            } catch (err) {
                console.error('Fallback copy failed:', err);
                showNotification('Failed to copy link. Please copy manually.', 'error');
                resetCopyButton();
            }
        }

        function shareToWhatsApp() {
            const joinLink = document.getElementById('joinLinkText').textContent;
            const message = `Join ${@json($league->name)} league! Click the link to register and join: ${joinLink}`;
            const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank');
        }

        function shareToTelegram() {
            const joinLink = document.getElementById('joinLinkText').textContent;
            const message = `Join ${@json($league->name)} league! Click the link to register and join: ${joinLink}`;
            const telegramUrl = `https://t.me/share/url?url=${encodeURIComponent(joinLink)}&text=${encodeURIComponent(message)}`;
            window.open(telegramUrl, '_blank');
        }

        function shareToEmail() {
            const joinLink = document.getElementById('joinLinkText').textContent;
            const subject = `Join ${@json($league->name)} League`;
            const body = `Hi! I'd like to invite you to join ${@json($league->name)} league. Click the link below to register and join:\n\n${joinLink}\n\nLooking forward to seeing you in the league!`;
            const emailUrl = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
            window.location.href = emailUrl;
        }
    </script>

@endsection
