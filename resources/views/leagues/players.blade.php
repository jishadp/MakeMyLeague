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
                        
                        <!-- Mobile: Dropdown filter -->
                        <div class="sm:hidden">
                            <select id="statusFilterMobile" class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 bg-white shadow-sm text-slate-700">
                                <option value="all">All Players</option>
                                <option value="retained">Retained</option>
                                <option value="available">Available</option>
                                <option value="auctioning">Auctioning</option>
                                <option value="sold">Sold</option>
                                <option value="unsold">Unsold</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                        
                        <!-- Desktop: Button filters -->
                        <div class="hidden sm:flex items-center gap-2 text-xs flex-wrap">
                            <button type="button" class="status-filter whitespace-nowrap px-3 py-1.5 rounded-full bg-indigo-600 text-white border border-indigo-600 shadow flex-shrink-0" data-filter="all">All</button>
                            <button type="button" class="status-filter whitespace-nowrap px-3 py-1.5 rounded-full bg-amber-600 text-white border border-amber-600 flex-shrink-0" data-filter="retained">Retained</button>
                            <button type="button" class="status-filter whitespace-nowrap px-3 py-1.5 rounded-full bg-blue-100 text-blue-700 border border-blue-200 flex-shrink-0" data-filter="available">Available</button>
                            <button type="button" class="status-filter whitespace-nowrap px-3 py-1.5 rounded-full bg-amber-100 text-amber-700 border border-amber-200 flex-shrink-0" data-filter="auctioning">Auctioning</button>
                            <button type="button" class="status-filter whitespace-nowrap px-3 py-1.5 rounded-full bg-green-100 text-green-700 border border-green-200 flex-shrink-0" data-filter="sold">Sold</button>
                            <button type="button" class="status-filter whitespace-nowrap px-3 py-1.5 rounded-full bg-red-100 text-red-700 border border-red-200 flex-shrink-0" data-filter="unsold">Unsold</button>
                            <button type="button" class="status-filter whitespace-nowrap px-3 py-1.5 rounded-full bg-gray-100 text-gray-700 border border-gray-200 flex-shrink-0" data-filter="pending">Pending</button>
                        </div>

                        <a id="whatsappShareBtn" href="https://wa.me/?text={{ urlencode('Check out players for ' . $league->name . ': ' . request()->url()) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-lg bg-green-500 text-white hover:bg-green-600 shadow flex-shrink-0">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 .5C5.648.5.5 5.648.5 12c0 2.016.527 3.964 1.527 5.679L.5 23.5l6.012-1.513C8.16 22.973 10.056 23.5 12 23.5c6.352 0 11.5-5.148 11.5-11.5S18.352.5 12 .5zm0 20.905c-1.793 0-3.538-.48-5.05-1.385l-.361-.213-3.57.897.951-3.48-.235-.359A9.39 9.39 0 012.61 12C2.61 6.536 6.536 2.61 12 2.61c5.465 0 9.39 3.926 9.39 9.39 0 5.465-3.925 9.405-9.39 9.405z"/><path d="M17.174 14.83c-.293-.146-1.733-.853-2.002-.949-.27-.098-.468-.146-.666.146-.195.293-.768.949-.94 1.146-.171.195-.342.22-.635.073-.293-.146-1.236-.456-2.353-1.454-.869-.775-1.456-1.733-1.627-2.025-.171-.293-.018-.451.129-.597.132-.132.293-.342.439-.513.146-.171.195-.293.293-.488.098-.195.049-.366-.024-.512-.073-.146-.666-1.607-.914-2.2-.241-.579-.487-.5-.666-.51l-.566-.01c-.195 0-.512.073-.78.366-.269.293-1.024.999-1.024 2.438 0 1.438 1.05 2.826 1.195 3.018.146.195 2.07 3.163 5.018 4.433.702.303 1.25.484 1.676.62.704.223 1.344.192 1.852.116.565-.085 1.733-.707 1.979-1.389.244-.683.244-1.268.171-1.389-.073-.122-.268-.195-.561-.342z"/>
                            </svg>
                            Share
                        </a>
                        <button id="exportCsvBtn" type="button" class="inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-lg bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 shadow flex-shrink-0">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Export
                        </button>
                        
                        <div class="flex items-center gap-2 flex-wrap">
                             <button type="button" class="player-tab inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-lg border bg-indigo-600 text-white border-indigo-600 shadow" data-player-tab="all">All</button>
                             <button type="button" class="player-tab inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-lg border bg-white text-slate-700 border-slate-200 hover:border-indigo-300 hover:text-indigo-700" data-player-tab="local">Local Body</button>
                             <button type="button" class="player-tab inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-lg border bg-white text-slate-700 border-slate-200 hover:border-indigo-300 hover:text-indigo-700" data-player-tab="poster">TBA Poster</button>
                        </div>
                    </div>
                </div>

                <div id="player-tab-all" class="player-tab-panel">
                    <div class="grid grid-cols-3 gap-3" data-player-list>
                        @foreach($playersOrdered as $player)
                            @php
                                $val = $player->bid_price ?? $player->base_price ?? 0;
                                $displayValue = '₹' . number_format($val);
                                $displayRole = $player->user?->position?->name ?? 'Role';
                                $displayName = $player->user?->name ? trim($player->user->name) : 'Unknown';
                                
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
                                
                                // Check for foreign player (different district)
                                $leagueDistrictId = $league->localBody?->district_id;
                                $playerDistrictId = $player->user?->localBody?->district_id;
                                $isForeign = $leagueDistrictId && $playerDistrictId && $leagueDistrictId !== $playerDistrictId;
                            @endphp
                            <div class="rounded-xl border border-slate-200 bg-white shadow-sm px-3 py-2 player-card" 
                                 data-player-name="{{ strtolower($player->user?->name ?? '') }}" 
                                 data-status="{{ $status }}" 
                                 data-retained="{{ $player->retention ? 'true' : 'false' }}"
                                 data-share-info="{{ $displayName }} - {{ $displayRole }} ({{ $displayValue }})"
                                 data-csv-name="{{ $displayName }}"
                                 data-csv-role="{{ $displayRole }}"
                                 data-csv-price="{{ $val }}"
                                 data-csv-status="{{ ucfirst($status) }}"
                                 data-csv-retained="{{ $player->retention ? 'Yes' : 'No' }}"
                                 >
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
                                            </span>
                                        @endif
                                        @if($isForeign)
                                            <span class="absolute -top-1 -left-1 inline-flex items-center justify-center w-5 h-5 rounded-full bg-indigo-500 text-white shadow" title="Foreign Player">
                                                <svg class="w-3 h-3 transform rotate-45" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
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
                                        {{ $displayValue }}
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
                                            $val = $player->bid_price ?? $player->base_price ?? 0;
                                            $displayValue = '₹' . number_format($val);
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'available' => 'bg-blue-100 text-blue-800',
                                                'sold' => 'bg-green-100 text-green-800',
                                                'unsold' => 'bg-red-100 text-red-800',
                                                'skip' => 'bg-gray-100 text-gray-800',
                                            ];
                                                'skip' => 'bg-gray-100 text-gray-800',
                                            ];
                                            $firstName = $player->user?->name ? explode(' ', trim($player->user->name))[0] : 'Unknown';
                                            
                                            // Check for foreign player (different district)
                                            $leagueDistrictId = $league->localBody?->district_id;
                                            $playerDistrictId = $player->user?->localBody?->district_id;
                                            $isForeign = $leagueDistrictId && $playerDistrictId && $leagueDistrictId !== $playerDistrictId;
                                        @endphp
                                        <div class="rounded-xl border border-slate-200 bg-white shadow-sm px-3 py-2 player-card" 
                                             data-player-name="{{ strtolower($player->user?->name ?? '') }}" 
                                             data-status="{{ $player->status ?? 'available' }}"
                                             data-retained="{{ $player->retention ? 'true' : 'false' }}"
                                             data-share-info="{{ $player->user?->name ? trim($player->user->name) : 'Unknown' }} - {{ $player->user?->position?->name ?? 'Role' }} (₹{{ number_format($val) }})"
                                             data-csv-name="{{ $player->user?->name ? trim($player->user->name) : 'Unknown' }}"
                                             data-csv-role="{{ $player->user?->position?->name ?? 'Role' }}"
                                             data-csv-price="{{ $val }}"
                                             data-csv-status="{{ ucfirst($player->status ?? 'available') }}"
                                             data-csv-retained="{{ $player->retention ? 'Yes' : 'No' }}"
                                             >
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
                                                    @if($isForeign)
                                                        <span class="absolute -top-1 -left-1 inline-flex items-center justify-center w-5 h-5 rounded-full bg-indigo-500 text-white shadow" title="Foreign Player">
                                                            <svg class="w-3 h-3 transform rotate-45" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
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
                                                    {{ $displayValue }}
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
    const whatsappBtn = document.getElementById('whatsappShareBtn');
    const exportCsvBtn = document.getElementById('exportCsvBtn');
    let activeStatus = 'all';
    let activeTab = 'all'; // Track current active tab

    // League info for share text
    const leagueName = "{{ $league->name }}";
    const pageUrl = "{{ request()->url() }}";

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.playerTab;
            activeTab = target; // Update active tab
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
            // Update share link when tab changes
            setTimeout(updateShareLink, 100);
        });
    });

    const updateShareLink = () => {
        if (!whatsappBtn) return;
        
        let visiblePlayers = [];
        
        // Collect visible players from the currently active tab
        const activePanel = document.querySelector(`#player-tab-${activeTab}`);
        if (!activePanel) return;
        
        const cards = activePanel.querySelectorAll('.player-card');
        
        cards.forEach(card => {
             if (!card.classList.contains('hidden')) {
                 const info = card.dataset.shareInfo;
                 if (info) {
                     visiblePlayers.push(info);
                 }
             }
        });

        let shareText = `*${leagueName} - Players List*\n`;
        shareText += `Tab: ${activeTab.charAt(0).toUpperCase() + activeTab.slice(1)}\n`;
        shareText += `Filter: ${activeStatus.charAt(0).toUpperCase() + activeStatus.slice(1)}\n\n`;
        
        if (visiblePlayers.length > 0) {
            visiblePlayers.forEach(p => {
                shareText += `• ${p}\n`;
            });
        } else {
            shareText += `No players found for this filter.\n`;
        }

        shareText += `\nCheck full list here: ${pageUrl}`;
        
        whatsappBtn.href = `https://wa.me/?text=${encodeURIComponent(shareText)}`;
    };
    
    const exportCSV = () => {
        let csvContent = "data:text/csv;charset=utf-8,";
        let filename = `players-${activeTab}`;
        
        if (activeTab === 'local') {
            // Export with local body grouping
            csvContent += "Local Body,Name,Role,Price,Status,Retained\n";
            
            const localPanel = document.querySelector('#player-tab-local');
            if (localPanel) {
                const localBodySections = localPanel.querySelectorAll('.border-slate-200.rounded-xl');
                
                localBodySections.forEach(section => {
                    const locationHeader = section.querySelector('.bg-slate-100 p');
                    const localBody = locationHeader ? locationHeader.textContent.trim() : 'Unknown';
                    
                    const cards = section.querySelectorAll('.player-card');
                    cards.forEach(card => {
                        if (!card.classList.contains('hidden')) {
                            const name = `"${(card.dataset.csvName || '').replace(/"/g, '""')}"`;
                            const role = `"${(card.dataset.csvRole || '').replace(/"/g, '""')}"`;
                            const price = `"${(card.dataset.csvPrice || '0').replace(/"/g, '""')}"`;
                            const status = `"${(card.dataset.csvStatus || '').replace(/"/g, '""')}"`;
                            const retained = `"${(card.dataset.csvRetained || '').replace(/"/g, '""')}"`;
                            
                            csvContent += `"${localBody}",${name},${role},${price},${status},${retained}\n`;
                        }
                    });
                });
            }
            
            if (activeStatus !== 'all') {
                filename += `-${activeStatus}`;
            }
        } else {
            // Export from All tab or other tabs
            csvContent += "Name,Role,Price,Status,Retained\n";
            
            const activePanel = document.querySelector(`#player-tab-${activeTab}`);
            if (activePanel) {
                const cards = activePanel.querySelectorAll('.player-card');
                cards.forEach(card => {
                     if (!card.classList.contains('hidden')) {
                         const name = `"${(card.dataset.csvName || '').replace(/"/g, '""')}"`;
                         const role = `"${(card.dataset.csvRole || '').replace(/"/g, '""')}"`;
                         const price = `"${(card.dataset.csvPrice || '0').replace(/"/g, '""')}"`;
                         const status = `"${(card.dataset.csvStatus || '').replace(/"/g, '""')}"`;
                         const retained = `"${(card.dataset.csvRetained || '').replace(/"/g, '""')}"`;
                         
                         csvContent += `${name},${role},${price},${status},${retained}\n`;
                     }
                });
            }
            
            if (activeStatus !== 'all') {
                filename += `-${activeStatus}`;
            }
        }
        
        filename += '.csv';
        
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    if (exportCsvBtn) {
        exportCsvBtn.addEventListener('click', exportCSV);
    }

    const applyFilters = () => {
        const term = searchInput?.value.trim().toLowerCase() || '';
        
        playerCards.forEach((card) => {
            const name = card.dataset.playerName || '';
            const status = card.dataset.status || '';
            const retained = card.dataset.retained === 'true';
            
            const matchesName = !term || name.includes(term);
            let matchesStatus = false;
            
            if (activeStatus === 'all') {
                matchesStatus = true;
            } else if (activeStatus === 'retained') {
                matchesStatus = retained;
            } else {
                matchesStatus = status === activeStatus;
            }
            
            card.classList.toggle('hidden', !(matchesName && matchesStatus));
        });
        
        // Update share link after filtering
        setTimeout(updateShareLink, 100);
    };

    statusButtons.forEach((button) => {
        button.addEventListener('click', () => {
            activeStatus = button.dataset.filter || 'all';
            statusButtons.forEach((btn) => {
                 // Remove active classes
                 btn.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600', 'shadow');
                 btn.classList.remove('bg-amber-600'); 
                 
                 // Reset all to white/default look first
                 btn.classList.add('bg-white', 'text-slate-700', 'border-slate-200');
                 
                 // Remove specific color classes if they were added inline for active state
                  if (btn.dataset.filter === 'retained') {
                     btn.classList.remove('text-white', 'bg-amber-600', 'border-amber-600');
                     btn.classList.add('text-amber-700', 'bg-amber-100', 'border-amber-200');
                  }
             });
             
             // Apply active class to clicked button
             button.classList.remove('bg-white', 'text-slate-700', 'border-slate-200');
              if (activeStatus === 'retained') {
                 button.classList.remove('text-amber-700', 'bg-amber-100', 'border-amber-200');
                 button.classList.add('bg-amber-600', 'text-white', 'border-amber-600', 'shadow');
             } else {
                 button.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600', 'shadow');
             }
            
            applyFilters();
        });
    });

    // Mobile dropdown filter handler
    const mobileFilterSelect = document.getElementById('statusFilterMobile');
    if (mobileFilterSelect) {
        mobileFilterSelect.addEventListener('change', (e) => {
            activeStatus = e.target.value || 'all';
            applyFilters();
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            applyFilters();
        });
        applyFilters();
    }
    
    // Initial share link update
    updateShareLink();
});
</script>
@endsection
