<section class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4 tracking-tight">
                Featured Teams
            </h2>
            <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto">
                Discover the best cricket teams in the region competing for glory
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            @foreach($featuredTeams as $team)
                <div class="group relative text-center rounded-xl bg-white shadow-md hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-1 overflow-hidden">
                    <div class="bg-gray-200 h-48 flex items-center justify-center">
                        @if($team->logo)
                            <img src="{{ asset($team->logo) }}" alt="{{ $team->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="text-gray-500 text-center p-4">
                                <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $team->name }}</h3>
                        <p class="text-gray-500 text-base mb-3">{{ $team->localBody->name }}, {{ $team->homeGround->district->name }}</p>
                        <p class="text-sm text-gray-600 mb-4">Home Ground: {{ $team->homeGround->name }}</p>
                        <a href="{{ route('teams.show', $team) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                            View Team
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="text-center">
            <a href="{{ route('teams.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                View All Teams
            </a>
        </div>
    </div>
</section>
