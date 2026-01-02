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
        
        <div class="title-card rounded-xl p-6 mb-8 border">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center">
                    @if($league->logo)
                        <img src="{{ Storage::url($league->logo) }}" class="w-16 h-16 rounded-full object-cover mr-4 title-logo shadow-lg border-4 border-white">
                    @endif
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 title-heading">{{ $league->name }}</h1>
                        <p class="text-gray-600 title-subtext">{{ $league->game->name }} • Season {{ $league->season }}</p>
                    </div>
                </div>
                <span class="status-badge px-4 py-2 rounded-full text-sm font-medium
                    @if($league->status === 'active') bg-green-100 text-green-800
                    @elseif($league->status === 'completed') bg-blue-100 text-blue-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($league->status) }}
                </span>
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 section-title">Teams ({{ $league->leagueTeams->count() }}/{{ $league->max_teams }})</h2>
        </div>

        <style>
            /* GPU Acceleration Helper */
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
            
            @keyframes float {
                0%, 100% { transform: translateY(0px) scale(1); }
                50% { transform: translateY(-8px) scale(1.02); }
            }
            
            /* Title Card Animations */
            @keyframes title-card-glow {
                0%, 100% { 
                    box-shadow: 
                        0 2px 12px rgba(99, 102, 241, 0.15),
                        0 4px 20px rgba(99, 102, 241, 0.1),
                        inset 0 0 8px rgba(99, 102, 241, 0.05);
                }
                50% { 
                    box-shadow: 
                        0 4px 16px rgba(99, 102, 241, 0.2),
                        0 8px 32px rgba(99, 102, 241, 0.15),
                        inset 0 0 12px rgba(99, 102, 241, 0.08);
                }
            }
            
            @keyframes title-card-enter {
                from {
                    opacity: 0;
                    transform: translateY(-20px) translateZ(0);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) translateZ(0);
                }
            }
            
            @keyframes title-slide-in {
                from {
                    opacity: 0;
                    transform: translateX(-30px) translateZ(0);
                }
                to {
                    opacity: 1;
                    transform: translateX(0) translateZ(0);
                }
            }
            
            @keyframes status-badge-pop {
                from {
                    opacity: 0;
                    transform: scale(0.8) translateZ(0);
                }
                to {
                    opacity: 1;
                    transform: scale(1) translateZ(0);
                }
            }
            
            .title-card {
                animation: title-card-enter 0.6s cubic-bezier(0.34, 1.56, 0.64, 1), 
                           title-card-glow 3s ease-in-out infinite 0.6s;
                background: linear-gradient(135deg, #ffffff 0%, #f0f4ff 100%);
                border: 1px solid #e0e7ff !important;
                will-change: box-shadow, transform;
                transform: translateZ(0);
                backface-visibility: hidden;
            }
            
            .title-card:hover {
                transform: translateY(-4px) translateZ(0);
                box-shadow: 
                    0 6px 24px rgba(99, 102, 241, 0.25),
                    0 10px 40px rgba(99, 102, 241, 0.15);
            }
            
            .title-heading {
                animation: title-slide-in 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) 0.1s backwards;
            }
            
            .title-subtext {
                animation: title-slide-in 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s backwards;
            }
            
            .title-logo {
                animation: float 3s ease-in-out infinite;
                will-change: transform;
            }
            
            .status-badge {
                animation: status-badge-pop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 0.4s backwards;
                will-change: transform;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .status-badge:hover {
                transform: scale(1.05) translateZ(0);
            }
            
            /* Teams section title */
            @keyframes section-title-enter {
                from {
                    opacity: 0;
                    transform: translateX(-20px) translateZ(0);
                }
                to {
                    opacity: 1;
                    transform: translateX(0) translateZ(0);
                }
            }
            
            .section-title {
                animation: section-title-enter 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 0.3s backwards;
            }
            
            /* GPU acceleration for smooth animations */
            .flight-orbit {
                animation: orbit-flight 4s linear infinite;
                will-change: transform;
                transform: translateZ(0);
                backface-visibility: hidden;
            }
            
            /* Foreign card with blue sky theme */
            @keyframes foreign-glow-sm {
                0%, 100% { 
                    box-shadow: 
                        0 0 12px rgba(245, 158, 11, 0.3),
                        0 0 20px rgba(245, 158, 11, 0.2),
                        inset 0 0 8px rgba(245, 158, 11, 0.1);
                }
                50% { 
                    box-shadow: 
                        0 0 18px rgba(245, 158, 11, 0.4),
                        0 0 35px rgba(245, 158, 11, 0.3),
                        inset 0 0 12px rgba(245, 158, 11, 0.15);
                }
            }
            
            @keyframes shine-sm {
                0% { transform: translateX(-100%) rotate(45deg); }
                100% { transform: translateX(200%) rotate(45deg); }
            }
            
            .foreign-card-sm {
                position: relative;
                overflow: hidden;
                animation: foreign-glow-sm 3s ease-in-out infinite;
                background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
                border: 1px solid #f59e0b !important;
                will-change: box-shadow;
                transform: translateZ(0);
                backface-visibility: hidden;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .foreign-card-sm:hover {
                transform: translateY(-2px) scale(1.01) translateZ(0);
                box-shadow: 
                    0 0 15px rgba(245, 158, 11, 0.5),
                    0 0 25px rgba(245, 158, 11, 0.3);
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
                will-change: transform;
            }
            
            .foreign-plane-sm {
                filter: drop-shadow(0 0 2px rgba(245, 158, 11, 0.6));
                will-change: filter;
            }
            
            /* Regular player card in team list */
            @keyframes regular-glow-sm {
                0%, 100% { 
                    box-shadow: 
                        0 0 8px rgba(59, 130, 246, 0.15),
                        inset 0 0 4px rgba(59, 130, 246, 0.08);
                }
                50% { 
                    box-shadow: 
                        0 0 12px rgba(59, 130, 246, 0.25),
                        inset 0 0 6px rgba(59, 130, 246, 0.12);
                }
            }
            
            .player-card-sm {
                position: relative;
                background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
                border: 1px solid #93c5fd;
                border-radius: 0.5rem;
                padding: 0.5rem;
                text-align: center;
                animation: regular-glow-sm 3s ease-in-out infinite;
                will-change: box-shadow;
                transform: translateZ(0);
                backface-visibility: hidden;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .player-card-sm:hover {
                transform: translateY(-2px) scale(1.01) translateZ(0);
                box-shadow: 
                    0 0 12px rgba(59, 130, 246, 0.3),
                    0 0 20px rgba(59, 130, 246, 0.15);
            }
            
            .player-card-sm::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 50%;
                height: 100%;
                background: linear-gradient(
                    90deg,
                    transparent,
                    rgba(59, 130, 246, 0.15),
                    transparent
                );
                animation: shine-sm 4s infinite;
                pointer-events: none;
                z-index: 0;
                will-change: transform;
            }
            
            /* Team card animations */
            @keyframes team-card-enter {
                from {
                    opacity: 0;
                    transform: translateY(20px) translateZ(0);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) translateZ(0);
                }
            }
            
            @keyframes team-stats-pulse {
                0%, 100% { 
                    box-shadow: 
                        0 1px 3px rgba(0, 0, 0, 0.1),
                        inset 0 0 4px rgba(100, 116, 139, 0.05);
                }
                50% { 
                    box-shadow: 
                        0 2px 4px rgba(0, 0, 0, 0.15),
                        inset 0 0 6px rgba(100, 116, 139, 0.08);
                }
            }
            
            .team-card {
                animation: team-card-enter 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) backwards;
                will-change: transform, opacity;
                transform: translateZ(0);
            }
            
            .team-card:nth-child(1) { animation-delay: 0.1s; }
            .team-card:nth-child(2) { animation-delay: 0.2s; }
            .team-card:nth-child(3) { animation-delay: 0.3s; }
            .team-card:nth-child(4) { animation-delay: 0.4s; }
            .team-card:nth-child(5) { animation-delay: 0.5s; }
            .team-card:nth-child(6) { animation-delay: 0.6s; }
            
            .team-stats {
                animation: team-stats-pulse 3s ease-in-out infinite;
                will-change: box-shadow;
                transform: translateZ(0);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .team-stats:hover {
                transform: scale(1.02) translateZ(0);
            }
            
            /* Reduce motion for accessibility */
            @media (prefers-reduced-motion: reduce) {
                .foreign-card-sm,
                .player-card-sm,
                .flight-orbit,
                .foreign-plane-sm,
                .team-card,
                .team-stats,
                .title-card,
                .title-heading,
                .title-subtext,
                .title-logo,
                .status-badge,
                .section-title {
                    animation: none !important;
                    will-change: auto;
                }
                
                .foreign-card-sm:hover,
                .player-card-sm:hover,
                .team-stats:hover,
                .title-card:hover,
                .status-badge:hover {
                    transform: none;
                }
            }
            
            /* Optimize for mobile devices */
            @media (max-width: 768px) {
                .foreign-card-sm,
                .player-card-sm,
                .team-stats,
                .title-card {
                    animation-duration: 4s;
                }
                
                .flight-orbit {
                    animation-duration: 5s;
                }
                
                .team-card {
                    animation: team-card-enter 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) backwards;
                }
                
                .title-heading,
                .title-subtext,
                .status-badge,
                .section-title {
                    animation-duration: 0.5s;
                }
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
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-shadow overflow-hidden team-card">
                    <!-- Team Header with Gradient -->
                    @if($leagueTeam->team->banner)
                        <div class="h-24 bg-cover bg-center" style="background-image: url('{{ Storage::url($leagueTeam->team->banner) }}')"></div>
                    @else
                        <div class="h-24 bg-gradient-to-r from-slate-400 to-slate-500"></div>
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
                                        <div class="text-center player-card-sm relative">
                                            <div class="relative inline-block mb-1.5">
                                                @if($player->user?->photo)
                                                    <img src="{{ Storage::url($player->user->photo) }}" class="w-12 h-12 rounded-full object-cover mx-auto border-2 {{ $player->retention ? 'border-purple-400' : 'border-blue-300' }} shadow relative z-10">
                                                @else
                                                    <div class="w-12 h-12 rounded-full {{ $player->retention ? 'bg-purple-100' : 'bg-blue-100' }} flex items-center justify-center mx-auto border-2 {{ $player->retention ? 'border-purple-300' : 'border-blue-300' }} shadow relative z-10">
                                                        <span class="text-xs font-bold {{ $player->retention ? 'text-purple-800' : 'text-blue-800' }}">{{ strtoupper(substr($player->user?->name ?? 'P', 0, 1)) }}</span>
                                                    </div>
                                                @endif
                                                @if($player->retention)
                                                    <div class="absolute -top-1 -right-1 w-5 h-5 bg-purple-500 rounded-full flex items-center justify-center border-2 border-white z-20 shadow-sm">
                                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <p class="text-[10px] font-semibold text-gray-900 truncate px-1 leading-tight relative z-10">{{ $player->user?->name ?? 'Unknown' }}</p>
                                            @if($player->retention)
                                                <p class="text-[10px] font-bold text-purple-600 leading-tight relative z-10">Retained</p>
                                            @else
                                                <p class="text-[10px] font-bold text-blue-600 leading-tight break-words relative z-10">₹{{ number_format($value) }}</p>
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
                        <div class="grid grid-cols-3 gap-2 pt-4 border-t border-gray-100 team-stats rounded-lg p-3">
                            <div class="text-center">
                                <div class="text-base sm:text-lg font-bold text-gray-900 break-words leading-tight">₹{{ number_format($remainingWallet) }}</div>
                                <div class="text-[11px] text-gray-500">Remaining</div>
                            </div>
                            <div class="text-center">
                                <div class="text-base sm:text-lg font-bold text-gray-900 break-words leading-tight">₹{{ number_format($totalSpent) }}</div>
                                <div class="text-[11px] text-gray-500">Spent</div>
                            </div>
                            <div class="text-center">
                                <div class="text-base sm:text-lg font-bold text-purple-600 leading-tight">{{ $sortedPlayers->where('retention', true)->count() }}</div>
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
