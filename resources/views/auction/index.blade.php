@extends('layouts.app')

@section('title', 'Auction - MakeMyLeague')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/auction.css') }}">
@endsection

@section('content')
<div class="min-h-screen auction-bg py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Header -->
        <div class="glacier-card mb-6">
            <div class="px-4 py-4 sm:px-6 border-b glacier-border">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold glacier-text-primary">Cricket League Auction</h1>
                    </div>
                </div>
            </div>
            
            <!-- Auction Statistics -->
            <div class="px-4 py-4 sm:px-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="glacier-card p-3 border-0">
                        <div class="flex items-center">
                            <div class="bg-green-100 rounded-full p-2 mr-3">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-green-600 font-medium">Total Players</p>
                                <p class="text-lg font-bold text-green-800">150</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="glacier-card p-3 border-0">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-full p-2 mr-3">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-blue-600 font-medium">Available</p>
                                <p class="text-lg font-bold text-blue-800">45</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="glacier-card p-3 border-0">
                        <div class="flex items-center">
                            <div class="bg-purple-100 rounded-full p-2 mr-3">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-purple-600 font-medium">Sold</p>
                                <p class="text-lg font-bold text-purple-800">105</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="glacier-card p-3 border-0">
                        <div class="flex items-center">
                            <div class="bg-yellow-100 rounded-full p-2 mr-3">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-yellow-600 font-medium">Revenue</p>
                                <p class="text-lg font-bold text-yellow-800">₹2,450,000</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Progress</span>
                        <span>70%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-green-500 to-blue-500 h-2 rounded-full transition-all duration-300" style="width: 70%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Players Section -->
        <div class="mb-8" id="availablePlayersSection">
            @include('auction.partials.available-players')
        </div>
            
        <!-- Player Bidding Section -->
        <div class="flex justify-center mb-8" id="biddingSection">
            <div class="w-full max-w-2xl">
                @include('auction.partials.player-bidding')
            </div>
        </div>
            
        <!-- Recent and Highest Bids Table -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            
            <!-- Highest Bids -->
            <div class="glacier-card">
                <div class="px-4 py-4 sm:px-6 border-b glacier-border">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold glacier-text-primary">Highest Bids</h2>
                        <div class="badge-purple px-3 py-1 rounded-full text-sm font-medium">
                            105 Sold
                        </div>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <div id="highestBidsTable" class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="table-header">
                                <tr>
                                    <th class="text-left py-3 px-4 font-medium glacier-text-secondary">Player</th>
                                    <th class="text-left py-3 px-4 font-medium glacier-text-secondary">Team</th>
                                    <th class="text-left py-3 px-4 font-medium glacier-text-secondary">Sold Price</th>
                                    <th class="text-left py-3 px-4 font-medium glacier-text-secondary">Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-row border-b border-gray-100">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-full overflow-hidden bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center shadow-lg">
                                                <img src="{{ asset('images/defaultplayer.jpeg') }}" 
                                                     alt="Virat Kohli" 
                                                     class="w-full h-full object-cover">
                                            </div>
                                            <div>
                                                <p class="font-medium glacier-text-primary text-sm">Virat Kohli</p>
                                                <p class="text-xs text-gray-500">Batsman</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="text-sm glacier-text-primary">Mumbai Indians</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="font-bold text-purple-600">₹85,000</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="font-medium text-green-600">₹35,000</span>
                                    </td>
                                </tr>
                                <tr class="table-row border-b border-gray-100">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-full overflow-hidden bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center shadow-lg">
                                                <img src="{{ asset('images/defaultplayer.jpeg') }}" 
                                                     alt="Rohit Sharma" 
                                                     class="w-full h-full object-cover">
                                            </div>
                                            <div>
                                                <p class="font-medium glacier-text-primary text-sm">Rohit Sharma</p>
                                                <p class="text-xs text-gray-500">Batsman</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="text-sm glacier-text-primary">Chennai Super Kings</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="font-bold text-purple-600">₹75,000</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="font-medium text-green-600">₹25,000</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Teams Table -->
            <div class="glacier-card">
                <div class="px-4 py-4 sm:px-6 border-b glacier-border">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold glacier-text-primary">Teams</h2>
                        <div class="badge-blue px-3 py-1 rounded-full text-sm font-medium">
                            8 Teams
                        </div>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                                    MI
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Mumbai Indians</p>
                                    <p class="text-sm text-gray-500">₹125,000 remaining</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-green-600">15 Players</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center text-white font-bold">
                                    CSK
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Chennai Super Kings</p>
                                    <p class="text-sm text-gray-500">₹95,000 remaining</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-green-600">14 Players</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Message Container -->
<div id="messageContainer" class="fixed top-4 right-4 z-50"></div>

@endsection

@section('scripts')
<script src="{{ asset('js/auction.js') }}"></script>
@endsection
