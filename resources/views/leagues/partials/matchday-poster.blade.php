@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $fixturesByDate = collect($fixturesByDate ?? collect())
        ->map(fn ($group) => collect($group)->values())
        ->filter(fn ($group) => $group->count() > 0);

    $posterFixtureCount = $fixturesByDate->flatten()->count();
    $seasonText = $seasonLabel ?? ($league->season ? $league->season : 'Season');
    $venueText = $venueLabel ?? optional($league->localBody)->name ?? ($league->venue_details ?? 'Venue TBA');
    $emptyTitle = $emptyTitle ?? 'Fixtures TBA';
    $emptyDescription = $emptyDescription ?? 'Add fixtures to populate this poster.';
    $containerClass = $containerClass ?? 'space-y-8';
    $leagueSubtitle = $leagueSubtitle ?? null;
    $posterDomId = 'fixture-poster-' . uniqid();

    $posterDays = $fixturesByDate->mapWithKeys(function ($dayFixtures, $dateLabel) {
        $groupedFixtures = collect($dayFixtures);
        $firstFixture = $groupedFixtures->first();
        $matchDate = $firstFixture?->match_date;
        $fullDate = $matchDate
            ? Str::upper($matchDate->format('j M Y'))
            : ($dateLabel ?: 'DATE TBA');
        $key = Str::slug($fullDate ?: uniqid());

        return [$dateLabel => [
            'key' => $key,
            'label' => $fullDate,
        ]];
    });
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap');
    
    .fixture-poster-container {
        --poster-primary: #4b5320;
        --poster-primary-strong: #3f4a1f;
        --poster-primary-contrast: #2f3313;
        --poster-primary-soft: #6f8f35;
        --poster-primary-soft-hover: #617a2d;
        --poster-primary-pop: #fbbf24;
        font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, var(--poster-primary) 0%, var(--poster-primary-strong) 50%, var(--poster-primary) 100%);
        min-height: auto;
        width: 100%;
        position: relative;
        overflow: hidden;
        padding: 10px 0 20px;
        margin: 0;
    }

    .fixture-theme-sky {
        --poster-primary: #0ea5e9;
        --poster-primary-strong: #0284c7;
        --poster-primary-contrast: #0b3651;
        --poster-primary-soft: #38bdf8;
        --poster-primary-soft-hover: #0ea5e9;
        --poster-primary-pop: #fff1a6;
    }

    .fixture-theme-red {
        --poster-primary: #b91c1c;
        --poster-primary-strong: #991b1b;
        --poster-primary-contrast: #4c0e0e;
        --poster-primary-soft: #ef4444;
        --poster-primary-soft-hover: #dc2626;
        --poster-primary-pop: #fde68a;
    }

    .fixture-theme-orange {
        --poster-primary: #ea580c;
        --poster-primary-strong: #c2410c;
        --poster-primary-contrast: #7c2d12;
        --poster-primary-soft: #f97316;
        --poster-primary-soft-hover: #ea580c;
        --poster-primary-pop: #fff3bf;
    }

    .fixture-theme-green {
        --poster-primary: #4b5320;
        --poster-primary-strong: #3f4a1f;
        --poster-primary-contrast: #2f3313;
        --poster-primary-soft: #6f8f35;
        --poster-primary-soft-hover: #617a2d;
        --poster-primary-pop: #fbbf24;
    }

    .fixture-theme-purple {
        --poster-primary: #6d28d9;
        --poster-primary-strong: #5b21b6;
        --poster-primary-contrast: #2e1065;
        --poster-primary-soft: #a855f7;
        --poster-primary-soft-hover: #8b5cf6;
        --poster-primary-pop: #fde68a;
    }

    .fixture-theme-teal {
        --poster-primary: #0d9488;
        --poster-primary-strong: #0f766e;
        --poster-primary-contrast: #0b4240;
        --poster-primary-soft: #2dd4bf;
        --poster-primary-soft-hover: #14b8a6;
        --poster-primary-pop: #e0f2fe;
    }

    .fixture-theme-slate {
        --poster-primary: #1e293b;
        --poster-primary-strong: #0f172a;
        --poster-primary-contrast: #0b1220;
        --poster-primary-soft: #334155;
        --poster-primary-soft-hover: #1f2937;
        --poster-primary-pop: #cbd5e1;
    }
    
    .fixture-poster-bg-pattern {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        opacity: 0.1;
        background-image: 
            radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    }
    
    .fixture-poster-diagonal-accent {
        position: absolute;
        top: -100px;
        left: -50%;
        width: 150%;
        height: 300px;
        background: linear-gradient(135deg, transparent 45%, rgba(255, 255, 255, 0.05) 45%, rgba(255, 255, 255, 0.05) 55%, transparent 55%);
        transform: rotate(-15deg);
    }
    
    .fixture-poster-content {
        position: relative;
        z-index: 10;
        max-width: 800px;
        margin: 0 auto;
        padding: 40px 20px;
    }
    
    .fixture-poster-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        text-align: left;
        margin-bottom: 28px;
        position: relative;
    }

    .fixture-poster-day-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: center;
        margin: 0 auto 18px;
        max-width: 680px;
    }

    .fixture-poster-day-tab {
        background: rgba(255, 255, 255, 0.12);
        color: #fbbf24;
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 999px;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .fixture-poster-day-tab.is-active {
        background: #fbbf24;
        color: var(--poster-primary-contrast);
        border-color: #fbbf24;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.35);
    }

    .fixture-theme-switch {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-start;
        margin: 0 0 16px;
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
    }

    .fixture-theme-label {
        font-size: 11px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .fixture-theme-chip {
        padding: 6px 10px;
        border-radius: 999px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #0f172a;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .fixture-theme-chip.is-active {
        background: var(--poster-primary);
        color: white;
        border-color: rgba(0, 0, 0, 0.15);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
    }
    
    .fixture-poster-header-logo {
        flex-shrink: 0;
    }

    .fixture-poster-logo-container {
        width: 80px;
        height: 80px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        background: transparent;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        border: 0;
        overflow: hidden;
    }
    
    .fixture-poster-logo-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: inherit;
    }
    
    .fixture-poster-logo-placeholder {
        font-size: 24px;
        font-weight: 900;
        color: var(--poster-primary);
    }
    
    .fixture-poster-title-block {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 15px;
        padding: 18px 20px;
        border: 2px solid rgba(255, 255, 255, 0.2);
        flex: 1;
    }
    
    .fixture-poster-season-label {
        font-size: 13px;
        font-weight: 700;
        color: #fef3c7;
        text-transform: uppercase;
        letter-spacing: 3px;
        margin-bottom: 6px;
    }
    
    .fixture-poster-league-name {
        font-size: 32px;
        font-weight: 900;
        color: white;
        text-transform: uppercase;
        line-height: 1.1;
        margin-bottom: 8px;
        text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
    }
    
    .fixture-poster-subtitle {
        font-size: 18px;
        font-weight: 700;
        color: #fbbf24;
        text-transform: uppercase;
        letter-spacing: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .fixture-poster-subtitle::before,
    .fixture-poster-subtitle::after {
        content: '';
        display: block;
        width: 40px;
        height: 3px;
        background: #fbbf24;
    }
    
    .fixture-poster-side-text {
        position: absolute;
        top: 50%;
        font-size: 24px;
        font-weight: 900;
        color: rgba(255, 255, 255, 0.15);
        text-transform: uppercase;
        letter-spacing: 8px;
        writing-mode: vertical-rl;
        transform: translateY(-50%);
        z-index: 5;
    }
    
    .fixture-poster-side-text-left {
        left: 15px;
    }
    
    .fixture-poster-side-text-right {
        right: 15px;
    }
    
    .fixture-poster-date-section {
        margin-bottom: 28px;
        display: none;
    }

    .fixture-poster-date-section.is-active {
        display: block;
    }
    
    .fixture-poster-date-header {
        text-align: center;
        margin-bottom: 20px;
    }
    
    .fixture-poster-date-text {
        font-size: 20px;
        font-weight: 700;
        color: white;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.4);
    }
    
    .fixture-poster-match-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.35);
        overflow: hidden;
        margin-bottom: 12px;
        box-shadow:
            0 14px 32px rgba(0, 0, 0, 0.32),
            0 8px 18px rgba(0, 0, 0, 0.22),
            inset 0 1px 0 rgba(255, 255, 255, 0.4);
        background-image: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(245, 245, 245, 0.92));
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
    }

    .fixture-poster-match-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: var(--league-logo, none);
        background-repeat: no-repeat;
        background-position: center;
        background-size: 70% auto;
        opacity: 0.18;
        filter: grayscale(100%);
        pointer-events: none;
    }
    
    .fixture-poster-match-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
    }
    
    .fixture-poster-match-content {
        display: flex;
        align-items: center;
        padding: 15px;
        gap: 8px;
    }
    
    .fixture-poster-team-section {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 12px;
        background: #fbbf24;
        padding: 12px 15px;
        border-radius: 8px;
        min-width: 0;
    }
    
    .fixture-poster-team-section.home-team {
        justify-content: flex-end;
        text-align: right;
    }
    
    .fixture-poster-team-section.away-team {
        justify-content: flex-start;
        text-align: left;
    }
    
    .fixture-poster-team-logo {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        padding: 0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        border: 0;
        overflow: hidden;
    }
    
    .fixture-poster-team-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .fixture-poster-team-logo-text {
        font-size: 16px;
        font-weight: 900;
        color: var(--poster-primary);
    }
    
    .fixture-poster-team-name {
        font-size: 16px;
        font-weight: 900;
        color: black;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .fixture-poster-vs-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 0 12px;
        flex-shrink: 0;
    }
    
    .fixture-poster-vs-text {
        font-size: 24px;
        font-weight: 900;
        color: var(--poster-primary);
        line-height: 1;
    }
    
    .fixture-poster-match-info {
        background: var(--poster-primary);
        color: white;
        padding: 8px 15px;
        text-align: center;
    }
    
    .fixture-poster-match-details {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .fixture-poster-footer {
        margin-top: 40px;
        text-align: center;
        padding: 25px 20px;
        background: rgba(0, 0, 0, 0.3);
        border-radius: 15px;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }
    
    .fixture-poster-sponsor-text {
        font-size: 12px;
        font-weight: 700;
        color: #fbbf24;
        margin-bottom: 8px;
    }
    
    .fixture-poster-sponsor-name {
        font-size: 14px;
        font-weight: 400;
        color: white;
    }
    
    .fixture-poster-social {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 15px;
        flex-wrap: wrap;
    }
    
    .fixture-poster-social-item {
        font-size: 11px;
        font-weight: 700;
        color: white;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .fixture-poster-empty {
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 20px;
        padding: 60px 30px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .fixture-poster-empty-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #f1f5f9;
        border: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    
    .fixture-poster-empty-title {
        font-size: 28px;
        font-weight: 900;
        color: #1e293b;
        margin-bottom: 10px;
    }
    
    .fixture-poster-empty-desc {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 20px;
    }
    
    .fixture-poster-empty-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: var(--poster-primary-soft);
        color: white;
        font-weight: 700;
        border-radius: 12px;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        transition: all 0.2s ease;
    }
    
    .fixture-poster-empty-btn:hover {
        background: var(--poster-primary-soft-hover);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
        transform: translateY(-2px);
    }
    
    @media (max-width: 640px) {
        .fixture-poster-container {
            min-height: auto;
            padding: 12px 0 16px;
        }

        .fixture-poster-bg-pattern,
        .fixture-poster-diagonal-accent,
        .fixture-poster-side-text {
            display: none;
        }

        .fixture-poster-content {
            padding: 16px 12px;
        }

        .fixture-theme-switch {
            margin-bottom: 14px;
            justify-content: center;
        }

        .fixture-poster-header {
            margin-bottom: 24px;
            gap: 10px;
        }

        .fixture-poster-logo-container {
            width: 56px;
            height: 56px;
            padding: 0;
            border-width: 0;
        }

        .fixture-poster-title-block {
            padding: 14px;
        }

        .fixture-poster-league-name {
            font-size: 22px;
        }

        .fixture-poster-subtitle {
            font-size: 13px;
            letter-spacing: 1px;
        }

        .fixture-poster-subtitle::before,
        .fixture-poster-subtitle::after {
            width: 22px;
        }

        .fixture-poster-date-text {
            font-size: 16px;
        }

        .fixture-poster-day-tab {
            padding: 7px 10px;
            font-size: 11px;
        }

        .fixture-poster-match-card {
            margin-bottom: 8px;
        }

        .fixture-poster-match-content {
            padding: 8px;
            gap: 5px;
        }

        .fixture-poster-team-section {
            padding: 6px 8px;
            gap: 8px;
        }

        .fixture-poster-team-name {
            font-size: 11px;
        }

        .fixture-poster-team-logo {
            width: 42px;
            height: 42px;
            padding: 0;
            border-width: 0;
        }

        .fixture-poster-team-logo-text {
            font-size: 12px;
        }

        .fixture-poster-vs-section {
            padding: 0 6px;
        }

        .fixture-poster-vs-text {
            font-size: 18px;
        }

        .fixture-poster-match-info {
            padding: 6px 10px;
        }

        .fixture-poster-match-details {
            font-size: 10px;
        }

        .fixture-poster-footer {
            margin-top: 18px;
            padding: 16px 14px;
        }
    }
</style>

@if($posterFixtureCount === 0)
    <div class="fixture-poster-empty">
        <div class="fixture-poster-empty-icon">
            <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h3 class="fixture-poster-empty-title">{{ $emptyTitle }}</h3>
        <p class="fixture-poster-empty-desc">{{ $emptyDescription }}</p>
        @if(!empty($emptyCtaRoute) && !empty($emptyCtaText))
            <a href="{{ $emptyCtaRoute }}" class="fixture-poster-empty-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ $emptyCtaText }}
            </a>
        @endif
    </div>
@else
    <div class="fixture-theme-switch" role="group" aria-label="Poster theme" data-fixture-theme-control data-fixture-target="#{{ $posterDomId }}">
        <span class="fixture-theme-label">Theme</span>
        <button type="button" class="fixture-theme-chip is-active" data-fixture-theme="green">Green</button>
        <button type="button" class="fixture-theme-chip" data-fixture-theme="sky">Sky</button>
        <button type="button" class="fixture-theme-chip" data-fixture-theme="red">Red</button>
        <button type="button" class="fixture-theme-chip" data-fixture-theme="orange">Orange</button>
        <button type="button" class="fixture-theme-chip" data-fixture-theme="purple">Purple</button>
        <button type="button" class="fixture-theme-chip" data-fixture-theme="teal">Teal</button>
        <button type="button" class="fixture-theme-chip" data-fixture-theme="slate">Slate</button>
    </div>

    <div id="{{ $posterDomId }}" class="fixture-poster-container fixture-theme-green" data-fixture-poster style="{{ $league->logo ? '--league-logo: url(' . Storage::url($league->logo) . ');' : '' }}">
        <div class="fixture-poster-bg-pattern"></div>
        <div class="fixture-poster-diagonal-accent"></div>
        
        <!-- Side Text -->
        <div class="fixture-poster-side-text fixture-poster-side-text-left">#{{ Str::upper(Str::slug($league->name, '')) }}</div>
        <div class="fixture-poster-side-text fixture-poster-side-text-right">#{{ Str::upper(Str::slug($league->name, '')) }}</div>
        
            <div class="fixture-poster-content">
            <!-- Header -->
            <div class="fixture-poster-header">
                <div class="fixture-poster-title-block">
                    <div class="fixture-poster-season-label">{{ $seasonText }}</div>
                    <h1 class="fixture-poster-league-name">{{ Str::upper($league->name) }}</h1>
                    @if(!empty($leagueSubtitle))
                        <div class="fixture-poster-subtitle">{{ Str::upper($leagueSubtitle) }}</div>
                    @endif
                </div>
                @if($league->logo)
                    <div class="fixture-poster-header-logo">
                        <div class="fixture-poster-logo-container">
                            <img src="{{ Storage::url($league->logo) }}" alt="{{ $league->name }}">
                        </div>
                    </div>
                @else
                    <div class="fixture-poster-header-logo">
                        <div class="fixture-poster-logo-container">
                            <span class="fixture-poster-logo-placeholder">{{ Str::upper(Str::substr($league->name, 0, 2)) }}</span>
                        </div>
                    </div>
                @endif
            </div>

            @if($posterDays->count() > 0)
                <div class="fixture-poster-day-tabs" role="tablist">
                    @foreach($posterDays as $posterDay)
                        <button
                            type="button"
                            class="fixture-poster-day-tab {{ $loop->first ? 'is-active' : '' }}"
                            data-fixture-day-tab="{{ $posterDay['key'] }}"
                            aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                        >
                            <span>{{ $posterDay['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            @endif
            
            <!-- Fixtures by Date -->
            @foreach($fixturesByDate as $dateLabel => $dayFixtures)
                @php
                    $firstFixture = $dayFixtures->first();
                    $matchDate = $firstFixture?->match_date;
                    $fullDate = $matchDate
                        ? Str::upper($matchDate->format('j M Y'))
                        : ($dateLabel ?: 'DATE TBA');
                    $posterDay = $posterDays[$dateLabel] ?? [
                        'key' => Str::slug($fullDate ?: uniqid()),
                        'label' => $fullDate,
                    ];
                    $dayKey = $posterDay['key'];
                    $dateLabelText = $posterDay['label'] ?? $fullDate;
                @endphp

                <div class="fixture-poster-date-section {{ $loop->first ? 'is-active' : '' }}" data-fixture-day-section="{{ $dayKey }}">
                    
                    @foreach($dayFixtures as $fixture)
                        @php
                            $homeTeam = $fixture->homeTeam?->team;
                            $awayTeam = $fixture->awayTeam?->team;
                            $homeName = $homeTeam->name ?? 'Team TBA';
                            $awayName = $awayTeam->name ?? 'Team TBA';
                            $homeLogo = $homeTeam->logo ?? null;
                            $awayLogo = $awayTeam->logo ?? null;
                            
                            $timeLabel = optional($fixture->match_time)->format('g:iA') ?? 'TBA';
                            $venueLabel = $fixture->venue ?? $venueText;
                            $matchInfo = $timeLabel . ' | ' . Str::upper($venueLabel);
                        @endphp
                        
                        <div class="fixture-poster-match-card">
                            <div class="fixture-poster-match-content">
                                <!-- Home Team -->
                                <div class="fixture-poster-team-section home-team">
                                    <div class="fixture-poster-team-name">{{ Str::upper(Str::limit($homeName, 20, '')) }}</div>
                                    <div class="fixture-poster-team-logo">
                                        @if($homeLogo)
                                            <img src="{{ Storage::url($homeLogo) }}" alt="{{ $homeName }}">
                                        @else
                                            <span class="fixture-poster-team-logo-text">{{ Str::upper(Str::substr($homeName, 0, 2)) }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- VS -->
                                <div class="fixture-poster-vs-section">
                                    <div class="fixture-poster-vs-text">VS</div>
                                </div>
                                
                                <!-- Away Team -->
                                <div class="fixture-poster-team-section away-team">
                                    <div class="fixture-poster-team-logo">
                                        @if($awayLogo)
                                            <img src="{{ Storage::url($awayLogo) }}" alt="{{ $awayName }}">
                                        @else
                                            <span class="fixture-poster-team-logo-text">{{ Str::upper(Str::substr($awayName, 0, 2)) }}</span>
                                        @endif
                                    </div>
                                    <div class="fixture-poster-team-name">{{ Str::upper(Str::limit($awayName, 20, '')) }}</div>
                                </div>
                            </div>
                            
                            <!-- Match Info Bar -->
                            <div class="fixture-poster-match-info">
                                <div class="fixture-poster-match-details">{{ $matchInfo }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
            
            <!-- Footer -->
            <div class="fixture-poster-footer">
                @if(!empty($league->sponsor))
                    <div class="fixture-poster-sponsor-text">
                        Sponsor by: <span class="fixture-poster-sponsor-name">{{ Str::upper($league->sponsor) }}</span>
                    </div>
                @endif
                
                @if(!empty($league->social_media))
                    <div class="fixture-poster-social">
                        @foreach(explode(',', $league->social_media) as $social)
                            <div class="fixture-poster-social-item">{{ trim($social) }}</div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeClasses = ['fixture-theme-green', 'fixture-theme-sky', 'fixture-theme-red', 'fixture-theme-orange', 'fixture-theme-purple', 'fixture-theme-teal', 'fixture-theme-slate'];

            document.querySelectorAll('[data-fixture-poster]').forEach((poster) => {
                const tabs = poster.querySelectorAll('[data-fixture-day-tab]');
                const sections = poster.querySelectorAll('[data-fixture-day-section]');

                if (tabs.length && sections.length) {
                    const activateDay = (key) => {
                        tabs.forEach((tab) => {
                            const isActive = tab.dataset.fixtureDayTab === key;
                            tab.classList.toggle('is-active', isActive);
                            tab.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                        });

                        sections.forEach((section) => {
                            const isActive = section.dataset.fixtureDaySection === key;
                            section.classList.toggle('is-active', isActive);
                        });
                    };

                    const defaultKey = poster.querySelector('.fixture-poster-day-tab.is-active')?.dataset.fixtureDayTab
                        || tabs[0]?.dataset.fixtureDayTab;

                    if (defaultKey) {
                        activateDay(defaultKey);
                    }

                    tabs.forEach((tab) => {
                        tab.addEventListener('click', () => activateDay(tab.dataset.fixtureDayTab));
                    });
                }
            });

            document.querySelectorAll('[data-fixture-theme-control]').forEach((control) => {
                const targetSelector = control.dataset.fixtureTarget;
                const poster = targetSelector ? document.querySelector(targetSelector) : null;
                if (!poster) return;

                const buttons = control.querySelectorAll('[data-fixture-theme]');

                const applyTheme = (theme) => {
                    themeClasses.forEach((cls) => poster.classList.remove(cls));
                    poster.classList.add(`fixture-theme-${theme}`);
                    buttons.forEach((btn) => {
                        const isActive = btn.dataset.fixtureTheme === theme;
                        btn.classList.toggle('is-active', isActive);
                        btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                    });
                };

                const initialTheme = themeClasses.find((cls) => poster.classList.contains(cls))?.replace('fixture-theme-', '') || 'green';
                applyTheme(initialTheme);

                buttons.forEach((btn) => {
                    btn.addEventListener('click', () => applyTheme(btn.dataset.fixtureTheme));
                });
            });
        });
    </script>
@endif
