@php
    $timestampLabel = $timestampLabel ?? '';
    $locationName = optional(optional($playerModel->league->localBody)->district)->name ?? 'Location';
    $homeBase = optional(optional(optional($playerModel->leagueTeam)->team)->localBody)->name ?? 'Home';
    $seasonLabel = 'S' . ($playerModel->league->season ?? '-');
    $showPosition = \Illuminate\Support\Str::length($playerModel->user->name) <= 18;
@endphp

<article class="flip-card flex-none w-[31%] min-w-[110px] max-w-[140px] sm:w-56 sm:max-w-[240px] snap-start snap-center perspective-1000 group focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2 focus-within:ring-offset-white">
    <div class="flip-card-inner relative w-full transition-transform duration-700 ease-out transform-style-3d group-hover:rotate-y-180 group-focus-within:rotate-y-180" style="transform-style: preserve-3d;">
        
        <!-- Front Side -->
        <div class="flip-card-front absolute w-full h-full backface-hidden bg-white border border-gray-200 rounded-2xl shadow-lg p-3 sm:p-5 overflow-hidden" style="backface-visibility: hidden;">
            <div class="flex items-center justify-between text-[9px] sm:text-[10px] font-bold uppercase tracking-wide mb-3 sm:mb-4 text-gray-500">
                <span>{{ $leagueAcronym }}</span>
                <span class="text-[8px] sm:text-[9px] font-semibold text-gray-400">{{ $timestampLabel }}</span>
            </div>

            <div class="absolute top-2 right-2 sm:hidden inline-flex items-center gap-1 text-[9px] font-semibold text-blue-600 bg-white/90 px-2 py-1 rounded-full shadow-sm" aria-hidden="true">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4" />
                    <circle cx="12" cy="16" r="0.75" fill="currentColor" stroke="none"></circle>
                    <circle cx="12" cy="12" r="9" stroke-width="2" stroke="currentColor"></circle>
                </svg>
            </div>

            <div class="sold-price-pill w-full text-center mb-3 sm:mb-4">
                <div class="inline-flex flex-col items-center bg-white border border-green-100 rounded-2xl px-3 sm:px-4 py-1.5 shadow-sm">
                    <span class="text-sm sm:text-base font-black text-green-600 leading-none">₹{{ number_format($playerModel->bid_price, 0) }}</span>
                </div>
            </div>

            <a href="{{ route('players.show', $playerModel->user) }}" class="flex flex-col items-center mb-3 sm:mb-4 group focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-3xl w-full">
                <div class="p-1.5 sm:p-2 rounded-3xl bg-gradient-to-br from-gray-50 to-gray-100">
                    @if($playerPhoto)
                    <img src="{{ $playerPhoto }}" alt="{{ $playerModel->user->name }}" class="w-24 h-24 sm:w-32 sm:h-32 rounded-2xl object-cover border-2 border-white shadow-md group-hover:scale-105 transition-transform" loading="lazy">
                    @else
                    <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 text-white font-black text-2xl sm:text-3xl flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                        {{ $playerInitials }}
                    </div>
                    @endif
                </div>

                <div class="text-center mt-2 sm:mt-3 w-full">
                    <p class="text-xs sm:text-base font-black text-gray-900 leading-tight group-hover:text-blue-600 transition-colors px-1 sm:px-2"
                       style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                        {{ $playerModel->user->name }}
                    </p>
                    @if($showPosition)
                    <p class="text-[10px] sm:text-xs font-semibold text-gray-500 truncate w-full mt-0.5 sm:mt-1">{{ $playerModel->user->position->name ?? 'Position N/A' }}</p>
                    @endif
                </div>
            </a>

        </div>

        <!-- Back Side -->
        <div class="flip-card-back absolute w-full h-full backface-hidden bg-gradient-to-br from-blue-500 to-blue-700 border border-blue-600 rounded-2xl shadow-lg p-3 sm:p-5 flex flex-col justify-center overflow-hidden" style="backface-visibility: hidden; transform: rotateY(180deg);">
            <div class="text-white space-y-2 sm:space-y-3">
                <h3 class="text-sm sm:text-lg font-black text-center mb-3 sm:mb-4">Player Details</h3>
                
                <div class="flex items-center gap-2 text-[10px] sm:text-xs font-semibold">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 7v6l-9 4-9-4V7"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13l9 4 9-4"/>
                    </svg>
                    <span class="truncate">{{ $teamShort }}</span>
                </div>
                
                <div class="flex items-center gap-2 text-[10px] sm:text-xs font-semibold">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.567 3-3.5S13.657 4 12 4 9 5.567 9 7.5 10.343 11 12 11z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v9"/>
                    </svg>
                    <span class="truncate">{{ $locationName }}</span>
                </div>
                
                <div class="flex items-center gap-2 text-[10px] sm:text-xs font-semibold">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/>
                        <circle cx="12" cy="12" r="9" stroke-width="2"/>
                    </svg>
                    <span class="truncate">{{ $seasonLabel }}</span>
                </div>
                
                <div class="flex items-center gap-2 text-[10px] sm:text-xs font-semibold">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10l9-7 9 7v7a2 2 0 01-2 2h-2.5a1.5 1.5 0 01-1.5-1.5v-3a1.5 1.5 0 00-1.5-1.5H9a1.5 1.5 0 00-1.5 1.5v3A1.5 1.5 0 016 19H4a2 2 0 01-2-2v-7z"/>
                    </svg>
                    <span class="truncate">{{ $homeBase }}</span>
                </div>

                <div class="text-center mt-4 pt-3 border-t border-blue-400 px-2">
                    <div class="text-[9px] sm:text-[10px] font-semibold uppercase mb-1 opacity-90">Sold Price</div>
                    <div class="text-lg sm:text-2xl font-black break-words leading-tight">₹{{ number_format($playerModel->bid_price, 0) }}</div>
                </div>
            </div>
        </div>
        
    </div>
</article>

<style>
.perspective-1000 {
    perspective: 1000px;
}

.transform-style-3d {
    transform-style: preserve-3d;
}

.backface-hidden {
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
}

.rotate-y-180 {
    transform: rotateY(180deg);
}

.flip-card-inner {
    transition: transform 0.7s cubic-bezier(0.4, 0.2, 0.1, 1);
    will-change: transform;
}

.flip-card:focus-within .flip-card-inner,
.flip-card:hover .flip-card-inner {
    transform: rotateY(180deg);
}
</style>
