@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('leagues.show', $league) }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to League
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center">
                    @if($league->logo)
                        <img src="{{ Storage::url($league->logo) }}" class="w-16 h-16 rounded-full object-cover mr-4">
                    @endif
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $league->name }}</h1>
                        <p class="text-gray-600">{{ $league->game->name }} • Season {{ $league->season }}</p>
                    </div>
                </div>
                <span class="px-4 py-2 rounded-full text-sm font-medium
                    @if($league->status === 'active') bg-green-100 text-green-800
                    @elseif($league->status === 'completed') bg-blue-100 text-blue-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($league->status) }}
                </span>
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Teams ({{ $league->leagueTeams->count() }}/{{ $league->max_teams }})</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($league->leagueTeams->sortByDesc('created_at') as $leagueTeam)
                @php
                    $players = $leagueTeam->leaguePlayers->sortByDesc(function ($player) {
                        $value = (int) ($player->bid_price ?? $player->base_price ?? 0);
                        return sprintf('%d-%012d', $player->retention ? 1 : 0, $value);
                    });
                @endphp
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow overflow-hidden flex flex-col">
                    @if($leagueTeam->team->banner)
                        <div class="h-32 bg-cover bg-center" style="background-image: url('{{ Storage::url($leagueTeam->team->banner) }}')"></div>
                    @else
                        <div class="h-32 bg-gradient-to-br from-indigo-500 to-purple-600"></div>
                    @endif
                    
                    <div class="p-6 flex-1 flex flex-col gap-4">
                        <div class="flex items-center mb-4">
                            @if($leagueTeam->team->logo)
                                <img src="{{ Storage::url($leagueTeam->team->logo) }}" class="w-12 h-12 rounded-full object-cover mr-3">
                            @else
                                <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                    <span class="text-xl font-bold text-gray-600">{{ substr($leagueTeam->team->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $leagueTeam->team->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $leagueTeam->team->owners->first()->name ?? 'No owner' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold text-indigo-600">{{ $leagueTeam->leaguePlayers->count() }}</div>
                                <div class="text-xs text-gray-600">Players</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold text-green-600">₹{{ number_format($leagueTeam->wallet_balance) }}</div>
                                <div class="text-xs text-gray-600">Wallet</div>
                            </div>
                        </div>

                        <div class="text-center">
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                @if($leagueTeam->status === 'active') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($leagueTeam->status) }}
                            </span>
                        </div>

                        @if($players->count() > 0)
                            <div class="border-t border-gray-200 pt-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-gray-900">Players</p>
                                    <span class="text-xs text-gray-500">Showing {{ $players->count() }}</span>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-64 overflow-auto pr-1">
                                    @foreach($players as $player)
                                        @php
                                            $value = $player->bid_price ?? $player->base_price ?? 0;
                                            $valueLabel = $player->bid_price ? 'Sold' : 'Base';
                                        @endphp
                                        <div class="flex items-center gap-3 rounded-lg border border-gray-100 bg-gray-50 px-3 py-2">
                                            @if($player->user?->photo)
                                                <img src="{{ Storage::url($player->user->photo) }}" class="w-10 h-10 rounded-full object-cover">
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-sm font-semibold text-gray-600">
                                                    {{ strtoupper(substr($player->user?->name ?? 'P', 0, 1)) }}
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $player->user?->name ?? 'Unknown' }}</p>
                                                <p class="text-xs text-gray-500 truncate">{{ $player->user?->position?->name ?? 'Role' }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-bold text-gray-900">₹{{ number_format($value) }}</p>
                                                <p class="text-[11px] text-gray-500">{{ $valueLabel }}</p>
                                            </div>
                                            @if($player->retention)
                                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-amber-600 bg-amber-50 border border-amber-100 rounded-full px-2 py-0.5">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                    Retained
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="text-xs text-gray-500 border-t border-gray-200 pt-4">No players added yet.</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p class="text-gray-500">No teams registered yet</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
