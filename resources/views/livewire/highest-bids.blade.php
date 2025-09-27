<div class="glacier-card"  wire:poll.5s>
                <div class="px-4 py-4 sm:px-6 border-b glacier-border">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold glacier-text-primary">Highest Bids</h2>
                        <div class="badge-purple px-3 py-1 rounded-full text-sm font-medium">
                            105 Sold
                        </div>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <div id="highestBidsTable" class="overflow-x-auto" >
                        <table class="w-full">
                            <thead class="table-header">
                                <tr>
                                    <th class="text-left py-3 px-4 font-medium glacier-text-secondary">Player</th>
                                    <th class="text-left py-3 px-4 font-medium glacier-text-secondary">Team</th>
                                    <th class="text-left py-3 px-4 font-medium glacier-text-secondary">Sold Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bids as $bid)
                                <tr class="table-row border-b border-gray-100">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-full overflow-hidden bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center shadow-lg">
                                                @if($bid->leaguePlayer->player->photo)
                                                    <img src="{{ asset($bid->leaguePlayer->player->photo) }}"
                                                         alt="{{ $bid->leaguePlayer->player->name }}"
                                                         class="w-full h-full object-cover">
                                                @else
                                                    <img src="{{ asset('images/defaultplayer.jpeg') }}"
                                                         alt="{{ $bid->leaguePlayer->player->name }}"
                                                         class="w-full h-full object-cover">
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-medium glacier-text-primary text-sm">{{ $bid->leaguePlayer->player->name}}</p>
                                                <p class="text-xs text-gray-500">{{ $bid->leaguePlayer->player->position->name}}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="text-sm glacier-text-primary">{{ $bid->leagueTeam->team->name}}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="font-bold text-purple-600">₹ {{$bid->amount}}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
