@extends('layouts.app')

@section('title', $team->name . ' - Cricket League Manager')

@section('content')
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('teams.index') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold flex items-center gap-1">← Back to Teams</a>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-8 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div>
                    <h2 class="text-4xl font-extrabold text-gray-900 mb-2 tracking-tight">{{ $team->name }}</h2>
                    <p class="text-gray-600 text-lg">{{ $team->country }}</p>
                </div>
                <div class="flex items-center space-x-4 mt-4 sm:mt-0">
                    @if($team->logo_url)
                        <img src="{{ $team->logo_url }}" alt="{{ $team->name }} logo" class="w-20 h-20 rounded-full shadow-md">
                    @endif
                    <div class="flex space-x-3">
                        <a href="{{ route('teams.edit', $team) }}" class="bg-yellow-500 text-white px-6 py-3 rounded-lg hover:bg-yellow-600 shadow-md hover:shadow-lg transition-all font-medium">
                            Edit Team
                        </a>
                        <form action="{{ route('teams.destroy', $team) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 shadow-md hover:shadow-lg transition-all font-medium" onclick="return confirm('Are you sure?')">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-100 p-6 rounded-xl">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Team Stats</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="font-semibold text-gray-800">Total Players:</span>
                            <span class="text-gray-900">{{ $team->players->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-gray-800">Batsmen:</span>
                            <span class="text-gray-900">{{ $team->players->where('position', 'Batsman')->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-gray-800">Bowlers:</span>
                            <span class="text-gray-900">{{ $team->players->where('position', 'Bowler')->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-gray-800">All-rounders:</span>
                            <span class="text-gray-900">{{ $team->players->where('position', 'All-rounder')->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-gray-800">Wicket-keepers:</span>
                            <span class="text-gray-900">{{ $team->players->where('position', 'Wicket-keeper')->count() }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 p-6 rounded-xl text-center shadow-sm hover:shadow-md transition-all">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Average Age</h3>
                    <p class="text-4xl font-bold text-blue-700">
                        {{ $team->players->avg('age') ? round($team->players->avg('age'), 1) : 'N/A' }}
                    </p>
                    <p class="text-sm text-gray-700 font-medium">years</p>
                </div>

                <div class="bg-green-50 p-6 rounded-xl text-center shadow-sm hover:shadow-md transition-all">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Total Runs</h3>
                    <p class="text-4xl font-bold text-green-700">
                        {{ $team->players->sum(function($player) { return $player->stats_json['runs'] ?? 0; }) }}
                    </p>
                    <p class="text-sm text-gray-700 font-medium">runs scored</p>
                </div>
            </div>
            </div>

        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">Squad Players</h3>
                
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($team->players as $player)
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 hover:shadow-md transition-all hover:bg-gray-100">
                        <h4 class="font-bold text-gray-900 text-lg mb-2">{{ $player->name }}</h4>
                        <p class="text-gray-600 mb-3">{{ $player->position }} • {{ $player->age }} years</p>
                        <div class="grid grid-cols-2 gap-2 mb-4 text-sm">
                            <div class="bg-white p-2 rounded-lg text-center">
                                <span class="font-bold text-green-600">{{ $player->stats_json['runs'] ?? 0 }}</span>
                                <p class="text-xs text-gray-500">Runs</p>
                            </div>
                            <div class="bg-white p-2 rounded-lg text-center">
                                <span class="font-bold text-red-600">{{ $player->stats_json['wickets'] ?? 0 }}</span>
                                <p class="text-xs text-gray-500">Wickets</p>
                            </div>
                        </div>
                        <a href="{{ route('players.show', $player) }}" class="text-indigo-600 hover:text-indigo-700 font-medium text-sm">
                            View Profile →
                        </a>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-600 text-lg font-medium">No players in this team yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection