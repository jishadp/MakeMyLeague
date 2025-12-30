@extends('layouts.app')

@section('title', 'Fixtures - ' . $league->name)

@section('content')
<div class="bg-slate-50 text-slate-900">
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 opacity-70 bg-[radial-gradient(circle_at_20%_20%,#d8eaff,transparent_32%),radial-gradient(circle_at_80%_0%,#e7ecff,transparent_30%),radial-gradient(circle_at_30%_80%,#e4f5ff,transparent_28%)]"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-12 relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3">
                        @if($league->logo)
                            <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                                <img src="{{ Storage::url($league->logo) }}" alt="{{ $league->name }} Logo" class="w-full h-full object-cover">
                            </div>
                        @endif
                        <div>
                            <p class="uppercase tracking-[0.18em] text-xs font-semibold text-sky-700/80">Season {{ $league->season }}</p>
                            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black leading-tight text-slate-900">{{ $league->name }} Fixtures</h1>
                        </div>
                    </div>
                    <p class="text-slate-600 mt-2 text-sm sm:text-base">Clean, mobile-first fixtures list with quick context and marquee buys.</p>
                    <div class="flex items-center gap-3 mt-4 text-xs text-slate-600">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/80 border border-slate-200 shadow-sm text-slate-700">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Squads locked in
                        </span>
                        <a href="{{ route('leagues.show', $league) }}" class="underline decoration-dotted underline-offset-4 text-slate-700 hover:text-slate-900">Back to league</a>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    @if(auth()->check() && (auth()->user()->isOrganizerForLeague($league->id) || auth()->user()->isAdmin()))
                        <a href="{{ route('leagues.fixtures.edit', $league) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-full shadow-md hover:bg-slate-800 transition">
                            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path d="M4 13.5V16h2.5l7.06-7.06-2.5-2.5L4 13.5z" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.5 5l2.5 2.5M9 16h7" stroke-linecap="round" />
                            </svg>
                            Edit fixtures
                        </a>
                        <button onclick="openBulkScorerModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-full shadow-md hover:bg-indigo-700 transition">
                            <i class="fa-solid fa-users-gear"></i>
                            Bulk Assign Scorer
                        </button>
                    @endif
                    <div class="inline-flex items-center rounded-full border border-slate-200 bg-white shadow-sm overflow-hidden text-sm">
                        <a href="{{ route('leagues.fixtures', [$league, 'sort' => 'group']) }}" class="px-3 py-1.5 font-semibold {{ $sortMode === 'group' ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">Group order</a>
                        <a href="{{ route('leagues.fixtures', [$league, 'sort' => 'time']) }}" class="px-3 py-1.5 font-semibold {{ $sortMode === 'time' ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">Time</a>
                    </div>
                    <div class="inline-flex items-center rounded-full border border-slate-200 bg-white shadow-sm overflow-hidden text-sm" id="fixture-view-toggle">
                        <button type="button" class="fixture-view-btn px-3 py-1.5 font-semibold bg-gradient-to-r from-indigo-600 to-pink-500 text-white shadow" data-view-target="list">List</button>
                        <button type="button" class="fixture-view-btn px-3 py-1.5 font-semibold bg-white text-slate-700 hover:bg-slate-50" data-view-target="poster">Poster</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-8 space-y-10">
        <div id="fixture-view-list" class="space-y-8">
            @if($fixtures->count() > 0)
                @foreach($fixtures as $groupName => $groupFixtures)
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ $sortMode === 'time' ? 'Date' : 'Group' }}</p>
                                <h2 class="text-2xl font-bold text-slate-900">{{ $groupName }}</h2>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white border border-slate-200 text-slate-700 shadow-sm">
                                {{ $groupFixtures->count() }} {{ Str::plural('Match', $groupFixtures->count()) }}
                            </span>
                        </div>

                        <div class="grid gap-4">
                            @foreach($groupFixtures as $fixture)
                                @php
                                    $homeRetention = $retentionByTeam->get($fixture->home_team_id) ?? collect();
                                    $awayRetention = $retentionByTeam->get($fixture->away_team_id) ?? collect();
                                    $homeRetained = $homeRetention->isNotEmpty() ? $homeRetention->random() : null;
                                    $awayRetained = $awayRetention->isNotEmpty() ? $awayRetention->random() : null;
                                    $homeTopBuy = optional($topBoughtByTeam->get($fixture->home_team_id))->first();
                                    $awayTopBuy = optional($topBoughtByTeam->get($fixture->away_team_id))->first();
                                @endphp
                                <div class="rounded-2xl bg-white text-slate-900 shadow-xl ring-1 ring-slate-100 overflow-hidden">
                                    <div class="bg-gradient-to-r from-white to-sky-50 text-slate-700 px-4 py-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100">
                                        <div class="flex items-center gap-2">
                                            <span class="h-2 w-2 rounded-full {{ $fixture->status === 'completed' ? 'bg-emerald-500' : ($fixture->status === 'in_progress' ? 'bg-amber-400 animate-pulse' : 'bg-slate-300') }}"></span>
                                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-700">{{ ucfirst(str_replace('_',' ', $fixture->status)) }}</p>
                                            @if($fixture->match_type !== 'group_stage')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-white/80 border border-slate-200 text-[11px] font-semibold text-slate-700">
                                                    {{ Str::headline(str_replace('_',' ', $fixture->match_type)) }}
                                                </span>
                                            @endif
                                        </div>
                                        @php
                                            $kickoff = $fixture->match_date
                                                ? ($fixture->match_time
                                                    ? $fixture->match_date->format('M d') . ', ' . $fixture->match_time->format('H:i')
                                                    : $fixture->match_date->format('M d'))
                                                : 'TBD';
                                        @endphp
                                        <div class="text-xs text-slate-600 flex items-center gap-2 sm:justify-end">
                                            <span>{{ $kickoff }}</span>
                                            @if($fixture->venue)
                                                <span class="text-slate-400">•</span>
                                                <span class="inline-flex items-center gap-1">
                                                    <i class="fa-solid fa-location-dot text-slate-500"></i>
                                                <span class="break-words">{{ $fixture->venue }}</span>
                                                </span>
                                            @endif
                                            @if($fixture->scorer)
                                                <span class="text-slate-400">•</span>
                                                <span class="inline-flex items-center gap-1 text-indigo-600" title="Assigned Scorer">
                                                    <i class="fa-solid fa-user-pen"></i>
                                                    <span class="break-words font-medium">{{ $fixture->scorer->name }}</span>
                                                </span>
                                            @endif
                                        </div>
                                    @if(auth()->check())
                                        <div class="px-4 py-2 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                                            @if(auth()->user()->isOrganizerForLeague($league->id) || auth()->user()->isAdmin())
                                                <button onclick="openScorerModal('{{ $fixture->id }}', '{{ $fixture->slug }}')" class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded hover:bg-indigo-200">
                                                    Assign Scorer
                                                </button>
                                            @endif
                                            
                                            @if($fixture->scorer_id == auth()->id() || auth()->user()->isAdmin() || auth()->user()->isOrganizerForLeague($league->id))
                                                 <a href="{{ route('scorer.console', $fixture->slug) }}" class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded hover:bg-green-200">
                                                    Scorer Console
                                                </a>
                                            @endif
                                             <a href="{{ route('matches.live', $fixture->slug) }}" class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200">
                                                Live View
                                            </a>
                                        </div>
                                    @endif
                                    </div>

                                    <div class="p-4 sm:p-6 space-y-4">
                                        @php $hasScore = $fixture->home_score !== null || $fixture->away_score !== null; @endphp

                                        <div class="sm:hidden">
                                            <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-3 space-y-3">
                                                <div class="flex items-center justify-between text-[11px] font-semibold text-slate-500">
                                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">{{ $fixture->match_date?->format('M d') ?? 'Date TBD' }}</span>
                                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">{{ $kickoff }}</span>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-base overflow-hidden flex-shrink-0 ring-1 ring-emerald-200">
                                                        @if($fixture->homeTeam->team->logo)
                                                            <img src="{{ Storage::url($fixture->homeTeam->team->logo) }}" alt="{{ $fixture->homeTeam->team->name }} Logo" class="w-full h-full object-cover">
                                                        @else
                                                            {{ strtoupper(substr($fixture->homeTeam->team->name, 0, 2)) }}
                                                        @endif
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-semibold text-slate-900 leading-tight break-words line-clamp-2">{{ $fixture->homeTeam->team->name }}</p>
                                                        <p class="text-[11px] text-slate-500">Home</p>
                                                    </div>
                                                    <div class="flex-1 flex justify-center">
                                                        @if($hasScore)
                                                            <span class="text-xl font-black text-slate-900 tracking-tight">{{ $fixture->home_score ?? 0 }} - {{ $fixture->away_score ?? 0 }}</span>
                                                        @else
                                                            <span class="inline-flex items-center justify-center px-3 py-1.5 rounded-full bg-slate-900 text-[11px] font-semibold text-white shadow-sm">VS</span>
                                                        @endif
                                                    </div>
                                                    <div class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-base overflow-hidden flex-shrink-0 ring-1 ring-indigo-200">
                                                        @if($fixture->awayTeam->team->logo)
                                                            <img src="{{ Storage::url($fixture->awayTeam->team->logo) }}" alt="{{ $fixture->awayTeam->team->name }} Logo" class="w-full h-full object-cover">
                                                        @else
                                                            {{ strtoupper(substr($fixture->awayTeam->team->name, 0, 2)) }}
                                                        @endif
                                                    </div>
                                                    <div class="min-w-0 text-right">
                                                        <p class="text-sm font-semibold text-slate-900 leading-tight break-words line-clamp-2">{{ $fixture->awayTeam->team->name }}</p>
                                                        <p class="text-[11px] text-slate-500">Away</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="hidden sm:grid sm:grid-cols-5 gap-4 sm:gap-6 items-center">
                                            <div class="sm:col-span-2 flex flex-col sm:flex-row sm:items-center gap-3">
                                                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-lg overflow-hidden">
                                                    @if($fixture->homeTeam->team->logo)
                                                        <img src="{{ Storage::url($fixture->homeTeam->team->logo) }}" alt="{{ $fixture->homeTeam->team->name }} Logo" class="w-full h-full object-cover">
                                                    @else
                                                        {{ strtoupper(substr($fixture->homeTeam->team->name, 0, 2)) }}
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-lg font-bold text-slate-900">{{ $fixture->homeTeam->team->name }}</p>
                                                    <p class="text-xs text-slate-500">Home</p>
                                                </div>
                                            </div>

                                            <div class="text-center">
                                                @if($hasScore)
                                                    <div class="text-3xl font-black text-slate-900">{{ $fixture->home_score ?? 0 }} - {{ $fixture->away_score ?? 0 }}</div>
                                                    @if($fixture->status == 'completed')
                                                        @php
                                                            $winner = null;
                                                            if($fixture->toss_conducted && $fixture->toss_winner_team_id) {
                                                                $winner = $fixture->toss_winner_team_id;
                                                            } elseif($fixture->has_penalties && $fixture->penalty_winner_team_id) {
                                                                $winner = $fixture->penalty_winner_team_id;
                                                            } elseif($fixture->home_score > $fixture->away_score) {
                                                                $winner = $fixture->home_team_id;
                                                            } elseif($fixture->away_score > $fixture->home_score) {
                                                                $winner = $fixture->away_team_id;
                                                            }
                                                        @endphp
                                                        @if($winner)
                                                            <div class="text-xs font-bold text-amber-600 mt-1">
                                                                {{ $winner == $fixture->home_team_id ? $fixture->homeTeam->team->name : $fixture->awayTeam->team->name }} wins
                                                                @if($fixture->toss_conducted)<span class="text-[10px]">(Toss)</span>@elseif($fixture->has_penalties)<span class="text-[10px]">(Pens)</span>@endif
                                                            </div>
                                                        @else
                                                            <div class="text-xs font-bold text-slate-500 mt-1">Draw</div>
                                                        @endif
                                                    @endif
                                                @else
                                                    <div class="inline-flex items-center justify-center px-4 py-2 rounded-full bg-slate-100 text-xs font-semibold text-slate-600">VS</div>
                                                @endif
                                            </div>

                                            <div class="sm:col-span-2 flex flex-col sm:flex-row sm:items-center gap-3 sm:justify-end text-right">
                                                <div class="sm:order-2 w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-lg overflow-hidden">
                                                    @if($fixture->awayTeam->team->logo)
                                                        <img src="{{ Storage::url($fixture->awayTeam->team->logo) }}" alt="{{ $fixture->awayTeam->team->name }} Logo" class="w-full h-full object-cover">
                                                    @else
                                                        {{ strtoupper(substr($fixture->awayTeam->team->name, 0, 2)) }}
                                                    @endif
                                                </div>
                                                <div class="sm:order-1 text-left sm:text-right">
                                                    <p class="text-lg font-bold text-slate-900">{{ $fixture->awayTeam->team->name }}</p>
                                                    <p class="text-xs text-slate-500">Away</p>
                                                </div>
                                            </div>
                                        </div>

                                        @if($homeRetained || $awayRetained)
                                            <div class="pt-2 sm:pt-0 flex flex-col items-center gap-2 text-[11px] text-slate-600">
                                                <div class="flex items-center justify-center gap-3 sm:gap-4">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-10 h-10 rounded-full bg-slate-100 overflow-hidden flex items-center justify-center text-sm font-bold text-slate-700 ring-2 ring-emerald-200">
                                                            @if($homeRetained?->player?->photo)
                                                                <img src="{{ Storage::url($homeRetained->player->photo) }}" alt="{{ $homeRetained->player->name }}" class="w-full h-full object-cover">
                                                            @elseif($homeRetained)
                                                                {{ strtoupper(substr($homeRetained->player->name ?? 'P', 0, 2)) }}
                                                            @else
                                                                —
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <i class="fa-solid fa-xmark text-slate-500"></i>
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-10 h-10 rounded-full bg-slate-100 overflow-hidden flex items-center justify-center text-sm font-bold text-slate-700 ring-2 ring-indigo-200">
                                                            @if($awayRetained?->player?->photo)
                                                                <img src="{{ Storage::url($awayRetained->player->photo) }}" alt="{{ $awayRetained->player->name }}" class="w-full h-full object-cover">
                                                            @elseif($awayRetained)
                                                                {{ strtoupper(substr($awayRetained->player->name ?? 'P', 0, 2)) }}
                                                            @else
                                                                —
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="bg-white border border-slate-200 rounded-2xl p-10 text-center shadow-sm">
                    <div class="w-16 h-16 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-slate-900 text-2xl font-bold mb-2">No Fixtures Yet</h3>
                    <p class="text-slate-600 text-sm mb-4">Generate fixtures to showcase groups, retention cores, and marquee signings.</p>
                    @if(auth()->user()->isOrganizer() && $league->user_id === auth()->id())
                        <a href="{{ route('leagues.tournament-setup', $league) }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-sky-500 text-white font-semibold shadow-md hover:shadow-lg transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Setup Tournament
                        </a>
                    @endif
                </div>
            @endif
        </div>

        <div id="fixture-view-poster" class="hidden">
            @include('leagues.partials.matchday-poster', [
                'league' => $league,
                'fixturesByDate' => $posterFixturesByDate,
                'venueLabel' => optional($league->localBody)->name ?? ($league->venue_details ?? 'Venue TBA'),
                'seasonLabel' => $league->season ? 'Season ' . $league->season : 'Season',
                'emptyTitle' => 'No Fixtures Yet',
                'emptyDescription' => 'Add fixtures to generate the shareable matchday poster.',
                'emptyCtaRoute' => (auth()->check() && auth()->user()->isOrganizer() && $league->user_id === auth()->id())
                    ? route('leagues.tournament-setup', $league)
                    : null,
                'emptyCtaText' => (auth()->check() && auth()->user()->isOrganizer() && $league->user_id === auth()->id())
                    ? 'Setup Tournament'
                    : null,
            ])
        </div>
    </div>
    <!-- Scorer Assignment Modal -->
    <div id="scorer-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeScorerModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="scorer-form" method="POST" action="">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Assign Scorer</h3>
                                <div class="mt-2">
                                    <label for="scorer_id" class="block text-sm font-medium text-gray-700">Select Scorer</label>
                                    <select name="scorer_id" id="scorer_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        @foreach($scorers as $scorer)
                                            <option value="{{ $scorer->id }}">{{ $scorer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">Assign</button>
                        <button type="button" onclick="closeScorerModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Scorer Assignment Modal -->
    <div id="bulk-scorer-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeBulkScorerModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="bulk-scorer-form" method="POST" action="{{ route('leagues.fixtures.bulk-assign-scorer', $league) }}">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fa-solid fa-users-gear text-indigo-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Bulk Assign Scorer</h3>
                                <div class="mt-2 space-y-4">
                                    <div>
                                        <label for="bulk_scorer_id" class="block text-sm font-medium text-gray-700">Select Scorer</label>
                                        <select name="scorer_id" id="bulk_scorer_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            @foreach($scorers as $scorer)
                                                <option value="{{ $scorer->id }}">{{ $scorer->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Assignment Scope</label>
                                        <div class="mt-2 space-y-2">
                                            <div class="flex items-center">
                                                <input id="scope_unassigned" name="scope" type="radio" value="unassigned" checked class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                                <label for="scope_unassigned" class="ml-3 block text-sm font-medium text-gray-700">
                                                    Assign only to unassigned fixtures
                                                </label>
                                            </div>
                                            <div class="flex items-center">
                                                <input id="scope_all" name="scope" type="radio" value="all" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                                <label for="scope_all" class="ml-3 block text-sm font-medium text-gray-700">
                                                    Overwrite all fixtures
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">Assign Scorer</button>
                        <button type="button" onclick="closeBulkScorerModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openScorerModal(fixtureId, fixtureSlug) {
        const modal = document.getElementById('scorer-modal');
        const form = document.getElementById('scorer-form');
         // We might need to adjust the route based on how your named routes are set up. 
         // Assuming 'fixtures.assign-scorer' takes the fixture ID or slug.
         // Since the route is fixtures/{fixture}/assign-scorer, and {fixture} binds to ID or slug
        form.action = `/fixtures/${fixtureSlug}/assign-scorer`; 
        modal.classList.remove('hidden');
    }

    function closeScorerModal() {
        document.getElementById('scorer-modal').classList.add('hidden');
    }

    function openBulkScorerModal() {
        document.getElementById('bulk-scorer-modal').classList.remove('hidden');
    }

    function closeBulkScorerModal() {
        document.getElementById('bulk-scorer-modal').classList.add('hidden');
    }

document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('toggle-top-buys');
    const panel = document.getElementById('top-buys-panel');
    const icon = document.getElementById('top-buys-toggle-icon');
    const viewButtons = document.querySelectorAll('.fixture-view-btn');
    const listView = document.getElementById('fixture-view-list');
    const posterView = document.getElementById('fixture-view-poster');

    if (toggleBtn && panel && icon) {
        toggleBtn.addEventListener('click', () => {
            const isHidden = panel.classList.contains('hidden');
            panel.classList.toggle('hidden');
            icon.textContent = isHidden ? 'Hide' : 'Show';
        });
    }

    const setActiveFixtureButton = (target) => {
        viewButtons.forEach((btn) => {
            const isActive = btn.dataset.viewTarget === target;
            btn.classList.toggle('bg-gradient-to-r', isActive);
            btn.classList.toggle('from-indigo-600', isActive);
            btn.classList.toggle('to-pink-500', isActive);
            btn.classList.toggle('text-white', isActive);
            btn.classList.toggle('shadow', isActive);
            btn.classList.toggle('bg-white', !isActive);
            btn.classList.toggle('text-slate-700', !isActive);
            btn.classList.toggle('hover:bg-slate-50', !isActive);
        });
    };

    viewButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.viewTarget;
            if (!target) {
                return;
            }
            if (listView) {
                listView.classList.toggle('hidden', target !== 'list');
            }
            if (posterView) {
                posterView.classList.toggle('hidden', target !== 'poster');
            }
            setActiveFixtureButton(target);
        });
    });

    if (viewButtons.length > 0) {
        setActiveFixtureButton('list');
    }
});
</script>
@endsection
