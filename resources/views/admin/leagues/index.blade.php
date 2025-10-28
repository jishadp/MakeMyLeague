@extends('layouts.app')

@section('title', 'Admin - League Management')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 mb-2">League Management</h1>
            <p class="text-gray-600">Manage all leagues across the platform</p>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                <div class="flex">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                <div class="flex">
                    <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Admin Navigation -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-gray-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                </svg>
                Admin Management
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('admin.organizer-requests.index') }}" class="flex items-center p-4 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 transition-colors">
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

                <a href="{{ route('admin.locations.index') }}" class="flex items-center p-4 bg-blue-50 border border-blue-200 rounded-xl hover:bg-blue-100 transition-colors">
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

                <a href="{{ route('admin.grounds.index') }}" class="flex items-center p-4 bg-green-50 border border-green-200 rounded-xl hover:bg-green-100 transition-colors">
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

                <a href="{{ route('admin.league-players.index') }}" class="flex items-center p-4 bg-purple-50 border border-purple-200 rounded-xl hover:bg-purple-100 transition-colors">
                    <div class="bg-purple-100 rounded-full p-2 mr-3">
                        <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-purple-900">League Players</h4>
                        <p class="text-sm text-purple-700">View all league players</p>
                    </div>
                </a>

                <a href="{{ route('admin.players.index') }}" class="flex items-center p-4 bg-purple-50 border border-purple-200 rounded-xl hover:bg-purple-100 transition-colors">
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

                <a href="{{ route('admin.analytics.index') }}" class="flex items-center p-4 bg-indigo-50 border border-indigo-200 rounded-xl hover:bg-indigo-100 transition-colors">
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

                <a href="{{ route('admin.leagues.index') }}" class="flex items-center p-4 bg-pink-50 border border-pink-200 rounded-xl hover:bg-pink-100 transition-colors ring-2 ring-pink-500">
                    <div class="bg-pink-100 rounded-full p-2 mr-3">
                        <svg class="w-5 h-5 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-pink-900">League Management</h4>
                        <p class="text-sm text-pink-700">Manage & edit all leagues (Current)</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Stats Cards - Mobile: 3 per row, Desktop: 5 per row -->
        <div class="grid grid-cols-3 md:grid-cols-5 gap-3 md:gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-4 md:p-6 border-l-4 border-blue-500">
                <div class="text-xs md:text-sm font-medium text-gray-600 mb-1">Total</div>
                <div class="text-2xl md:text-3xl font-black text-gray-900">{{ $leagueStats['total'] ?? 0 }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 md:p-6 border-l-4 border-yellow-500">
                <div class="text-xs md:text-sm font-medium text-gray-600 mb-1">Pending</div>
                <div class="text-2xl md:text-3xl font-black text-yellow-600">{{ $leagueStats['pending'] ?? 0 }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 md:p-6 border-l-4 border-green-500">
                <div class="text-xs md:text-sm font-medium text-gray-600 mb-1">Active</div>
                <div class="text-2xl md:text-3xl font-black text-green-600">{{ $leagueStats['active'] ?? 0 }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 md:p-6 border-l-4 border-purple-500">
                <div class="text-xs md:text-sm font-medium text-gray-600 mb-1">Done</div>
                <div class="text-2xl md:text-3xl font-black text-purple-600">{{ $leagueStats['completed'] ?? 0 }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 md:p-6 border-l-4 border-red-500">
                <div class="text-xs md:text-sm font-medium text-gray-600 mb-1">Cancelled</div>
                <div class="text-2xl md:text-3xl font-black text-red-600">{{ $leagueStats['cancelled'] ?? 0 }}</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <form method="GET" action="{{ route('admin.leagues.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="League name..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Game Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Game</label>
                    <select name="game_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">All Games</option>
                        @foreach($games as $game)
                            <option value="{{ $game->id }}" {{ request('game_id') == $game->id ? 'selected' : '' }}>{{ $game->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Button -->
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-bold shadow-lg hover:shadow-xl transition-all">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Leagues Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">League</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Game</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Organizer</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Season</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Teams/Players</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($leagues as $league)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($league->logo)
                                        <img src="{{ Storage::url($league->logo) }}" alt="{{ $league->name }}" class="w-10 h-10 rounded-lg object-cover mr-3">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
                                            <span class="text-purple-600 font-bold text-lg">{{ substr($league->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div class="flex-grow">
                                        <div class="font-bold text-gray-900">{{ $league->name }}</div>
                                        <div class="text-sm text-gray-500 mb-1">{{ \Carbon\Carbon::parse($league->start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($league->end_date)->format('M d, Y') }}</div>
                                        @php
                                            $completion = $league->getCompletionPercentage();
                                            $progressColor = $completion >= 75 ? 'bg-green-500' : ($completion >= 50 ? 'bg-yellow-500' : ($completion >= 25 ? 'bg-orange-500' : 'bg-red-500'));
                                        @endphp
                                        <div class="flex items-center gap-2">
                                            <div class="flex-grow h-2 bg-gray-200 rounded-full overflow-hidden" style="max-width: 120px;">
                                                <div class="{{ $progressColor }} h-full transition-all" style="width: {{ $completion }}%"></div>
                                            </div>
                                            <span class="text-xs font-bold text-gray-600">{{ $completion }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                    {{ $league->game->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $league->localBody->name }}</div>
                                <div class="text-xs text-gray-500">{{ $league->localBody->district->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($league->approvedOrganizers->isNotEmpty())
                                    <div class="text-sm text-gray-900">{{ $league->approvedOrganizers->first()->name }}</div>
                                    @if($league->approvedOrganizers->count() > 1)
                                        <div class="text-xs text-gray-500">+{{ $league->approvedOrganizers->count() - 1 }} more</div>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400">No organizer</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">{{ $league->season }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="text-gray-900 font-medium">{{ $league->leagueTeams->count() }}/{{ $league->max_teams }} Teams</div>
                                    <div class="text-gray-500">{{ $league->leaguePlayers->count() }} Players</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.leagues.update-status', $league) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="px-3 py-1 rounded-full text-sm font-bold cursor-pointer transition-all
                                        {{ $league->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $league->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $league->status === 'completed' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $league->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                        <option value="pending" {{ $league->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="active" {{ $league->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="completed" {{ $league->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $league->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.leagues.flow', $league) }}" class="inline-flex items-center px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold rounded-lg shadow hover:shadow-lg transition-all" title="Progress Flow">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('leagues.show', $league) }}" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg shadow hover:shadow-lg transition-all" title="View as Organizer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.leagues.show', $league) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg shadow hover:shadow-lg transition-all" title="View Admin">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.leagues.edit', $league) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg shadow hover:shadow-lg transition-all" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.leagues.restart', $league) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to restart this league? This will delete all teams, players, fixtures, and auction data!');">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-sm font-bold rounded-lg shadow hover:shadow-lg transition-all" title="Restart">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.leagues.destroy', $league) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this league?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg shadow hover:shadow-lg transition-all" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="text-gray-400 text-lg">No leagues found</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($leagues->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $leagues->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

