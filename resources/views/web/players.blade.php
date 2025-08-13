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
                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 h-32 flex items-center justify-center">
                        <div class="text-white text-center p-4">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium">{{ $player->role->name ?? 'Player' }}</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $player->name }}</h3>
                        <p class="text-gray-500 text-base mb-3">{{ $player->role->name ?? 'Cricket Player' }}</p>
                        <p class="text-sm text-gray-600 mb-4">
                            @if($player->role)
                                @switch($player->role->name)
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
                        <a href="{{ route('players.show', $player->id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
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
