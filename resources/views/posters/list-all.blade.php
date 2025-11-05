@extends('layouts.app')

@section('title', 'Team Posters')

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Team Posters</h1>
            <p class="text-gray-600 mt-2">Professional team showcase posters</p>
        </div>

        @foreach($leagues as $league)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $league->name }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($league->leagueTeams as $leagueTeam)
                        <a href="{{ route('posters.show', [$league, $leagueTeam]) }}" 
                           class="block border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow">
                            <div class="flex items-center mb-3">
                                @if($leagueTeam->team->logo)
                                    <img src="{{ Storage::url($leagueTeam->team->logo) }}" 
                                         class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold">
                                        {{ substr($leagueTeam->team->name, 0, 2) }}
                                    </div>
                                @endif
                                <div class="ml-3">
                                    <h3 class="font-semibold text-gray-900">{{ $leagueTeam->team->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $leagueTeam->players->count() }} Players</p>
                                </div>
                            </div>
                            <button class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
                                View Poster
                            </button>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
