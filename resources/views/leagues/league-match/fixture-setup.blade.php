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

                <!-- Manual Fixture Creation -->
                <div class="bg-gray-50 rounded-lg p-4 sm:p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Create New Fixture</h2>
                    <form id="fixture-form" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Match Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Match Type</label>
                                <select id="match_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option value="group_stage">League Match</option>
                                    <option value="quarter_final">Quarter Final</option>
                                    <option value="semi_final">Semi Final</option>
                                    <option value="final">Final</option>
                                </select>
                            </div>
                            
                            <!-- Group Selection -->
                            <div id="group-selection">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Group</label>
                                <select id="league_group_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option value="">Select Group</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Match Date</label>
                                <input type="date" id="match_date" min="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Home Team -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Home Team</label>
                                <select id="home_team_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option value="">Select Home Team</option>
                                </select>
                            </div>
                            
                            <!-- Away Team -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Away Team</label>
                                <select id="away_team_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option value="">Select Away Team</option>
                                </select>
                            </div>
                            
                            <!-- Time -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Match Time</label>
                                <input type="time" id="match_time" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Venue</label>
                            <input type="text" id="venue" placeholder="Enter venue name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 font-medium text-sm">
                                Create Fixture
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Existing Fixtures -->
                @if($fixtures->count() > 0)
                    <!-- PDF Export Button -->
                    <div class="mb-6 flex justify-end">
                        <a href="{{ route('leagues.fixtures.pdf', $league) }}" 
                           class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download PDF
                        </a>
                    </div>
                    
                    <!-- Fixtures Management -->
                    <div class="space-y-6">
                        <!-- Group Stage Fixtures -->
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
                        
                        <!-- Knockout Stage Fixtures -->
                        @php
                            $knockoutFixtures = $fixtures->whereNotNull('match_type')->where('match_type', '!=', 'group_stage')->groupBy('match_type');
                        @endphp
                        
                        @if($knockoutFixtures->has('quarter_final'))
                            <div class="bg-yellow-50 rounded-lg p-4 sm:p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Quarter Finals</h2>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ $knockoutFixtures['quarter_final']->count() }} Matches
                                    </span>
                                </div>
                                <div class="space-y-3">
                                    @foreach($knockoutFixtures['quarter_final'] as $fixture)
                                        @include('leagues.league-match.partials.fixture-row', ['fixture' => $fixture])
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        @if($knockoutFixtures->has('semi_final'))
                            <div class="bg-orange-50 rounded-lg p-4 sm:p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Semi Finals</h2>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        {{ $knockoutFixtures['semi_final']->count() }} Matches
                                    </span>
                                </div>
                                <div class="space-y-3">
                                    @foreach($knockoutFixtures['semi_final'] as $fixture)
                                        @include('leagues.league-match.partials.fixture-row', ['fixture' => $fixture])
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        @if($knockoutFixtures->has('final'))
                            <div class="bg-green-50 rounded-lg p-4 sm:p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Final</h2>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $knockoutFixtures['final']->count() }} Match
                                    </span>
                                </div>
                                <div class="space-y-3">
                                    @foreach($knockoutFixtures['final'] as $fixture)
                                        @include('leagues.league-match.partials.fixture-row', ['fixture' => $fixture])
                                    @endforeach
                                </div>
                            </div>
                        @endif
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
const groups = @json($groups);
console.log('Groups data:', groups); // Debug log

document.addEventListener('DOMContentLoaded', function() {
    const matchTypeSelect = document.getElementById('match_type');
    const groupSelection = document.getElementById('group-selection');
    const groupSelect = document.getElementById('league_group_id');
    const homeTeamSelect = document.getElementById('home_team_id');
    const awayTeamSelect = document.getElementById('away_team_id');
    const fixtureForm = document.getElementById('fixture-form');
    
    // Handle match type change
    matchTypeSelect.addEventListener('change', function() {
        if (this.value === 'group_stage') {
            groupSelection.style.display = 'block';
        } else {
            groupSelection.style.display = 'none';
            groupSelect.value = '';
            updateTeamOptions([]);
        }
    });
    
    // Handle group selection change
    groupSelect.addEventListener('change', function() {
        const selectedGroup = groups.find(g => g.id == this.value);
        console.log('Selected group:', selectedGroup); // Debug log
        if (selectedGroup && selectedGroup.league_teams) {
            updateTeamOptions(selectedGroup.league_teams);
        } else {
            updateTeamOptions([]);
        }
    });
    
    // Handle form submission
    fixtureForm.addEventListener('submit', function(e) {
        e.preventDefault();
        createFixture();
    });
    
    function updateTeamOptions(teams) {
        console.log('Updating team options with:', teams); // Debug log
        homeTeamSelect.innerHTML = '<option value="">Select Home Team</option>';
        awayTeamSelect.innerHTML = '<option value="">Select Away Team</option>';
        
        // For knockout matches, show all teams from all groups
        const matchType = matchTypeSelect.value;
        if (matchType !== 'group_stage') {
            // Get all teams from all groups
            const allTeams = [];
            groups.forEach(group => {
                if (group.league_teams) {
                    allTeams.push(...group.league_teams);
                }
            });
            
            allTeams.forEach(leagueTeam => {
                const teamName = leagueTeam.team ? leagueTeam.team.name : 'Unknown Team';
                const option = `<option value="${leagueTeam.id}">${teamName}</option>`;
                homeTeamSelect.innerHTML += option;
                awayTeamSelect.innerHTML += option;
            });
        } else if (teams && teams.length > 0) {
            // For group stage, show only teams from selected group
            teams.forEach(leagueTeam => {
                const teamName = leagueTeam.team ? leagueTeam.team.name : 'Unknown Team';
                const option = `<option value="${leagueTeam.id}">${teamName}</option>`;
                homeTeamSelect.innerHTML += option;
                awayTeamSelect.innerHTML += option;
            });
        }
    }
    
    function createFixture() {
        const formData = {
            match_type: document.getElementById('match_type').value,
            league_group_id: document.getElementById('league_group_id').value || null,
            home_team_id: document.getElementById('home_team_id').value,
            away_team_id: document.getElementById('away_team_id').value,
            match_date: document.getElementById('match_date').value || null,
            match_time: document.getElementById('match_time').value || null,
            venue: document.getElementById('venue').value || null
        };
        
        if (!formData.home_team_id || !formData.away_team_id) {
            alert('Please select both home and away teams.');
            return;
        }
        
        if (formData.home_team_id === formData.away_team_id) {
            alert('Home and away teams cannot be the same.');
            return;
        }
        
        fetch(`/leagues/{{ $league->slug }}/fixtures`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Fixture created successfully!');
                location.reload();
            } else {
                alert('Error creating fixture: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while creating the fixture.');
        });
    }
});

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