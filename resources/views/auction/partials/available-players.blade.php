<!-- Available Players Section -->
<div class="lg:col-span-2 availPlayers">
    <div class="glass-card">
        <!-- Header Section -->
        <div class="px-4 py-4 sm:px-6 border-b border-gray-200/30">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Available Players</h2>
                </div>
                <div class="flex items-center space-x-4">
                                    <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                    {{ count($leaguePlayers) }} Players
                </div>
                <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium" id="visiblePlayersCount">
                    {{ min(3, count($leaguePlayers)) }} on this page
                </div>
                </div>
            </div>
        </div>

        <div class="p-4 sm:p-6">

            <!-- Search and Filter Bar -->
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" id="playerSearch" placeholder="Search players by name, mobile, or role..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent glass-input">
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <select id="roleFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent glass-input">
                            <option value="">All Roles</option>
                            <option value="Batsman">Batsman</option>
                            <option value="Bowler">Bowler</option>
                            <option value="All-rounder">All-rounder</option>
                            <option value="Wicket-keeper">Wicket-keeper</option>
                        </select>
                        <select id="priceFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent glass-input">
                            <option value="">All Prices</option>
                            <option value="0-100">₹0 - ₹100</option>
                            <option value="101-200">₹101 - ₹200</option>
                            <option value="201-500">₹201 - ₹500</option>
                            <option value="500+">₹500+</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Card View Design -->
            <div id="cardView" class="players-container" url="{{route('leagues.player.broadcast')}}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="playersList">
                    <!-- Static Dummy Players -->
                    <!-- Player 1 -->
                     @foreach($leaguePlayers as $index => $leaguePlayer)
                    <div class="glass-card p-4 hover:shadow-lg transition-all duration-300 player-card group {{ $index >= 3 ? 'hidden' : '' }}" data-player-index="{{ $index }}">

                        <!-- Player Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-full overflow-hidden bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center shadow-lg">
                                    @if($leaguePlayer->player->photo)
                                        <img src="{{ asset($leaguePlayer->player->photo) }}"
                                             alt="{{$leaguePlayer->player->name}}"
                                             class="w-full h-full object-cover"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    @else
                                        <img src="{{ asset('images/defaultplayer.jpeg') }}"
                                             alt="{{$leaguePlayer->player->name}}"
                                             class="w-full h-full object-cover"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 text-lg truncate">{{$leaguePlayer->player->name}}</h3>
                                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                                        @if($leaguePlayer->player->position)
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                                {{$leaguePlayer->player->position->name}}
                                            </span>
                                        @else
                                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs font-medium">
                                                No Position
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-green-600">₹{{$leaguePlayer->base_price}}</div>
                                <div class="text-xs text-gray-500">Base Price</div>
                            </div>
                        </div>

                        <!-- Player Details -->
                        <div class="grid grid-cols-1 gap-3 mb-4 text-sm">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span class="text-gray-600 truncate">{{ $leaguePlayer->player->mobile}}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2">
                            <button player-id="{{$leaguePlayer->user_id}}"
                                league-player-id="{{$leaguePlayer->id}}"
                                player-name="{{$leaguePlayer->player->name}}"
                                base-price="{{$leaguePlayer->base_price}}"
                                position="{{$leaguePlayer->player->position ? $leaguePlayer->player->position->name : 'No Position'}}"
                                league-id="{{ $league->id}}"
                                start-bid-action="{{ route('auction.start')}}"
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center startAuction justify-center group-hover:shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                <span class="hidden sm:inline">Start Bidding</span>
                                <span class="sm:hidden">Bid</span>
                            </button>
                            <button onclick="viewPlayerDetails('1')"
                                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>


