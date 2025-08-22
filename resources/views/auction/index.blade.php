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
        @if(auth()->user()->isOrganizer())
        <div class="mb-8" id="availablePlayersSection">
            @include('auction.partials.available-players')
        </div>
        @endif

        <!-- Player Bidding Section -->
        <div class="flex justify-center mb-8" id="biddingSection">
            <div class="w-full max-w-2xl">
                @include('auction.partials.player-bidding')
            </div>
        </div>

        <!-- Recent and Highest Bids Table -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

            <!-- Highest Bids -->
            @livewire('highest-bids', ['leagueId' => $league->id])

            <!-- Teams Table -->
            @livewire('teams', ['leagueId' => $league->id])
        </div>
    </div>
</div>

<!-- Message Container -->
<div id="messageContainer" class="fixed top-4 right-4 z-50"></div>

@endsection

@section('scripts')
<script
  src="https://code.jquery.com/jquery-3.7.1.js"
  integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
  crossorigin="anonymous"></script>
<script src="{{ asset('js/auction.js') }}"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="{{ asset('js/pusher-main.js') }}"></script>
@endsection
