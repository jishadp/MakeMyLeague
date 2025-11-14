@extends('layouts.app')

@section('title', $league->name . ' - Share Page')

@section('content')
<div id="share-page-loader">
    <div class="loader-bubble">
        <div class="loader-ring"></div>
        <p>Loading league showcase…</p>
    </div>
</div>

<!-- Hero Section with League Banner -->
<section class="relative overflow-hidden">
    @if($league->banner)
        <div class="absolute inset-0">
            <img src="{{ Storage::url($league->banner) }}" alt="{{ $league->name }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-gray-900"></div>
        </div>
    @else
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600"></div>
    @endif
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
        <div class="text-center">
            <!-- League Logo -->
            @if($league->logo)
                <div class="mb-6 flex justify-center animate-fade-in">
                    <img src="{{ Storage::url($league->logo) }}" alt="{{ $league->name }} Logo" 
                         class="w-24 h-24 md:w-32 md:h-32 rounded-full object-cover border-4 border-white shadow-2xl">
                </div>
            @endif
            
            <!-- League Name & Info -->
            <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-4 drop-shadow-lg animate-slide-down">
                {{ $league->name }}
            </h1>
            <div class="flex flex-wrap items-center justify-center gap-4 text-white/90 text-lg mb-6">
                <span class="flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 010 12z"/>
                    </svg>
                    {{ $league->game->name ?? 'N/A' }}
                </span>
                <span class="flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                    Season {{ $league->season }}
                </span>
                @if($league->localBody)
                    <span class="flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                        {{ $league->localBody->name }}, {{ $league->localBody->district->name ?? '' }}
                    </span>
                @endif
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto mt-8">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 hover:bg-white/20 transition-all">
                    <div class="text-3xl md:text-4xl font-bold text-white">{{ $league->leagueTeams->count() }}</div>
                    <div class="text-white/80 text-sm mt-1">Teams</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 hover:bg-white/20 transition-all">
                    <div class="text-3xl md:text-4xl font-bold text-white">{{ $auctionStats['total_players'] }}</div>
                    <div class="text-white/80 text-sm mt-1">Players</div>
                </div>
            </div>
        </div>
    </div>
</section>

@if($availablePlayers->count() > 0)
@php
    $availableByLocation = $availablePlayers->groupBy(function ($player) {
        return optional(optional($player->user)->localBody)->name ?? 'Free Agents';
    });
    $locationKeys = $availableByLocation->keys();
@endphp
<section class="available-shell" id="available-players">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="available-heading">
            <div>
                <p class="available-pill">Auction pool preview</p>
                <h2>Available players ready to sign</h2>
                <p>Share this roster with scouts and managers to finish retention calls before bidding starts.</p>
            </div>
            <div class="available-cta">
                <div class="available-tabs">
                    @foreach($locationKeys as $index => $location)
                        <button
                            type="button"
                            class="available-tab {{ $index === 0 ? 'active' : '' }}"
                            data-location="location-{{ \Illuminate\Support\Str::slug($location) }}">
                            {{ strtoupper($location) }}
                            <span>{{ $availableByLocation[$location]->count() }}</span>
                        </button>
                    @endforeach
                </div>
                <span class="available-count">
                    <strong>{{ $availablePlayers->count() }}</strong>
                    <small>Total Available</small>
                </span>
            </div>
        </div>
        <div class="location-panels">
            @foreach($availableByLocation as $location => $playersGroup)
                <div class="location-panel {{ $loop->first ? 'active' : '' }}"
                     data-location-panel="location-{{ \Illuminate\Support\Str::slug($location) }}">
                    <header class="location-head">
                        <div>
                            <p class="location-label">{{ strtoupper($location) }}</p>
                            <h3>{{ $playersGroup->count() }} player{{ $playersGroup->count() > 1 ? 's' : '' }}</h3>
                        </div>
                        <span class="location-chip">
                            Wallet sync · {{ $league->start_date ? $league->start_date->format('d M') : 'On demand' }}
                        </span>
                    </header>
                    <div class="player-scroll">
                        @foreach($playersGroup as $player)
                            @php
                                $playerName = optional($player->user)->name ?? 'Player TBD';
                                $role = optional(optional($player->user)->position)->name ?? 'All-rounder';
                                $phone = optional($player->user)->phone;
                                $phoneLink = $phone ? preg_replace('/\D+/', '', $phone) : null;
                                $district = optional(optional(optional($player->user)->localBody)->district)->name ?? 'District TBA';
                            @endphp
                            <article class="match-card">
                                <header class="match-card__head">
                                    <div class="match-avatar">
                                        @if($player->user?->photo)
                                            <img src="{{ Storage::url($player->user->photo) }}" alt="{{ $playerName }}">
                                        @else
                                            <span>{{ strtoupper(substr($playerName, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="match-name">{{ $playerName }}</p>
                                        <p class="match-role">{{ $role }}</p>
                                    </div>
                                    @if($phoneLink)
                                        <a href="tel:{{ $phoneLink }}" class="match-phone" aria-label="Call {{ $playerName }}">📞</a>
                                    @endif
                                </header>
                                <div class="match-card__body">
                                    <div>
                                        <span class="match-label">Location</span>
                                        <p>{{ $location }}</p>
                                    </div>
                                    <div>
                                        <span class="match-label">District</span>
                                        <p>{{ $district }}</p>
                                    </div>
                                    <div>
                                        <span class="match-label">Base Price</span>
                                        <p>₹{{ number_format($player->base_price ?? 0) }}</p>
                                    </div>
                                </div>
                                <footer class="match-card__foot">
                                    <span class="match-pill">Wallet ready</span>
                                    <span class="match-pill light">Updated {{ optional($player->updated_at)->diffForHumans(null, true) ?? 'recently' }}</span>
                                </footer>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<style>
    #share-page-loader {
        position: fixed;
        inset: 0;
        background: radial-gradient(circle at top, #020617, #030712);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        transition: opacity 0.4s ease, visibility 0.4s ease;
    }
    #share-page-loader.loaded {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }
    .loader-bubble {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        color: #e4edff;
        text-align: center;
    }
    .loader-ring {
        width: 64px;
        height: 64px;
        border-radius: 999px;
        border: 4px solid rgba(255, 255, 255, 0.2);
        border-top-color: #38bdf8;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .available-shell {
        background: linear-gradient(180deg, #020617 0%, #0f172a 60%, #030712 100%);
        color: #e4edff;
    }
    .available-heading {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .available-heading h2 {
        font-size: clamp(1.8rem, 3vw, 2.8rem);
        font-weight: 700;
    }
    .available-pill {
        display: inline-flex;
        padding: 0.25rem 0.9rem;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        letter-spacing: 0.3em;
        text-transform: uppercase;
        font-size: 0.68rem;
        color: rgba(255, 255, 255, 0.8);
    }
    .available-cta {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex-wrap: wrap;
    }
    .available-tabs {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        padding-bottom: 0.25rem;
    }
    .available-tab {
        border: 1px solid rgba(148, 163, 184, 0.3);
        color: rgba(226, 232, 240, 0.8);
        padding: 0.5rem 0.9rem;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.6);
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s ease;
    }
    .available-tab span {
        font-size: 0.75rem;
        color: #38bdf8;
    }
    .available-tab.active {
        border-color: #38bdf8;
        color: #0f172a;
        background: #38bdf8;
    }
    .available-count {
        display: flex;
        align-items: baseline;
        gap: 0.5rem;
        font-size: 1.5rem;
    }
    .available-count strong {
        font-size: clamp(2rem, 4vw, 3rem);
    }
    .available-link {
        padding: 0.75rem 1.5rem;
        border-radius: 999px;
        background: #38bdf8;
        color: #0f172a;
        font-weight: 600;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .available-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(56, 189, 248, 0.35);
    }
    .location-panels {
        margin-top: 1rem;
    }
    .location-panel { display: none; }
    .location-panel.active { display: block; }
    .player-scroll {
        display: flex;
        gap: 1rem;
        overflow-x: auto;
        padding-bottom: 1rem;
        scroll-snap-type: x mandatory;
    }
    .match-card {
        min-width: 240px;
        background: rgba(15, 23, 42, 0.85);
        border-radius: 26px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 1rem;
        scroll-snap-align: start;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        box-shadow: 0 20px 45px rgba(2, 6, 23, 0.6);
    }
    .match-card__head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.5rem;
    }
    .match-name {
        font-size: 1.1rem;
        font-weight: 600;
    }
    .match-role {
        font-size: 0.85rem;
        color: rgba(148, 163, 184, 0.85);
    }
    .match-avatar {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        overflow: hidden;
        background: rgba(148, 163, 184, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.5rem;
    }
    .match-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .match-avatar span {
        font-weight: 700;
        color: #0f172a;
    }
    .match-phone {
        text-decoration: none;
        font-size: 1.3rem;
    }
    .match-card__body {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.6rem;
        font-size: 0.9rem;
    }
    .match-label {
        display: block;
        font-size: 0.7rem;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: rgba(148, 163, 184, 0.8);
        margin-bottom: 0.2rem;
    }
    .match-card__foot {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
    }
    .match-pill {
        padding: 0.2rem 0.7rem;
        border-radius: 999px;
        background: rgba(56, 189, 248, 0.15);
        color: rgba(56, 189, 248, 0.9);
        font-size: 0.75rem;
    }
    .match-pill.light {
        background: rgba(255, 255, 255, 0.08);
        color: rgba(226, 232, 240, 0.9);
    }
    .location-panel {
        border-radius: 32px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 1.5rem;
        background: rgba(15, 23, 42, 0.6);
        box-shadow: 0 20px 45px rgba(2, 6, 23, 0.45);
    }
    .location-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .location-label {
        letter-spacing: 0.4em;
        font-size: 0.7rem;
        text-transform: uppercase;
        color: rgba(148, 163, 184, 0.9);
    }
    .location-card h3 {
        font-size: 1.25rem;
        font-weight: 600;
    }
    .location-chip {
        padding: 0.4rem 0.8rem;
        border-radius: 999px;
        background: rgba(148, 163, 184, 0.15);
        font-size: 0.85rem;
        color: rgba(226, 232, 240, 0.8);
    }
    @media (max-width: 768px) {
        .available-heading {
            text-align: left;
        }
        .available-cta {
            flex-direction: column;
            align-items: flex-start;
        }
        .match-card__body {
            grid-template-columns: 1fr;
        }
    .match-card {
        min-width: 80%;
    }
    .available-tabs {
        width: 100%;
    }
    }
.team-shell {
    background: linear-gradient(120deg, #f8fafc, #eef2ff);
}
.team-heading h2 {
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 700;
}
.team-card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.25rem;
    margin-top: 2rem;
}
.team-match-card {
    background: #fff;
    border-radius: 28px;
    padding: 1.5rem;
    border: 1px solid rgba(148, 163, 184, 0.2);
    box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}
.team-match-head {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.team-match-logo {
    width: 64px;
    height: 64px;
    border-radius: 20px;
    background: #eef2ff;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.team-match-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: inherit;
}
.team-match-logo span {
    font-size: 1.5rem;
    font-weight: 700;
    color: #4338ca;
}
    .team-match-meta h3 {
        font-size: 1.3rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .team-name-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .team-retained-pill {
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
        background: rgba(148, 163, 184, 0.15);
        color: #475569;
        font-size: 0.7rem;
        letter-spacing: 0.15em;
    }
    .team-match-meta p {
        color: #475569;
        font-size: 0.9rem;
        margin: 0.15rem 0 0;
    }
    .team-match-meta .team-owner {
        font-size: 0.8rem;
        color: #64748b;
    }
.team-match-chip {
    margin-left: auto;
    padding: 0.35rem 0.9rem;
    border-radius: 999px;
    background: rgba(79, 70, 229, 0.1);
    color: #4338ca;
    font-weight: 600;
    font-size: 0.85rem;
}
.team-match-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.75rem;
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 18px;
    border: 1px solid rgba(148, 163, 184, 0.15);
}
.team-match-stats span {
    display: block;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    color: #94a3b8;
    margin-bottom: 0.2rem;
}
.team-match-stats strong {
    font-size: 1.2rem;
    color: #0f172a;
}
    .football-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.75rem;
        margin-top: 1.25rem;
    }
    .football-card {
        background: #f8fafc;
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 18px;
        padding: 0.65rem;
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    .football-card.retained {
        background: linear-gradient(135deg, #fffceb, #fff7d6);
        border-color: rgba(250, 204, 21, 0.5);
    }
    .football-photo {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        overflow: hidden;
        background: #e0e7ff;
        position: relative;
    }
    .football-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .football-photo span {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #4338ca;
    }
    .football-badge {
        position: absolute;
        bottom: -3px;
        right: -3px;
        background: #facc15;
        color: #78350f;
        font-weight: 700;
        font-size: 0.6rem;
        border-radius: 999px;
        padding: 0.1rem 0.3rem;
        border: 2px solid #fff;
    }
    .football-info {
        flex: 1;
    }
    .football-name {
        margin: 0;
        font-weight: 600;
        color: #0f172a;
        font-size: 0.85rem;
    }
    .football-role {
        margin: 0;
        font-size: 0.7rem;
        color: #475569;
    }
    .football-price {
        margin: 0.15rem 0 0;
        font-size: 0.75rem;
        color: #2563eb;
        font-weight: 600;
    }
    @media (max-width: 1024px) {
        .football-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 640px) {
        .football-grid {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
        .team-match-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .team-match-chip {
            margin-left: 0;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const loader = document.getElementById('share-page-loader');
    const hideLoader = () => loader && loader.classList.add('loaded');
    if (loader) {
        window.addEventListener('load', hideLoader, { once: true });
        setTimeout(hideLoader, 2000);
    }

    document.querySelectorAll('.available-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.location;
            document.querySelectorAll('.available-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            document.querySelectorAll('[data-location-panel]').forEach(panel => {
                panel.classList.toggle('active', panel.dataset.locationPanel === target);
            });
        });
    });
});
</script>
<!-- Prize Pool & Winners Section with 3D Glass Card Design -->
@if($league->winner_prize || $league->runner_prize || $league->winner_team_id || $league->runner_team_id)
<style>
/* 3D Glass Card Styling */
.card-container {
  width: 100%;
  max-width: 320px;
  height: 340px;
  perspective: 1000px;
  margin: 0 auto;
}

.prize-card {
  height: 100%;
  border-radius: 50px;
  transition: all 0.5s ease-in-out;
  transform-style: preserve-3d;
  box-shadow: rgba(5, 71, 17, 0) 40px 50px 25px -40px, rgba(5, 71, 17, 0.2) 0px 25px 25px -5px;
  position: relative;
}

.prize-card-winner {
  background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
}

.prize-card-runner {
  background: linear-gradient(135deg, #C0C0C0 0%, #808080 100%);
}

.glass-layer {
  transform-style: preserve-3d;
  position: absolute;
  inset: 8px;
  border-radius: 55px;
  border-top-right-radius: 100%;
  background: linear-gradient(0deg, rgba(255, 255, 255, 0.349) 0%, rgba(255, 255, 255, 0.815) 100%);
  backdrop-filter: blur(5px);
  -webkit-backdrop-filter: blur(5px);
  transform: translate3d(0px, 0px, 25px);
  border-left: 1px solid white;
  border-bottom: 1px solid white;
  transition: all 0.5s ease-in-out;
}

.card-content {
  padding: 30px 30px 0px 30px;
  transform: translate3d(0, 0, 26px);
  position: relative;
  z-index: 10;
}

.team-logo-badge {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  object-fit: cover;
  border: 4px solid rgba(255, 255, 255, 0.9);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
  margin-bottom: 20px;
  background: white;
}

.badge-label {
  display: inline-block;
  padding: 6px 16px;
  border-radius: 20px;
  font-weight: 800;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 12px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

.badge-winner {
  background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
  color: #8B4513;
}

.badge-runner {
  background: linear-gradient(135deg, #E8E8E8 0%, #C0C0C0 100%);
  color: #4A4A4A;
}

.team-title {
  display: block;
  color: #2d2d2d;
  font-weight: 900;
  font-size: 22px;
  line-height: 1.3;
  margin-bottom: 8px;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.team-subtitle {
  display: block;
  color: rgba(45, 45, 45, 0.7);
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 25px;
}

.card-bottom {
  padding: 15px 20px;
  transform-style: preserve-3d;
  position: absolute;
  bottom: 25px;
  left: 20px;
  right: 20px;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 25px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  transform: translate3d(0, 0, 26px);
  backdrop-filter: blur(10px);
}

.prize-display {
  text-align: center;
}

.prize-label {
  font-size: 11px;
  font-weight: 700;
  color: #666;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  margin-bottom: 8px;
}

.prize-amount {
  font-size: 28px;
  font-weight: 900;
  color: #2d2d2d;
  line-height: 1;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.prize-amount-winner {
  background: linear-gradient(135deg, #FF8C00 0%, #FF6B00 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.prize-amount-runner {
  background: linear-gradient(135deg, #708090 0%, #556270 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.decorative-circles {
  position: absolute;
  right: 0;
  top: 0;
  transform-style: preserve-3d;
}

.circle {
  display: block;
  position: absolute;
  aspect-ratio: 1;
  border-radius: 50%;
  top: 0;
  right: 0;
  box-shadow: rgba(100, 100, 111, 0.2) -10px 10px 20px 0px;
  backdrop-filter: blur(5px);
  -webkit-backdrop-filter: blur(5px);
  transition: all 0.5s ease-in-out;
}

.circle-winner {
  background: rgba(255, 215, 0, 0.2);
}

.circle-runner {
  background: rgba(192, 192, 192, 0.2);
}

.circle1 {
  width: 170px;
  transform: translate3d(0, 0, 20px);
  top: 8px;
  right: 8px;
}

.circle2 {
  width: 140px;
  transform: translate3d(0, 0, 40px);
  top: 10px;
  right: 10px;
  backdrop-filter: blur(1px);
  -webkit-backdrop-filter: blur(1px);
  transition-delay: 0.4s;
}

.circle3 {
  width: 110px;
  transform: translate3d(0, 0, 60px);
  top: 17px;
  right: 17px;
  transition-delay: 0.8s;
}

.circle4 {
  width: 80px;
  transform: translate3d(0, 0, 80px);
  top: 23px;
  right: 23px;
  transition-delay: 1.2s;
}

.circle5 {
  width: 50px;
  transform: translate3d(0, 0, 100px);
  top: 30px;
  right: 30px;
  display: grid;
  place-content: center;
  transition-delay: 1.6s;
}

.circle5 svg {
  width: 24px;
  height: 24px;
  fill: white;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
}

/* Hover Effects */
.card-container:hover .prize-card {
  transform: rotate3d(1, 1, 0, 30deg);
  box-shadow: rgba(5, 71, 17, 0.3) 30px 50px 25px -40px, rgba(5, 71, 17, 0.1) 0px 25px 30px 0px;
}

.card-container:hover .circle2 {
  transform: translate3d(0, 0, 60px);
}

.card-container:hover .circle3 {
  transform: translate3d(0, 0, 80px);
}

.card-container:hover .circle4 {
  transform: translate3d(0, 0, 100px);
}

.card-container:hover .circle5 {
  transform: translate3d(0, 0, 120px);
}

/* Prize Only Card */
.prize-only-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  padding: 40px 30px;
  transform: translate3d(0, 0, 26px);
}

.prize-only-icon {
  width: 80px;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 50%;
  margin-bottom: 25px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.prize-only-icon svg {
  width: 40px;
  height: 40px;
}

.prize-only-icon-winner svg {
  fill: #FF8C00;
}

.prize-only-icon-runner svg {
  fill: #708090;
}

.prize-only-title {
  font-size: 20px;
  font-weight: 900;
  color: #2d2d2d;
  margin-bottom: 15px;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.prize-only-amount {
  font-size: 36px;
  font-weight: 900;
  line-height: 1;
}

/* Responsive Design */
@media (max-width: 768px) {
  .card-container {
    max-width: 300px;
    height: 320px;
  }
  
  .team-title {
    font-size: 20px;
  }
  
  .prize-amount {
    font-size: 24px;
  }
  
  .team-logo-badge {
    width: 60px;
    height: 60px;
  }
  
  .circle1 { width: 150px; }
  .circle2 { width: 120px; }
  .circle3 { width: 95px; }
  .circle4 { width: 70px; }
  .circle5 { width: 45px; }
}

@media (max-width: 480px) {
  .card-container {
    max-width: 280px;
    height: 300px;
  }
  
  .card-content {
    padding: 25px 25px 0px 25px;
  }
  
  .team-title {
    font-size: 18px;
  }
  
  .prize-amount {
    font-size: 22px;
  }
  
  .team-logo-badge {
    width: 55px;
    height: 55px;
  }
}
</style>

<section class="py-12 md:py-20 bg-gradient-to-br from-slate-50 via-gray-50 to-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-12 md:mb-16">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-3 tracking-tight">
                Tournament Champions
            </h2>
            <div class="w-24 h-1 bg-gradient-to-r from-blue-600 to-indigo-600 mx-auto mb-4"></div>
            <p class="text-gray-600 text-base md:text-lg max-w-2xl mx-auto">
                Celebrating excellence and outstanding performance
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 max-w-5xl mx-auto">
            <!-- Winner Card -->
            @if($league->winner_team_id && $league->winnerTeam)
                <div class="card-container">
                    <div class="prize-card prize-card-winner">
                        <div class="glass-layer"></div>
                        
                        <!-- Decorative Circles -->
                        <div class="decorative-circles">
                            <span class="circle circle1 circle-winner"></span>
                            <span class="circle circle2 circle-winner"></span>
                            <span class="circle circle3 circle-winner"></span>
                            <span class="circle circle4 circle-winner"></span>
                            <span class="circle circle5 circle-winner">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </span>
                        </div>

                        <!-- Card Content -->
                        <div class="card-content">
                            @if($league->winnerTeam->team->logo)
                                <img src="{{ Storage::url($league->winnerTeam->team->logo) }}" 
                                     alt="{{ $league->winnerTeam->team->name }}"
                                     class="team-logo-badge">
                            @endif
                            
                            <span class="badge-label badge-winner">
                                🏆 Champion
                            </span>
                            
                            <span class="team-title">{{ $league->winnerTeam->team->name }}</span>
                            <span class="team-subtitle">League Winner</span>
                        </div>

                        <!-- Prize Display -->
                        @if($league->winner_prize)
                            <div class="card-bottom">
                                <div class="prize-display">
                                    <div class="prize-label">Prize Money</div>
                                    <div class="prize-amount prize-amount-winner">
                                        ₹{{ number_format($league->winner_prize) }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @elseif($league->winner_prize)
                <div class="card-container">
                    <div class="prize-card prize-card-winner">
                        <div class="glass-layer"></div>
                        
                        <div class="decorative-circles">
                            <span class="circle circle1 circle-winner"></span>
                            <span class="circle circle2 circle-winner"></span>
                            <span class="circle circle3 circle-winner"></span>
                            <span class="circle circle4 circle-winner"></span>
                            <span class="circle circle5 circle-winner">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </span>
                        </div>

                        <div class="prize-only-content">
                            <div class="prize-only-icon">
                                <svg class="prize-only-icon-winner" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                            <div class="prize-only-title">Winner Prize</div>
                            <div class="prize-only-amount prize-amount-winner">
                                ₹{{ number_format($league->winner_prize) }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Runner-up Card -->
            @if($league->runner_team_id && $league->runnerTeam)
                <div class="card-container">
                    <div class="prize-card prize-card-runner">
                        <div class="glass-layer"></div>
                        
                        <!-- Decorative Circles -->
                        <div class="decorative-circles">
                            <span class="circle circle1 circle-runner"></span>
                            <span class="circle circle2 circle-runner"></span>
                            <span class="circle circle3 circle-runner"></span>
                            <span class="circle circle4 circle-runner"></span>
                            <span class="circle circle5 circle-runner">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </span>
                        </div>

                        <!-- Card Content -->
                        <div class="card-content">
                            @if($league->runnerTeam->team->logo)
                                <img src="{{ Storage::url($league->runnerTeam->team->logo) }}" 
                                     alt="{{ $league->runnerTeam->team->name }}"
                                     class="team-logo-badge">
                            @endif
                            
                            <span class="badge-label badge-runner">
                                🥈 Runner-up
                            </span>
                            
                            <span class="team-title">{{ $league->runnerTeam->team->name }}</span>
                            <span class="team-subtitle">Second Place</span>
                        </div>

                        <!-- Prize Display -->
                        @if($league->runner_prize)
                            <div class="card-bottom">
                                <div class="prize-display">
                                    <div class="prize-label">Prize Money</div>
                                    <div class="prize-amount prize-amount-runner">
                                        ₹{{ number_format($league->runner_prize) }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @elseif($league->runner_prize)
                <div class="card-container">
                    <div class="prize-card prize-card-runner">
                        <div class="glass-layer"></div>
                        
                        <div class="decorative-circles">
                            <span class="circle circle1 circle-runner"></span>
                            <span class="circle circle2 circle-runner"></span>
                            <span class="circle circle3 circle-runner"></span>
                            <span class="circle circle4 circle-runner"></span>
                            <span class="circle circle5 circle-runner">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </span>
                        </div>

                        <div class="prize-only-content">
                            <div class="prize-only-icon">
                                <svg class="prize-only-icon-runner" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                            <div class="prize-only-title">Runner-up Prize</div>
                            <div class="prize-only-amount prize-amount-runner">
                                ₹{{ number_format($league->runner_prize) }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endif

<!-- Top 3 Auction Highlights -->
@if($topAuctions->count() > 0)
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-3 flex items-center justify-center gap-3">
                <svg class="w-10 h-10 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                </svg>
                Top 3 Auction Highlights
            </h2>
            <p class="text-gray-600 text-lg">Most valuable signings of the league</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($topAuctions as $index => $auction)
                <div class="group relative rounded-3xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 animate-fade-in-up" style="animation-delay: {{ $index * 0.1 }}s">
                    <!-- Rank Badge -->
                    <div class="absolute top-4 left-4 z-10">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center text-white font-black text-2xl shadow-lg
                            {{ $index == 0 ? 'bg-gradient-to-br from-yellow-400 to-yellow-600' : '' }}
                            {{ $index == 1 ? 'bg-gradient-to-br from-gray-300 to-gray-500' : '' }}
                            {{ $index == 2 ? 'bg-gradient-to-br from-orange-400 to-orange-600' : '' }}">
                            #{{ $index + 1 }}
                        </div>
                    </div>

                    <!-- Player Image with League Banner Background -->
                    <div class="relative h-64 flex items-center justify-center">
                        @if($league->banner)
                            <img src="{{ Storage::url($league->banner) }}" alt="{{ $league->name }}" 
                                 class="absolute inset-0 w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/80 to-purple-900/80"></div>
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 to-purple-600"></div>
                        @endif
                        <div class="absolute inset-0 bg-black/20"></div>
                        <div class="relative">
                            @if($auction->user->photo)
                                <img src="{{ Storage::url($auction->user->photo) }}" alt="{{ $auction->user->name }}" 
                                     class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-2xl">
                            @else
                                <div class="w-32 h-32 rounded-full bg-white/20 backdrop-blur-sm border-4 border-white shadow-2xl flex items-center justify-center text-white text-4xl font-bold">
                                    {{ substr($auction->user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Player Details -->
                    <div class="p-6 bg-white">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $auction->user->name }}</h3>
                        <div class="flex items-center gap-2 text-gray-600 mb-4">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">{{ $auction->user->position->name ?? 'Player' }}</span>
                        </div>

                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-4 mb-4">
                            <div class="text-sm text-gray-600 mb-1">Sold For</div>
                            <div class="text-3xl font-black text-green-600">₹{{ number_format($auction->bid_price) }}</div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                </svg>
                                <span class="text-sm font-semibold text-gray-700">{{ $auction->leagueTeam->team->name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- All Teams Section -->
<section class="team-shell">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="team-heading">
            <div>
                <p class="available-pill">Match-ready lineups</p>
                <h2>League teams and live rosters</h2>
                <p>Get a glance of who is match-fit, retained, or still available across every franchise.</p>
            </div>
        </div>

        <div class="team-card-grid">
            @foreach($teams as $team)
                @php
                    $sortedPlayers = $team->leaguePlayers->sortBy([
                        fn($a, $b) => $b->retention <=> $a->retention,
                        fn($a, $b) => ($b->bid_price ?? 0) <=> ($a->bid_price ?? 0)
                    ]);
                    $retainedCount = $team->leaguePlayers->where('retention', true)->count();
                @endphp
                <article class="team-match-card">
                    <header class="team-match-head">
                        <div class="team-match-logo">
                            @if($team->team->logo)
                                <img src="{{ Storage::url($team->team->logo) }}" alt="{{ $team->team->name }}">
                            @else
                                <span>{{ substr($team->team->name, 0, 1) }}</span>
                            @endif
                        </div>
                        <div class="team-match-meta">
                            <div class="team-name-row">
                                <h3>{{ $team->team->name }}</h3>
                                <span class="team-retained-pill">{{ $retainedCount }} RET</span>
                            </div>
                            <p>{{ optional($team->team->homeGround)->name ?? 'Home venue TBA' }}</p>
                            <p class="team-owner">Owner: {{ optional($team->team->owners->first())->name ?? 'TBA' }}</p>
                        </div>
                        <div class="team-match-chip">
                            {{ ucfirst($league->status) }}
                        </div>
                    </header>
                    <div class="team-match-stats">
                        <div>
                            <span>Players</span>
                            <strong>{{ $team->leaguePlayers->count() }}</strong>
                        </div>
                        <div>
                            <span>Retained</span>
                            <strong>{{ $retainedCount }}</strong>
                        </div>
                        <div>
                            <span>Wallet</span>
                            <strong>₹{{ number_format($team->wallet_balance ?? 0) }}</strong>
                        </div>
                    </div>
                    <div class="football-grid">
                        @foreach($sortedPlayers as $player)
                            <div class="football-card {{ $player->retention ? 'retained' : '' }}">
                                <div class="football-photo">
                                    @if($player->user?->photo)
                                        <img src="{{ Storage::url($player->user->photo) }}" alt="{{ $player->user->name }}">
                                    @else
                                        <span>{{ strtoupper(substr($player->user->name ?? 'P', 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="football-info">
                                    <p class="football-name">{{ $player->user->name }}</p>
                                    <p class="football-price">₹{{ number_format($player->bid_price ?? $player->base_price ?? 0) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>


<!-- Share Section -->
<section class="py-16 bg-gradient-to-br from-indigo-600 to-purple-600">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Share This League</h2>
        <p class="text-white/90 text-lg mb-8">Spread the word about {{ $league->name }}</p>
        
        <div class="flex flex-wrap justify-center gap-4">
            <!-- Copy Link Button -->
            <button onclick="copyShareLink()" class="flex items-center gap-2 bg-white text-indigo-600 px-6 py-3 rounded-xl font-semibold hover:bg-gray-100 transition-all shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                Copy Link
            </button>

            <!-- WhatsApp Share -->
            <a href="https://wa.me/?text=Check out {{ $league->name }} - {{ url()->current() }}" 
               target="_blank"
               class="flex items-center gap-2 bg-green-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-green-600 transition-all shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
                WhatsApp
            </a>

            <!-- View League Details -->
            <a href="{{ route('leagues.show', $league) }}" 
               class="flex items-center gap-2 bg-white/10 backdrop-blur-sm text-white border-2 border-white px-6 py-3 rounded-xl font-semibold hover:bg-white/20 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                View Details
            </a>
        </div>
    </div>
</section>

<script>
function copyShareLink() {
    const url = window.location.href;
    
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(() => {
            showToast('Link copied to clipboard!');
        }).catch(err => {
            fallbackCopyToClipboard(url);
        });
    } else {
        fallbackCopyToClipboard(url);
    }
}

function fallbackCopyToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        showToast('Link copied to clipboard!');
    } catch (err) {
        showToast('Failed to copy link. Please copy manually.', 'error');
    }
    
    document.body.removeChild(textArea);
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-xl shadow-2xl z-50 animate-slide-up ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white font-semibold`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(20px)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => document.body.removeChild(toast), 300);
    }, 3000);
}
</script>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-down {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out;
}

.animate-slide-down {
    animation: slide-down 0.6s ease-out;
}

.animate-slide-up {
    animation: slide-up 0.3s ease-out;
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Responsive improvements */
@media (max-width: 640px) {
    .text-4xl {
        font-size: 2rem;
    }
    .text-3xl {
        font-size: 1.75rem;
    }
}
</style>
@endsection
