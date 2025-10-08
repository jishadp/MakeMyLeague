@extends('layouts.app')

@section('title', 'Auction Dashboard')

@section('content')
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-indigo-600 to-purple-600 py-8 px-4 sm:px-6 lg:px-8 text-white animate-fadeIn">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold mb-2 drop-shadow">
                        Auction Dashboard
                    </h1>
                    <p class="text-indigo-100">
                        Live auctions, past results, and upcoming events
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

    <!-- Tab Navigation -->
    <section class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex space-x-8">
                <button onclick="showSection('live')" id="liveTab" 
                        class="py-4 px-1 border-b-2 border-indigo-500 font-medium text-sm text-indigo-600">
                    Live Auctions
                    @if($liveAuctions->count() > 0)
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ $liveAuctions->count() }}
                        </span>
                    @endif
                </button>
                <button onclick="showSection('past')" id="pastTab" 
                        class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Past Auctions
                </button>
                <button onclick="showSection('upcoming')" id="upcomingTab" 
                        class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Upcoming Auctions
                    @if($upcomingAuctions->count() > 0)
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $upcomingAuctions->count() }}
                        </span>
                    @endif
                </button>
            </div>
        </div>
    </section>

    <!-- Content Sections -->
    <section class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto">
            
            <!-- Live Auctions Section -->
            <div id="liveSection" class="section-content">
                @if($liveAuctions->isEmpty())
                    <div class="bg-white rounded-xl shadow-lg p-8 text-center animate-fadeInUp">
                        <div class="mb-4">
                            <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Live Auctions</h3>
                        <p class="text-gray-600 mb-6">There are currently no live auctions happening.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($liveAuctions as $league)
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $league->name }}</h3>
                                        <div class="flex items-center space-x-2">
                                            <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                                            <span class="text-red-600 font-semibold text-sm">LIVE</span>
                                        </div>
                                    </div>
                                    
                                    <p class="text-gray-600 mb-4">{{ $league->game->name }} League</p>
                                    
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-indigo-600">{{ $league->leagueTeams->count() }}</p>
                                            <p class="text-sm text-gray-500">Teams</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-green-600">{{ $league->leaguePlayers->count() }}</p>
                                            <p class="text-sm text-gray-500">Players</p>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-2 mb-6">
                                        @foreach($league->leagueTeams->take(3) as $leagueTeam)
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="text-gray-700">{{ $leagueTeam->team->name }}</span>
                                                <span class="font-semibold text-green-600">₹{{ number_format($leagueTeam->wallet_balance, 0) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <a href="{{ route('auctions.live', $league) }}" 
                                       class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors font-medium text-center block">
                                        Watch Live Auction
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Past Auctions Section -->
            <div id="pastSection" class="section-content hidden">
                @if($pastAuctions->isEmpty())
                    <div class="bg-white rounded-xl shadow-lg p-8 text-center animate-fadeInUp">
                        <div class="mb-4">
                            <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5
                                         a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3
                                         m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Past Auctions</h3>
                        <p class="text-gray-600 mb-6">No completed auction records found.</p>
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
                                        Top Bids
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($pastAuctions as $auction)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    @if($auction->leaguePlayer && $auction->leaguePlayer->user && $auction->leaguePlayer->user->photo)
                                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset($auction->leaguePlayer->user->photo) }}" alt="{{ $auction->leaguePlayer->user->name }}">
                                                    @else
                                                        <svg class="h-10 w-10 rounded-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        @if($auction->leaguePlayer && $auction->leaguePlayer->user)
                                                            {{ $auction->leaguePlayer->user->name }}
                                                        @else
                                                            Unknown Player
                                                        @endif
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        @if($auction->leaguePlayer && $auction->leaguePlayer->user && $auction->leaguePlayer->user->position)
                                                            {{ $auction->leaguePlayer->user->position->name }}
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
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm">
                                                @php
                                                    $topBids = \App\Models\Auction::where('league_player_id', $auction->league_player_id)
                                                        ->orderBy('amount', 'desc')
                                                        ->take(3)
                                                        ->with('leagueTeam.team')
                                                        ->get();
                                                @endphp
                                                <div class="space-y-1">
                                                    @foreach($topBids as $index => $bid)
                                                        <div class="flex justify-between items-center">
                                                            <span class="text-xs {{ $index === 0 ? 'font-bold text-green-600' : 'text-gray-500' }}">
                                                                {{ $index + 1 }}. {{ $bid->leagueTeam->team->name }}
                                                            </span>
                                                            <span class="text-xs {{ $index === 0 ? 'font-bold text-green-600' : 'text-gray-500' }}">
                                                                ₹{{ number_format($bid->amount, 0) }}
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $pastAuctions->links() }}
                    </div>
                @endif
            </div>

            <!-- Upcoming Auctions Section -->
            <div id="upcomingSection" class="section-content hidden">
                @if($upcomingAuctions->isEmpty())
                    <div class="bg-white rounded-xl shadow-lg p-8 text-center animate-fadeInUp">
                        <div class="mb-4">
                            <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Upcoming Auctions</h3>
                        <p class="text-gray-600 mb-6">No upcoming auctions scheduled.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($upcomingAuctions as $league)
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $league->name }}</h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Upcoming
                                        </span>
                                    </div>
                                    
                                    <p class="text-gray-600 mb-4">{{ $league->game->name }} League</p>
                                    
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-indigo-600">{{ $league->leagueTeams->count() }}</p>
                                            <p class="text-sm text-gray-500">Teams</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-green-600">{{ $league->leaguePlayers->count() }}</p>
                                            <p class="text-sm text-gray-500">Players</p>
                                        </div>
                                    </div>
                                    
                                    @if($league->start_date)
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-500">Start Date</p>
                                            <p class="font-semibold text-gray-900">{{ $league->start_date->format('M d, Y') }}</p>
                                        </div>
                                    @endif
                                    
                                    <div class="space-y-2 mb-6">
                                        @foreach($league->leagueTeams->take(3) as $leagueTeam)
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="text-gray-700">{{ $leagueTeam->team->name }}</span>
                                                <span class="font-semibold text-green-600">₹{{ number_format($leagueTeam->wallet_balance, 0) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <button disabled class="w-full bg-gray-300 text-gray-500 py-2 px-4 rounded-lg font-medium cursor-not-allowed">
                                        Auction Not Started
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    <style>
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.5s ease-in-out; }
        .animate-fadeInUp { animation: fadeInUp 0.4s ease-in-out; }
    </style>

    <script>
        function showSection(sectionName) {
            // Hide all sections
            document.querySelectorAll('.section-content').forEach(section => {
                section.classList.add('hidden');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('[id$="Tab"]').forEach(tab => {
                tab.classList.remove('border-indigo-500', 'text-indigo-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected section
            document.getElementById(sectionName + 'Section').classList.remove('hidden');
            
            // Add active class to selected tab
            const activeTab = document.getElementById(sectionName + 'Tab');
            activeTab.classList.remove('border-transparent', 'text-gray-500');
            activeTab.classList.add('border-indigo-500', 'text-indigo-600');
        }

        // Auto-refresh every 5 seconds for live auctions
        setInterval(function() {
            if (!document.getElementById('liveSection').classList.contains('hidden')) {
                // Only refresh if we're on the live section
                location.reload();
            }
        }, 5000);
    </script>
@endsection
