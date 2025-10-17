<div class="glacier-card">
    <div class="px-4 py-4 sm:px-6 border-b glacier-border">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold glacier-text-primary">Teams</h2>
            <div class="badge-blue px-3 py-1 rounded-full text-sm font-medium">
                {{ count($teams) }} {{ Str::plural('Team', count($teams)) }}
            </div>
        </div>
    </div>
    <div class="p-4 sm:p-6">
        <div class="space-y-4">
            @foreach ($teams as $team)
            <div class="border glacier-border rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                <!-- Team Header -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center font-bold text-lg backdrop-blur-sm">
                                {{ strtoupper(substr($team->team->name, 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">{{ $team->team->name }}</h3>
                                <p class="text-sm text-blue-100">{{ $team->league_players_count }} Players</p>
                                @if($team->teamAuctioneer && $team->teamAuctioneer->auctioneer)
                                    <p class="text-xs text-blue-200">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Auctioneer: {{ $team->teamAuctioneer->auctioneer->name }}
                                    </p>
                                @elseif($team->auctioneer)
                                    <p class="text-xs text-blue-200">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Auctioneer: {{ $team->auctioneer->name }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold">₹{{ number_format($team->wallet_balance) }}</p>
                            <p class="text-xs text-blue-100">Balance</p>
                        </div>
                    </div>
                </div>

                <!-- Players List -->
                @if($team->leaguePlayers && $team->leaguePlayers->count() > 0)
                <div class="p-3 bg-gray-50">
                    <div class="space-y-2">
                        @foreach($team->leaguePlayers as $index => $leaguePlayer)
                        <div class="flex items-center justify-between p-2 bg-white rounded border border-gray-200">
                            <div class="flex items-center space-x-2">
                                <!-- Serial Number -->
                                <div class="flex items-center justify-center w-6 h-6 bg-blue-600 text-white rounded-full text-xs font-bold flex-shrink-0">
                                    {{ $index + 1 }}
                                </div>
                                <!-- Player Avatar -->
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($leaguePlayer->player->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-sm text-gray-900">{{ $leaguePlayer->player->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        @if($leaguePlayer->player->primaryGameRole && $leaguePlayer->player->primaryGameRole->gamePosition)
                                            {{ $leaguePlayer->player->primaryGameRole->gamePosition->name }}
                                        @elseif($leaguePlayer->player->position)
                                            {{ $leaguePlayer->player->position->name }}
                                        @else
                                            Player
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                @if($leaguePlayer->status === 'retained')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        Retained
                                    </span>
                                @else
                                    <p class="text-sm font-bold text-green-600">₹{{ number_format($leaguePlayer->bid_price ?? 0) }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="p-4 text-center text-gray-500 text-sm">
                    No players yet
                </div>
                @endif

                <!-- Team Stats Footer -->
                <div class="bg-gray-100 px-4 py-2 flex items-center justify-between text-xs text-gray-600">
                    <span>Total Spent: <strong class="text-gray-900">₹{{ number_format($team->leaguePlayers->sum('bid_price') ?? 0) }}</strong></span>
                    <span>Avg: <strong class="text-gray-900">₹{{ $team->leaguePlayers->count() > 0 ? number_format($team->leaguePlayers->avg('bid_price') ?? 0) : 0 }}</strong></span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
