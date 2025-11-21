@extends('layouts.broadcast')

@section('title', 'Live Broadcast - ' . $league->name)

@section('content')
    <livewire:broadcast-view :league-id="$league->id" />
@endsection
