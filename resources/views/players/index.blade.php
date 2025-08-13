@extends('layouts.app')

@section('title', 'Players - Cricket League Manager')

@section('content')
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Cricket Players</h1>
                <p class="mt-2 text-gray-600">Browse and manage all cricket players</p>
            </div>
            <div class="mt-4 md:mt-0 flex flex-wrap gap-3">
                <a href="{{ route('players.create') }}" 
                   class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all font-medium flex items-center gap-2">
                    <span>Add Player</span>
                </a>
                <a href="{{ route('teams.index') }}" 
                   class="bg-gray-200 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-300 shadow-sm hover:shadow-md transition-all font-medium flex items-center gap-2">
                    <span>View Teams</span>
                </a>
            </div>
        </div>

        <!-- Players Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($players as $player)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 p-6 flex flex-col overflow-hidden">
                    <!-- Player Info -->
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-gray-900 mb-3 tracking-tight">{{ $player->name }}</h3>
                        <p class="text-gray-700 mb-2 flex items-center gap-2">
                            <span class="font-semibold text-gray-800">Position:</span> {{ $player->position ?? 'Not specified' }}
                        </p>
                        <p class="text-gray-700 mb-2 flex items-center gap-2">
                            <span class="font-semibold text-gray-800">Age:</span> {{ $player->age ?? 'Not specified' }}
                        </p>
                        <p class="text-gray-700 mb-4 flex items-center gap-2">
                            <span class="font-semibold text-gray-800">Team:</span> {{ $player->team?->name ?? 'No team' }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200">
                        <a href="{{ route('players.show', $player) }}" 
                           class="flex-1 bg-indigo-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-600 shadow-md hover:shadow-lg transition-all font-medium text-center">
                            View
                        </a>
                        <a href="{{ route('players.edit', $player) }}" 
                           class="flex-1 bg-green-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-600 shadow-md hover:shadow-lg transition-all font-medium text-center">
                            Edit
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl border border-gray-200 shadow-lg p-8 text-center">
                    <p class="text-gray-700 text-lg">
                        No players found. 
                        <a href="{{ route('players.create') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">
                            Add the first player
                        </a>!
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $players->links() }}
        </div>
    </div>
@endsection