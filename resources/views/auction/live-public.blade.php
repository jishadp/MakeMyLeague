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
@endsection
