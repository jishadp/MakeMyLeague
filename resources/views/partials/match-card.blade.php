@php
    $homeTeam = $match->homeTeam?->team;
    $awayTeam = $match->awayTeam?->team;
    $isKnockout = !in_array($match->match_type, ['group_stage']);
    $isCompleted = $match->status === 'completed';
    $homeWon = $isCompleted && $match->home_score > $match->away_score;
    $awayWon = $isCompleted && $match->away_score > $match->home_score;
    $isDraw = $isCompleted && $match->home_score === $match->away_score;
    
    // Get cards for this match
    $homeYellows = $match->events->where('team_id', $match->home_team_id)->where('event_type', 'YELLOW_CARD')->count();
    $homeReds = $match->events->where('team_id', $match->home_team_id)->where('event_type', 'RED_CARD')->count();
    $awayYellows = $match->events->where('team_id', $match->away_team_id)->where('event_type', 'YELLOW_CARD')->count();
    $awayReds = $match->events->where('team_id', $match->away_team_id)->where('event_type', 'RED_CARD')->count();
@endphp
<div class="rounded-2xl border overflow-hidden shadow-sm group transition-all bg-[var(--bg-card)] border-[var(--border)] hover:border-[var(--accent)]/20">
    <a href="{{ route('matches.live', $match->slug) }}" class="block relative">
        <div class="p-4 sm:p-6 pb-2">
             <div class="flex items-center justify-between">
                  <!-- Home -->
                  <div class="flex-1 flex flex-col items-center">
                      <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full p-2 mb-2 border shadow-inner bg-[var(--bg-element)] border-[var(--border)] relative">
                          @if($homeTeam && $homeTeam->logo)
                            <img src="{{ url(Storage::url($homeTeam->logo)) }}" class="w-full h-full object-contain" alt="">
                          @else
                            <div class="w-full h-full flex items-center justify-center font-bold text-lg text-[var(--text-muted)]">{{ substr($homeTeam?->name ?? 'H', 0, 1) }}</div>
                          @endif
                          @if($homeWon && $isKnockout)
                            <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-[var(--accent)] flex items-center justify-center">
                                <i class="fa-solid fa-trophy text-white text-[10px]"></i>
                            </div>
                          @endif
                      </div>
                      <h3 class="text-xs sm:text-sm font-bold text-center leading-tight max-w-[100px] truncate" :class="{{ $homeWon ? '"text-[var(--accent)]"' : '"text-[var(--text-main)]"' }}">{{ $homeTeam?->name ?? 'Home' }}</h3>
                      @if($isCompleted && ($homeYellows > 0 || $homeReds > 0))
                        <div class="flex items-center gap-1 mt-1">
                            @if($homeYellows > 0)
                                <span class="flex items-center gap-0.5 text-[10px] font-bold text-amber-500">
                                    <div class="w-2 h-3 bg-amber-400 rounded-sm"></div>{{ $homeYellows }}
                                </span>
                            @endif
                            @if($homeReds > 0)
                                <span class="flex items-center gap-0.5 text-[10px] font-bold text-rose-500">
                                    <div class="w-2 h-3 bg-rose-500 rounded-sm"></div>{{ $homeReds }}
                                </span>
                            @endif
                        </div>
                      @endif
                  </div>

                  <!-- Score / Time -->
                  <div class="px-2 flex flex-col items-center min-w-[100px]">
                      @if($match->status === 'completed')
                          <div class="text-2xl sm:text-3xl font-black tabular-nums tracking-tighter text-[var(--text-main)]">
                              {{ $match->home_score ?? 0 }}<span class="text-[var(--text-muted)] mx-1">-</span>{{ $match->away_score ?? 0 }}
                          </div>
                          @if($match->has_penalties)
                            <span class="text-[10px] font-bold bg-purple-500/10 text-purple-600 px-2 py-0.5 rounded mt-1">Pens: {{ $match->home_penalty_score }}-{{ $match->away_penalty_score }}</span>
                          @else
                            <span class="text-[10px] font-bold bg-[var(--bg-element)] px-2 py-0.5 rounded text-[var(--text-muted)] mt-1">FT</span>
                          @endif
                      @elseif($match->status === 'in_progress')
                          <div class="text-3xl sm:text-4xl font-black tabular-nums tracking-tighter text-[var(--text-main)]">
                              {{ $match->home_score ?? 0 }}<span class="text-[var(--text-muted)] mx-1">-</span>{{ $match->away_score ?? 0 }}
                          </div>
                           <span class="text-[10px] font-bold text-red-500 animate-pulse mt-1">LIVE</span>
                      @else
                          <span class="text-sm font-bold px-2 py-1 rounded mb-1 bg-[var(--bg-element)] text-[var(--text-muted)] whitespace-nowrap">{{ $match->match_time ? $match->match_time->format('h:i A') : 'TBA' }}</span>
                          <span class="text-[10px] uppercase font-medium text-[var(--text-muted)] whitespace-nowrap">{{ $match->match_date ? $match->match_date->format('M d') : 'Date TBA' }}</span>
                      @endif
                  </div>

                  <!-- Away -->
                  <div class="flex-1 flex flex-col items-center">
                      <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full p-2 mb-2 border shadow-inner bg-[var(--bg-element)] border-[var(--border)] relative">
                          @if($awayTeam && $awayTeam->logo)
                            <img src="{{ url(Storage::url($awayTeam->logo)) }}" class="w-full h-full object-contain" alt="">
                          @else
                            <div class="w-full h-full flex items-center justify-center font-bold text-lg text-[var(--text-muted)]">{{ substr($awayTeam?->name ?? 'A', 0, 1) }}</div>
                          @endif
                          @if($awayWon && $isKnockout)
                            <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-[var(--accent)] flex items-center justify-center">
                                <i class="fa-solid fa-trophy text-white text-[10px]"></i>
                            </div>
                          @endif
                      </div>
                      <h3 class="text-xs sm:text-sm font-bold text-center leading-tight max-w-[100px] truncate" :class="{{ $awayWon ? '"text-[var(--accent)]"' : '"text-[var(--text-main)]"' }}">{{ $awayTeam?->name ?? 'Away' }}</h3>
                      @if($isCompleted && ($awayYellows > 0 || $awayReds > 0))
                        <div class="flex items-center gap-1 mt-1">
                            @if($awayYellows > 0)
                                <span class="flex items-center gap-0.5 text-[10px] font-bold text-amber-500">
                                    <div class="w-2 h-3 bg-amber-400 rounded-sm"></div>{{ $awayYellows }}
                                </span>
                            @endif
                            @if($awayReds > 0)
                                <span class="flex items-center gap-0.5 text-[10px] font-bold text-rose-500">
                                    <div class="w-2 h-3 bg-rose-500 rounded-sm"></div>{{ $awayReds }}
                                </span>
                            @endif
                        </div>
                      @endif
                  </div>
             </div>
             
             <!-- League/Group Info -->
             <div class="mt-4 text-center">
                <div class="inline-flex items-center gap-2 text-[10px] font-semibold text-[var(--text-muted)] bg-[var(--bg-element)] px-2 py-1 rounded-full border border-[var(--border)]">
                    <span>{{ $match->league->name ?? 'League' }}</span>
                    @if($match->leagueGroup)
                         <span class="w-1 h-1 rounded-full bg-[var(--text-muted)]"></span>
                        <span>{{ $match->leagueGroup->name }}</span>
                    @endif
                    <span class="w-1 h-1 rounded-full bg-[var(--text-muted)]"></span>
                    <span>{{ ucfirst(str_replace('_', ' ', $match->match_type)) }}</span>
                </div>
             </div>
        </div>
    </a>
</div>
