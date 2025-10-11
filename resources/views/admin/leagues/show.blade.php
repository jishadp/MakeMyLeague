@extends('layouts.app')

@section('title', 'League Details - Admin')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('admin.leagues.index') }}" class="inline-flex items-center text-purple-600 hover:text-purple-700 font-medium mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Leagues
            </a>
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-black text-gray-900">League Details</h1>
                <a href="{{ route('admin.leagues.edit', $league) }}" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold shadow-lg hover:shadow-xl transition-all">
                    Edit League
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm font-medium text-gray-600">League Name</div>
                            <div class="text-lg font-bold text-gray-900">{{ $league->name }}</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-600">Game</div>
                            <div class="text-lg font-bold text-gray-900">{{ $league->game->name }}</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-600">Season</div>
                            <div class="text-lg font-bold text-gray-900">{{ $league->season }}</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-600">Status</div>
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-bold
                                {{ $league->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $league->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $league->status === 'completed' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $league->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($league->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Dates -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Important Dates</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm font-medium text-gray-600">League Period</div>
                            <div class="text-gray-900">{{ \Carbon\Carbon::parse($league->start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($league->end_date)->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Venue Details -->
                @if($league->venue_details)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Venue Details</h2>
                    <div class="text-gray-900">{{ $league->venue_details }}</div>
                </div>
                @endif

                <!-- Teams -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Registered Teams ({{ $league->leagueTeams->count() }}/{{ $league->max_teams }})</h2>
                    @if($league->leagueTeams->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($league->leagueTeams as $leagueTeam)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        @if($leagueTeam->team->logo)
                                            <img src="{{ Storage::url($leagueTeam->team->logo) }}" alt="{{ $leagueTeam->team->name }}" class="w-12 h-12 rounded-lg object-cover mr-3">
                                        @else
                                            <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                                                <span class="text-blue-600 font-bold">{{ substr($leagueTeam->team->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $leagueTeam->team->name }}</div>
                                            <div class="text-sm text-gray-600">{{ $leagueTeam->leaguePlayers->count() }} players</div>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-600">Wallet: ₹{{ number_format($leagueTeam->wallet_balance, 0) }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No teams registered yet.</p>
                    @endif
                </div>

                <!-- Players -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Registered Players ({{ $league->leaguePlayers->count() }})</h2>
                    @if($league->leaguePlayers->isNotEmpty())
                        <div class="space-y-2">
                            @foreach($league->leaguePlayers->take(10) as $player)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                            <span class="text-purple-600 font-bold">{{ substr($player->user->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $player->user->name }}</div>
                                            <div class="text-xs text-gray-600">{{ $player->status }}</div>
                                        </div>
                                    </div>
                                    @if($player->bid_price)
                                        <div class="text-sm font-bold text-green-600">₹{{ number_format($player->bid_price, 0) }}</div>
                                    @endif
                                </div>
                            @endforeach
                            @if($league->leaguePlayers->count() > 10)
                                <p class="text-sm text-gray-500 text-center mt-3">And {{ $league->leaguePlayers->count() - 10 }} more...</p>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500">No players registered yet.</p>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Stats -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Statistics</h2>
                    <div class="space-y-4">
                        <div>
                            <div class="text-sm font-medium text-gray-600">Teams</div>
                            <div class="text-2xl font-black text-gray-900">{{ $league->leagueTeams->count() }}/{{ $league->max_teams }}</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-600">Players</div>
                            <div class="text-2xl font-black text-gray-900">{{ $league->leaguePlayers->count() }}</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-600">Organizers</div>
                            <div class="text-2xl font-black text-gray-900">{{ $league->organizers->count() }}</div>
                        </div>
                        @if($league->fixtures)
                        <div>
                            <div class="text-sm font-medium text-gray-600">Matches</div>
                            <div class="text-2xl font-black text-gray-900">{{ $league->fixtures->count() }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Location -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Location</h2>
                    <div class="space-y-2">
                        <div>
                            <div class="text-sm font-medium text-gray-600">State</div>
                            <div class="text-gray-900">{{ $league->localBody->district->state->name }}</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-600">District</div>
                            <div class="text-gray-900">{{ $league->localBody->district->name }}</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-600">Local Body</div>
                            <div class="text-gray-900">{{ $league->localBody->name }}</div>
                        </div>
                    </div>
                </div>

                <!-- Prizes & Fees -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Prizes & Fees</h2>
                    <div class="space-y-3">
                        @if($league->winner_prize)
                        <div class="flex justify-between">
                            <div class="text-sm font-medium text-gray-600">Winner Prize</div>
                            <div class="font-bold text-green-600">₹{{ number_format($league->winner_prize, 0) }}</div>
                        </div>
                        @endif
                        @if($league->runner_prize)
                        <div class="flex justify-between">
                            <div class="text-sm font-medium text-gray-600">Runner-up Prize</div>
                            <div class="font-bold text-green-600">₹{{ number_format($league->runner_prize, 0) }}</div>
                        </div>
                        @endif
                        @if($league->team_reg_fee)
                        <div class="flex justify-between">
                            <div class="text-sm font-medium text-gray-600">Team Reg Fee</div>
                            <div class="font-bold text-blue-600">₹{{ number_format($league->team_reg_fee, 0) }}</div>
                        </div>
                        @endif
                        @if($league->player_reg_fee)
                        <div class="flex justify-between">
                            <div class="text-sm font-medium text-gray-600">Player Reg Fee</div>
                            <div class="font-bold text-blue-600">₹{{ number_format($league->player_reg_fee, 0) }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Configuration -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Configuration</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <div class="text-sm font-medium text-gray-600">Max Players/Team</div>
                            <div class="font-bold text-gray-900">{{ $league->max_team_players }}</div>
                        </div>
                        @if($league->team_wallet_limit)
                        <div class="flex justify-between">
                            <div class="text-sm font-medium text-gray-600">Team Wallet</div>
                            <div class="font-bold text-gray-900">₹{{ number_format($league->team_wallet_limit, 0) }}</div>
                        </div>
                        @endif
                        @if($league->retention)
                        <div class="flex justify-between">
                            <div class="text-sm font-medium text-gray-600">Retention Players</div>
                            <div class="font-bold text-gray-900">{{ $league->retention_players }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Images -->
                @if($league->logo || $league->banner)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Images</h2>
                    @if($league->logo)
                        <div class="mb-4">
                            <div class="text-sm font-medium text-gray-600 mb-2">Logo</div>
                            <img src="{{ Storage::url($league->logo) }}" alt="Logo" class="w-full rounded-lg">
                        </div>
                    @endif
                    @if($league->banner)
                        <div>
                            <div class="text-sm font-medium text-gray-600 mb-2">Banner</div>
                            <img src="{{ Storage::url($league->banner) }}" alt="Banner" class="w-full rounded-lg">
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

