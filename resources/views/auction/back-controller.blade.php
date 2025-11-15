@extends('layouts.app')

@section('title', 'Auction Control Room - ' . $league->name)

@section('styles')
<style>
    .control-surface {
        background: radial-gradient(circle at top, #0f172a, #020617 60%);
    }
    .control-card {
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
    .control-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 1rem;
    }
</style>
@endsection

@section('content')
@php
    $currentBidAmount = $currentHighestBid->amount ?? ($currentPlayer->base_price ?? 0);
@endphp
<div class="min-h-screen control-surface py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <input type="hidden" id="controller-league-id" value="{{ $league->id }}">
        <input type="hidden" id="controller-league-slug" value="{{ $league->slug }}">
        <input type="hidden" id="controller-player-id" value="{{ $currentPlayer->player->id ?? '' }}">
        <input type="hidden" id="controller-league-player-id" value="{{ $currentPlayer->id ?? '' }}">
        <input type="hidden" id="controller-base-price" value="{{ $currentBidAmount }}">
        <input type="hidden" id="controller-default-team" value="{{ $currentHighestBid?->league_team_id ?? '' }}">
        <input type="hidden" id="controller-bid-increments" value='@json($bidIncrements)'>
        <input type="hidden" id="controller-bid-action" value="{{ route('auction.call') }}">
        <input type="hidden" id="controller-sold-action" value="{{ route('auction.sold') }}">
        <input type="hidden" id="controller-unsold-action" value="{{ route('auction.unsold') }}">

        <!-- Header -->
        <div class="bg-white/90 control-card rounded-3xl shadow-2xl border border-white/40">
            <div class="p-6 sm:p-8">
                <div class="flex flex-col lg:flex-row justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $league->isAuctionActive() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $league->isAuctionActive() ? 'Live Auction' : 'Standby Mode' }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                                Season {{ $league->season }}
                            </span>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-bold text-slate-900 mb-2">{{ $league->name }}</h1>
                        <p class="text-slate-500 text-sm sm:text-base">
                            {{ $league->game->name ?? 'Game TBA' }} · {{ $league->league_teams_count }} Teams
                            @if($league->localBody)
                                · {{ $league->localBody->name }}, {{ $league->localBody->district->name ?? '' }}
                            @endif
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                        <a href="{{ route('auction.index', $league) }}" class="inline-flex items-center px-5 py-3 rounded-2xl bg-indigo-600 text-white font-semibold shadow-lg hover:bg-indigo-700 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5-5 5M6 12h12"></path>
                            </svg>
                            Open Auction Page
                        </a>
                        <a href="{{ route('leagues.show', $league) }}" class="inline-flex items-center px-5 py-3 rounded-2xl border border-slate-200 text-slate-700 font-semibold hover:bg-white">
                            League Overview
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Current Player -->
                <div class="bg-white/90 control-card rounded-3xl shadow-2xl border border-white/40">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <p class="text-sm font-semibold text-slate-500 uppercase tracking-wide">Current Player</p>
                                <h2 class="text-2xl font-bold text-slate-900">{{ $currentPlayer?->player?->name ?? 'Awaiting selection' }}</h2>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-slate-500">Current Bid</p>
                                <p id="controller-current-bid-label" class="text-3xl font-bold text-emerald-600">₹{{ number_format($currentBidAmount) }}</p>
                            </div>
                        </div>
                        @if($currentPlayer)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                                <div class="flex items-center gap-4">
                                    <div class="w-20 h-20 rounded-3xl overflow-hidden border border-white shadow-lg">
                                        <img src="{{ $currentPlayer->player?->photo ? asset($currentPlayer->player->photo) : asset('images/defaultplayer.jpeg') }}" alt="{{ $currentPlayer->player?->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <p class="text-sm text-slate-500">Role</p>
                                        <p class="font-semibold text-slate-900">{{ $currentPlayer->player?->primaryGameRole?->gamePosition?->name ?? $currentPlayer->player?->position?->name ?? '—' }}</p>
                                        <p class="text-sm text-slate-500">Base ₹{{ number_format($currentPlayer->base_price ?? 0) }}</p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500">Status</p>
                                    <p class="font-semibold text-amber-600 capitalize">{{ $currentPlayer->status }}</p>
                                    <p class="text-sm text-slate-500">Updated {{ optional($currentPlayer->updated_at)->diffForHumans(null, true) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500">Highest Bid Team</p>
                                    <p class="font-semibold text-slate-900">{{ $currentHighestBid?->leagueTeam?->team?->name ?? 'No bids yet' }}</p>
                                    @if($currentHighestBid)
                                        <p class="text-sm text-slate-500">₹{{ number_format($currentHighestBid->amount) }}</p>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-center py-12 text-slate-500">
                                <p>No player is currently on the block. Start an auction to enable the controller.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Bidding Console -->
                <div class="bg-white/90 control-card rounded-3xl shadow-2xl border border-white/40">
                    <div class="p-6 sm:p-8 space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-slate-900">Bidding Console</h3>
                            <span class="text-sm text-slate-500">Place bids for any team in this league</span>
                        </div>
                        <div id="controller-feedback" class="hidden px-4 py-2 rounded-2xl text-sm font-medium"></div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-semibold text-slate-600">Select Team</label>
                                <select id="controller-team" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" {{ $currentPlayer ? '' : 'disabled' }}>
                                    <option value="">Choose team</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" {{ $currentHighestBid?->league_team_id === $team->id ? 'selected' : '' }}>
                                            {{ $team->team?->name ?? 'Team #' . $team->id }} · Wallet ₹{{ number_format($team->wallet_balance ?? 0) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-600">Custom Bid Amount</label>
                                <div class="mt-2 flex items-center gap-3">
                                    <div class="flex-1">
                                        <input id="controller-custom-amount" type="number" min="0" class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Enter amount" value="{{ $currentBidAmount > 0 ? $currentBidAmount + ($bidIncrements[0] ?? 0) : '' }}">
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-slate-500">Target Bid</p>
                                        <p id="controller-bid-preview" class="text-lg font-bold text-slate-900">₹{{ number_format($currentBidAmount) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-600 mb-3">Quick increments</p>
                            <div class="flex flex-wrap gap-3">
                                @foreach($bidIncrements as $increment)
                                    <button type="button" class="px-4 py-2 rounded-2xl border border-slate-200 text-slate-700 hover:border-indigo-500 hover:text-indigo-600" data-ctrl-increment="{{ $increment }}">
                                        +₹{{ number_format($increment) }}
                                    </button>
                                @endforeach
                                <button type="button" class="px-4 py-2 rounded-2xl border border-dashed border-slate-300 text-slate-500" data-ctrl-reset="true">Reset</button>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-4">
                            <button type="button" onclick="placeControllerBid(this)" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold shadow-lg hover:opacity-90 transition {{ $currentPlayer ? '' : 'opacity-50 cursor-not-allowed' }}" {{ $currentPlayer ? '' : 'disabled' }}>
                                Trigger Bid
                            </button>
                            <p class="text-sm text-slate-500">Bids will instantly reflect on the public auction page.</p>
                        </div>
                    </div>
                </div>

                <!-- Finalize Player -->
                <div class="bg-white/90 control-card rounded-3xl shadow-2xl border border-white/40">
                    <div class="p-6 sm:p-8 space-y-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-semibold text-slate-900">Finalize Player</h3>
                                <p class="text-sm text-slate-500">Lock sold amounts or mark the player unsold.</p>
                            </div>
                            @if($currentHighestBid)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">Highest: {{ $currentHighestBid->leagueTeam?->team?->name }}</span>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-semibold text-slate-600">Winning Team</label>
                                <p class="text-xs text-slate-500">Select the team before marking sold.</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-600">Override Amount (optional)</label>
                                <input id="controller-override-amount" type="number" min="0" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" placeholder="₹" {{ $currentPlayer ? '' : 'disabled' }}>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <button type="button" onclick="markControllerSold(this)" class="px-5 py-3 rounded-2xl bg-emerald-600 text-white font-semibold shadow-lg hover:bg-emerald-700 transition {{ $currentPlayer ? '' : 'opacity-50 cursor-not-allowed' }}" {{ $currentPlayer ? '' : 'disabled' }}>
                                Mark Sold
                            </button>
                            <button type="button" onclick="markControllerUnsold(this)" class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-800 font-semibold hover:bg-slate-200 transition {{ $currentPlayer ? '' : 'opacity-50 cursor-not-allowed' }}" {{ $currentPlayer ? '' : 'disabled' }}>
                                Mark Unsold
                            </button>
                        </div>
                        <p class="text-xs text-slate-500">Sold/unsold updates will broadcast instantly to all viewers.</p>
                    </div>
                </div>

                <!-- Upcoming Players -->
                <div class="bg-white/90 control-card rounded-3xl shadow-2xl border border-white/40">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-semibold text-slate-900">Upcoming Players</h3>
                            <span class="text-sm text-slate-500">Latest registrations</span>
                        </div>
                        @if($availablePlayers->isEmpty())
                            <p class="text-slate-500 text-sm">No available players waiting in the pool.</p>
                        @else
                            <div class="divide-y divide-slate-100">
                                @foreach($availablePlayers as $player)
                                    <div class="py-4 flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ $player->player?->name ?? 'Player #' . $player->id }}</p>
                                            <p class="text-sm text-slate-500">{{ $player->player?->primaryGameRole?->gamePosition?->name ?? $player->player?->position?->name ?? 'Role TBA' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-slate-500">Base Price</p>
                                            <p class="text-lg font-semibold text-slate-900">₹{{ number_format($player->base_price ?? 0) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Stats Sidebar -->
            <div class="space-y-6">
                <div class="bg-white/90 control-card rounded-3xl shadow-2xl border border-white/40 p-6 space-y-6">
                    <div>
                        <p class="text-sm text-slate-500">Auction Progress</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-3xl font-bold text-slate-900">{{ $progressPercentage }}%</span>
                            <span class="text-sm text-slate-500">{{ $auctionStats['sold_players'] }} / {{ $auctionStats['total_players'] }} sold</span>
                        </div>
                        <div class="mt-4 h-3 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-emerald-400 to-blue-500" style="width: {{ $progressPercentage }}%"></div>
                        </div>
                    </div>
                    <div class="control-grid">
                        <div class="p-4 rounded-2xl bg-slate-50">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Available</p>
                            <p class="text-2xl font-semibold text-slate-900">{{ $auctionStats['available_players'] }}</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Sold</p>
                            <p class="text-2xl font-semibold text-emerald-600">{{ $auctionStats['sold_players'] }}</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Unsold</p>
                            <p class="text-2xl font-semibold text-amber-600">{{ $auctionStats['unsold_players'] }}</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Wallet Used</p>
                            <p class="text-xl font-semibold text-slate-900">₹{{ number_format($auctionStats['wallet_spent']) }}</p>
                        </div>
                    </div>
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 text-white">
                        <p class="text-sm text-white/70">Total Remaining Wallet</p>
                        <p class="text-3xl font-bold">₹{{ number_format($auctionStats['wallet_remaining']) }}</p>
                    </div>
                </div>

                <div class="bg-white/90 control-card rounded-3xl shadow-2xl border border-white/40 p-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Quick Notes</h3>
                    <ul class="space-y-3 text-sm text-slate-600">
                        <li class="flex items-start gap-3">
                            <span class="w-2 h-2 mt-2 rounded-full bg-indigo-500"></span>
                            Live bids placed here instantly sync with the live auction UI.
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-2 h-2 mt-2 rounded-full bg-indigo-500"></span>
                            Use quick increments to keep the flow consistent.
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-2 h-2 mt-2 rounded-full bg-indigo-500"></span>
                            Keep an eye on wallet balances before locking a winner.
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Live Feed -->
            <div class="lg:col-span-2 bg-white/90 control-card rounded-3xl shadow-2xl border border-white/40">
                <div class="p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-slate-900">Live Bid Feed</h3>
                        <span class="text-sm text-slate-500">Last {{ $recentBids->count() }} bids</span>
                    </div>
                    @if($recentBids->isEmpty())
                        <p class="text-slate-500 text-sm">No bids recorded yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left text-sm">
                                <thead class="text-xs uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th class="py-2 pr-4">Time</th>
                                        <th class="py-2 pr-4">Team</th>
                                        <th class="py-2 pr-4">Player</th>
                                        <th class="py-2 pr-4 text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($recentBids as $bid)
                                        <tr>
                                            <td class="py-3 pr-4 text-slate-500">{{ $bid->created_at->format('H:i:s') }}</td>
                                            <td class="py-3 pr-4 font-semibold text-slate-900">{{ $bid->leagueTeam?->team?->name ?? 'Team #' . $bid->league_team_id }}</td>
                                            <td class="py-3 pr-4 text-slate-600">{{ $bid->leaguePlayer?->player?->name ?? 'Player #' . $bid->league_player_id }}</td>
                                            <td class="py-3 pr-4 text-right font-semibold text-slate-900">₹{{ number_format($bid->amount ?? 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Team Wallets -->
            <div class="bg-white/90 control-card rounded-3xl shadow-2xl border border-white/40 p-6">
                <h3 class="text-xl font-semibold text-slate-900 mb-4">Team Wallets</h3>
                <div class="space-y-4 max-h-[32rem] overflow-y-auto pr-2">
                    @forelse($teams as $team)
                        @php
                            $balance = $team->wallet_balance ?? 0;
                            $spent = $team->spent_amount ?? 0;
                        @endphp
                        <div class="p-4 rounded-2xl border border-slate-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $team->team?->name ?? 'Team #' . $team->id }}</p>
                                    <p class="text-xs text-slate-500">{{ $team->sold_players_count }} sold · {{ $team->total_players_count }} roster</p>
                                </div>
                                <span class="text-xs px-3 py-1 rounded-full bg-slate-100 text-slate-600">Wallet ₹{{ number_format($balance) }}</span>
                            </div>
                            <div class="mt-3 text-sm text-slate-500">
                                <p>Spent: <span class="font-semibold text-slate-900">₹{{ number_format($spent) }}</span></p>
                                <p>Auctioneer: {{ $team->teamAuctioneer?->auctioneer?->name ?? '—' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-500 text-sm">No teams registered in this league.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const controllerBidInput = document.getElementById('controller-custom-amount');
    const controllerBaseInput = document.getElementById('controller-base-price');
    const controllerPreview = document.getElementById('controller-bid-preview');
    const controllerTeamSelect = document.getElementById('controller-team');
    const controllerDefaultTeam = document.getElementById('controller-default-team');

    function formatCurrency(value) {
        const amount = Number(value) || 0;
        return '₹' + amount.toLocaleString('en-IN');
    }

    function updatePreview(value) {
        if (controllerPreview) {
            controllerPreview.textContent = formatCurrency(value);
        }
    }

    function resetBidInput() {
        if (!controllerBidInput || !controllerBaseInput) {
            return;
        }
        controllerBidInput.value = '';
        updatePreview(controllerBaseInput.value || 0);
    }

    if (controllerTeamSelect && controllerDefaultTeam && !controllerTeamSelect.value && controllerDefaultTeam.value) {
        controllerTeamSelect.value = controllerDefaultTeam.value;
    }

    document.querySelectorAll('[data-ctrl-increment]').forEach(button => {
        button.addEventListener('click', () => {
            if (!controllerBidInput || !controllerBaseInput) {
                return;
            }
            const increment = Number(button.dataset.ctrlIncrement || 0);
            const base = Number(controllerBaseInput.value || 0);
            const target = base + increment;
            controllerBidInput.value = target;
            updatePreview(target);
        });
    });

    document.querySelectorAll('[data-ctrl-reset]').forEach(button => {
        button.addEventListener('click', resetBidInput);
    });

    if (controllerBidInput) {
        controllerBidInput.addEventListener('input', (event) => {
            updatePreview(event.target.value || 0);
        });
    }

    function showControllerMessage(message, type = 'success') {
        const feedback = document.getElementById('controller-feedback');
        if (!feedback) {
            alert(message);
            return;
        }

        feedback.textContent = message;
        feedback.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-700', 'bg-red-50', 'text-red-700');
        if (type === 'success') {
            feedback.classList.add('bg-emerald-50', 'text-emerald-700');
        } else {
            feedback.classList.add('bg-red-50', 'text-red-700');
        }

        setTimeout(() => {
            feedback.classList.add('hidden');
        }, 3500);
    }

    async function placeControllerBid(button) {
        const playerId = document.getElementById('controller-player-id')?.value;
        const leaguePlayerId = document.getElementById('controller-league-player-id')?.value;
        const leagueId = document.getElementById('controller-league-id')?.value;
        const teamId = document.getElementById('controller-team')?.value;
        const action = document.getElementById('controller-bid-action')?.value;
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!playerId || !leaguePlayerId) {
            showControllerMessage('No player is currently active for bidding.', 'error');
            return;
        }

        if (!teamId) {
            showControllerMessage('Select a team before placing a bid.', 'error');
            return;
        }

        const baseAmount = Number(controllerBaseInput?.value || 0);
        const targetAmount = Number(controllerBidInput?.value || 0);

        if (!targetAmount || targetAmount <= baseAmount) {
            showControllerMessage('Bid must be greater than the current price.', 'error');
            return;
        }

        if (!action || !token) {
            showControllerMessage('Missing bid configuration. Refresh and try again.', 'error');
            return;
        }

        const increment = targetAmount - baseAmount;

        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="flex items-center gap-2"><svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3.536-3.536A8 8 0 014 12z"></path></svg>Placing...</span>';

        try {
            const response = await fetch(action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    league_id: leagueId,
                    player_id: playerId,
                    league_player_id: leaguePlayerId,
                    base_price: baseAmount,
                    increment: increment,
                    league_team_id: teamId
                })
            });

            const data = await response.json().catch(() => ({ success: false, message: 'Unable to place bid.' }));

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Unable to place bid.');
            }

            controllerBaseInput.value = targetAmount;
            updatePreview(targetAmount);
            const bidLabel = document.getElementById('controller-current-bid-label');
            if (bidLabel) {
                bidLabel.textContent = formatCurrency(targetAmount);
            }

            showControllerMessage('Bid placed successfully. The live auction has been updated.');
        } catch (error) {
            showControllerMessage(error.message || 'Unable to place bid.', 'error');
        } finally {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }

    updatePreview(controllerBidInput?.value || controllerBaseInput?.value || 0);

    window.placeControllerBid = placeControllerBid;
    window.markControllerSold = markControllerSold;
    window.markControllerUnsold = markControllerUnsold;

    function getControllerToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    }

    function ensurePlayerActive() {
        const leaguePlayerId = document.getElementById('controller-league-player-id')?.value;
        if (!leaguePlayerId) {
            showControllerMessage('No player is currently active for this action.', 'error');
            return false;
        }
        return true;
    }

    async function markControllerSold(button) {
        if (!ensurePlayerActive()) {
            return;
        }

        const teamId = controllerTeamSelect?.value || controllerDefaultTeam?.value;
        if (!teamId) {
            showControllerMessage('Select a winning team before marking sold.', 'error');
            return;
        }

        const action = document.getElementById('controller-sold-action')?.value;
        const token = getControllerToken();
        if (!action || !token) {
            showControllerMessage('Missing configuration for sold action.', 'error');
            return;
        }

        const leaguePlayerId = document.getElementById('controller-league-player-id').value;
        const overrideAmount = document.getElementById('controller-override-amount')?.value;

        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="flex items-center gap-2"><svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3.536-3.536A8 8 0 014 12z"></path></svg>Saving...</span>';

        try {
            const response = await fetch(action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    league_player_id: leaguePlayerId,
                    team_id: teamId,
                    override_amount: overrideAmount || null
                })
            });

            const data = await response.json().catch(() => ({ success: false }));

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Unable to mark player as sold.');
            }

            showControllerMessage('Player marked as sold. Refreshing...', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } catch (error) {
            showControllerMessage(error.message || 'Unable to mark player as sold.', 'error');
        } finally {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }

    async function markControllerUnsold(button) {
        if (!ensurePlayerActive()) {
            return;
        }

        const action = document.getElementById('controller-unsold-action')?.value;
        const token = getControllerToken();
        if (!action || !token) {
            showControllerMessage('Missing configuration for unsold action.', 'error');
            return;
        }

        const leaguePlayerId = document.getElementById('controller-league-player-id').value;

        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="flex items-center gap-2"><svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3.536-3.536A8 8 0 014 12z"></path></svg>Updating...</span>';

        try {
            const response = await fetch(action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    league_player_id: leaguePlayerId
                })
            });

            const data = await response.json().catch(() => ({ success: false }));

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Unable to mark player as unsold.');
            }

            showControllerMessage('Player marked as unsold. Refreshing...', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } catch (error) {
            showControllerMessage(error.message || 'Unable to mark player as unsold.', 'error');
        } finally {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }
</script>
@endsection
