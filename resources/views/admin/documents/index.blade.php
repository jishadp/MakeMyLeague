@extends('layouts.app')

@section('title', 'Admin - Document Center | ' . config('app.name'))

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-indigo-50">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.4em] text-indigo-500 font-semibold">Document Center</p>
                    <h1 class="text-3xl font-bold text-slate-900 mt-2">Printable Player Cards</h1>
                    <p class="text-slate-500 mt-3">
                        Export polished PDFs for any registered player. Filter by league or search by name to curate a list of players that need shareable paperwork.
                    </p>
                </div>
                <a href="{{ route('admin.players.index') }}" class="inline-flex items-center px-5 py-3 rounded-xl border border-slate-200 text-slate-700 font-medium hover:border-indigo-200 hover:text-indigo-600 transition-colors">
                    <svg class="w-4 h-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 15.707a1 1 0 01-1.414 0L6 10.414l5.293-5.293a1 1 0 011.414 1.414L8.414 10l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                    </svg>
                    Back to User Management
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-md p-6 border border-slate-100">
            <form method="GET" class="grid grid-cols-1 lg:grid-cols-6 gap-4 items-end">
                <div class="lg:col-span-3">
                    <label class="block text-sm font-semibold text-slate-600 tracking-wide uppercase mb-2">Search Players</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ old('search', $filters['search'] ?? '') }}" placeholder="Search by name, phone, or email" class="w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-100 pl-11">
                        <svg class="w-5 h-5 text-slate-400 absolute inset-y-0 left-3 my-auto" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.9 14.32a8 8 0 111.414-1.414l4.1 4.1a1 1 0 01-1.414 1.415l-4.1-4.1zM14 8a6 6 0 11-12 0 6 6 0 0112 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-sm font-semibold text-slate-600 tracking-wide uppercase mb-2">Filter by League</label>
                    <select name="league_id" class="w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-100">
                        <option value="">All leagues</option>
                        @foreach($leagues as $league)
                            <option value="{{ $league->id }}" @selected(($filters['league_id'] ?? null) == $league->id)>{{ $league->name }} · S{{ $league->season }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-1 flex gap-3">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-3 rounded-xl bg-indigo-600 text-indigo-50 font-semibold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-colors">
                        Apply
                    </button>
                    <a href="{{ route('admin.documents.index') }}" class="inline-flex items-center justify-center px-4 py-3 rounded-xl border border-slate-200 text-slate-600 hover:border-indigo-200 hover:text-indigo-600 transition-colors">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        @php
            $query = array_filter([
                'search' => $filters['search'] ?? null,
                'league_id' => $filters['league_id'] ?? null,
            ], fn ($value) => filled($value));
        @endphp

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
            <div class="px-6 py-4 border-b border-slate-100 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-indigo-500">Players</p>
                    <h2 class="text-xl font-semibold text-slate-900">{{ number_format($players->total()) }} printable profiles</h2>
                </div>
                <p class="text-sm text-slate-500">
                    Select any player to preview their printable profile and export a polished PDF instantly.
                </p>
            </div>

            @if($players->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L7.5 20.5H4v-3.5L16.732 3.732z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900">No players found</h3>
                    <p class="text-slate-500 max-w-lg mx-auto mt-2">
                        Try adjusting the filters or search keyword. Every registered player can be exported as a PDF once they appear in this list.
                    </p>
                </div>
            @else
                <div class="p-6 grid gap-6 grid-cols-1 lg:grid-cols-2">
                    @foreach($players as $player)
                        @php
                            $leaguesForCard = $player->leaguePlayers
                                ->map(fn ($leaguePlayer) => optional($leaguePlayer->league)->name)
                                ->filter()
                                ->unique()
                                ->values();
                            $previewRoute = route('admin.documents.players.show', array_merge(['player' => $player->slug], $query));
                            $downloadRoute = route('admin.documents.players.download', array_merge(['player' => $player->slug], $query));
                            $photoUrl = $player->photo ? asset('storage/' . $player->photo) : asset('images/defaultplayer.jpeg');
                            $photoPath = $player->photo;
                            if ($photoPath) {
                                $photoUrl = \Illuminate\Support\Str::startsWith($photoPath, ['http://', 'https://'])
                                    ? $photoPath
                                    : asset('storage/' . ltrim($photoPath, '/'));
                            } else {
                                $photoUrl = asset('images/defaultplayer.jpeg');
                            }
                        @endphp
                        <div class="border border-slate-200 rounded-2xl p-5 hover:border-indigo-200 transition-colors bg-gradient-to-b from-white to-slate-50/60">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-2xl overflow-hidden bg-indigo-100 flex items-center justify-center shadow-inner">
                                    <img src="{{ $photoUrl }}" alt="{{ $player->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="hidden w-full h-full items-center justify-center text-indigo-600 font-semibold text-xl">
                                        {{ strtoupper(substr($player->name, 0, 2)) }}
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.35em] text-slate-400 font-semibold">Player #{{ $player->id }}</p>
                                    <h3 class="text-xl font-bold text-slate-900">{{ $player->name }}</h3>
                                    <p class="text-sm text-slate-500">
                                        {{ optional($player->position)->name ?? 'Role N/A' }} · {{ optional($player->localBody)->name ?? 'No Local Body' }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-slate-600">
                                <div class="space-y-1">
                                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400 font-semibold">Contact</p>
                                    <p class="font-semibold">{{ $player->formatted_phone_number ?? $player->mobile ?? 'Not provided' }}</p>
                                    <p>{{ $player->email ?? 'No email' }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400 font-semibold">Leagues</p>
                                    @if($leaguesForCard->isEmpty())
                                        <p class="font-semibold text-slate-500">No assignments yet</p>
                                    @else
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($leaguesForCard->take(2) as $leagueName)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 font-semibold text-xs">
                                                    {{ $leagueName }}
                                                </span>
                                            @endforeach
                                            @if($leaguesForCard->count() > 2)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-slate-600 font-semibold text-xs">
                                                    +{{ $leaguesForCard->count() - 2 }} more
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-6 flex flex-col sm:flex-row gap-3">
                                <a href="{{ $previewRoute }}" class="flex-1 inline-flex items-center justify-center px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold hover:border-indigo-200 hover:text-indigo-600 transition-colors">
                                    Preview Printable
                                </a>
                                <a href="{{ $downloadRoute }}" class="flex-1 inline-flex items-center justify-center px-4 py-3 rounded-xl bg-indigo-600 text-indigo-50 font-semibold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-colors">
                                    Download PDF
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $players->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
