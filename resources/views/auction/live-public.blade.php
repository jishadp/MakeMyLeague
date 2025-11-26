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
    <script src="{{ asset('js/pusher-main.js') }}?v={{ time() + 1 }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
