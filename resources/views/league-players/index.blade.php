@extends('layouts.app')

@section('title', 'League Players - ' . $league->name)

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">League Players</h1>
                    <p class="text-gray-600 mt-2">{{ $league->name }} - Managing {{ $leaguePlayers->total() }} players</p>
                </div>
                <!-- Mobile: 2 buttons per row -->
                <div class="grid grid-cols-2 gap-3 sm:hidden">
                    <a href="{{ route('players.create') }}?league_slug={{ $league->slug }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Player
                    </a>
                    <a href="{{ route('league-players.create', $league) }}" 
                       class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Add Player
                    </a>
                </div>
                
                <div class="grid grid-cols-2 gap-3 sm:hidden">
                    <a href="{{ route('league-players.bulk-create', $league) }}" 
                       class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Bulk Add
                    </a>
                    <a href="{{ route('leagues.show', $league) }}" 
                       class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back
                    </a>
                </div>
                
                <!-- Desktop: Original layout -->
                <div class="hidden sm:flex gap-3">
                    <a href="{{ route('players.create') }}?league_slug={{ $league->slug }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create New Player
                    </a>
                    <a href="{{ route('league-players.create', $league) }}" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Add Player to League
                    </a>
                    <a href="{{ route('league-players.bulk-create', $league) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Bulk Add Players
                    </a>
                    <a href="{{ route('leagues.show', $league) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to League
                    </a>
                </div>
            </div>
        </div>

        @auth
            @if(auth()->user()->isAdmin() || auth()->user()->isOrganizerForLeague($league->id))
                <!-- Bulk Base Price Card -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                    <div class="flex items-start justify-between gap-4 flex-col md:flex-row">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Bulk Base Price</h2>
                            <p class="text-sm text-gray-600 mt-1">Set base price for all players with status <span class="font-medium text-blue-700">Available</span>.</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('league-players.bulk-base-price', $league) }}" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        @csrf
                        <div>
                            <label for="bulk_base_price" class="block text-sm font-medium text-gray-700 mb-2">New Base Price</label>
                            <input id="bulk_base_price" type="number" name="base_price" min="0" step="1" required
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Enter amount">
                        </div>
                        <div class="md:col-span-2 flex items-end">
                            <button type="submit"
                                    onclick="return confirm('Update base price for all available players?');"
                                    class="inline-flex items-center px-4 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors text-sm">
                                Apply New Base Price
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        @endauth

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="sold" {{ request('status') === 'sold' ? 'selected' : '' }}>Sold</option>
                        <option value="unsold" {{ request('status') === 'unsold' ? 'selected' : '' }}>Unsold</option>
                        <option value="skip" {{ request('status') === 'skip' ? 'selected' : '' }}>Skip</option>
                    </select>
                </div>
                
                <div>
                    <label for="retention" class="block text-sm font-medium text-gray-700 mb-2">Retention</label>
                    <select name="retention" id="retention" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Players</option>
                        <option value="true" {{ request('retention') === 'true' ? 'selected' : '' }}>Retained</option>
                        <option value="false" {{ request('retention') === 'false' ? 'selected' : '' }}>Not Retained</option>
                    </select>
                </div>
                
                <div>
                    <label for="team" class="block text-sm font-medium text-gray-700 mb-2">Team</label>
                    <select name="team" id="team" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Teams</option>
                        <option value="unassigned" {{ request('team') == 'unassigned' ? 'selected' : '' }}>
                            Unassigned (Auction Pool)
                        </option>
                        @foreach($teams as $team)
                            <option value="{{ $team->slug }}" {{ request('team') == $team->slug ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Mobile: Filter buttons in 2 columns -->
                <div class="md:hidden grid grid-cols-2 gap-3">
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Apply Filters
                    </button>
                    <a href="{{ route('league-players.index', $league) }}" 
                       class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear
                    </a>
                </div>
                
                <!-- Desktop: Original layout -->
                <div class="hidden md:flex items-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter
                    </button>
                </div>
                
                <div class="hidden md:flex items-end">
                    <a href="{{ route('league-players.index', $league) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Stats Summary -->
        <div class="grid grid-cols-2 md:grid-cols-7 gap-4 mb-8">
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-center">
                    <p class="text-2xl font-semibold text-gray-900">{{ $leaguePlayers->total() }}</p>
                    <p class="text-xs text-gray-500">Total Players</p>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-center">
                    <p class="text-2xl font-semibold text-gray-900">{{ $unassignedCount ?? 0 }}</p>
                    <p class="text-xs text-indigo-600">Unassigned</p>
                </div>
            </div>
            
            @foreach(['pending' => 'yellow', 'available' => 'blue', 'sold' => 'green', 'unsold' => 'red', 'skip' => 'gray'] as $status => $color)
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-center">
                        <p class="text-2xl font-semibold text-gray-900">{{ $statusCounts[$status] ?? 0 }}</p>
                        <p class="text-xs text-{{ $color }}-600">{{ ucfirst($status) }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Players Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            @if($leaguePlayers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Sl
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Player
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Team
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Base Price
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Retention
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($leaguePlayers as $index => $leaguePlayer)
                                <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location.href='{{ route('league-players.show', [$league, $leaguePlayer]) }}'">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ($leaguePlayers->currentPage() - 1) * $leaguePlayers->perPage() + $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-700">
                                                        {{ substr($leaguePlayer->user->name, 0, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $leaguePlayer->user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $leaguePlayer->user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($leaguePlayer->leagueTeam)
                                            <div class="text-sm text-gray-900">{{ $leaguePlayer->leagueTeam->team->name ?? 'No Team Assigned' }}</div>
                                        @else
                                            <div class="text-sm text-indigo-600 font-medium">Available for Auction</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $leaguePlayer->user->position->name ?? 'No Role' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                        ₹{{ number_format($leaguePlayer->base_price) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'available' => 'bg-blue-100 text-blue-800',
                                                'sold' => 'bg-green-100 text-green-800',
                                                'unsold' => 'bg-red-100 text-red-800',
                                                'skip' => 'bg-gray-100 text-gray-800'
                                            ];
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$leaguePlayer->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($leaguePlayer->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($leaguePlayer->retention)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Retained
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                No
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        @if($leaguePlayer->status === 'pending')
                                            <form action="{{ route('league-players.updateStatus', [$league, $leaguePlayer]) }}" 
                                                  method="POST" class="inline" 
                                                  onsubmit="return confirm('Approve this player?')">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="available">
                                                <button type="submit" class="text-green-600 hover:text-green-900">Approve</button>
                                            </form>
                                            <form action="{{ route('league-players.updateStatus', [$league, $leaguePlayer]) }}" 
                                                  method="POST" class="inline" 
                                                  onsubmit="return confirm('Reject this player?')">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="unsold">
                                                <button type="submit" class="text-red-600 hover:text-red-900">Reject</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('league-players.show', [$league, $leaguePlayer]) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">View</a>
                                        <a href="{{ route('league-players.edit', [$league, $leaguePlayer]) }}" 
                                           class="text-blue-600 hover:text-blue-900">Edit</a>
                                        <form action="{{ route('league-players.destroy', [$league, $leaguePlayer]) }}" 
                                              method="POST" class="inline" 
                                              onsubmit="return confirm('Are you sure you want to remove this player from the league?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $leaguePlayers->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No players</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by adding a player to this league.</p>
                    <div class="mt-6">
                        <a href="{{ route('league-players.create', $league) }}" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Add Player
                        </a>
                    </div>
                </div>
            @endif
        </div>
        
    </div>
</div>
@endsection
