<section class="panel-shell">
    <div class="panel-inner">
        <div class="panel-heading">
            <p class="panel-pill">Premier Cricket Grounds</p>
            <h2>Grounds ready for your fixtures</h2>
            <p>{{ $featuredGrounds->isEmpty() ? 'Add your ground to showcase it to league organizers.' : 'Latest ' . $featuredGrounds->count() . ' grounds synced by organizers this week.' }}</p>
        </div>

        <div class="card-scroll">
            <div class="card-track">
                @forelse($featuredGrounds as $ground)
                    <article class="glass-card">
                        <div class="glass-thumb">
                            @if($ground->image)
                                <img src="{{ asset($ground->image) }}" alt="{{ $ground->name }}">
                            @else
                                <div class="thumb-placeholder">No image</div>
                            @endif
                        </div>
                        <div class="glass-body">
                            <h3>{{ $ground->name }}</h3>
                            <p class="meta">{{ optional($ground->localBody)->name }}, {{ optional($ground->district)->name }}</p>
                            @if($ground->capacity)
                                <p class="detail">Capacity: {{ number_format($ground->capacity) }}</p>
                            @endif
                            <a href="{{ route('grounds.show', $ground) }}" class="glass-link">View details →</a>
                        </div>
                    </article>
                @empty
                    <article class="glass-card text-center">
                        <p class="meta">No public grounds yet. Log in and add your venue to make it discoverable.</p>
                    </article>
                @endforelse
            </div>
        </div>

        <div class="panel-cta">
            <a href="{{ route('grounds.index') }}" class="panel-btn">Browse all grounds</a>
        </div>
    </div>
</section>

<style>
    .panel-shell {
        background: radial-gradient(circle at top, rgba(8, 16, 48, 0.95), #020616);
        padding: 4rem 1rem;
        color: #e7edff;
    }
    .panel-inner {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }
    .panel-heading {
        text-align: center;
        max-width: 720px;
        margin: 0 auto;
    }
    .panel-pill {
        display: inline-flex;
        padding: 0.35rem 1rem;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        letter-spacing: 0.25em;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: rgba(231, 237, 255, 0.75);
        margin-bottom: 1rem;
    }
    .panel-heading h2 {
        font-size: clamp(2rem, 4vw, 3.5rem);
        font-weight: 700;
        margin-bottom: 0.75rem;
    }
    .panel-heading p {
        color: rgba(231, 237, 255, 0.75);
    }
    .card-scroll {
        overflow-x: auto;
        padding-bottom: 1rem;
    }
    .card-track {
        display: flex;
        gap: 1.2rem;
        min-width: 100%;
    }
    .glass-card {
        min-width: 260px;
        background: rgba(5, 10, 32, 0.9);
        border-radius: 26px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        display: flex;
        flex-direction: column;
    }
    .glass-thumb {
        height: 170px;
        background: rgba(255, 255, 255, 0.05);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .glass-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .thumb-placeholder {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.2em;
    }
    .glass-body {
        padding: 1.4rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .glass-body h3 {
        font-size: 1.25rem;
        font-weight: 600;
    }
    .meta {
        color: rgba(231, 237, 255, 0.7);
        font-size: 0.95rem;
    }
    .detail {
        color: rgba(231, 237, 255, 0.6);
        font-size: 0.85rem;
    }
    .glass-link {
        margin-top: auto;
        color: #7dd3fc;
        font-weight: 600;
    }
    .panel-cta {
        text-align: center;
    }
    .panel-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.9rem 2rem;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #e7edff;
        font-weight: 600;
    }
</style>
