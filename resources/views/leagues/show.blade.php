@extends('layouts.app')

@section('title', $league->name . ' - Cricket League Manager')

@section('content')
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('leagues.index') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold flex items-center gap-1">← Back to Leagues</a>
        </div>

        <!-- League Header -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-8 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight mb-2">{{ $league->name }}</h1>
                    <div class="flex items-center space-x-4">
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            @if($league->status === 'Active') bg-green-100 text-green-800
                            @elseif($league->status === 'Completed') bg-gray-100 text-gray-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ $league->status }}
                        </span>
                        <span class="text-gray-600">{{ $league->start_date->format('M d, Y') }} - {{ $league->end_date->format('M d, Y') }}</span>
                    </div>
                </div>
                <div class="flex space-x-3 mt-4 sm:mt-0">
                    <a href="{{ route('leagues.edit', $league) }}" class="bg-yellow-500 text-white px-6 py-3 rounded-lg hover:bg-yellow-600 shadow-md hover:shadow-lg transition-all font-medium">
                        Edit League
                    </a>
                    @if($league->auction_started)
                    <a href="{{ route('auction.public', $league) }}" class="bg-purple-500 text-white px-6 py-3 rounded-lg hover:bg-purple-600 shadow-md hover:shadow-lg transition-all font-medium">
                        View Auction
                    </a>
                    <a href="{{ route('auction.show', $league) }}" class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 shadow-md hover:shadow-lg transition-all font-medium">
                        Manage Auction
                    </a>
                    @else
                    <a href="{{ route('auction.setup', $league) }}" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 shadow-md hover:shadow-lg transition-all font-medium">
                        Setup Auction
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Add Teams/Players Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Add Teams -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Add Teams</h3>
                @if($availableTeams->count() > 0)
                    <form action="{{ route('league-teams.store', $league) }}" method="POST">
                        @csrf
                        <a href="{{ route('league-teams.create', $league) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-all font-medium w-full text-center block">
                            Add Teams to League
                        </a>
                    </form>
                @else
                    <p class="text-gray-600">All teams have been added to this league.</p>
                @endif
            </div>

            <!-- Add Players -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Add Players</h3>
                @if($availablePlayers->count() > 0)
                    <form action="{{ route('leagues.add-players', $league) }}" method="POST">
                        @csrf
                        <div class="space-y-2 mb-4 max-h-48 overflow-y-auto">
                            @foreach($availablePlayers as $player)
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg">
                                    <input type="checkbox" name="player_ids[]" value="{{ $player->id }}" class="mr-3">
                                    <span class="font-medium">{{ $player->name }}</span>
                                    <span class="ml-auto text-sm text-gray-500">{{ $player->position }}</span>
                                </label>
                            @endforeach
                        </div>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-all font-medium w-full">
                            Add Selected Players
                        </button>
                    </form>
                @else
                    <p class="text-gray-600">All players have been added to this league.</p>
                @endif
            </div>
        </div>

        <!-- League Teams -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-900">Teams in League</h2>
                    <div class="flex gap-2">
                        <a href="{{ route('league-teams.create', $league) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all">
                            Add Teams
                        </a>
                        @if($league->leagueTeams->count() > 0)
                            <a href="{{ route('auction.show', $league) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 shadow-md hover:shadow-lg transition-all">
                                Auction
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="p-8">
                @forelse($league->leagueTeams as $team)
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg mb-4 last:mb-0">
                        <div class="flex items-center space-x-4">
                            @if($team->logo_url)
                                <img src="{{ $team->logo_url }}" alt="{{ $team->name }}" class="w-12 h-12 rounded-full object-cover">
                            @else
                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">{{ substr($team->pivot->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $team->pivot->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $team->name }} ({{ $team->country }})</p>
                                @if($league->auction_started)
                                    <p class="text-sm text-green-600">Purse: ₹{{ number_format($team->pivot->purse_balance) }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Active</span>
                            <form action="{{ route('league-teams.destroy', [$league, $team->id]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Remove this team from league?')" class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No teams added</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by adding teams to your league.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- League Players -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">League Players ({{ $league->players->count() }})</h3>
            @if($league->players->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($league->players as $player)
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 hover:shadow-md transition-all">
                            <h4 class="font-bold text-gray-900 mb-1">{{ $player->name }}</h4>
                            <p class="text-sm text-gray-600 mb-2">{{ $player->position }} • {{ $player->age }} years</p>
                            <p class="text-xs text-gray-500 mb-2">Team: {{ $player->team?->name ?? 'No team' }}</p>
                            <a href="{{ route('players.show', $player) }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                                View Player →
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600">No players added to this league yet.</p>
            @endif
        </div>

        <!-- League Auction Status -->
        @if($league->auction_started)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6 mt-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                <h3 class="text-2xl font-bold text-gray-900">Auction Status</h3>
                <div class="flex space-x-3 mt-2 sm:mt-0">
                    <a href="{{ route('auction.public', $league) }}" class="text-indigo-600 hover:text-indigo-700 font-semibold flex items-center gap-1">
                        View Public Auction <span class="text-lg">→</span>
                    </a>
                    <a href="{{ route('auction.show', $league) }}" class="text-green-600 hover:text-green-700 font-semibold flex items-center gap-1">
                        Manage Auction <span class="text-lg">→</span>
                    </a>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                    <h4 class="font-semibold text-gray-700 mb-1">Sold Players</h4>
                    <p class="text-2xl font-bold text-green-600">
                        {{ $league->leaguePlayers()->wherePivot('auction_status', 'sold')->count() }}
                    </p>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                    <h4 class="font-semibold text-gray-700 mb-1">Unsold Players</h4>
                    <p class="text-2xl font-bold text-red-600">
                        {{ $league->leaguePlayers()->wherePivot('auction_status', 'unsold')->count() }}
                    </p>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <h4 class="font-semibold text-gray-700 mb-1">Skipped Players</h4>
                    <p class="text-2xl font-bold text-yellow-600">
                        {{ $league->leaguePlayers()->wherePivot('auction_status', 'skip')->count() }}
                    </p>
                </div>
            </div>
            
            <h4 class="font-bold text-gray-900 mb-3">Recent Auction Activities</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-2 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Player</th>
                            <th class="py-2 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Team</th>
                            <th class="py-2 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="py-2 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bid Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($league->leaguePlayers()->orderBy('league_player.updated_at', 'desc')->take(5)->get() as $player)
                        <tr>
                            <td class="py-2 px-4 text-sm text-gray-900">{{ $player->name }}</td>
                            <td class="py-2 px-4 text-sm text-gray-900">
                                @if($player->pivot->league_team_id)
                                    {{ DB::table('league_team')->join('teams', 'league_team.team_id', '=', 'teams.id')->where('league_team.id', $player->pivot->league_team_id)->value('teams.name') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-2 px-4 text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($player->pivot->auction_status === 'sold') bg-green-100 text-green-800
                                    @elseif($player->pivot->auction_status === 'unsold') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($player->pivot->auction_status) }}
                                </span>
                            </td>
                            <td class="py-2 px-4 text-sm text-gray-900">
                                {{ $player->pivot->bid_amount ? '₹' . number_format($player->pivot->bid_amount) : '-' }}
                            </td>
                        </tr>
                        @endforeach
                        
                        @if($league->leaguePlayers()->count() === 0)
                        <tr>
                            <td colspan="4" class="py-4 px-4 text-sm text-gray-500 text-center">
                                No auction activities yet
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
@endsection