@extends('layouts.app')

@section('title', 'My Auctions')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full mb-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Auctions</h1>
            <p class="text-gray-600">Join live auctions and manage your bidding activities</p>
        </div>

        @if($auctionLeagues->isEmpty())
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Auctions Available</h3>
                <p class="text-gray-600 mb-6">You don't have access to any auctions yet. Join a league or get assigned as an auctioneer to participate.</p>
                <a href="{{ route('my-leagues') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Browse Leagues
                </a>
            </div>
        @else
            <!-- Auctions Grid -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($auctionLeagues as $auctionData)
                    @php
                        $league = $auctionData['league'];
                        $role = $auctionData['role'];
                        $leagueTeam = $auctionData['league_team'];
                        $canBid = $auctionData['can_bid'];
                        $status = $auctionData['auction_status'];
                    @endphp
                    
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <!-- League Header -->
                        <div class="p-6 bg-gradient-to-r from-blue-500 to-purple-600 text-white">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-lg font-semibold">{{ $league->name }}</h3>
                                <span class="px-2 py-1 bg-white/20 rounded-full text-xs font-medium">
                                    {{ ucfirst(str_replace('_', ' ', $role)) }}
                                </span>
                            </div>
                            @if($leagueTeam && $leagueTeam->team)
                                <p class="text-blue-100 text-sm">{{ $leagueTeam->team->name }}</p>
                            @endif
                        </div>
                        
                        <!-- Auction Status -->
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    @if($status === 'live')
                                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                                        <span class="text-green-600 font-medium text-sm">Live Auction</span>
                                    @elseif($status === 'ready')
                                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                                        <span class="text-blue-600 font-medium text-sm">Ready to Start</span>
                                    @elseif($status === 'completed')
                                        <div class="w-3 h-3 bg-gray-400 rounded-full mr-2"></div>
                                        <span class="text-gray-600 font-medium text-sm">Completed</span>
                                    @else
                                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                                        <span class="text-yellow-600 font-medium text-sm">Pending Access</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- League Details -->
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Game:</span>
                                    <span class="font-medium">{{ $league->game->name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Teams:</span>
                                    <span class="font-medium">{{ $league->leagueTeams->count() }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Players:</span>
                                    <span class="font-medium">{{ $league->leaguePlayers->count() }}</span>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="space-y-2">
                                @if($status === 'live' && $canBid)
                                    <a href="{{ route('auction.index', $league->slug) }}" 
                                       class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-medium rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        @if($role === 'auctioneer')
                                            Join Auction (Bidding for {{ $leagueTeam->team->name }})
                                        @else
                                            Join Live Auction
                                        @endif
                                    </a>
                                @elseif($status === 'ready' && $canBid)
                                    <a href="{{ route('auction.index', $league->slug) }}" 
                                       class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-300">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        View Auction
                                    </a>
                                @elseif($status === 'completed')
                                    <a href="{{ route('league-teams.show', [$league->slug, $leagueTeam->id ?? 1]) }}" 
                                       class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-purple-500 to-pink-600 text-white font-medium rounded-lg hover:from-purple-600 hover:to-pink-700 transition-all duration-300">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        View Results
                                    </a>
                                @else
                                    <div class="w-full flex items-center justify-center px-4 py-3 bg-gray-100 text-gray-500 font-medium rounded-lg">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Waiting for Access
                                    </div>
                                @endif
                                
                                <!-- Secondary Action -->
                                @if($role === 'team_owner' || $role === 'organizer')
                                    <a href="{{ route('league-teams.manage', $league->slug) }}" 
                                       class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                        Manage Teams
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
