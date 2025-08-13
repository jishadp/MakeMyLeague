<section class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4 tracking-tight">
                Premier Cricket Grounds
            </h2>
            <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto">
                Discover world-class cricket grounds across Wayanad ready to host your next match
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            @foreach($featuredGrounds as $ground)
                <div class="group relative text-center rounded-xl bg-white shadow-md hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-1 overflow-hidden">
                    <div class="bg-gray-200 h-48 flex items-center justify-center">
                        @if($ground->image)
                            <img src="{{ asset($ground->image) }}" alt="{{ $ground->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="text-gray-500 text-center p-4">
                                <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $ground->name }}</h3>
                        <p class="text-gray-500 text-base mb-3">{{ $ground->localBody->name }}, {{ $ground->district->name }}</p>
                        @if($ground->capacity)
                            <p class="text-sm text-gray-600 mb-4">Capacity: {{ number_format($ground->capacity) }}</p>
                        @endif
                        <a href="{{ route('grounds.show', $ground) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                            View Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="text-center">
            <a href="{{ route('grounds.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                View All Grounds
            </a>
        </div>
    </div>
</section>
