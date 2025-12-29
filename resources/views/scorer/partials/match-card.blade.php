@php
    $homeTeam = $match->homeTeam?->team;
    $awayTeam = $match->awayTeam?->team;
@endphp
<div class="rounded-2xl border overflow-hidden shadow-sm group transition-all bg-[var(--bg-card)] border-[var(--border)] hover:border-[var(--accent)]/20">
    <div class="block relative">
        <!-- Status Banner -->
        @if(isset($isLive) && $isLive)
            <div class="bg-gradient-to-r from-red-600 to-red-500 text-white text-[10px] font-bold px-3 py-1 flex justify-between items-center">
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                    LIVE NOW
                </div>
                <span class="opacity-90">{{ $match->match_type ? Str::headline($match->match_type) : 'TBD' }}</span>
            </div>
        @endif

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
                      </div>
                      <h3 class="text-xs sm:text-sm font-bold text-center leading-tight text-[var(--text-main)] max-w-[100px] truncate">{{ $homeTeam?->name ?? 'Home' }}</h3>
                  </div>

                  <!-- Score / Time -->
                  <div class="px-2 flex flex-col items-center min-w-[100px]">
                      @if(isset($isLive) && $isLive)
                          <div class="text-3xl sm:text-4xl font-black tabular-nums tracking-tighter text-[var(--text-main)]">
                              {{ $match->home_score ?? 0 }}<span class="text-[var(--text-muted)] mx-1">-</span>{{ $match->away_score ?? 0 }}
                          </div>
                      @elseif(isset($isCompleted) && $isCompleted)
                          <div class="text-2xl sm:text-3xl font-black tabular-nums tracking-tighter text-[var(--text-main)]">
                              {{ $match->home_score ?? 0 }}<span class="text-[var(--text-muted)] mx-1">-</span>{{ $match->away_score ?? 0 }}
                          </div>
                          <span class="text-[10px] font-bold bg-[var(--bg-element)] px-2 py-0.5 rounded text-[var(--text-muted)] mt-1">FT</span>
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
                      </div>
                      <h3 class="text-xs sm:text-sm font-bold text-center leading-tight text-[var(--text-main)] max-w-[100px] truncate">{{ $awayTeam?->name ?? 'Away' }}</h3>
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

        <!-- Actions -->
        <div class="px-4 py-3 border-t bg-[var(--bg-element)]/30 border-[var(--border)] flex justify-between items-center gap-2">
            @if(isset($isCompleted) && $isCompleted)
                <a href="{{ route('scorer.console', $match->slug) }}" class="flex-1 text-center py-2 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] hover:text-[var(--text-main)] transition-colors">
                    View Stats
                </a>
            @else
                <a href="{{ route('scorer.console', $match->slug) }}" class="flex-1 flex items-center justify-center gap-2 py-2 rounded-lg font-bold text-xs uppercase tracking-wider transition-all shadow-md bg-[var(--accent)] hover:bg-[var(--accent-hover)]">
                    @if(isset($isLive) && $isLive)
                        <i class="fa-solid fa-pen-to-square"></i> Continue Scoring
                    @else
                        <i class="fa-solid fa-play"></i> Start Scoring
                    @endif
                </a>
                @if(!isset($isLive))
                <a href="{{ route('scorer.matches.edit', $match->id) }}" class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent)] hover:bg-[var(--bg-element)] transition-all">
                    <i class="fa-solid fa-edit"></i>
                </a>
                @endif
            @endif
        </div>
    </div>
</div>
