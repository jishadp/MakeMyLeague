@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('teams.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Teams
            </a>
        </div>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">League Teams</h1>
            <p class="text-gray-600 mt-2">Browse teams organized by leagues</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar -->
            <div class="lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-lg shadow sticky top-8">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="font-bold text-gray-900">All Leagues</h3>
                        <p class="text-sm text-gray-600">{{ $allLeagues->count() }} leagues</p>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        @foreach($allLeagues as $sidebarLeague)
                            <a href="#league-{{ $sidebarLeague->id }}" class="block p-4 hover:bg-gray-50 border-b border-gray-100 transition-colors">
                                <div class="flex items-center">
                                    @if($sidebarLeague->logo)
                                        <img src="{{ Storage::url($sidebarLeague->logo) }}" class="w-8 h-8 rounded-full object-cover mr-3">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                            <span class="text-xs font-bold text-indigo-600">{{ substr($sidebarLeague->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $sidebarLeague->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $sidebarLeague->league_teams_count }} teams</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1">

        @forelse($leagues as $league)
            <div id="league-{{ $league->id }}" class="bg-white rounded-lg shadow mb-8 scroll-mt-8">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center">
                            @if($league->logo)
                                <img src="{{ Storage::url($league->logo) }}" class="w-12 h-12 rounded-full object-cover mr-4">
                            @endif
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">{{ $league->name }}</h2>
                                <p class="text-gray-600">{{ $league->game->name }} • Season {{ $league->season }}</p>
                            </div>
                        </div>
                        <a href="{{ route('leagues.public-teams', $league) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                            View All \u2192
                        </a>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($league->leagueTeams->sortByDesc('created_at')->take(6) as $leagueTeam)
                            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-xl transition-all duration-300">
                                <!-- Team Header -->
                                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            @if($leagueTeam->team->logo)
                                                <img src="{{ Storage::url($leagueTeam->team->logo) }}" class="w-12 h-12 rounded-full object-cover border-2 border-white mr-3">
                                            @else
                                                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center mr-3 border-2 border-white">
                                                    <span class="text-xl font-bold text-white">{{ substr($leagueTeam->team->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <h3 class="font-bold text-white text-lg">{{ $leagueTeam->team->name }}</h3>
                                                <p class="text-sm text-white/80">{{ $leagueTeam->team->owners->first()->name ?? 'No owner' }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-white">{{ $leagueTeam->leaguePlayers->where('status', 'sold')->count() }}</div>
                                            <div class="text-xs text-white/80">Players</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Retention Players -->
                                @php
                                    $retentionPlayers = $leagueTeam->leaguePlayers->where('retention', true)->where('status', 'sold')->take(3);
                                @endphp
                                @if($retentionPlayers->count() > 0)
                                    <div class="bg-yellow-50 border-b border-yellow-100 p-3">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-4 h-4 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <span class="text-xs font-semibold text-yellow-800 uppercase">Retention Players</span>
                                        </div>
                                        <div class="grid grid-cols-3 gap-2">
                                            @foreach($retentionPlayers as $player)
                                                <div class="bg-white rounded-lg p-2 text-center">
                                                    @if($player->user->photo)
                                                        <img src="{{ Storage::url($player->user->photo) }}" class="w-12 h-12 rounded-full object-cover mx-auto mb-1 border-2 border-yellow-400">
                                                    @else
                                                        <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center mx-auto mb-1 border-2 border-yellow-400">
                                                            <span class="text-sm font-bold text-yellow-600">{{ substr($player->user->name, 0, 1) }}</span>
                                                        </div>
                                                    @endif
                                                    <p class="text-xs font-medium text-gray-900 truncate">{{ $player->user->name }}</p>
                                                    <p class="text-xs text-yellow-600 font-semibold">₹{{ number_format($player->bid_price/1000, 0) }}K</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Regular Players -->
                                @php
                                    $regularPlayers = $leagueTeam->leaguePlayers->where('retention', false)->where('status', 'sold')->take(6);
                                @endphp
                                @if($regularPlayers->count() > 0)
                                    <div class="p-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-semibold text-gray-700 uppercase">Squad</span>
                                            <span class="text-xs text-gray-500">{{ $leagueTeam->leaguePlayers->where('status', 'sold')->count() }} players</span>
                                        </div>
                                        <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
                                            @foreach($regularPlayers as $player)
                                                <div class="text-center">
                                                    @if($player->user->photo)
                                                        <img src="{{ Storage::url($player->user->photo) }}" class="w-10 h-10 rounded-full object-cover mx-auto mb-1 border border-gray-200">
                                                    @else
                                                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-1 border border-gray-200">
                                                            <span class="text-xs font-bold text-gray-600">{{ substr($player->user->name, 0, 1) }}</span>
                                                        </div>
                                                    @endif
                                                    <p class="text-xs text-gray-900 truncate">{{ explode(' ', $player->user->name)[0] }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Team Stats Footer -->
                                <div class="bg-gray-50 border-t border-gray-200 p-3">
                                    <div class="grid grid-cols-3 gap-2 text-center">
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">₹{{ number_format($leagueTeam->wallet_balance/1000, 0) }}K</div>
                                            <div class="text-xs text-gray-600">Remaining</div>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">₹{{ number_format(($league->team_wallet_limit - $leagueTeam->wallet_balance)/1000, 0) }}K</div>
                                            <div class="text-xs text-gray-600">Spent</div>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">{{ $leagueTeam->leaguePlayers->where('retention', true)->count() }}</div>
                                            <div class="text-xs text-gray-600">Retained</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($league->leagueTeams->count() > 6)
                        <div class="mt-4 text-center">
                            <a href="{{ route('leagues.public-teams', $league) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                View all {{ $league->leagueTeams->count() }} teams \u2192
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-gray-500">No league teams found</p>
            </div>
        @endforelse

                <div class="mt-8">
                    {{ $leagues->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
html {
    scroll-behavior: smooth;
}
</style>
@endsection
