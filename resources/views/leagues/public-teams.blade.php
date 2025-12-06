@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('teams.league-teams') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to League List
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
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-shadow overflow-hidden">
                    <!-- Team Header with Gradient -->
                    @if($leagueTeam->team->banner)
                        <div class="h-24 bg-cover bg-center" style="background-image: url('{{ Storage::url($leagueTeam->team->banner) }}')"></div>
                    @else
                        <div class="h-24 bg-gradient-to-r from-blue-500 to-blue-600"></div>
                    @endif
                    
                    <div class="p-4">
                        <!-- Team Info -->
                        <div class="flex items-center gap-3 mb-4">
                            @if($leagueTeam->team->logo)
                                <img src="{{ Storage::url($leagueTeam->team->logo) }}" class="w-16 h-16 rounded-full object-cover border-4 border-white -mt-10 shadow-lg">
                            @else
                                <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center border-4 border-white -mt-10 shadow-lg">
                                    <span class="text-xl font-bold text-gray-600">{{ substr($leagueTeam->team->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-gray-900 truncate">{{ $leagueTeam->team->name }}</h3>
                                <p class="text-sm text-gray-600 truncate">{{ $leagueTeam->team->owners->first()->name ?? 'No owner' }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-blue-600">{{ $leagueTeam->leaguePlayers->count() }}</div>
                                <div class="text-xs text-gray-500">Players</div>
                            </div>
                        </div>

                        <!-- SQUAD Section -->
                        @if($players->count() > 0)
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide">SQUAD</h4>
                                    <span class="text-xs text-gray-500">{{ $players->count() }} players</span>
                                </div>
                                
                                <!-- 3 Players Per Row Grid -->
                                <div class="grid grid-cols-3 gap-3">
                                    @foreach($players as $player)
                                        @php
                                            $value = $player->bid_price ?? $player->base_price ?? 0;
                                        @endphp
                                        <div class="text-center">
                                            <div class="relative inline-block mb-2">
                                                @if($player->user?->photo)
                                                    <img src="{{ Storage::url($player->user->photo) }}" class="w-16 h-16 rounded-full object-cover mx-auto border-2 {{ $player->retention ? 'border-yellow-400' : 'border-gray-200' }}">
                                                @else
                                                    <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center mx-auto border-2 {{ $player->retention ? 'border-yellow-400' : 'border-gray-200' }}">
                                                        <span class="text-sm font-bold text-gray-600">{{ strtoupper(substr($player->user?->name ?? 'P', 0, 1)) }}</span>
                                                    </div>
                                                @endif
                                                @if($player->retention)
                                                    <div class="absolute -top-1 -right-1 w-6 h-6 bg-yellow-400 rounded-full flex items-center justify-center border-2 border-white">
                                                        <span class="text-xs">⭐</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <p class="text-xs font-semibold text-gray-900 truncate px-1">{{ $player->user?->name ?? 'Unknown' }}</p>
                                            @if($player->retention)
                                                <p class="text-xs font-bold text-yellow-600">Retained</p>
                                            @else
                                                <p class="text-xs font-bold text-green-600">₹{{ number_format($value) }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Stats Footer -->
                        @php
                            $playerSpend = $players->sum(fn($p) => $p->bid_price ?? $p->base_price ?? 0);
                            $remainingWallet = $leagueTeam->wallet_balance;
                            $totalSpent = $league->team_wallet_limit
                                ? max(0, ($league->team_wallet_limit - $remainingWallet))
                                : $playerSpend;
                        @endphp
                        <div class="grid grid-cols-3 gap-2 pt-4 border-t border-gray-100">
                            <div class="text-center">
                                <div class="text-xl font-bold text-gray-900">₹{{ number_format($remainingWallet) }}</div>
                                <div class="text-xs text-gray-500">Remaining</div>
                            </div>
                            <div class="text-center">
                                <div class="text-xl font-bold text-gray-900">₹{{ number_format($totalSpent) }}</div>
                                <div class="text-xs text-gray-500">Spent</div>
                            </div>
                            <div class="text-center">
                                <div class="text-xl font-bold text-yellow-600">{{ $players->where('retention', true)->count() }}</div>
                                <div class="text-xs text-gray-500">Retained</div>
                            </div>
                        </div>
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
