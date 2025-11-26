@extends('layouts.broadcast')

@section('title', 'Live Broadcast - ' . $league->name)

@section('content')
    <input type="hidden" id="league-id" value="{{ $league->id }}">
    <livewire:broadcast-view :league-id="$league->id" />
@endsection

@section('scripts')
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        const PUSHER_KEY = '{{ config('broadcasting.connections.pusher.key') }}';
        const PUSHER_CLUSTER = '{{ config('broadcasting.connections.pusher.options.cluster') }}';
        const PUSHER_LOG_TO_CONSOLE = {{ config('app.debug') ? 'true' : 'false' }};
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (!window.Pusher || !PUSHER_KEY || !PUSHER_CLUSTER) {
                console.warn('Pusher config missing for broadcast');
                return;
            }

            const leagueId = document.getElementById('league-id')?.value;
            const pusher = new Pusher(PUSHER_KEY, {
                cluster: PUSHER_CLUSTER,
                forceTLS: true,
                enabledTransports: ['ws', 'wss'],
            });
            Pusher.logToConsole = PUSHER_LOG_TO_CONSOLE;

            const channels = [
                pusher.subscribe('auctions'),
                leagueId ? pusher.subscribe(`auctions.league.${leagueId}`) : null,
            ];

            const refreshBroadcast = () => {
                try {
                    // Prefer finding the broadcast Livewire instance directly
                    if (window.Livewire?.all) {
                        const comps = Livewire.all();
                        const broadcastComp = comps.find((c) => (c.name || c.__instance?.name || '').includes('broadcast'));
                        if (broadcastComp?.call) {
                            broadcastComp.call('refreshData');
                            return;
                        }
                    }
                    // Fallback: iterate over roots with wire:id
                    document.querySelectorAll('[wire\\:id]').forEach((el) => {
                        const comp = el.__livewire || (window.Livewire?.find && Livewire.find(el.getAttribute('wire:id')));
                        if (comp?.call) {
                            comp.call('refreshData');
                        }
                    });
                } catch (e) {
                    console.warn('Broadcast refresh failed', e);
                }
            };

            ['player-sold', 'player-unsold', 'new-player-started', 'new-player-bid-call'].forEach((event) => {
                channels.forEach((ch) => ch?.bind(event, refreshBroadcast));
            });
        });
    </script>
@endsection
