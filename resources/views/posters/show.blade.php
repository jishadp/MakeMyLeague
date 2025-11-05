@extends('layouts.app')

@section('title', $leagueTeam->team->name . ' Poster')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 py-4 px-2 sm:py-8 sm:px-4">
    <div class="max-w-2xl mx-auto">
        <div class="mb-4 flex justify-between items-center">
            <a href="{{ route('posters.list') }}" class="px-3 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20 text-sm">
                ← Back
            </a>
            <button onclick="downloadPoster()" class="px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
                Download
            </button>
        </div>

        <!-- Mobile-Friendly Poster -->
        <div id="poster" class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl shadow-2xl overflow-hidden">
            
            <!-- Header -->
            <div class="relative bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-4">
                <div class="flex items-center justify-between">
                    @if($league->logo)
                        <img src="{{ Storage::url($league->logo) }}" class="h-12 w-12 object-contain">
                    @else
                        <div class="h-12 w-12 bg-white/20 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr($league->name, 0, 1) }}
                        </div>
                    @endif
                    <div class="flex-1 text-center px-2">
                        <h1 class="text-lg sm:text-xl font-bold text-white">{{ $league->name }}</h1>
                        <p class="text-white/80 text-xs">Season {{ date('Y') }}</p>
                    </div>
                    <div class="h-12 w-12"></div>
                </div>
            </div>

            <!-- Team & Owner Section -->
            <div class="relative p-6">
                <!-- Decorative Elements -->
                <div class="absolute top-4 left-4 opacity-10">
                    <svg class="w-8 h-8 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
                <div class="absolute top-4 right-4 opacity-10">
                    <svg class="w-8 h-8 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>

                <!-- Team Logo -->
                <div class="text-center relative z-10">
                    @if($leagueTeam->team->logo)
                        <img src="{{ Storage::url($leagueTeam->team->logo) }}" 
                             class="w-20 h-20 mx-auto rounded-full object-cover border-4 border-white/20 shadow-xl">
                    @else
                        <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl border-4 border-white/20 shadow-xl">
                            {{ substr($leagueTeam->team->name, 0, 2) }}
                        </div>
                    @endif
                    <h2 class="text-2xl sm:text-3xl font-bold text-white mt-4">{{ $leagueTeam->team->name }}</h2>
                    
                    @if($owner)
                        <div class="mt-3 inline-flex items-center bg-gradient-to-r from-yellow-400 to-orange-500 px-4 py-2 rounded-full">
                            <svg class="w-4 h-4 text-slate-900 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <div class="text-left">
                                <p class="text-xs font-semibold text-slate-900">OWNER</p>
                                <p class="text-sm font-bold text-slate-900">{{ $owner->user->name }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Players Squad -->
            <div class="px-4 pb-6">
                <div class="bg-slate-800/50 rounded-xl p-4 backdrop-blur border border-white/10">
                    <div class="flex items-center justify-center mb-4">
                        <div class="h-px bg-gradient-to-r from-transparent via-indigo-500 to-transparent flex-1"></div>
                        <h3 class="text-xl font-bold text-white px-4">SQUAD</h3>
                        <div class="h-px bg-gradient-to-r from-transparent via-indigo-500 to-transparent flex-1"></div>
                    </div>
                    
                    @if($leagueTeam->players->count() > 0)
                        <div class="grid grid-cols-3 gap-2">
                            @foreach($leagueTeam->players->sortBy('user.name') as $player)
                                <div class="bg-gradient-to-br from-slate-700/80 to-slate-800/80 rounded-lg p-2 border border-white/5 hover:border-indigo-500/50 transition-all">
                                    <div class="flex flex-col items-center text-center">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm mb-2 shadow-lg">
                                            {{ substr($player->user->name, 0, 1) }}
                                        </div>
                                        <p class="text-white font-semibold text-xs leading-tight mb-1">{{ $player->user->name }}</p>
                                        <span class="inline-block px-2 py-0.5 bg-indigo-500/20 text-indigo-300 rounded text-[10px] font-medium">
                                            {{ $player->user->position->name ?? 'Player' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p class="text-gray-400 text-sm">No players yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 py-3 text-center">
                <p class="text-white font-semibold text-sm">{{ $league->name }} • {{ date('Y') }}</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
function downloadPoster() {
    const poster = document.getElementById('poster');
    const button = event.target;
    button.textContent = 'Generating...';
    button.disabled = true;
    
    html2canvas(poster, {
        scale: 3,
        backgroundColor: '#0f172a',
        logging: false,
        useCORS: true
    }).then(canvas => {
        const link = document.createElement('a');
        link.download = '{{ Str::slug($leagueTeam->team->name) }}-poster.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
        button.textContent = 'Download';
        button.disabled = false;
    });
}
</script>
@endsection
