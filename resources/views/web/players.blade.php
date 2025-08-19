<section class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4 tracking-tight">
                Featured Players
            </h2>
            <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto">
                Meet the talented cricketers ready to compete in your leagues
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            @foreach($featuredPlayers as $player)
                <div class="group relative text-center rounded-xl bg-white shadow-md hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-1 overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 h-32 flex items-center justify-center relative overflow-hidden">
                        <div class="w-full h-full flex items-center justify-center">
                            <div class="w-16 h-16 rounded-full overflow-hidden bg-white bg-opacity-20 flex items-center justify-center">
                                <img src="{{ asset('images/defaultplayer.jpeg') }}" 
                                     alt="{{ $player->name }}" 
                                     class="w-full h-full object-cover"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-full h-full flex items-center justify-center text-white font-bold text-sm" style="display: none;">
                                    {{ strtoupper(substr($player->name, 0, 2)) }}
                                </div>
                            </div>
                        </div>
                        <div class="absolute bottom-2 left-2 right-2">
                            <span class="text-white text-xs font-medium bg-black bg-opacity-30 px-2 py-1 rounded-full">
                                {{ $player->position->name ?? 'Player' }}
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $player->name }}</h3>
                        <p class="text-gray-500 text-base mb-3">{{ $player->position->name ?? 'Cricket Player' }}</p>
                        <p class="text-sm text-gray-600 mb-4">
                            @if($player->position)
                                @switch($player->position->name)
                                    @case('Batter')
                                        Specialist in scoring runs and building innings
                                        @break
                                    @case('Bowler')
                                        Expert in taking wickets and restricting runs
                                        @break
                                    @case('All-Rounder')
                                        Contributes with both bat and ball
                                        @break
                                    @case('Wicket-Keeper Batter')
                                        Combines keeping and batting skills
                                        @break
                                    @default
                                        Talented cricket player
                                @endswitch
                            @else
                                Talented cricket player
                            @endif
                        </p>
                        <a href="{{ route('players.show', $player->slug) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                            View Profile
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="text-center">
            <a href="{{ route('players.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                View All Players
            </a>
        </div>
    </div>
</section>
