@extends('layouts.broadcast')

@section('title', 'Live Broadcast - ' . $league->name)

@section('content')
<input type="hidden" id="league-id" value="{{ $league->id }}">
<input type="hidden" id="league-slug" value="{{ $league->slug }}">

<div class="min-h-screen bg-slate-950 text-white py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex flex-col gap-2">
            <p class="text-xs uppercase tracking-[0.25em] text-emerald-300 font-semibold">Live Broadcast</p>
            <h1 class="text-2xl sm:text-3xl font-bold">{{ $league->name }}</h1>
            <p class="text-sm text-slate-400">{{ $league->game->name ?? 'League' }} • Public view (auto-refresh)</p>
        </div>

        @php
            $currentPlayer = $currentPlayer ?? null;
            $currentHighestBid = $currentHighestBid ?? null;
            $displayPlayer = $currentPlayer?->player;
            $playerName = $displayPlayer?->name ?? 'Awaiting player';
            $playerRole = $displayPlayer?->primaryGameRole?->gamePosition?->name
                ?? $displayPlayer?->position?->name
                ?? 'Player';
            $currentBidAmount = $currentHighestBid?->amount ?? ($currentPlayer?->base_price ?? 0);
            $basePrice = $currentPlayer?->base_price ?? 0;
            $leadingTeamName = $currentHighestBid?->leagueTeam?->team?->name ?? 'Awaiting new bids..';
        @endphp

        <div class="bg-slate-900/70 border border-slate-800 rounded-3xl p-6 shadow-2xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Current Player</p>
                    <h2 class="text-3xl font-bold playerName">{{ $playerName }}</h2>
                    <p class="text-sm text-slate-300 position">{{ $playerRole }}</p>
                </div>
                <div class="text-right space-y-1">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-400 bidStatus">
                        {{ $currentHighestBid ? 'Current Bid' : 'Base Price' }}
                    </p>
                    <p class="text-4xl font-black text-emerald-300">
                        ₹ <span class="currentBid">{{ number_format($currentBidAmount) }}</span>
                    </p>
                    <p class="text-sm text-slate-300 bidTeam">{{ $leadingTeamName }}</p>
                    <p class="text-xs text-slate-500">Base: ₹<span class="basePrice">{{ number_format($basePrice) }}</span></p>
                </div>
            </div>
        </div>

        <div class="bg-slate-900/70 border border-slate-800 rounded-3xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold">Recent Bids</h3>
                <span class="text-xs text-slate-400">Live</span>
            </div>
            <div id="recentBids" class="space-y-3 max-h-[26rem] overflow-y-auto pr-1" aria-live="polite">
                <div class="text-center text-slate-500 py-4">
                    <p>No recent bids yet...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        const PUSHER_KEY = '{{ config('broadcasting.connections.pusher.key') }}';
        const PUSHER_CLUSTER = '{{ config('broadcasting.connections.pusher.options.cluster') }}';
        const PUSHER_LOG_TO_CONSOLE = {{ config('app.debug') ? 'true' : 'false' }};
    </script>
    <script src="{{ asset('js/pusher-main.js') }}?v={{ time() + 1 }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Stubs to avoid missing function errors from shared JS
            if (typeof showMessage !== 'function') {
                window.showMessage = (msg) => console.log(msg);
            }
            if (typeof updateTeamBalanceInUI !== 'function') {
                window.updateTeamBalanceInUI = () => {};
            }
            // If Livewire broadcast component exists, hook into pusher-main events by refreshing on bid/sold updates
            const leagueId = document.getElementById('league-id')?.value;
            const refreshBroadcast = () => {
                try {
                    if (window.Livewire?.all) {
                        window.Livewire.all().forEach((comp) => {
                            if (comp?.call) comp.call('refreshData');
                        });
                    }
                } catch (e) {
                    console.warn('Broadcast refresh failed', e);
                }
            };

            if (window.leagueChannel) {
                ['player-sold', 'player-unsold', 'new-player-started', 'new-player-bid-call'].forEach((event) => {
                    window.leagueChannel.bind(event, refreshBroadcast);
                });
            }
            if (window.channel) {
                ['player-sold', 'player-unsold', 'new-player-started', 'new-player-bid-call'].forEach((event) => {
                    window.channel.bind(event, refreshBroadcast);
                });
            }
        });
    </script>
@endsection
