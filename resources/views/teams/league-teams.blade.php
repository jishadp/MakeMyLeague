@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
<div class="min-h-screen bg-slate-50 py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="space-y-1">
                <a href="{{ route('teams.index') }}" class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Teams
                </a>
                <h1 class="text-3xl font-bold text-slate-900">Leagues by Game</h1>
                <p class="text-slate-600">Browse leagues that have registered teams, organized under each game. Pick a league card to view its teams.</p>
            </div>
            <a href="{{ route('teams.league-players') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg shadow hover:bg-indigo-700 transition">
                View League Players →
            </a>
        </div>

        @forelse($groupedLeagues as $gameName => $leagues)
            <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 sm:px-6 py-4 flex items-center justify-between gap-3 flex-wrap bg-slate-50">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Game</p>
                        <h2 class="text-xl font-bold text-slate-900 mt-1">{{ $gameName }}</h2>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-700">
                        {{ $leagues->count() }} {{ Str::plural('league', $leagues->count()) }}
                    </span>
                </div>

                <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($leagues as $league)
                        @php
                            $teamCount = $league->leagueTeams->count();
                            $startLabel = $league->start_date ? $league->start_date->format('M j, Y') : 'Date TBA';
                        @endphp
                        <div class="group rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-lg transition-shadow duration-300">
                            <div class="p-5 space-y-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="space-y-1">
                                        <p class="text-[11px] font-semibold uppercase tracking-wide text-indigo-600">League</p>
                                        <h3 class="text-lg font-bold text-slate-900 leading-snug">{{ $league->name }}</h3>
                                        <p class="text-sm text-slate-600">Season {{ $league->season ?? 'N/A' }}</p>
                                        <p class="text-xs text-slate-500 mt-1">
                                            Slug: <code class="text-slate-700">{{ $league->slug }}</code>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2 py-1 text-[11px] font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            {{ ucfirst($league->status ?? 'active') }}
                                        </span>
                                        <p class="text-xs text-slate-500 mt-2">Starts {{ $startLabel }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center flex-wrap gap-2 text-xs text-slate-600">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100 text-slate-700">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16m10-9l-7 7-3-3"></path>
                                        </svg>
                                        {{ $teamCount }} {{ Str::plural('team', $teamCount) }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100 text-slate-700">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"></path>
                                        </svg>
                                        Season {{ $league->season ?? '—' }}
                                    </span>
                                </div>
                            </div>
                            <div class="px-5 pb-5 flex items-center justify-between gap-3">
                                <div class="text-xs text-slate-500">
                                    Updated {{ $league->updated_at?->diffForHumans() ?? 'recently' }}
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('leagues.public-teams', $league) }}" class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg shadow hover:bg-indigo-700 transition">
                                        View Teams
                                    </a>
                                    <a href="{{ route('leagues.fixtures', $league) }}" class="inline-flex items-center px-3 py-2 text-sm font-semibold text-indigo-700 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition">
                                        Fixtures
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="bg-white border border-dashed border-slate-200 rounded-2xl p-10 text-center">
                <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8a9 9 0 110-18 9 9 0 010 18z"></path>
                    </svg>
                </div>
                <p class="text-slate-600 font-semibold">No leagues with teams yet.</p>
                <p class="text-slate-500 text-sm mt-1">Create a league and add teams to see them listed here.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
