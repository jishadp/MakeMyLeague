@php
    $val = $player->bid_price ?? $player->base_price ?? 0;
    $displayValue = $player->retention ? '∞' : '₹' . number_format($val);
    $displayRole = $player->user?->position?->name ?? 'Role';
    $displayName = $player->user?->name ? trim($player->user->name) : 'Unknown';
    
    $status = $player->status ?? 'available';
    $statusColors = [
        'pending' => 'bg-gray-100 text-gray-800',
        'available' => 'bg-blue-100 text-blue-800',
        'auctioning' => 'bg-amber-100 text-amber-800',
        'sold' => 'bg-green-100 text-green-800',
        'unsold' => 'bg-red-100 text-red-800',
        'skip' => 'bg-gray-100 text-gray-800',
    ];
    $letterMap = [
        'available' => 'A',
        'auctioning' => 'B',
        'sold' => 'S',
        'unsold' => 'U',
        'pending' => 'P',
        'skip' => 'P',
    ];
    $statusLetter = $letterMap[$status] ?? 'A';
    $firstName = $player->user?->name ? explode(' ', trim($player->user->name))[0] : 'Unknown';
    
    // Foreign status is now pre-calculated in the sort function or via accessor if available, 
    // but here we fall back to checking the property if not set, or re-calculating if needed.
    // In the main view, we set $player->is_foreign.
    $isForeign = $player->is_foreign ?? false;
    $placeName = $player->user?->localBody?->name ?? 'Unknown';
@endphp
<a href="{{ route('players.show', $player->user) }}" class="block text-decoration-none h-full">
<div class="relative h-full rounded-xl border transition-all duration-300 {{ $isForeign ? 'foreign-card' : 'border-slate-200 bg-white shadow-sm hover:shadow-md' }} px-3 py-4 flex flex-col justify-between player-card" 
     data-player-name="{{ strtolower($player->user?->name ?? '') }}" 
     data-status="{{ $status }}" 
     data-retained="{{ $player->retention ? 'true' : 'false' }}"
     data-share-info="{{ $displayName }} - {{ $displayRole }} ({{ $displayValue }})"
     data-csv-name="{{ $displayName }}"
     data-csv-role="{{ $displayRole }}"
     data-csv-price="{{ $val }}"
     data-csv-status="{{ ucfirst($status) }}"
     data-csv-retained="{{ $player->retention ? 'Yes' : 'No' }}"
     >
    @if($isForeign)
        <div class="absolute top-0 left-0 foreign-badge text-white text-[9px] font-bold px-2 py-0.5 rounded-tl-xl rounded-br-lg z-10">
            {{ Str::limit(strtoupper($placeName), 12) }}
        </div>
    @endif
    <div class="flex flex-col items-center text-center space-y-2 mt-1 {{ $isForeign ? 'relative z-10' : '' }}">
        <div class="relative inline-block">
            @if($player->user?->photo)
                <img src="{{ Storage::url($player->user->photo) }}" class="w-16 h-16 rounded-full object-cover border-2 {{ $isForeign ? 'border-amber-400' : 'border-white' }} shadow ring-2 {{ $isForeign ? 'ring-amber-300' : 'ring-slate-100' }} relative z-0">
            @else
                <div class="w-16 h-16 rounded-full {{ $isForeign ? 'bg-amber-100 border-amber-300' : 'bg-slate-100 border-slate-200' }} border flex items-center justify-center text-sm font-semibold {{ $isForeign ? 'text-amber-800' : 'text-slate-600' }} ring-2 {{ $isForeign ? 'ring-amber-300' : 'ring-slate-100' }} shadow relative z-0">
                    {{ strtoupper(substr($player->user?->name ?? 'P', 0, 1)) }}
                </div>
            @endif
            @if($player->retention)
                <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-500 text-white shadow z-10 border-2 border-white">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </span>
            @endif
            @if($isForeign)
                <div class="absolute inset-[-8px] pointer-events-none flight-orbit z-20">
                    <div class="absolute -top-1 left-1/2 -translate-x-1/2 transform text-amber-600 foreign-plane">
                        <svg class="w-6 h-6 transform rotate-90" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                        </svg>
                    </div>
                </div>
            @endif
            <span class="absolute -bottom-1 -right-1 inline-flex items-center justify-center w-6 h-6 rounded-full text-white text-[10px] font-bold {{ $statusColors[$status] ?? 'bg-gray-400 text-white' }} border-2 border-white z-10">
                {{ $statusLetter }}
            </span>
        </div>
        <p class="text-sm font-semibold text-slate-900 truncate w-full">{{ $firstName }}</p>
        <p class="text-[11px] text-slate-500 truncate w-full">{{ $player->user?->position?->name ?? 'Role' }}</p>
        <p class="text-sm font-bold {{ in_array($status, ['sold','available']) ? 'text-green-700' : ($status === 'auctioning' ? 'text-amber-700' : ($status === 'unsold' ? 'text-red-700' : 'text-slate-800')) }}">
            {{ $displayValue }}
        </p>
    </div>
</div>
</a>
