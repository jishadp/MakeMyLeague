@extends('layouts.app')

@section('title', $player->name . ' - Cricket League Manager')

@section('content')
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('players.index') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold flex items-center gap-1">← Back to Players</a>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-4 sm:mb-0 tracking-tight">{{ $player->name }}</h2>
                <div class="flex space-x-3">
                    <a href="{{ route('players.edit', $player) }}" class="bg-yellow-500 text-white px-6 py-3 rounded-lg hover:bg-yellow-600 shadow-md hover:shadow-lg transition-all font-medium">
                        Edit Player
                    </a>
                    <form action="{{ route('players.destroy', $player) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 shadow-md hover:shadow-lg transition-all font-medium" onclick="return confirm('Are you sure?')">
                            Delete
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Player Info</h3>
                    <div class="bg-gray-100 p-6 rounded-xl space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-800">Position:</span>
                            <span class="text-gray-900">{{ $player->position ?? 'Not specified' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-800">Age:</span>
                            <span class="text-gray-900">{{ $player->age ?? 'Not specified' }} years</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-800">Team:</span>
                            @if($player->team)
                                <a href="{{ route('teams.show', $player->team) }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                                    {{ $player->team->name }}
                                </a>
                            @else
                                <span class="text-gray-900">No team assigned</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Career Statistics</h3>
                    @if($player->stats_json)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-blue-50 p-4 rounded-xl text-center shadow-sm hover:shadow-md transition-all">
                                <p class="text-3xl font-bold text-blue-700">{{ $player->stats_json['matches'] ?? 0 }}</p>
                                <p class="text-sm text-gray-700 font-medium">Matches</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-xl text-center shadow-sm hover:shadow-md transition-all">
                                <p class="text-3xl font-bold text-green-700">{{ $player->stats_json['runs'] ?? 0 }}</p>
                                <p class="text-sm text-gray-700 font-medium">Runs</p>
                            </div>
                            <div class="bg-red-50 p-4 rounded-xl text-center shadow-sm hover:shadow-md transition-all">
                                <p class="text-3xl font-bold text-red-700">{{ $player->stats_json['wickets'] ?? 0 }}</p>
                                <p class="text-sm text-gray-700 font-medium">Wickets</p>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-xl text-center shadow-sm hover:shadow-md transition-all">
                                <p class="text-3xl font-bold text-yellow-700">{{ $player->stats_json['average'] ?? 0 }}</p>
                                <p class="text-sm text-gray-700 font-medium">Average</p>
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-100 p-8 rounded-xl text-center">
                            <p class="text-gray-700 text-lg font-medium">No statistics available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection