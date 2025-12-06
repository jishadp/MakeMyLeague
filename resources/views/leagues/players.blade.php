@extends('layouts.app')

@php
    use Illuminate\Support\Str;

    $players = $league->leaguePlayers ?? collect();
    $playersOrdered = $players->sortByDesc(function ($player) {
        $value = (int) ($player->bid_price ?? $player->base_price ?? 0);
        return sprintf('%d-%012d', $player->retention ? 1 : 0, $value);
    });

    $statusCounts = [
        'total' => $players->count(),
        'retained' => $players->where('retention', true)->count(),
        'sold' => $players->where('status', 'sold')->count(),
        'available' => $players->where('status', 'available')->count(),
        'unsold' => $players->where('status', 'unsold')->count(),
    ];
@endphp

@section('content')
<div class="min-h-screen bg-slate-50 py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="space-y-1">
                <a href="{{ route('teams.league-players') }}" class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Leagues
                </a>
                <h1 class="text-3xl font-bold text-slate-900">{{ $league->name }}</h1>
                <p class="text-slate-600">{{ $league->game->name ?? 'Game' }} • Season {{ $league->season ?? 'N/A' }}</p>
            </div>
            <span class="px-4 py-2 rounded-full text-sm font-medium
                @if($league->status === 'active') bg-green-100 text-green-800
                @elseif($league->status === 'completed') bg-blue-100 text-blue-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ ucfirst($league->status) }}
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
            <div class="bg-white border border-slate-200 rounded-xl p-3 text-center shadow-sm">
                <p class="text-2xl font-bold text-slate-900">{{ $statusCounts['total'] }}</p>
                <p class="text-xs text-slate-500">Total Players</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3 text-center shadow-sm">
                <p class="text-2xl font-bold text-amber-600">{{ $statusCounts['retained'] }}</p>
                <p class="text-xs text-slate-500">Retained</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3 text-center shadow-sm">
                <p class="text-2xl font-bold text-green-600">{{ $statusCounts['sold'] }}</p>
                <p class="text-xs text-slate-500">Sold</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3 text-center shadow-sm">
                <p class="text-2xl font-bold text-blue-600">{{ $statusCounts['available'] }}</p>
                <p class="text-xs text-slate-500">Available</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-3 text-center shadow-sm">
                <p class="text-2xl font-bold text-red-600">{{ $statusCounts['unsold'] }}</p>
                <p class="text-xs text-slate-500">Unsold</p>
            </div>
        </div>

        @if($playersOrdered->count() > 0)
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Players</p>
                        <h2 class="text-xl font-bold text-slate-900">Roster</h2>
                        <p class="text-sm text-slate-600">Retained first, then value</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @foreach($playersOrdered as $player)
                        @php
                            $value = $player->bid_price ?? $player->base_price ?? 0;
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'available' => 'bg-blue-100 text-blue-800',
                                'sold' => 'bg-green-100 text-green-800',
                                'unsold' => 'bg-red-100 text-red-800',
                                'skip' => 'bg-gray-100 text-gray-800',
                            ];
                        @endphp
                        <div class="flex items-center gap-2 rounded-lg border border-slate-100 bg-slate-50 px-3 py-2">
                            <div class="relative">
                                @if($player->user?->photo)
                                    <img src="{{ Storage::url($player->user->photo) }}" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-sm font-semibold text-slate-600">
                                        {{ strtoupper(substr($player->user?->name ?? 'P', 0, 1)) }}
                                    </div>
                                @endif
                                @if($player->retention)
                                    <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-500 text-white shadow">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-900 truncate">{{ $player->user?->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $player->user?->position?->name ?? 'Role' }}</p>
                                <p class="text-[11px] text-slate-600 truncate">{{ $player->leagueTeam?->team?->name ?? 'No team' }}</p>
                            </div>
                            <div class="text-right space-y-1">
                                <span class="inline-flex px-2 py-1 text-[11px] font-semibold rounded-full {{ $statusColors[$player->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($player->status) }}
                                </span>
                                <p class="text-sm font-bold text-slate-900">₹{{ number_format($value) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-white border border-dashed border-slate-200 rounded-2xl p-10 text-center">
                <p class="text-slate-600 font-semibold">No players available for this league yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection
