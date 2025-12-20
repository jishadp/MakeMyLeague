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
    $playersByLocalBody = $playersOrdered->groupBy(function ($player) {
        return $player->user?->localBody?->name ?? 'Unknown';
    });
    $posterFixturesByDate = $league->fixtures
        ->sortBy(function ($fixture) {
            $date = optional($fixture->match_date)->format('Ymd') ?? '99999999';
            $time = optional($fixture->match_time)->format('Hi') ?? '9999';
            return $date . '-' . $time;
        })
        ->groupBy(function ($fixture) {
            return optional($fixture->match_date)->format('d.m.Y') ?? 'Date TBA';
        })
        ->map(function ($fixtures) {
            return $fixtures->values();
        });
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
                    <div class="flex items-center gap-2 flex-wrap">
                        <input type="text" id="playerSearch" placeholder="Search players..." class="w-40 sm:w-56 rounded-lg border border-slate-200 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20" />
                        <div class="flex items-center gap-2 text-xs">
                            <button type="button" class="status-filter px-2 py-1 rounded-full bg-indigo-600 text-white border border-indigo-600 shadow" data-filter="all">All</button>
                            <button type="button" class="status-filter px-2 py-1 rounded-full bg-blue-100 text-blue-700 border border-blue-200" data-filter="available">Available</button>
                            <button type="button" class="status-filter px-2 py-1 rounded-full bg-amber-100 text-amber-700 border border-amber-200" data-filter="auctioning">Auctioning</button>
                            <button type="button" class="status-filter px-2 py-1 rounded-full bg-green-100 text-green-700 border border-green-200" data-filter="sold">Sold</button>
                            <button type="button" class="status-filter px-2 py-1 rounded-full bg-red-100 text-red-700 border border-red-200" data-filter="unsold">Unsold</button>
                            <button type="button" class="status-filter px-2 py-1 rounded-full bg-gray-100 text-gray-700 border border-gray-200" data-filter="pending">Pending</button>
                        </div>
                        <button type="button" class="player-tab inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-lg border bg-indigo-600 text-white border-indigo-600 shadow" data-player-tab="all">All</button>
                        <button type="button" class="player-tab inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-lg border bg-white text-slate-700 border-slate-200 hover:border-indigo-300 hover:text-indigo-700" data-player-tab="local">Local Body</button>
                        <button type="button" class="player-tab inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-lg border bg-white text-slate-700 border-slate-200 hover:border-indigo-300 hover:text-indigo-700" data-player-tab="poster">TBA Poster</button>
                    </div>
                </div>

                <div id="player-tab-all" class="player-tab-panel">
                    <div class="grid grid-cols-3 gap-3" data-player-list>
                        @foreach($playersOrdered as $player)
                            @php
                                $value = $player->bid_price ?? $player->base_price ?? 0;
                                $status = $player->status ?? 'available';
                                $statusColors = [
                                    'pending' => 'bg-gray-100 text-gray-800',
                                    'available' => 'bg-blue-100 text-blue-800',
                                    'auctioning' => 'bg-amber-100 text-amber-800',
                                    'sold' => 'bg-green-100 text-green-800',
                                    'unsold' => 'bg-red-100 text-red-800',
                                    'skip' => 'bg-gray-100 text-gray-800',
                                ];
                                $letterMap = [
                                    'available' => 'A',
                                    'auctioning' => 'B',
                                    'sold' => 'S',
                                    'unsold' => 'U',
                                    'pending' => 'P',
                                    'skip' => 'P',
                                ];
                                $statusLetter = $letterMap[$status] ?? 'A';
                                $firstName = $player->user?->name ? explode(' ', trim($player->user->name))[0] : 'Unknown';
                            @endphp
                            <div class="rounded-xl border border-slate-200 bg-white shadow-sm px-3 py-2 player-card" data-player-name="{{ strtolower($player->user?->name ?? '') }}" data-status="{{ $status }}">
                                <div class="flex flex-col items-center text-center space-y-1">
                                    <div class="relative">
                                        @if($player->user?->photo)
                                            <img src="{{ Storage::url($player->user->photo) }}" class="w-14 h-14 rounded-full object-cover border-2 border-white shadow ring-2 ring-slate-100">
                                        @else
                                            <div class="w-14 h-14 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-sm font-semibold text-slate-600 ring-2 ring-slate-100 shadow">
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
                                        <span class="absolute -bottom-1 -right-1 inline-flex items-center justify-center w-5 h-5 rounded-full text-white text-[10px] font-bold {{ $statusColors[$status] ?? 'bg-gray-400 text-white' }}">
                                            {{ $statusLetter }}
                                        </span>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-900 truncate w-full">{{ $firstName }}</p>
                                    <p class="text-[11px] text-slate-500 truncate w-full">{{ $player->user?->position?->name ?? 'Role' }}</p>
                                    <p class="text-sm font-bold {{ in_array($status, ['sold','available']) ? 'text-green-700' : ($status === 'auctioning' ? 'text-amber-700' : ($status === 'unsold' ? 'text-red-700' : 'text-slate-800')) }}">
                                        ₹{{ number_format($value) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div id="player-tab-local" class="player-tab-panel hidden">
                    <div class="space-y-4">
                        @foreach($playersByLocalBody as $location => $localPlayers)
                            <div class="border border-slate-200 rounded-xl">
                                <div class="px-4 py-3 flex items-center justify-between bg-slate-100 rounded-t-xl">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                        <p class="text-sm font-semibold text-slate-900">{{ $location }}</p>
                                    </div>
                                    <span class="text-xs text-slate-600">{{ $localPlayers->count() }} players</span>
                                </div>
                                <div class="p-3 grid grid-cols-3 sm:grid-cols-3 md:grid-cols-4 gap-2">
                                    @foreach($localPlayers as $player)
                                        @php
                                            $value = $player->bid_price ?? $player->base_price ?? 0;
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'available' => 'bg-blue-100 text-blue-800',
                                                'sold' => 'bg-green-100 text-green-800',
                                                'unsold' => 'bg-red-100 text-red-800',
                                                'skip' => 'bg-gray-100 text-gray-800',
                                            ];
                                            $firstName = $player->user?->name ? explode(' ', trim($player->user->name))[0] : 'Unknown';
                                        @endphp
                                        <div class="rounded-xl border border-slate-200 bg-white shadow-sm px-3 py-2 player-card" data-player-name="{{ strtolower($player->user?->name ?? '') }}" data-status="{{ $player->status ?? 'available' }}">
                                            <div class="flex flex-col items-center text-center space-y-1">
                                                <div class="relative">
                                                    @if($player->user?->photo)
                                                        <img src="{{ Storage::url($player->user->photo) }}" class="w-12 h-12 rounded-full object-cover border-2 border-white shadow ring-2 ring-slate-100">
                                                    @else
                                                        <div class="w-12 h-12 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-sm font-semibold text-slate-600 ring-2 ring-slate-100 shadow">
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
                                                    <span class="absolute -bottom-1 -right-1 inline-flex items-center justify-center w-5 h-5 rounded-full text-white text-[10px] font-bold {{ $statusColors[$player->status ?? 'available'] ?? 'bg-gray-400 text-white' }}">
                                                        {{ $statusLetter }}
                                                    </span>
                                                </div>
                                                <p class="text-sm font-semibold text-slate-900 truncate w-full">{{ $firstName }}</p>
                                                <p class="text-[11px] text-slate-500 truncate w-full">{{ $player->user?->position?->name ?? 'Role' }}</p>
                                                <p class="text-sm font-bold {{ in_array($player->status, ['sold','available']) ? 'text-green-700' : ($player->status === 'auctioning' ? 'text-amber-700' : ($player->status === 'unsold' ? 'text-red-700' : 'text-slate-800')) }}">
                                                    ₹{{ number_format($value) }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div id="player-tab-poster" class="player-tab-panel hidden">
                    @include('leagues.partials.matchday-poster', [
                        'league' => $league,
                        'fixturesByDate' => $posterFixturesByDate,
                        'venueLabel' => optional($league->localBody)->name ?? ($league->venue_details ?? 'Venue TBA'),
                        'emptyTitle' => 'Fixtures TBA',
                        'emptyDescription' => 'Add fixtures for ' . $league->name . ' to populate this shareable card.',
                    ])
                </div>
            </div>
        @else
            <div class="bg-white border border-dashed border-slate-200 rounded-2xl p-10 text-center">
                <p class="text-slate-600 font-semibold">No players available for this league yet.</p>
            </div>
        @endif
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.player-tab');
    const panels = document.querySelectorAll('.player-tab-panel');
    const searchInput = document.getElementById('playerSearch');
    const playerCards = document.querySelectorAll('[data-player-name]');
    const statusButtons = document.querySelectorAll('.status-filter');
    let activeStatus = 'all';

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.playerTab;
            tabs.forEach((t) => {
                const isActive = t.dataset.playerTab === target;
                t.classList.toggle('bg-indigo-600', isActive);
                t.classList.toggle('text-white', isActive);
                t.classList.toggle('border-indigo-600', isActive);
                t.classList.toggle('shadow', isActive);
                t.classList.toggle('bg-white', !isActive);
                t.classList.toggle('text-slate-700', !isActive);
                t.classList.toggle('border-slate-200', !isActive);
            });
            panels.forEach((panel) => {
                panel.classList.toggle('hidden', panel.id !== `player-tab-${target}`);
            });
        });
    });

    const applyFilters = () => {
        const term = searchInput?.value.trim().toLowerCase() || '';
        playerCards.forEach((card) => {
            const name = card.dataset.playerName || '';
            const status = card.dataset.status || '';
            const matchesName = !term || name.includes(term);
            const matchesStatus = activeStatus === 'all' || status === activeStatus;
            card.classList.toggle('hidden', !(matchesName && matchesStatus));
        });
    };

    statusButtons.forEach((button) => {
        button.addEventListener('click', () => {
            activeStatus = button.dataset.filter || 'all';
            statusButtons.forEach((btn) => {
                btn.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600', 'shadow');
                btn.classList.add('bg-white', 'text-slate-700', 'border-slate-200');
            });
            button.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600', 'shadow');
            button.classList.remove('bg-white', 'text-slate-700', 'border-slate-200');
            applyFilters();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            applyFilters();
        });
        applyFilters();
    }
});
</script>
@endsection
