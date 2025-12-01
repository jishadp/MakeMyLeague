@if(isset($upcomingFixtures) && $upcomingFixtures->isNotEmpty())
<section class="py-12 sm:py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-3 mb-6 sm:mb-8">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-600">Fixtures</p>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900">Upcoming Matches</h2>
                <p class="text-sm text-slate-600">Quick glance at what is coming up next.</p>
            </div>
            <a href="{{ route('leagues.fixtures', optional($upcomingFixtures->first())->league) }}" class="hidden sm:inline-flex items-center px-4 py-2 rounded-xl bg-white border border-slate-200 text-sm font-semibold text-slate-800 shadow-sm hover:border-slate-300">
                View all fixtures
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($upcomingFixtures as $fixture)
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-4 sm:p-5 flex flex-col gap-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">{{ $fixture->league->name }}</p>
                        <h3 class="text-lg font-bold text-slate-900 leading-tight break-words">
                            {{ $fixture->homeTeam?->team?->name }} vs {{ $fixture->awayTeam?->team?->name }}
                        </h3>
                        @if($fixture->leagueGroup)
                            <p class="text-xs text-slate-500">Group {{ $fixture->leagueGroup->name }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-500">Date</p>
                        <p class="text-sm font-semibold text-slate-900">
                            {{ optional($fixture->match_date)->format('M d') ?? 'TBD' }}
                        </p>
                        <p class="text-xs text-slate-500">
                            {{ $fixture->match_time ? \Carbon\Carbon::parse($fixture->match_time)->format('h:i A') : 'Time TBD' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-sm text-slate-600">
                    <span class="inline-flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Scheduled
                    </span>
                    @if($fixture->venue)
                        <span class="truncate text-right">📍 {{ $fixture->venue }}</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <a href="{{ route('leagues.fixtures', optional($upcomingFixtures->first())->league) }}" class="sm:hidden mt-6 inline-flex items-center justify-center w-full px-4 py-2 rounded-xl bg-white border border-slate-200 text-sm font-semibold text-slate-800 shadow-sm hover:border-slate-300">
            View all fixtures
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>
</section>
@endif
