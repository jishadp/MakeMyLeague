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

        <style>
            @keyframes orbit-flight {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            
            @keyframes glow-pulse {
                0%, 100% { 
                    box-shadow: 
                        0 0 10px rgba(245, 158, 11, 0.3),
                        0 0 20px rgba(245, 158, 11, 0.2),
                        inset 0 0 5px rgba(245, 158, 11, 0.1);
                }
                50% { 
                    box-shadow: 
                        0 0 15px rgba(245, 158, 11, 0.4),
                        0 0 30px rgba(245, 158, 11, 0.3),
                        inset 0 0 10px rgba(245, 158, 11, 0.15);
                }
            }
            
            @keyframes shine {
                0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
                100% { transform: translateX(200%) translateY(200%) rotate(45deg); }
            }
            
            .foreign-card-sm {
                position: relative;
                overflow: hidden;
                animation: glow-pulse 3s ease-in-out infinite;
                background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
                border: 1px solid #f59e0b !important;
            }
            
            .foreign-card-sm::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.3) 50%, transparent 70%);
                animation: shine 4s infinite;
                pointer-events: none;
                z-index: 1;
            }
            
            .foreign-plane-sm {
                filter: drop-shadow(0 0 2px rgba(245, 158, 11, 0.6));
            }
        </style>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($league->leagueTeams->sortByDesc('created_at') as $leagueTeam)
                @php
                    $leagueDistrictId = $league->localBody?->district_id;
                    $allPlayers = $leagueTeam->leaguePlayers->map(function($player) use ($leagueDistrictId) {
                        $playerDistrictId = $player->user?->localBody?->district_id;
                        $player->is_foreign = $leagueDistrictId && $playerDistrictId && $leagueDistrictId !== $playerDistrictId;
                        return $player;
                    });

                    $sortedPlayers = $allPlayers->sortByDesc(function ($player) {
                        $value = (int) ($player->bid_price ?? $player->base_price ?? 0);
                        // Sort: Foreign Retained (3) > Regular Retained (2) > Value (1)
                        if ($player->is_foreign && $player->retention) return '3-' . sprintf('%012d', $value);
                        if ($player->retention) return '2-' . sprintf('%012d', $value);
                        return '1-' . sprintf('%012d', $value);
                    });

                    $foreignRetained = $sortedPlayers->filter(function($p) {
                        return $p->is_foreign && $p->retention;
                    });

                    $otherPlayers = $sortedPlayers->reject(function($p) {
                        return $p->is_foreign && $p->retention;
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
                        @if($sortedPlayers->count() > 0)
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide">SQUAD</h4>
                                    <span class="text-xs text-gray-500">{{ $sortedPlayers->count() }} players</span>
                                </div>
                                
                                <!-- Foreign Retained Players -->
                                @if($foreignRetained->count() > 0)
                                    <div class="mb-4">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded border border-amber-200 uppercase tracking-wide">Foreign Stars</span>
                                            <div class="h-px bg-amber-200 flex-1"></div>
                                        </div>
                                        <div class="grid grid-cols-3 gap-2">
                                            @foreach($foreignRetained as $player)
                                                @php
                                                    $placeName = $player->user?->localBody?->name ?? 'Unknown';
                                                @endphp
                                                <div class="text-center p-2 rounded-lg foreign-card-sm">
                                                    <div class="relative inline-block mb-1.5">
                                                        <div class="absolute top-0 left-0 bg-amber-600 text-white text-[7px] font-bold px-1.5 py-0.5 rounded-tl-md rounded-br-sm z-10 leading-none">
                                                            {{ Str::limit(strtoupper($placeName), 8) }}
                                                        </div>
                                                        
                                                        @if($player->user?->photo)
                                                            <img src="{{ Storage::url($player->user->photo) }}" class="w-12 h-12 rounded-full object-cover mx-auto border-2 border-amber-400 shadow-sm relative z-10">
                                                        @else
                                                            <div class="w-12 h-12 rounded-full bg-amber-100 border-2 border-amber-300 flex items-center justify-center mx-auto relative z-10">
                                                                <span class="text-xs font-bold text-amber-800">{{ strtoupper(substr($player->user?->name ?? 'P', 0, 1)) }}</span>
                                                            </div>
                                                        @endif

                                                        <!-- Plane Icon -->
                                                        <div class="absolute inset-[-4px] pointer-events-none flight-orbit z-20">
                                                            <div class="absolute -top-1 left-1/2 -translate-x-1/2 transform text-amber-600 foreign-plane-sm">
                                                                <svg class="w-4 h-4 transform rotate-90" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="absolute -top-1 -right-1 w-5 h-5 bg-amber-500 rounded-full flex items-center justify-center border border-white z-20 shadow-sm">
                                                            <span class="text-[10px] text-white">⭐</span>
                                                        </div>
                                                    </div>
                                                    <p class="text-[10px] font-bold text-slate-900 truncate px-1 leading-tight relative z-10">{{ $player->user?->name ?? 'Unknown' }}</p>
                                                    <p class="text-[9px] font-bold text-amber-700 leading-tight flex items-center justify-center gap-1 relative z-10">
                                                        Retained
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Other Players -->
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach($otherPlayers as $player)
                                        @php
                                            $value = $player->bid_price ?? $player->base_price ?? 0;
                                        @endphp
                                        <div class="text-center">
                                            <div class="relative inline-block mb-1.5">
                                                @if($player->user?->photo)
                                                    <img src="{{ Storage::url($player->user->photo) }}" class="w-12 h-12 rounded-full object-cover mx-auto border {{ $player->retention ? 'border-yellow-400' : 'border-gray-200' }}">
                                                @else
                                                    <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center mx-auto border {{ $player->retention ? 'border-yellow-400' : 'border-gray-200' }}">
                                                        <span class="text-xs font-bold text-gray-600">{{ strtoupper(substr($player->user?->name ?? 'P', 0, 1)) }}</span>
                                                    </div>
                                                @endif
                                                @if($player->retention)
                                                    <div class="absolute -top-1 -right-1 w-5 h-5 bg-yellow-400 rounded-full flex items-center justify-center border border-white">
                                                        <span class="text-[10px]">⭐</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <p class="text-[10px] font-semibold text-gray-900 truncate px-1 leading-tight">{{ $player->user?->name ?? 'Unknown' }}</p>
                                            @if($player->retention)
                                                <p class="text-[10px] font-bold text-yellow-600 leading-tight">Retained</p>
                                            @else
                                                <p class="text-[10px] font-bold text-green-600 leading-tight break-words">₹{{ number_format($value) }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Stats Footer -->
                        @php
                            $playerSpend = $sortedPlayers->sum(fn($p) => $p->bid_price ?? $p->base_price ?? 0);
                            $remainingWallet = $leagueTeam->wallet_balance;
                            $totalSpent = $league->team_wallet_limit
                                ? max(0, ($league->team_wallet_limit - $remainingWallet))
                                : $playerSpend;
                        @endphp
                        <div class="grid grid-cols-3 gap-2 pt-4 border-t border-gray-100">
                            <div class="text-center">
                                <div class="text-base sm:text-lg font-bold text-gray-900 break-words leading-tight">₹{{ number_format($remainingWallet) }}</div>
                                <div class="text-[11px] text-gray-500">Remaining</div>
                            </div>
                            <div class="text-center">
                                <div class="text-base sm:text-lg font-bold text-gray-900 break-words leading-tight">₹{{ number_format($totalSpent) }}</div>
                                <div class="text-[11px] text-gray-500">Spent</div>
                            </div>
                            <div class="text-center">
                                <div class="text-base sm:text-lg font-bold text-yellow-600 leading-tight">{{ $sortedPlayers->where('retention', true)->count() }}</div>
                                <div class="text-[11px] text-gray-500">Retained</div>
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
