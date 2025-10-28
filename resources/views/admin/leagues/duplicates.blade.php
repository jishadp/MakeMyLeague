@extends('layouts.app')

@section('title', 'Duplicate Players - ' . $league->name)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 mb-2">Duplicate Players</h1>
            <p class="text-gray-600">{{ $league->name }}</p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if($duplicatePlayers->isEmpty())
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-gray-600">No duplicate players found!</p>
                <a href="{{ route('admin.leagues.index') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800">
                    Back to Leagues
                </a>
            </div>
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Player Name</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Slug</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Base Price</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Created</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($duplicatePlayers as $player)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $player->id }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $player->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $player->slug }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $player->status === 'available' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $player->status === 'sold' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $player->status === 'unsold' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($player->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">₹{{ number_format($player->base_price) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($player->created_at)->format('M d, Y H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.leagues.delete-player', [$league, $player->id]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this player entry?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex gap-4">
                <a href="{{ route('admin.leagues.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-bold">
                    Back to Leagues
                </a>
                <form action="{{ route('admin.leagues.restart', $league) }}" method="POST" class="inline" onsubmit="return confirm('All duplicates cleaned. Restart league now?');">
                    @csrf
                    <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg font-bold">
                        Restart League
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
