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
                                @if (auth()->user()->isOwner())
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

                                    @if (!$existingPlayer)
                                        <button onclick="openRegistrationModal()"
                                            class="inline-flex items-center justify-center px-4 py-2.5 bg-green-600 text-white font-medium rounded-lg shadow-sm hover:bg-green-700 transition-colors text-center text-sm">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Register
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
                                    @if (auth()->user()->isOwner())
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
                                @if (auth()->user()->isOrganizer() || auth()->user()->isOwner())
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

                                        @if (!$existingPlayer)
                                            <button onclick="openRegistrationModal()"
                                                class="inline-flex items-center justify-center px-6 py-2.5 bg-green-600 text-white font-medium rounded-lg shadow-sm hover:bg-green-700 transition-colors text-base">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Register
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
                    @if (auth()->user()->isOrganizer() || auth()->user()->isOwner())
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
                                    <dt class="text-gray-600">Organizer:</dt>
                                    <dd class="font-medium">{{ $league->organizer->name }}</dd>
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

        </div>
    </div>

    <!-- Player Registration Modal -->
    <div id="registrationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div
            class="relative top-4 sm:top-20 mx-auto p-4 sm:p-6 border w-11/12 max-w-md shadow-lg rounded-lg bg-white mb-20 sm:mb-0">
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
        <div
            class="relative top-4 sm:top-20 mx-auto p-4 sm:p-6 border w-11/12 max-w-4xl shadow-lg rounded-lg bg-white mb-20 sm:mb-0">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900">Team Ownership Request</h3>
                    <button onclick="closeOwnershipModal()" class="text-gray-400 hover:text-gray-600 p-1">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Ownership Content -->
                <div class="space-y-6">
                    <!-- League Information -->
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <div class="bg-purple-100 rounded-full p-2 mr-3">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-purple-900">{{ $league->name }} - Team Ownership</h4>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-purple-700">Season:</span>
                                <span class="text-sm font-medium text-purple-900">{{ $league->season }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-purple-700">Available Teams:</span>
                                <span class="text-sm font-medium text-purple-900">{{ $leagueTeamsCount }} /
                                    {{ $league->max_teams }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-purple-700">Game:</span>
                                <span class="text-sm font-medium text-purple-900">{{ $league->game->name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Teams List -->
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h4 class="text-lg font-semibold text-gray-900">Available Teams</h4>
                            <p class="text-sm text-gray-600 mt-1">Select a team to request ownership</p>
                        </div>

                        <div class="divide-y divide-gray-200">
                            <!-- Team 1 -->
                            <div class="p-6 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h5 class="text-lg font-semibold text-gray-900">Mumbai Indians</h5>
                                            <p class="text-sm text-gray-600">Available for ownership</p>
                                            <div class="flex items-center mt-1 space-x-4">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Available
                                                </span>
                                                <span class="text-xs text-gray-500">Wallet:
                                                    ₹{{ number_format(100000, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button onclick="requestOwnership('Mumbai Indians', 1)"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Request Ownership
                                    </button>
                                </div>
                            </div>

                            <!-- Team 2 -->
                            <div class="p-6 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h5 class="text-lg font-semibold text-gray-900">Chennai Super Kings</h5>
                                            <p class="text-sm text-gray-600">Available for ownership</p>
                                            <div class="flex items-center mt-1 space-x-4">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Available
                                                </span>
                                                <span class="text-xs text-gray-500">Wallet:
                                                    ₹{{ number_format(150000, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button onclick="requestOwnership('Chennai Super Kings', 2)"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Request Ownership
                                    </button>
                                </div>
                            </div>

                            <!-- Team 3 -->
                            <div class="p-6 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h5 class="text-lg font-semibold text-gray-900">Royal Challengers Bangalore
                                            </h5>
                                            <p class="text-sm text-gray-600">Available for ownership</p>
                                            <div class="flex items-center mt-1 space-x-4">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Available
                                                </span>
                                                <span class="text-xs text-gray-500">Wallet:
                                                    ₹{{ number_format(120000, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button onclick="requestOwnership('Royal Challengers Bangalore', 3)"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Request Ownership
                                    </button>
                                </div>
                            </div>

                            <!-- Team 4 -->
                            <div class="p-6 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h5 class="text-lg font-semibold text-gray-900">Kolkata Knight Riders</h5>
                                            <p class="text-sm text-gray-600">Available for ownership</p>
                                            <div class="flex items-center mt-1 space-x-4">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Available
                                                </span>
                                                <span class="text-xs text-gray-500">Wallet:
                                                    ₹{{ number_format(90000, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button onclick="requestOwnership('Kolkata Knight Riders', 4)"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Request Ownership
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Information Notice -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-full p-2 mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-blue-900">Ownership Request Process</h4>
                                <p class="text-sm text-blue-700 mt-1">Your ownership request will be reviewed by the league
                                    organizers. You will be notified once your request is approved or rejected.</p>
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
            class="relative top-4 sm:top-20 mx-auto p-4 sm:p-6 border w-11/12 max-w-md shadow-lg rounded-lg bg-white mb-20 sm:mb-0">
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
        </div>
    </div>

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
    </script>

@endsection
