@extends('layouts.app')

@section('title', 'Fixture Setup - ' . $league->name)

@section('content')
<div class="py-2 sm:py-4 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8">
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl overflow-hidden">
            <div class="p-3 sm:p-6 lg:p-8">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start mb-6 sm:mb-8 gap-3 sm:gap-4">
                    <div class="w-full sm:w-auto">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">Fixture Setup</h1>
                        <p class="text-sm sm:text-base text-gray-600">{{ $league->name }} - Season {{ $league->season }}</p>
                    </div>
                    <div class="w-full sm:w-auto flex gap-2">
                        <a href="{{ route('leagues.league-match', $league) }}" 
                           class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-gray-100 text-gray-800 font-medium rounded-lg shadow-sm hover:bg-gray-200 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Groups
                        </a>
                        <a href="{{ route('leagues.fixtures', $league) }}" 
                           class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg shadow-sm hover:bg-indigo-700 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            View Fixtures
                        </a>
                    </div>
                </div>

                @if($fixtures->count() > 0)
                    <!-- Fixtures Management -->
                    <div class="space-y-6">
                        @foreach($groups as $group)
                            <div class="bg-gray-50 rounded-lg p-4 sm:p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900">{{ $group->name }}</h2>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $fixtures->where('league_group_id', $group->id)->count() }} Matches
                                    </span>
                                </div>

                                <div class="space-y-3">
                                    @foreach($fixtures->where('league_group_id', $group->id) as $fixture)
                                        <div class="bg-white rounded-lg border border-gray-200 p-3 sm:p-4">
                                            <!-- Mobile Layout -->
                                            <div class="sm:hidden">
                                                <div class="flex items-center justify-between mb-3">
                                                    <span class="text-xs font-medium text-gray-500">Match {{ $loop->iteration }}</span>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                        {{ $fixture->status === 'scheduled' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                        {{ ucfirst(str_replace('_', ' ', $fixture->status)) }}
                                                    </span>
                                                </div>
                                                
                                                <div class="space-y-2 mb-4">
                                                    <div class="flex items-center justify-between">
                                                        <span class="font-medium text-gray-900">{{ $fixture->homeTeam->team->name }}</span>
                                                        <span class="text-sm text-gray-600">Home</span>
                                                    </div>
                                                    <div class="text-center text-xs text-gray-500">VS</div>
                                                    <div class="flex items-center justify-between">
                                                        <span class="font-medium text-gray-900">{{ $fixture->awayTeam->team->name }}</span>
                                                        <span class="text-sm text-gray-600">Away</span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Mobile Schedule Form -->
                                                <div class="grid grid-cols-2 gap-2">
                                                    <input type="date" 
                                                           value="{{ $fixture->match_date ? $fixture->match_date->format('Y-m-d') : '' }}"
                                                           class="border border-gray-300 rounded px-2 py-1 text-xs"
                                                           onchange="updateFixture({{ $fixture->id }}, 'match_date', this.value)">
                                                    <input type="time" 
                                                           value="{{ $fixture->match_time ? $fixture->match_time->format('H:i') : '' }}"
                                                           class="border border-gray-300 rounded px-2 py-1 text-xs"
                                                           onchange="updateFixture({{ $fixture->id }}, 'match_time', this.value)">
                                                    <input type="text" 
                                                           placeholder="Venue"
                                                           value="{{ $fixture->venue ?? '' }}"
                                                           class="border border-gray-300 rounded px-2 py-1 text-xs col-span-2"
                                                           onchange="updateFixture({{ $fixture->id }}, 'venue', this.value)">
                                                </div>
                                            </div>

                                            <!-- Desktop Layout -->
                                            <div class="hidden sm:block">
                                                <div class="grid grid-cols-12 gap-4 items-center">
                                                    <!-- Teams -->
                                                    <div class="col-span-4">
                                                        <div class="flex items-center justify-between">
                                                            <span class="font-medium text-gray-900">{{ $fixture->homeTeam->team->name }}</span>
                                                            <span class="text-sm text-gray-500">vs</span>
                                                            <span class="font-medium text-gray-900">{{ $fixture->awayTeam->team->name }}</span>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Date -->
                                                    <div class="col-span-2">
                                                        <input type="date" 
                                                               value="{{ $fixture->match_date ? $fixture->match_date->format('Y-m-d') : '' }}"
                                                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                                                               onchange="updateFixture({{ $fixture->id }}, 'match_date', this.value)">
                                                    </div>
                                                    
                                                    <!-- Time -->
                                                    <div class="col-span-2">
                                                        <input type="time" 
                                                               value="{{ $fixture->match_time ? $fixture->match_time->format('H:i') : '' }}"
                                                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                                                               onchange="updateFixture({{ $fixture->id }}, 'match_time', this.value)">
                                                    </div>
                                                    
                                                    <!-- Venue -->
                                                    <div class="col-span-3">
                                                        <input type="text" 
                                                               placeholder="Venue"
                                                               value="{{ $fixture->venue ?? '' }}"
                                                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                                                               onchange="updateFixture({{ $fixture->id }}, 'venue', this.value)">
                                                    </div>
                                                    
                                                    <!-- Status -->
                                                    <div class="col-span-1">
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                            {{ $fixture->status === 'scheduled' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                            {{ ucfirst(str_replace('_', ' ', $fixture->status)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- No Fixtures -->
                    <div class="text-center py-8 sm:py-12">
                        <div class="w-16 h-16 sm:w-24 sm:h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                            <svg class="w-8 h-8 sm:w-12 sm:h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">No Fixtures Available</h3>
                        <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6 px-4">Generate fixtures first to set up match schedules.</p>
                        <a href="{{ route('leagues.league-match', $league) }}" 
                           class="inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors text-sm sm:text-base">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Generate Fixtures
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function updateFixture(fixtureId, field, value) {
    fetch(`/leagues/{{ $league->slug }}/fixtures/${fixtureId}/update`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            [field]: value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success feedback
            const element = event.target;
            const originalBorder = element.className;
            element.className = element.className.replace('border-gray-300', 'border-green-500');
            setTimeout(() => {
                element.className = originalBorder;
            }, 1000);
        } else {
            alert('Error updating fixture: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the fixture.');
    });
}
</script>
@endsection