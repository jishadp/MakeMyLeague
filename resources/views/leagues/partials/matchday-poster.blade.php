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
    $leagueSubtitle = $leagueSubtitle ?? 'MATCH DAY FIXTURES';
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap');
    
    .fixture-poster-container {
        font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 50%, #1e40af 100%);
        min-height: 100vh;
        width: 100%;
        position: relative;
        overflow: hidden;
        padding: 0;
        margin: 0;
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
        text-align: center;
        margin-bottom: 40px;
        position: relative;
    }
    
    .fixture-poster-logos {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 30px;
        margin-bottom: 25px;
    }
    
    .fixture-poster-logo-container {
        width: 80px;
        height: 80px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        border: 4px solid rgba(255, 255, 255, 0.9);
    }
    
    .fixture-poster-logo-container img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    
    .fixture-poster-logo-placeholder {
        font-size: 24px;
        font-weight: 900;
        color: #1e40af;
    }
    
    .fixture-poster-title-block {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 15px;
        padding: 20px 25px;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }
    
    .fixture-poster-season-label {
        font-size: 14px;
        font-weight: 700;
        color: #fef3c7;
        text-transform: uppercase;
        letter-spacing: 3px;
        margin-bottom: 8px;
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
        margin-bottom: 35px;
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
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 15px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        transition: transform 0.2s ease;
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
        width: 50px;
        height: 50px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        padding: 5px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        border: 3px solid white;
    }
    
    .fixture-poster-team-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    
    .fixture-poster-team-logo-text {
        font-size: 16px;
        font-weight: 900;
        color: #1e40af;
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
        color: #1e40af;
        line-height: 1;
    }
    
    .fixture-poster-match-info {
        background: #1e40af;
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
        background: #0ea5e9;
        color: white;
        font-weight: 700;
        border-radius: 12px;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
        transition: all 0.2s ease;
    }
    
    .fixture-poster-empty-btn:hover {
        background: #0284c7;
        box-shadow: 0 6px 16px rgba(14, 165, 233, 0.4);
        transform: translateY(-2px);
    }
    
    @media (max-width: 640px) {
        .fixture-poster-content {
            padding: 30px 15px;
        }
        
        .fixture-poster-league-name {
            font-size: 24px;
        }
        
        .fixture-poster-subtitle {
            font-size: 14px;
        }
        
        .fixture-poster-subtitle::before,
        .fixture-poster-subtitle::after {
            width: 25px;
        }
        
        .fixture-poster-match-content {
            padding: 10px;
            gap: 6px;
        }
        
        .fixture-poster-team-section {
            padding: 8px 10px;
        }
        
        .fixture-poster-team-name {
            font-size: 12px;
        }
        
        .fixture-poster-team-logo {
            width: 40px;
            height: 40px;
        }
        
        .fixture-poster-team-logo-text {
            font-size: 14px;
        }
        
        .fixture-poster-vs-section {
            padding: 0 8px;
        }
        
        .fixture-poster-vs-text {
            font-size: 20px;
        }
        
        .fixture-poster-match-details {
            font-size: 11px;
        }
        
        .fixture-poster-side-text {
            font-size: 18px;
            letter-spacing: 6px;
        }
        
        .fixture-poster-side-text-left {
            left: 8px;
        }
        
        .fixture-poster-side-text-right {
            right: 8px;
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
    <div class="fixture-poster-container">
        <div class="fixture-poster-bg-pattern"></div>
        <div class="fixture-poster-diagonal-accent"></div>
        
        <!-- Side Text -->
        <div class="fixture-poster-side-text fixture-poster-side-text-left">#{{ Str::upper(Str::slug($league->name, '')) }}</div>
        <div class="fixture-poster-side-text fixture-poster-side-text-right">#{{ Str::upper(Str::slug($league->name, '')) }}</div>
        
        <div class="fixture-poster-content">
            <!-- Header -->
            <div class="fixture-poster-header">
                <div class="fixture-poster-logos">
                    @if($league->logo)
                        <div class="fixture-poster-logo-container">
                            <img src="{{ Storage::url($league->logo) }}" alt="{{ $league->name }}">
                        </div>
                    @else
                        <div class="fixture-poster-logo-container">
                            <span class="fixture-poster-logo-placeholder">{{ Str::upper(Str::substr($league->name, 0, 2)) }}</span>
                        </div>
                    @endif
                    
                    @if($league->game && $league->game->logo)
                        <div class="fixture-poster-logo-container">
                            <img src="{{ Storage::url($league->game->logo) }}" alt="{{ $league->game->name }}">
                        </div>
                    @endif
                </div>
                
                <div class="fixture-poster-title-block">
                    <div class="fixture-poster-season-label">{{ $seasonText }}</div>
                    <h1 class="fixture-poster-league-name">{{ Str::upper($league->name) }}</h1>
                    <div class="fixture-poster-subtitle">{{ Str::upper($leagueSubtitle) }}</div>
                </div>
            </div>
            
            <!-- Fixtures by Date -->
            @foreach($fixturesByDate as $dateLabel => $dayFixtures)
                <div class="fixture-poster-date-section">
                    @php
                        $firstFixture = $dayFixtures->first();
                        $matchDate = $firstFixture?->match_date;
                        $dayName = $matchDate ? Str::upper($matchDate->format('D')) : 'MATCH DAY';
                        $dateNum = $matchDate ? $matchDate->format('jS') : '';
                        $monthYear = $matchDate ? Str::upper($matchDate->format('M.y')) : '';
                        $fullDate = $dateNum && $monthYear ? "{$dayName}.{$dateNum} {$monthYear}" : $dateLabel;
                    @endphp
                    
                    <div class="fixture-poster-date-header">
                        <div class="fixture-poster-date-text">{{ $fullDate }}</div>
                    </div>
                    
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
@endif