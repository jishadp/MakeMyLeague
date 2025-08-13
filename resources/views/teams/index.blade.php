@extends('layouts.app')

@section('title', 'IPL Teams - Cricket League Manager')

@section('content')
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Teams</h1>
                <p class="mt-2 text-gray-600 text-lg">Manage all cricket teams and their players</p>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-3">
                <a href="{{ route('teams.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all font-medium">
                    Add Team
                </a>
                <a href="{{ route('players.index') }}" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 shadow-md hover:shadow-lg transition-all font-medium">
                    View Players
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($teams as $team)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6 hover:shadow-xl transition-all hover:scale-105">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-900">{{ $team->name }}</h3>
                        @if($team->logo_url)
                            <img src="{{ $team->logo_url }}" alt="{{ $team->name }} logo" class="w-12 h-12 rounded-full shadow-md">
                        @endif
                    </div>
                    <div class="space-y-2 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Country:</span>
                            <span class="font-medium text-gray-900">{{ $team->country }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Players:</span>
                            <span class="font-medium text-indigo-600">{{ $team->players_count }}</span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('teams.show', $team) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition-all flex-1 text-center font-medium">
                            View Team
                        </a>
                        <a href="{{ route('teams.edit', $team) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-yellow-600 transition-all">
                            Edit
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl border border-gray-200 shadow-lg p-12 text-center">
                    <p class="text-gray-600 text-lg font-medium">No teams found.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $teams->links() }}
        </div>
    </div>
@endsection