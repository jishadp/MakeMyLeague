@extends('layouts.app')

@section('title', $league->name . ' - Share Page')

@section('content')
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

<!-- Prize Pool & Winners Section with 3D Glass Card Design + Gyroscope -->
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
  transition: transform 0.1s ease-out;
  transform-style: preserve-3d;
  box-shadow: rgba(5, 71, 17, 0) 40px 50px 25px -40px, rgba(5, 71, 17, 0.2) 0px 25px 25px -5px;
  position: relative;
  will-change: transform;
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
  transition: all 0.1s ease-out;
  will-change: transform;
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
}

.circle3 {
  width: 110px;
  transform: translate3d(0, 0, 60px);
  top: 17px;
  right: 17px;
}

.circle4 {
  width: 80px;
  transform: translate3d(0, 0, 80px);
  top: 23px;
  right: 23px;
}

.circle5 {
  width: 50px;
  transform: translate3d(0, 0, 100px);
  top: 30px;
  right: 30px;
  display: grid;
  place-content: center;
}

.circle5 svg {
  width: 24px;
  height: 24px;
  fill: white;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
}

/* Desktop Hover Effects */
@media (hover: hover) {
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

/* Gyro Indicator */
.gyro-indicator {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 10px 15px;
  border-radius: 20px;
  font-size: 12px;
  z-index: 1000;
  display: none;
}

.gyro-active {
  display: block;
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
                <div class="card-container" data-card="winner">
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
                <div class="card-container" data-card="winner-prize">
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
                <div class="card-container" data-card="runner">
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
                <div class="card-container" data-card="runner-prize">
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

    <!-- Gyro Indicator -->
    <div class="gyro-indicator" id="gyroIndicator">📱 Gyroscope Active</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let gyroSupported = false;
    const cards = document.querySelectorAll('.card-container');
    const indicator = document.getElementById('gyroIndicator');
    
    // Check if device supports gyroscope
    if (window.DeviceOrientationEvent) {
        // Request permission for iOS 13+
        if (typeof DeviceOrientationEvent.requestPermission === 'function') {
            // iOS device - request permission on first interaction
            const enableGyro = () => {
                DeviceOrientationEvent.requestPermission()
                    .then(response => {
                        if (response === 'granted') {
                            initGyroscope();
                        }
                    })
                    .catch(console.error);
            };
            
            // Add click listener to enable gyro on iOS
            document.addEventListener('click', enableGyro, { once: true });
        } else {
            // Non-iOS device - directly init
            initGyroscope();
        }
    }
    
    function initGyroscope() {
        gyroSupported = true;
        
        window.addEventListener('deviceorientation', function(event) {
            const beta = event.beta;  // Front-to-back tilt (-180 to 180)
            const gamma = event.gamma; // Left-to-right tilt (-90 to 90)
            
            // Only apply on mobile devices
            if (window.innerWidth <= 768 && beta !== null && gamma !== null) {
                indicator.classList.add('gyro-active');
                
                cards.forEach(card => {
                    const prizeCard = card.querySelector('.prize-card');
                    const circles = card.querySelectorAll('.circle');
                    
                    // Normalize values for smoother animation
                    const tiltX = Math.max(-30, Math.min(30, gamma)); // -30 to 30 degrees
                    const tiltY = Math.max(-30, Math.min(30, beta - 90)); // -30 to 30 degrees
                    
                    // Apply 3D rotation to card
                    prizeCard.style.transform = `
                        rotateX(${-tiltY * 0.5}deg) 
                        rotateY(${tiltX * 0.5}deg)
                        scale3d(1.02, 1.02, 1.02)
                    `;
                    
                    // Animate decorative circles based on tilt
                    circles.forEach((circle, index) => {
                        const baseDepth = [20, 40, 60, 80, 100][index];
                        const extraDepth = (Math.abs(tiltX) + Math.abs(tiltY)) * 0.5;
                        const depth = baseDepth + extraDepth;
                        
                        circle.style.transform = `translate3d(0, 0, ${depth}px)`;
                    });
                });
                
                // Hide indicator after 3 seconds
                setTimeout(() => {
                    indicator.classList.remove('gyro-active');
                }, 3000);
            }
        }, true);
    }
    
    // Fallback to mouse move on desktop
    if (window.innerWidth > 768) {
        cards.forEach(card => {
            card.addEventListener('mousemove', function(e) {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = (y - centerY) / 10;
                const rotateY = (centerX - x) / 10;
                
                const prizeCard = card.querySelector('.prize-card');
                prizeCard.style.transform = `
                    rotateX(${rotateX}deg) 
                    rotateY(${rotateY}deg)
                    scale3d(1.05, 1.05, 1.05)
                `;
            });
            
            card.addEventListener('mouseleave', function() {
                const prizeCard = card.querySelector('.prize-card');
                prizeCard.style.transform = 'rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
            });
        });
    }
});
</script>
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
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-3 flex items-center justify-center gap-3">
                <svg class="w-10 h-10 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
                League Teams
            </h2>
            <p class="text-gray-600 text-lg">Complete squad breakdown for all participating teams</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @foreach($teams as $team)
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-3xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 border border-gray-200 animate-fade-in">
                    <!-- Team Header -->
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                        <div class="flex items-center gap-4">
                            @if($team->team->logo)
                                <img src="{{ Storage::url($team->team->logo) }}" alt="{{ $team->team->name }}" 
                                     class="w-16 h-16 md:w-20 md:h-20 rounded-full object-cover border-4 border-white shadow-lg">
                            @else
                                <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-white/20 backdrop-blur-sm border-4 border-white shadow-lg flex items-center justify-center text-white text-2xl font-bold">
                                    {{ substr($team->team->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="flex-1">
                                <h3 class="text-2xl md:text-3xl font-bold text-white mb-1">{{ $team->team->name }}</h3>
                                <div class="flex items-center gap-4 text-white/90 text-sm">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $team->leaguePlayers->count() }} Players
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                        </svg>
                                        ₹{{ number_format($team->wallet_balance) }} Remaining
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Team Players - Football Formation Style -->
                    <div class="p-4">
                        @if($team->leaguePlayers->count() > 0)
                            @php
                                // Sort: Retention players first, then by bid price (highest first)
                                $sortedPlayers = $team->leaguePlayers->sortBy([
                                    fn($a, $b) => $b->retention <=> $a->retention,
                                    fn($a, $b) => ($b->bid_price ?? 0) <=> ($a->bid_price ?? 0)
                                ]);
                            @endphp
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                @foreach($sortedPlayers as $player)
                                    <!-- Football Formation Card -->
                                    <div class="relative group">
                                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-3 shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1">
                                            <!-- Retention Badge -->
                                            @if($player->retention)
                                                <div class="absolute -top-2 -right-2 z-10">
                                                    <div class="bg-yellow-400 text-yellow-900 text-xs font-black px-2 py-1 rounded-full border-2 border-white shadow-lg">
                                                        R
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <!-- Player Photo -->
                                            <div class="relative mb-2">
                                                @if($player->user->photo)
                                                    <img src="{{ Storage::url($player->user->photo) }}" alt="{{ $player->user->name }}" 
                                                         class="w-full aspect-square rounded-xl object-cover border-2 border-white/50">
                                                @else
                                                    <div class="w-full aspect-square rounded-xl bg-white/20 backdrop-blur-sm border-2 border-white/50 flex items-center justify-center">
                                                        <span class="text-white text-3xl font-bold">{{ substr($player->user->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                                
                                                <!-- Position Badge -->
                                                <div class="absolute bottom-1 left-1 right-1">
                                                    <div class="bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2 py-1 rounded-lg text-center truncate">
                                                        {{ $player->user->position->name ?? 'PLR' }}
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Player Name -->
                                            <h4 class="text-white font-bold text-sm text-center truncate mb-1">
                                                {{ $player->user->name }}
                                            </h4>
                                            
                                            <!-- Price/Status -->
                                            @if($player->status === 'sold' && $player->bid_price)
                                                <div class="bg-green-400 text-green-900 text-xs font-black py-1 px-2 rounded-lg text-center">
                                                    ₹{{ number_format($player->bid_price) }}
                                                </div>
                                            @elseif($player->retention)
                                                <div class="bg-blue-400 text-blue-900 text-xs font-bold py-1 px-2 rounded-lg text-center">
                                                    RETAINED
                                                </div>
                                            @else
                                                <div class="bg-gray-400 text-gray-900 text-xs font-bold py-1 px-2 rounded-lg text-center">
                                                    UNSOLD
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <p class="font-medium">No players yet</p>
                            </div>
                        @endif
                    </div>
                </div>
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

