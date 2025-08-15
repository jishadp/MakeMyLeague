@extends('layouts.app')

@section('title', 'Auction Listings')

@section('content')
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-indigo-600 to-purple-600 py-8 px-4 sm:px-6 lg:px-8 text-white animate-fadeIn">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold mb-2 drop-shadow">
                        Auction Listings
                    </h1>
                    <p class="text-indigo-100">
                        View all completed player auctions and transactions
                    </p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('dashboard') }}"
                       class="bg-white text-indigo-600 px-4 py-2 rounded-lg font-medium 
                              hover:bg-indigo-50 active:scale-95 transition-all shadow-md hover:shadow-lg">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters Section -->
    <section class="py-6 px-4 sm:px-6 lg:px-8 bg-white border-b">
        <div class="max-w-7xl mx-auto">
            <form action="{{ route('auctions.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="league" class="block text-sm font-medium text-gray-700 mb-1">Filter by League</label>
                    <select id="league" name="league_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">All Leagues</option>
                        @foreach($leagues as $league)
                            <option value="{{ $league->id }}" {{ request('league_id') == $league->id ? 'selected' : '' }}>
                                {{ $league->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="team" class="block text-sm font-medium text-gray-700 mb-1">Filter by Team</label>
                    <select id="team" name="team_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">All Teams</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                                {{ $team->team->name }} ({{ $team->league->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort by</label>
                    <select id="sort" name="sort" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Latest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="amount_high" {{ request('sort') == 'amount_high' ? 'selected' : '' }}>Highest Amount</option>
                        <option value="amount_low" {{ request('sort') == 'amount_low' ? 'selected' : '' }}>Lowest Amount</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Auctions List Section -->
    <section class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto">
            @if($auctions->isEmpty())
                <div class="bg-white rounded-xl shadow-lg p-8 text-center animate-fadeInUp">
                    <div class="mb-4">
                        <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5
                                     a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3
                                     m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No auction records found</h3>
                    <p class="text-gray-600 mb-6">No player auction records match your current filters.</p>
                    <a href="{{ route('auctions.index') }}"
                       class="inline-block bg-indigo-600 text-white py-2 px-6 rounded-lg font-medium 
                              hover:bg-indigo-700 active:scale-95 transition-all shadow-md hover:shadow-lg">
                        Clear All Filters
                    </a>
                </div>
            @else
                <div class="overflow-x-auto bg-white rounded-xl shadow-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Player
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Team
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    League
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created By
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($auctions as $auction)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($auction->player && $auction->player->photo)
                                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ asset($auction->player->photo) }}" alt="{{ $auction->player->name }}">
                                                @else
                                                    <svg class="h-10 w-10 rounded-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    @if($auction->player)
                                                        {{ $auction->player->name }}
                                                    @else
                                                        Unknown Player
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    @if($auction->player && $auction->player->role)
                                                        {{ $auction->player->role->name }}
                                                    @else
                                                        Role Unknown
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if($auction->leagueTeam && $auction->leagueTeam->team)
                                                {{ $auction->leagueTeam->team->name }}
                                            @else
                                                Unknown Team
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if($auction->leagueTeam && $auction->leagueTeam->league)
                                                {{ $auction->leagueTeam->league->name }}
                                            @else
                                                Unknown League
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-emerald-600">
                                            ₹{{ number_format($auction->amount, 2) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $auction->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($auction->creator)
                                            {{ $auction->creator->name }}
                                        @else
                                            Unknown
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-6">
                    {{ $auctions->links() }}
                </div>
            @endif
        </div>
    </section>

    <style>
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.5s ease-in-out; }
        .animate-fadeInUp { animation: fadeInUp 0.4s ease-in-out; }
    </style>
@endsection
