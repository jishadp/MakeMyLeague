@extends('layouts.app')

@section('title', $leagueTeam->team->name . ' Poster')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 py-6 px-3 sm:py-10 sm:px-6">
    <div class="max-w-4xl mx-auto space-y-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('posters.list') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white/80 hover:text-white hover:border-white/40 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to posters
            </a>
        </div>

        <div id="poster" class="relative overflow-hidden rounded-[32px] border border-white/10 bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-900 shadow-[0_25px_90px_rgba(15,23,42,0.75)]">
            <div class="pointer-events-none absolute inset-0 opacity-70">
                <div class="absolute -top-32 right-0 h-64 w-64 bg-pink-500/30 blur-[120px]"></div>
                <div class="absolute bottom-0 left-0 h-72 w-72 bg-indigo-500/40 blur-[120px]"></div>
            </div>

            <div class="relative z-10 space-y-8 p-6 sm:p-10">
                <div class="hidden sm:flex sm:flex-col sm:gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="space-y-3">
                        <p class="text-xs uppercase tracking-[0.4em] text-slate-300">{{ $league->name }}</p>
                        <h1 class="text-4xl font-black text-white sm:text-5xl">{{ $leagueTeam->team->name }}</h1>
                        <p class="text-sm text-white/80">{{ $leagueTeam->team->homeGround->name ?? 'Home Ground TBD' }} • Season {{ $league->season ?? date('Y') }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="rounded-3xl border border-white/10 bg-white/10 p-3 backdrop-blur">
                            @if($league->logo)
                                <img src="{{ Storage::url($league->logo) }}" class="h-14 w-14 object-contain" loading="lazy" crossorigin="anonymous">
                            @else
                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 text-xl font-bold text-white">
                                    {{ strtoupper(substr($league->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="rounded-full border border-white/20 bg-white/10 p-3 backdrop-blur">
                            @if($leagueTeam->team->logo)
                                <img src="{{ Storage::url($leagueTeam->team->logo) }}" class="h-20 w-20 rounded-full object-cover" loading="lazy" crossorigin="anonymous">
                            @else
                                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-2xl font-black text-white">
                                    {{ strtoupper(substr($leagueTeam->team->name, 0, 2)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="sm:hidden space-y-4">
                    <div class="flex items-center gap-3">
                        @if($leagueTeam->team->logo)
                            <img src="{{ Storage::url($leagueTeam->team->logo) }}" class="h-16 w-16 rounded-2xl object-cover border border-white/20" loading="lazy" crossorigin="anonymous">
                        @else
                            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-xl font-black text-white border border-white/20">
                                {{ strtoupper(substr($leagueTeam->team->name, 0, 2)) }}
                            </div>
                        @endif
                        <div>
                            <p class="text-xs uppercase tracking-[0.4em] text-slate-300">{{ $league->name }}</p>
                            <h1 class="text-2xl font-black text-white">{{ $leagueTeam->team->name }}</h1>
                            <p class="text-xs text-white/70">{{ $leagueTeam->team->homeGround->name ?? 'Home Ground TBD' }}</p>
                        </div>
                    </div>
                </div>

                @if($owner)
                    <div class="flex items-center gap-3 rounded-2xl border border-amber-200/40 bg-amber-100/10 px-5 py-3 text-amber-100">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a6 6 0 00-6 6v2.586l-.707.707A1 1 0 004 13h12a1 1 0 00.707-1.707L16 10.586V8a6 6 0 00-6-6z"/>
                            <path d="M15 13v1a3 3 0 01-3 3H8a3 3 0 01-3-3v-1h10z"/>
                        </svg>
                        <div>
                            <p class="text-xs uppercase tracking-[0.35em]">Team Owner</p>
                            <p class="text-base font-semibold text-white">{{ $owner->user->name }}</p>
                        </div>
                    </div>
                @endif

                <div class="hidden sm:grid sm:grid-cols-3 sm:gap-2">
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4 text-white text-center">
                        <p class="text-[10px] uppercase tracking-[0.35em] text-slate-400 mb-2">Squad Size</p>
                        <p class="text-3xl font-black">{{ $players->count() }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4 text-white text-center">
                        <p class="text-[10px] uppercase tracking-[0.35em] text-slate-400 mb-2">League</p>
                        <p class="text-sm font-bold">{{ $league->short_name ?? $league->name }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4 text-white text-center">
                        <p class="text-[10px] uppercase tracking-[0.35em] text-slate-400 mb-2">Season</p>
                        <p class="text-2xl font-black">{{ $league->season ?? date('Y') }}</p>
                    </div>
                </div>

                @php
                    $retainedPlayers = $players->filter(fn($player) => $player->retention);
                    $nonRetainedPlayers = $players->reject(fn($player) => $player->retention);
                @endphp

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Squad</p>
                            <h2 class="text-2xl font-bold text-white">Featured Players</h2>
                        </div>
                        <div class="flex h-px flex-1 bg-gradient-to-r from-transparent via-white/40 to-transparent"></div>
                    </div>

                    @if($players->isEmpty())
                        <div class="rounded-2xl border border-dashed border-white/20 py-10 text-center text-white/60">
                            Squad will be revealed soon.
                        </div>
                    @else
                        @if($retainedPlayers->isNotEmpty())
                            <div class="rounded-2xl border border-amber-200/20 bg-amber-200/5 p-4">
                                <p class="text-xs uppercase tracking-[0.35em] text-amber-200 mb-3">Retained Stars</p>
                                <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 lg:grid-cols-5">
                                    @foreach($retainedPlayers as $player)
                                        @php
                                            $photoUrl = $player->user->photo
                                                ? asset('storage/' . $player->user->photo)
                                                : asset('images/defaultplayer.jpeg');
                                        @endphp
                                        <div class="group rounded-2xl border border-white/10 bg-white/5 p-3 text-white transition hover:-translate-y-1 hover:border-amber-300/70">
                                            <div class="relative mb-2">
                                                <img src="{{ $photoUrl }}" alt="{{ $player->user->name }}" class="w-full aspect-square rounded-xl object-cover" loading="lazy" crossorigin="anonymous">
                                                <span class="absolute top-2 left-2 rounded-full bg-amber-300/90 px-2 py-0.5 text-[9px] font-semibold uppercase tracking-[0.2em] text-slate-900">Retained</span>
                                            </div>
                                            <p class="text-xs font-semibold leading-tight text-center sm:text-sm">{{ $player->user->name }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 lg:grid-cols-5">
                            @foreach($nonRetainedPlayers as $player)
                                @php
                                    $photoUrl = $player->user->photo
                                        ? asset('storage/' . $player->user->photo)
                                        : asset('images/defaultplayer.jpeg');
                                @endphp
                                <div class="group rounded-2xl border border-white/10 bg-white/5 p-3 text-white shadow-lg shadow-indigo-950/30 transition hover:-translate-y-1 hover:border-indigo-400/60">
                                    <div class="relative mb-3">
                                        <img src="{{ $photoUrl }}" alt="{{ $player->user->name }}" class="w-full aspect-square rounded-xl object-cover" loading="lazy" crossorigin="anonymous">
                                        <div class="absolute inset-0 rounded-xl bg-gradient-to-t from-slate-950/60 via-transparent"></div>
                                    </div>
                                    <p class="text-sm font-semibold leading-tight sm:text-lg">{{ $player->user->name }}</p>
                                    @if($player->retention)
                                        <p class="text-xs uppercase tracking-[0.35em] text-amber-300">Retained</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-between rounded-2xl border border-white/5 bg-white/5 px-5 py-3 text-sm text-white/70">
                    <span>{{ $league->name }} • Powered by MakeMyLeague</span>
                    <span class="text-white/40">{{ now()->format('M d, Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
