@extends('layouts.app')

@section('title', 'Live Auctions')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="text-center mb-10">
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Live Cricket Auctions</h1>
        <p class="text-lg text-gray-600 max-w-3xl mx-auto">Follow the excitement of live player auctions in real-time. See which teams are bidding and which players are being sold!</p>
        <div class="mt-4">
            <button onclick="window.location.reload()" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh Auction Data
            </button>
        </div>
    </div>

    <!-- Active Auctions -->
    @if($activeAuctions->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($activeAuctions as $auction)
                <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300 border border-gray-200">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6">
                        <h2 class="text-xl font-bold text-white mb-2">{{ $auction->name }}</h2>
                        <div class="flex justify-between items-center">
                            <span class="text-indigo-100">
                                {{ $auction->leagueTeams()->count() }} Teams
                            </span>
                            <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-white text-sm font-medium">
                                Active
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <div class="text-sm text-gray-600">Started on:</div>
                            <div class="font-medium">{{ $auction->updated_at->format('M d, Y') }}</div>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-4 mb-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-indigo-600">
                                    {{ $auction->leaguePlayers()->wherePivot('auction_status', 'sold')->count() }}
                                </div>
                                <div class="text-xs text-gray-500">Sold</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-indigo-600">
                                    {{ $auction->leagueTeams()->count() }}
                                </div>
                                <div class="text-xs text-gray-500">Teams</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-indigo-600">
                                    ₹{{ number_format($auction->min_bid_amount) }}
                                </div>
                                <div class="text-xs text-gray-500">Min Bid</div>
                            </div>
                        </div>
                        
                        <a href="{{ route('auction.public', $auction) }}" class="block w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-center font-medium rounded-lg transition-colors">
                            View Auction
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center max-w-3xl mx-auto">
            <div class="text-yellow-800 text-xl font-medium mb-4">No Active Auctions Right Now</div>
            <p class="text-yellow-700">Check back soon for upcoming auctions, or create your own league to start an auction!</p>
            
            @auth
                <a href="{{ route('leagues.create') }}" class="inline-block mt-6 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-md">
                    Create a League
                </a>
            @else
                <a href="{{ route('login') }}" class="inline-block mt-6 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-md">
                    Sign In to Create a League
                </a>
            @endauth
        </div>
    @endif
</div>
@endsection
