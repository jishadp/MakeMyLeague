<section class="panel-shell alt">
    <div class="panel-inner">
        <div class="panel-heading">
            <p class="panel-pill">Featured Teams</p>
            <h2>Clubs and franchises ready for auction</h2>
            <p>{{ $featuredTeams->isEmpty() ? 'Invite teams to appear on your public landing page.' : 'Highlighted rosters from recent auctions.' }}</p>
        </div>

        <div class="card-scroll">
            <div class="card-track">
                @forelse($featuredTeams as $team)
                    <article class="glass-card">
                        <div class="glass-thumb">
                            @if($team->logo)
                                <img src="{{ asset($team->logo) }}" alt="{{ $team->name }}">
                            @else
                                <div class="thumb-placeholder">No logo</div>
                            @endif
                        </div>
                        <div class="glass-body">
                            <h3>{{ $team->name }}</h3>
                            <p class="meta">{{ optional($team->localBody)->name }}, {{ optional($team->homeGround?->district)->name }}</p>
                            <p class="detail">Home ground: {{ optional($team->homeGround)->name ?? 'TBA' }}</p>
                            <a href="{{ route('teams.show', $team) }}" class="glass-link">View team →</a>
                        </div>
                    </article>
                @empty
                    <article class="glass-card text-center">
                        <p class="meta">No featured teams yet. Add teams through the dashboard.</p>
                    </article>
                @endforelse
            </div>
        </div>

        <div class="panel-cta">
            <a href="{{ route('teams.index') }}" class="panel-btn">See all teams</a>
        </div>
    </div>
</section>
