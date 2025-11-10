@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">League Players</h1>
                    <p class="text-gray-600 mt-2">Browse players organized by leagues</p>
                </div>
                <a href="{{ route('teams.league-teams') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    View League Teams →
                </a>
            </div>
        </div>

        @php
            $activeLeague = $allLeagues->firstWhere('status', 'active') ?? $allLeagues->sortBy('name')->first();
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-6">
            @foreach($allLeagues->sortBy('name') as $tabLeague)
                <button onclick="showLeague({{ $tabLeague->id }})" 
                        id="tab-{{ $tabLeague->id }}"
                        class="futuristic-card relative p-1 rounded-2xl overflow-visible transition-all duration-500 {{ $tabLeague->id === $activeLeague->id ? 'active' : '' }}">
                    <div class="card-content bg-gray-900 rounded-xl p-4 h-full flex flex-col justify-center items-center relative z-10 transition-colors duration-1000">
                        <div class="text-center">
                            <div class="font-extrabold text-sm leading-tight {{ $tabLeague->id === $activeLeague->id ? 'text-cyan-300' : 'text-white' }} transition-colors duration-1000 line-clamp-2">{{ $tabLeague->name }}</div>
                            <div class="text-xs mt-2 opacity-70 font-semibold {{ $tabLeague->id === $activeLeague->id ? 'text-cyan-200' : 'text-gray-400' }}">{{ $tabLeague->leaguePlayers->count() }} players</div>
                            @if($tabLeague->start_date->isFuture())
                                <div class="text-xs mt-1 font-bold {{ $tabLeague->id === $activeLeague->id ? 'text-cyan-300' : 'text-emerald-400' }}" 
                                     data-countdown="{{ $tabLeague->start_date->toIso8601String() }}"
                                     id="countdown-{{ $tabLeague->id }}">
                                </div>
                            @endif
                        </div>
                    </div>
                </button>
            @endforeach
        </div>

        <style>
        .futuristic-card {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
            min-height: 100px;
        }
        .futuristic-card.active {
            background: linear-gradient(135deg, #0284c7 0%, #0891b2 100%);
        }
        .futuristic-card::after {
            position: absolute;
            content: "";
            top: 30px;
            left: 0;
            right: 0;
            z-index: -1;
            height: 100%;
            width: 100%;
            transform: scale(0.8);
            filter: blur(25px);
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
            transition: opacity 0.5s;
        }
        .futuristic-card.active::after {
            background: linear-gradient(135deg, #0284c7 0%, #0891b2 100%);
        }
        .futuristic-card:hover::after {
            opacity: 0;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-break: break-word;
        }
        </style>

        <script>
        function updateCountdowns() {
            document.querySelectorAll('[data-countdown]').forEach(el => {
                const targetDate = new Date(el.dataset.countdown);
                const now = new Date();
                const diff = targetDate - now;
                
                if (diff > 0) {
                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                    
                    el.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s`;
                } else {
                    el.textContent = 'Started';
                }
            });
        }
        
        updateCountdowns();
        setInterval(updateCountdowns, 1000);
        </script>

        <div class="flex-1">
        @forelse($allLeagues->sortBy('name') as $league)
            <div id="league-{{ $league->id }}" class="league-content bg-white rounded-lg shadow mb-8 {{ $league->id !== $activeLeague->id ? 'hidden' : '' }}">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center">
                            @if($league->logo)
                                <img src="{{ Storage::url($league->logo) }}" class="w-12 h-12 rounded-full object-cover mr-4">
                            @endif
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">{{ $league->name }}</h2>
                                <p class="text-gray-600">{{ $league->game->name }} • Season {{ $league->season }}</p>
                            </div>
                        </div>
                        <a href="{{ route('leagues.public-players', $league) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                            View All →
                        </a>
                    </div>
                </div>

                <div class="p-6">
                    @php
                        $retentionPlayers = $league->leaguePlayers->where('retention', true)->sortBy('leagueTeam.team.name');
                        $soldPlayers = $league->leaguePlayers->where('status', 'sold')->where('retention', false)->sortByDesc('bid_price');
                        $availablePlayers = $league->leaguePlayers->where('status', 'available')->where('retention', false)->sortBy('user.name');
                        $unsoldPlayers = $league->leaguePlayers->where('status', 'unsold')->where('retention', false)->sortBy('user.name');
                    @endphp

                    @if($retentionPlayers->count() > 0)
                        <div class="mb-8">
                            <button onclick="toggleSection('retention-{{ $league->id }}')" class="w-full text-left">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center justify-between">
                                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">Retained Players ({{ $retentionPlayers->count() }})</span>
                                    <svg id="retention-{{ $league->id }}-icon" class="w-5 h-5 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </h3>
                            </button>
                            <div id="retention-{{ $league->id }}" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                @foreach($retentionPlayers as $player)
                                    <div class="bg-white border-2 border-yellow-200 rounded-xl p-4 text-center hover:shadow-lg transition-all relative">
                                        <svg class="w-6 h-6 text-yellow-500 absolute top-2 right-2 drop-shadow-lg" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        @if($player->user && $player->user->photo)
                                            <img src="{{ Storage::url($player->user->photo) }}" class="w-20 h-20 rounded-full object-cover mx-auto mb-2 border-2 border-yellow-400">
                                        @else
                                            <div class="w-20 h-20 rounded-full bg-yellow-100 flex items-center justify-center mx-auto mb-2 border-2 border-yellow-400">
                                                <span class="text-2xl font-bold text-yellow-600">{{ $player->user ? substr($player->user->name, 0, 1) : '?' }}</span>
                                            </div>
                                        @endif
                                        <p class="font-semibold text-gray-900 truncate">{{ $player->user->name ?? 'Unknown' }}</p>
                                        @if($player->leagueTeam)
                                            <p class="text-xs text-gray-600 truncate">{{ $player->leagueTeam->team->name }}</p>
                                        @endif
                                        <p class="text-xs text-yellow-600 font-semibold mt-1">Retained</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($soldPlayers->count() > 0)
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">Sold Players ({{ $soldPlayers->count() }})</span>
                            </h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                @foreach($soldPlayers as $player)
                                    <div class="bg-white border-2 border-green-200 rounded-xl p-4 text-center hover:shadow-lg transition-all">
                                        @if($player->user && $player->user->photo)
                                            <img src="{{ Storage::url($player->user->photo) }}" class="w-20 h-20 rounded-full object-cover mx-auto mb-2 border-2 border-green-400">
                                        @else
                                            <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-2 border-2 border-green-400">
                                                <span class="text-2xl font-bold text-green-600">{{ $player->user ? substr($player->user->name, 0, 1) : '?' }}</span>
                                            </div>
                                        @endif
                                        <p class="font-semibold text-gray-900 truncate">{{ $player->user->name ?? 'Unknown' }}</p>
                                        @if($player->leagueTeam)
                                            <p class="text-xs text-gray-600 truncate">{{ $player->leagueTeam->team->name }}</p>
                                        @endif
                                        @if($player->bid_price)
                                            <p class="text-sm text-green-600 font-bold mt-1">₹{{ number_format($player->bid_price) }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($availablePlayers->count() > 0)
                        <div class="mb-8">
                            <button onclick="toggleSection('available-{{ $league->id }}')" class="w-full text-left">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center justify-between">
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">Available Players ({{ $availablePlayers->count() }})</span>
                                    <svg id="available-{{ $league->id }}-icon" class="w-5 h-5 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </h3>
                            </button>
                            <div id="available-{{ $league->id }}" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                @foreach($availablePlayers as $player)
                                    <div class="bg-white border-2 border-blue-200 rounded-xl p-4 text-center hover:shadow-lg transition-all">
                                        @if($player->user && $player->user->photo)
                                            <img src="{{ Storage::url($player->user->photo) }}" class="w-20 h-20 rounded-full object-cover mx-auto mb-2 border-2 border-blue-400">
                                        @else
                                            <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-2 border-2 border-blue-400">
                                                <span class="text-2xl font-bold text-blue-600">{{ $player->user ? substr($player->user->name, 0, 1) : '?' }}</span>
                                            </div>
                                        @endif
                                        <p class="font-semibold text-gray-900 truncate">{{ $player->user->name ?? 'Unknown' }}</p>
                                        @if($player->base_price)
                                            <p class="text-sm text-blue-600 font-bold mt-1">₹{{ number_format($player->base_price) }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($unsoldPlayers->count() > 0)
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">Unsold Players ({{ $unsoldPlayers->count() }})</span>
                            </h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                @foreach($unsoldPlayers as $player)
                                    <div class="bg-white border-2 border-gray-200 rounded-xl p-4 text-center hover:shadow-lg transition-all">
                                        @if($player->user && $player->user->photo)
                                            <img src="{{ Storage::url($player->user->photo) }}" class="w-20 h-20 rounded-full object-cover mx-auto mb-2 border-2 border-gray-400">
                                        @else
                                            <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-2 border-2 border-gray-400">
                                                <span class="text-2xl font-bold text-gray-600">{{ $player->user ? substr($player->user->name, 0, 1) : '?' }}</span>
                                            </div>
                                        @endif
                                        <p class="font-semibold text-gray-900 truncate">{{ $player->user->name ?? 'Unknown' }}</p>
                                        @if($player->base_price)
                                            <p class="text-sm text-gray-600 font-bold mt-1">₹{{ number_format($player->base_price) }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($soldPlayers->count() == 0 && $retentionPlayers->count() == 0 && $availablePlayers->count() == 0 && $unsoldPlayers->count() == 0)
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <p class="text-gray-500">No players found in this league</p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <p class="text-gray-500">No league players found</p>
            </div>
        @endforelse
        </div>
    </div>
</div>

<script>
function showLeague(leagueId) {
    document.querySelectorAll('.league-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.futuristic-card').forEach(el => el.classList.remove('active'));
    
    document.getElementById('league-' + leagueId).classList.remove('hidden');
    document.getElementById('tab-' + leagueId).classList.add('active');
}

function toggleSection(sectionId) {
    const section = document.getElementById(sectionId);
    const icon = document.getElementById(sectionId + '-icon');
    
    if (section.classList.contains('hidden')) {
        section.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        section.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}
</script>
@endsection
