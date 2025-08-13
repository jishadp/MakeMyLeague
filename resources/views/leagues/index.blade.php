@extends('layouts.app')

@section('title', 'My Leagues - Cricket League Manager')

@section('content')
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">My Leagues</h1>
                <p class="mt-2 text-gray-600 text-lg">Manage your ssssssscricket leagues and tournaments</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('leagues.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all font-medium">
                    Create League
                </a>
            </div>
        </div>

        <div class="space-y-6">
            @forelse($leagues as $league)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6 hover:shadow-xl transition-all">
                    <div class="md:flex md:items-center md:justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="text-2xl font-bold text-gray-900">{{ $league->name }}</h3>
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    @if($league->status === 'Active') bg-green-100 text-green-800
                                    @elseif($league->status === 'Completed') bg-gray-100 text-gray-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ $league->status }}
                                </span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                                <div>
                                    <span class="font-medium">Start:</span> {{ $league->start_date->format('M d, Y') }}
                                </div>
                                <div>
                                    <span class="font-medium">End:</span> {{ $league->end_date->format('M d, Y') }}
                                </div>
                                <div>
                                    <span class="font-medium">Teams:</span> {{ $league->teams->count() }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 md:mt-0 flex space-x-3">
                            <a href="{{ route('leagues.show', $league) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-all font-medium">
                                View
                            </a>
                            @if($league->teams->count() > 0)
                                <a href="{{ route('auction.show', $league) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-all font-medium">
                                    Auction
                                </a>
                            @endif
                            <a href="{{ route('leagues.edit', $league) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-all font-medium">
                                Edit
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-12 text-center">
                    <h3 class="text-xl font-medium text-gray-900 mb-2">No leagues yet</h3>
                    <p class="text-gray-600 mb-6">Create your first cricket league to get started</p>
                    <a href="{{ route('leagues.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-all font-medium">
                        Create Your First League
                    </a>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $leagues->links() }}
        </div>
    </div>
@endsection