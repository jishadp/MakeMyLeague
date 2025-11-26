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
            if (!PUSHER_KEY || !PUSHER_CLUSTER) {
                console.warn('Pusher keys missing');
                return;
            }

            const leagueId = document.getElementById('league-id')?.value;
            const pusher = new Pusher(PUSHER_KEY, {
                cluster: PUSHER_CLUSTER,
                forceTLS: true,
                enabledTransports: ['ws', 'wss'],
            });
            Pusher.logToConsole = PUSHER_LOG_TO_CONSOLE;

            const channel = pusher.subscribe('auctions');
            const leagueChannel = leagueId ? pusher.subscribe(`auctions.league.${leagueId}`) : null;

            const refreshBroadcast = () => {
                const root = document.getElementById('broadcastRoot');
                const wireId = root ? root.getAttribute('wire:id') : null;

                if (root && root.__livewire && typeof root.__livewire.call === 'function') {
                    root.__livewire.call('refreshData');
                    return;
                }

                if (window.Livewire) {
                    if (typeof Livewire.find === 'function' && wireId) {
                        const comp = Livewire.find(wireId);
                        if (comp && typeof comp.call === 'function') {
                            comp.call('refreshData');
                            return;
                        }
                    }
                    if (typeof Livewire.all === 'function') {
                        Livewire.all().forEach(comp => {
                            if (comp && typeof comp.call === 'function') {
                                comp.call('refreshData');
                            }
                        });
                    }
                }
            };

            const bindEvents = targetChannel => {
                if (!targetChannel) return;
                ['player-sold', 'player-unsold', 'new-player-started', 'new-player-bid-call'].forEach(event => {
                    targetChannel.bind(event, refreshBroadcast);
                });
            };

            bindEvents(channel);
            bindEvents(leagueChannel);
        });
    </script>
@endsection
