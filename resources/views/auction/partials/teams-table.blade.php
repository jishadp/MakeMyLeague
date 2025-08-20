<!-- Teams Table Section -->
<div class="px-4 py-4 sm:px-6 border-b glacier-border">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold glacier-text-primary">League Teams & Balances</h2>
        <div class="badge-primary px-3 py-1 rounded-full text-sm font-medium">
            {{ $leagueTeams->count() }} Teams
        </div>
    </div>
</div>
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="table-header">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium glacier-text-secondary uppercase tracking-wider">
                    Team
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium glacier-text-secondary uppercase tracking-wider">
                    Wallet Balance
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200/50" id="teamsTableBody">
            @foreach($leagueTeams as $leagueTeam)

            <tr class="table-row hover:bg-gray-50/70 transition-colors duration-200 team-row" 
                data-team-id="{{ $leagueTeam->id }}" 
                data-wallet="{{ $leagueTeam->wallet_balance }}">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-full w-10 h-10 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg">
                            {{ strtoupper(substr($leagueTeam->team->name, 0, 2)) }}
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium glacier-text-primary">{{ $leagueTeam->team->name }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-green-600" id="teamSpent_{{ $leagueTeam->id }}">
                        ₹{{ number_format($leagueTeam->players()->where('status', 'sold')->sum('bid_price')) }}
                    </div>
                    <div class="text-sm text-red-500">10300</div>
                </td>
            </tr>

            
            @endforeach
        </tbody>
    </table>
</div>

@if(auth()->user()->isOrganizer())
<!-- Summary Row -->
<div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div>
            <div class="text-gray-500">Total Teams</div>
            <div class="font-medium text-gray-900">{{ $leagueTeams->count() }}</div>
        </div>
        <div>
            <div class="text-gray-500">Total Wallet</div>
            <div class="font-medium text-green-600">₹{{ number_format($leagueTeams->sum('wallet_balance')) }}</div>
        </div>
        <div>
            <div class="text-gray-500">Total Spent</div>
            <div class="font-medium text-red-600">
                ₹{{ number_format($leagueTeams->sum(function($team) { 
                    return $team->players()->where('status', 'sold')->sum('bid_price'); 
                })) }}
            </div>
        </div>
        <div>
            <div class="text-gray-500">Avg Balance</div>
            <div class="font-medium text-blue-600">
                ₹{{ number_format($leagueTeams->avg('wallet_balance')) }}
            </div>
        </div>
    </div>
</div>
@endif`