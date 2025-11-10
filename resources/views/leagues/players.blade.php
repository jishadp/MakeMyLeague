@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center">
                        @if($league->logo)
                            <img src="{{ Storage::url($league->logo) }}" class="w-16 h-16 rounded-full object-cover mr-4">
                        @endif
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $league->name }}</h1>
                            <p class="text-gray-600 mt-1">{{ $league->game->name }} • Season {{ $league->season }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @php
                    $soldPlayers = $league->leaguePlayers->where('status', 'sold')->where('retention', false)->sortBy('user.name');
                    $retentionPlayers = $league->leaguePlayers->where('retention', true)->sortBy('user.name');
                    $availablePlayers = $league->leaguePlayers->where('status', 'available')->where('retention', false)->sortBy('user.name');
                    $unsoldPlayers = $league->leaguePlayers->where('status', 'unsold')->where('retention', false)->sortBy('user.name');
                @endphp

                @if($soldPlayers->count() > 0)
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">Sold Players ({{ $soldPlayers->count() }})</span>
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach($soldPlayers as $player)
                                <div class="bg-white border-2 border-green-200 rounded-xl p-4 text-center hover:shadow-lg transition-all">
                                    @if($player->user && $player->user->photo)
                                        <img src="{{ Storage::url($player->user->photo) }}" class="w-20 h-20 rounded-full object-cover mx-auto mb-2 border-2 border-green-400">
                                    @else
                                        <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-2 border-2 border-green-400">
                                            <span class="text-2xl font-bold text-green-600">{{ $player->user ? substr($player->user->name, 0, 1) : '?' }}</span>
                                        </div>
                                    @endif
                                    <p class="font-semibold text-gray-900 truncate">{{ $player->user->name ?? 'Unknown' }}</p>
                                    @if($player->leagueTeam)
                                        <p class="text-xs text-gray-600 truncate">{{ $player->leagueTeam->team->name }}</p>
                                    @endif
                                    @if($player->bid_price)
                                        <p class="text-sm text-green-600 font-bold mt-1">₹{{ number_format($player->bid_price) }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($retentionPlayers->count() > 0)
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">Retained Players ({{ $retentionPlayers->count() }})</span>
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach($retentionPlayers as $player)
                                <div class="bg-white border-2 border-yellow-200 rounded-xl p-4 text-center hover:shadow-lg transition-all relative">
                                    <svg class="w-6 h-6 text-yellow-500 absolute top-2 right-2 drop-shadow-lg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    @if($player->user && $player->user->photo)
                                        <img src="{{ Storage::url($player->user->photo) }}" class="w-20 h-20 rounded-full object-cover mx-auto mb-2 border-2 border-yellow-400">
                                    @else
                                        <div class="w-20 h-20 rounded-full bg-yellow-100 flex items-center justify-center mx-auto mb-2 border-2 border-yellow-400">
                                            <span class="text-2xl font-bold text-yellow-600">{{ $player->user ? substr($player->user->name, 0, 1) : '?' }}</span>
                                        </div>
                                    @endif
                                    <p class="font-semibold text-gray-900 truncate">{{ $player->user->name ?? 'Unknown' }}</p>
                                    @if($player->leagueTeam)
                                        <p class="text-xs text-gray-600 truncate">{{ $player->leagueTeam->team->name }}</p>
                                    @endif
                                    <p class="text-xs text-yellow-600 font-semibold mt-1">Retained</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($availablePlayers->count() > 0)
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">Available Players ({{ $availablePlayers->count() }})</span>
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach($availablePlayers as $player)
                                <div class="bg-white border-2 border-blue-200 rounded-xl p-4 text-center hover:shadow-lg transition-all">
                                    @if($player->user && $player->user->photo)
                                        <img src="{{ Storage::url($player->user->photo) }}" class="w-20 h-20 rounded-full object-cover mx-auto mb-2 border-2 border-blue-400">
                                    @else
                                        <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-2 border-2 border-blue-400">
                                            <span class="text-2xl font-bold text-blue-600">{{ $player->user ? substr($player->user->name, 0, 1) : '?' }}</span>
                                        </div>
                                    @endif
                                    <p class="font-semibold text-gray-900 truncate">{{ $player->user->name ?? 'Unknown' }}</p>
                                    @if($player->base_price)
                                        <p class="text-sm text-blue-600 font-bold mt-1">₹{{ number_format($player->base_price) }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($unsoldPlayers->count() > 0)
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">Unsold Players ({{ $unsoldPlayers->count() }})</span>
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach($unsoldPlayers as $player)
                                <div class="bg-white border-2 border-gray-200 rounded-xl p-4 text-center hover:shadow-lg transition-all">
                                    @if($player->user && $player->user->photo)
                                        <img src="{{ Storage::url($player->user->photo) }}" class="w-20 h-20 rounded-full object-cover mx-auto mb-2 border-2 border-gray-400">
                                    @else
                                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-2 border-2 border-gray-400">
                                            <span class="text-2xl font-bold text-gray-600">{{ $player->user ? substr($player->user->name, 0, 1) : '?' }}</span>
                                        </div>
                                    @endif
                                    <p class="font-semibold text-gray-900 truncate">{{ $player->user->name ?? 'Unknown' }}</p>
                                    @if($player->base_price)
                                        <p class="text-sm text-gray-600 font-bold mt-1">₹{{ number_format($player->base_price) }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($soldPlayers->count() == 0 && $retentionPlayers->count() == 0 && $availablePlayers->count() == 0 && $unsoldPlayers->count() == 0)
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <p class="text-gray-500">No players found in this league</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
