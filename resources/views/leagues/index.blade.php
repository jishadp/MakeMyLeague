@extends('layouts.app')

@section('title', 'League Manager - My Leagues')

@section('content')
<div class="py-12 bg-gradient-to-br from-gray-50 via-white to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 animate-fadeIn">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                My Leagues
            </h1>
            <a href="{{ route('leagues.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 transition-all duration-200
                      text-white px-5 py-2 rounded-xl shadow-md hover:shadow-lg w-full sm:w-auto text-center">
                + Create New League
            </a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 p-4 mb-6 rounded-xl shadow-md animate-fadeInUp">
                {{ session('success') }}
            </div>
        @endif

        <!-- No Leagues -->
        @if($leagues->isEmpty())
            <div class="bg-white border border-gray-200 rounded-xl shadow-md p-8 text-center animate-fadeInUp">
                <p class="text-gray-600 mb-4">You haven't created any leagues yet.</p>
                <a href="{{ route('leagues.create') }}"
                   class="text-indigo-600 hover:text-indigo-800 font-medium">
                    Register by creating your first league
                </a>
            </div>
        @else
            <!-- League Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($leagues as $league)
                    <div class="bg-white border border-gray-100 rounded-xl shadow-lg overflow-hidden
                                hover:shadow-xl hover:scale-[1.02] transition-all duration-300 animate-fadeInUp">
                        <div class="p-5">

                            <!-- Title & Default Badge -->
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-bold text-gray-900">{{ $league->name }}</h3>
                                @if($league->is_default)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                                 bg-green-100 text-green-800 shadow-sm">
                                        Default
                                    </span>
                                @endif
                            </div>

                            <!-- Status -->
                            <div class="mb-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium shadow-sm
                                    {{
                                        $league->status === 'active' ? 'bg-green-100 text-green-800' :
                                        ($league->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                        ($league->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800'))
                                    }}">
                                    {{ ucfirst($league->status) }}
                                </span>
                            </div>

                            <!-- Details -->
                            <div class="space-y-3 text-sm text-gray-600">
                                <p><span class="font-medium">🎮 Game:</span> {{ $league->game->name }}</p>
                                <p><span class="font-medium">📅 Season:</span> {{ $league->season }}</p>
                                <p><span class="font-medium">⏳ Duration:</span> {{ $league->start_date->format('M d, Y') }} - {{ $league->end_date->format('M d, Y') }}</p>
                                <p><span class="font-medium">👥 Teams:</span> {{ $league->max_teams }} (max {{ $league->max_team_players }} players each)</p>

                                <!-- Venue Details -->
                                @if($league->localBody)
                                <p><span class="font-medium">🏟️ Venue:</span> {{ $league->localBody->name }}, {{ $league->localBody->district->name }}</p>
                                @endif

                                @if($league->venue_details)
                                <p><span class="font-medium">📍 Details:</span> {{ strlen($league->venue_details) > 30 ? substr($league->venue_details, 0, 30) . '...' : $league->venue_details }}</p>
                                @endif

                                <!-- Ground Count -->
                                @if($league->ground_id)
                                <p><span class="font-medium">🏏 Ground:</span> Assigned</p>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="mt-6 flex justify-between items-center">
                                <a href="{{ route('leagues.show', $league) }}"
                                   class="text-indigo-600 hover:text-indigo-800 font-medium">
                                    View Details
                                </a>
                                <div class="flex space-x-3">
                                    <!-- Edit -->
                                    <a href="{{ route('leagues.edit', $league) }}"
                                       class="text-gray-600 hover:text-gray-900 transition-colors" title="Edit">
                                        ✏️
                                    </a>
                                    <!-- Set Default -->
                                    @if(!$league->is_default)
                                        <form action="{{ route('leagues.setDefault', $league) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="text-gray-600 hover:text-gray-900 transition-colors"
                                                    title="Set as Default">
                                                ✅
                                            </button>
                                        </form>
                                    @endif
                                    <!-- Delete -->
                                    <form action="{{ route('leagues.destroy', $league) }}" method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this league?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-800 transition-colors"
                                                title="Delete">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Animations -->
<style>
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fadeIn { animation: fadeIn 0.5s ease-in-out; }
.animate-fadeInUp { animation: fadeInUp 0.4s ease-in-out; }
</style>
@endsection
