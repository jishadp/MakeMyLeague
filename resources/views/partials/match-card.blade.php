@php
    $homeTeam = $match->homeTeam?->team;
    $awayTeam = $match->awayTeam?->team;
@endphp
<div class="rounded-2xl border overflow-hidden shadow-sm group transition-all bg-[var(--bg-card)] border-[var(--border)] hover:border-[var(--accent)]/20">
    <div class="block relative">
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
                      @if($match->status === 'completed')
                          <div class="text-2xl sm:text-3xl font-black tabular-nums tracking-tighter text-[var(--text-main)]">
                              {{ $match->home_score ?? 0 }}<span class="text-[var(--text-muted)] mx-1">-</span>{{ $match->away_score ?? 0 }}
                          </div>
                          <span class="text-[10px] font-bold bg-[var(--bg-element)] px-2 py-0.5 rounded text-[var(--text-muted)] mt-1">FT</span>
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
    </div>
</div>
