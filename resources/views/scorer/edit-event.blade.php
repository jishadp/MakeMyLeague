@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 py-8 px-4">
    <div class="max-w-lg mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
        
        <!-- Header -->
        <div class="p-6 border-b border-slate-100 bg-white flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Edit Goal</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Fixture: <span class="font-bold text-slate-700">{{ $fixture->homeTeam->team->name }} vs {{ $fixture->awayTeam->team->name }}</span>
                </p>
            </div>
            <a href="{{ route('scorer.console', $fixture) }}" class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-200 transition-colors">
                <i class="fa-solid fa-times"></i>
            </a>
        </div>

        <form action="{{ route('scorer.event.update', ['fixture' => $fixture, 'event' => $event]) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="p-6 space-y-6">
                
                <!-- Event Details Badge -->
                <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="w-12 h-12 rounded-lg bg-white border border-slate-200 flex items-center justify-center font-mono font-bold text-lg text-slate-600 shadow-sm">
                        {{ $event->minute }}'
                    </div>
                    <div>
                        <div class="font-bold text-sm text-slate-900 uppercase tracking-wide">
                            {{ str_replace('_', ' ', $event->event_type) }}
                        </div>
                        <div class="text-xs text-slate-500">
                            Team: <span class="font-bold">{{ $team->team->name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Scorer Selection -->
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Scorer</label>
                    <div class="relative">
                        <select name="player_id" class="w-full appearance-none p-4 rounded-xl bg-white border border-slate-200 text-slate-900 font-bold focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                            <option value="">Unknown / Custom Name</option>
                            @foreach($players as $p)
                                <option value="{{ $p->id }}" {{ $event->player_id == $p->player_id ? 'selected' : '' }}>
                                    {{ $p->player->user->name ?? $p->custom_name }} (#{{ $p->player->jersey_number ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                    </div>
                    @if($event->event_type == 'OWN_GOAL')
                        <p class="mt-2 text-xs text-rose-500 font-medium">
                            <i class="fa-solid fa-circle-info mr-1"></i> For Own Goals, select the opponent player who scored.
                        </p>
                    @endif
                </div>

                <!-- Assist Selection (Hidden for Own Goals usually, but let's keep it conditional) -->
                @if($event->event_type == 'GOAL')
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Assist</label>
                    <div class="relative">
                        <select name="assist_player_id" required class="w-full appearance-none p-4 rounded-xl bg-white border border-slate-200 text-slate-900 font-bold focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                            <option value="">Select Assist</option>
                            @foreach($players as $p)
                                <option value="{{ $p->id }}" {{ $event->assist_player_id == $p->player_id ? 'selected' : '' }}>
                                    {{ $p->player->user->name ?? $p->custom_name }} (#{{ $p->player->jersey_number ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                    </div>
                </div>
                @endif

            </div>

            <!-- Actions -->
            <div class="p-6 border-t border-slate-100 bg-slate-50 grid grid-cols-2 gap-4">
                <a href="{{ route('scorer.console', $fixture) }}" class="py-4 rounded-xl font-bold text-center text-slate-500 hover:bg-white hover:shadow-sm hover:text-slate-700 transition-all border border-transparent hover:border-slate-200">
                    Cancel
                </a>
                <button type="submit" class="py-4 rounded-xl bg-blue-600 hover:bg-blue-700 font-bold shadow-lg shadow-blue-200 transition-all transform active:scale-95">
                    Update Goal
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
