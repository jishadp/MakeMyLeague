@extends('layouts.app')

@section('title', 'Player Details - ' . $leaguePlayer->user->name)

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                <div class="flex items-center space-x-4">
                    <div class="w-20 h-20 rounded-full overflow-hidden bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        <img src="{{ asset('images/defaultplayer.jpeg') }}" 
                             alt="{{ $leaguePlayer->user->name }}" 
                             class="w-full h-full object-cover"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-full h-full flex items-center justify-center text-white font-bold text-xl" style="display: none;">
                            {{ strtoupper(substr($leaguePlayer->user->name, 0, 2)) }}
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $leaguePlayer->user->name }}</h1>
                        <p class="text-gray-600 mt-2">{{ $league->name }} - {{ $leaguePlayer->leagueTeam->team->name }}</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('league-players.edit', [$league, $leaguePlayer]) }}" 
                       class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        Edit Player
                    </a>
                    <a href="{{ route('league-players.index', $league) }}" 
                       class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        Back to Players
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Player Info -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Player Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <p class="text-gray-900">{{ $leaguePlayer->user->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <p class="text-gray-900">{{ $leaguePlayer->user->email }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <p class="text-gray-900">{{ $leaguePlayer->user->phone ?? 'Not provided' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <p class="text-gray-900">{{ $leaguePlayer->user->position->name ?? 'Not Set' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Team</label>
                            <p class="text-gray-900">{{ $leaguePlayer->leagueTeam->team->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Base Price</label>
                            <p class="text-gray-900 font-semibold">₹{{ number_format($leaguePlayer->base_price) }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'available' => 'bg-blue-100 text-blue-800',
                                    'sold' => 'bg-green-100 text-green-800',
                                    'unsold' => 'bg-red-100 text-red-800',
                                    'skip' => 'bg-gray-100 text-gray-800'
                                ];
                            @endphp
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$leaguePlayer->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($leaguePlayer->status) }}
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Retention Status</label>
                            @if($leaguePlayer->retention)
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                    Retained
                                </span>
                            @else
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Not Retained
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if($leaguePlayer->auctionBids->count() > 0)
                <!-- Auction History -->
                <div class="bg-white rounded-lg shadow-sm p-6 mt-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Auction History</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Auction</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bidding Team</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($leaguePlayer->auctionBids as $bid)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $bid->auction->title ?? 'Auction #' . $bid->auction_id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $bid->leagueTeam->team->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        ₹{{ number_format($bid->amount) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $bid->status === 'winning' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($bid->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $bid->created_at->format('M d, Y H:i') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Actions Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('league-players.edit', [$league, $leaguePlayer]) }}" 
                           class="w-full flex items-center justify-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
                            Edit Player
                        </a>
                        
                        <form action="{{ route('league-players.destroy', [$league, $leaguePlayer]) }}" 
                              method="POST" 
                              onsubmit="return confirm('Are you sure you want to remove this player from the league?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full flex items-center justify-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700">
                                Remove from Team
                            </button>
                        </form>
                        
                        @if($leaguePlayer->retention)
                        <form action="{{ route('league-players.updateStatus', [$league, $leaguePlayer]) }}" 
                              method="POST" 
                              onsubmit="return confirm('Are you sure you want to remove the retention status from this player?')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="retention" value="0">
                            <button type="submit" 
                                    class="w-full flex items-center justify-center px-4 py-2 bg-amber-600 text-white font-medium rounded-lg hover:bg-amber-700">
                                Remove Retention
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                
                <!-- Player Stats -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Bids</span>
                            <span class="font-semibold">{{ $leaguePlayer->auctionBids->count() }}</span>
                        </div>
                        
                        @if($leaguePlayer->auctionBids->count() > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Highest Bid</span>
                            <span class="font-semibold">₹{{ number_format($leaguePlayer->auctionBids->max('amount')) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Average Bid</span>
                            <span class="font-semibold">₹{{ number_format($leaguePlayer->auctionBids->avg('amount')) }}</span>
                        </div>
                        @endif
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Joined</span>
                            <span class="font-semibold">{{ $leaguePlayer->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection
