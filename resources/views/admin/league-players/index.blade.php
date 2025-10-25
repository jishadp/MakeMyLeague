@extends('layouts.app')

@section('title', 'League Players - Admin')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">League Players Management</h1>
            <p class="text-gray-600 mt-1">View and manage all league players</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('admin.league-players.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">League</label>
                    <select name="league_id" class="w-full border-gray-300 rounded-lg">
                        <option value="">All Leagues</option>
                        @foreach($leagues as $league)
                            <option value="{{ $league->id }}" {{ request('league_id') == $league->id ? 'selected' : '' }}>
                                {{ $league->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full border-gray-300 rounded-lg">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="auctioning" {{ request('status') == 'auctioning' ? 'selected' : '' }}>Auctioning</option>
                        <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                        <option value="unsold" {{ request('status') == 'unsold' ? 'selected' : '' }}>Unsold</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Retention</label>
                    <select name="retention" class="w-full border-gray-300 rounded-lg">
                        <option value="">All</option>
                        <option value="true" {{ request('retention') == 'true' ? 'selected' : '' }}>Yes</option>
                        <option value="false" {{ request('retention') == 'false' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Player</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">League</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Team</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Base Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bid Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Retention</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($leaguePlayers as $player)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $player->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $player->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $player->league->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $player->leagueTeam?->team->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ₹{{ number_format($player->base_price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $player->bid_price ? '₹' . number_format($player->bid_price, 2) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($player->status == 'sold') bg-green-100 text-green-800
                                    @elseif($player->status == 'available') bg-blue-100 text-blue-800
                                    @elseif($player->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($player->status == 'unsold') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($player->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($player->retention)
                                    <span class="text-green-600 font-medium">Yes</span>
                                @else
                                    <span class="text-gray-400">No</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                No league players found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $leaguePlayers->links() }}
        </div>

    </div>
</div>
@endsection
