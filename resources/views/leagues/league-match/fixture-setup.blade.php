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

                <div class="bg-indigo-50 border border-indigo-100 text-indigo-900 rounded-lg p-4 mb-6 flex gap-3">
                    <div class="mt-0.5">
                        <svg class="w-5 h-5 text-indigo-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-11a.75.75 0 011.5 0v3.5a.75.75 0 01-.36.64l-2 1.2a.75.75 0 11-.78-1.28l1.92-1.15V7z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold">Fixture editor for organizers</p>
                        <p class="text-sm">Drag group matches to reorder, auto-schedule uses that order, and add knockout games later once group stage is done.</p>
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
                                <input type="text" id="match_date" autocomplete="off" placeholder="Select date" class="flatpickr w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
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
                            <input type="text" id="venue" placeholder="Search or enter venue" list="venue-options" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <datalist id="venue-options">
                                @foreach($grounds as $ground)
                                    <option value="{{ $ground->name }}"></option>
                                @endforeach
                            </datalist>
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
                                <div class="flex items-center justify-between mb-2 sm:mb-4">
                                    <div>
                                        <h2 class="text-lg sm:text-xl font-semibold text-gray-900">{{ $group->name }}</h2>
                                        <p class="text-xs text-gray-500 mt-0.5">Drag to reorder. Auto-schedule follows this order.</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $fixtures->where('league_group_id', $group->id)->count() }} Matches
                                    </span>
                                </div>
                                <div class="bg-white border border-dashed border-gray-200 rounded-lg p-3 mb-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Start date</label>
                                        <input id="start-date-{{ $group->id }}" type="text" autocomplete="off" placeholder="Select date" class="flatpickr w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Start time</label>
                                        <input id="start-time-{{ $group->id }}" type="time" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Match duration (mins)</label>
                                        <input id="duration-{{ $group->id }}" type="number" min="10" value="90" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Gap between games (mins)</label>
                                        <input id="gap-{{ $group->id }}" type="number" min="0" value="15" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Matches per day</label>
                                        <div class="flex gap-2">
                                            <input id="matches-per-day-{{ $group->id }}" type="number" min="1" value="3" class="flex-1 border border-gray-300 rounded px-2 py-1.5 text-sm">
                                            <button type="button" class="px-3 py-1.5 bg-indigo-600 text-white rounded text-sm font-semibold" onclick="autoScheduleGroup({{ $group->id }})">Auto schedule</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-3" data-fixture-group-list data-group-id="{{ $group->id }}">
                                    @foreach($fixtures->where('league_group_id', $group->id) as $fixture)
                                        <div class="bg-white rounded-lg border border-gray-200 p-3 sm:p-4 shadow-sm" data-fixture-id="{{ $fixture->slug }}" data-group-id="{{ $group->id }}" data-draggable-fixture draggable="true">
                                            <div class="flex items-center justify-between mb-3 sm:mb-4">
                                                <div class="flex items-center gap-2 text-xs font-semibold text-gray-600">
                                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-gray-100 text-gray-600 border border-gray-200 cursor-move" aria-label="Drag to reorder" title="Drag to reorder">
                                                        <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                                            <path d="M2 3h8M2 6h8M2 9h8" stroke-linecap="round" />
                                                        </svg>
                                                    </span>
                                                    <span>Match {{ $loop->iteration }}</span>
                                                </div>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                    {{ $fixture->status === 'scheduled' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $fixture->status)) }}
                                                </span>
                                            </div>
                                            <!-- Mobile Layout -->
                                            <div class="sm:hidden">
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
                                                    <input type="text" autocomplete="off" placeholder="Date"
                                                           value="{{ $fixture->match_date ? $fixture->match_date->format('Y-m-d') : '' }}"
                                                           class="flatpickr border border-gray-300 rounded px-2 py-1 text-xs"
                                                           onchange="updateFixture('{{ $fixture->slug }}', 'match_date', this.value, this)">
                                                    <input type="time" 
                                                           value="{{ $fixture->match_time ? $fixture->match_time->format('H:i') : '' }}"
                                                           class="border border-gray-300 rounded px-2 py-1 text-xs"
                                                           onchange="updateFixture('{{ $fixture->slug }}', 'match_time', this.value, this)">
                                                    <input type="text" 
                                                           placeholder="Venue"
                                                           value="{{ $fixture->venue ?? '' }}"
                                                           list="venue-options"
                                                           class="border border-gray-300 rounded px-2 py-1 text-xs col-span-2"
                                                           onchange="updateFixture('{{ $fixture->slug }}', 'venue', this.value, this)">
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
                                                        <input type="text" autocomplete="off" placeholder="Date"
                                                               value="{{ $fixture->match_date ? $fixture->match_date->format('Y-m-d') : '' }}"
                                                               class="flatpickr w-full border border-gray-300 rounded px-3 py-2 text-sm"
                                                               onchange="updateFixture('{{ $fixture->slug }}', 'match_date', this.value, this)">
                                                    </div>
                                                    
                                                    <!-- Time -->
                                                    <div class="col-span-2">
                                                        <input type="time" 
                                                               value="{{ $fixture->match_time ? $fixture->match_time->format('H:i') : '' }}"
                                                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                                                               onchange="updateFixture('{{ $fixture->slug }}', 'match_time', this.value, this)">
                                                    </div>
                                                    
                                                    <!-- Venue -->
                                                    <div class="col-span-3">
                                                        <input type="text" 
                                                               placeholder="Venue"
                                                               value="{{ $fixture->venue ?? '' }}"
                                                               list="venue-options"
                                                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                                                           onchange="updateFixture('{{ $fixture->slug }}', 'venue', this.value, this)">
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

                                            <div class="flex justify-end mt-3">
                                                <button type="button"
                                                        class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-600 text-white text-xs sm:text-sm font-semibold rounded-lg shadow-sm hover:bg-indigo-700 transition"
                                                        onclick="saveFixtureRow('{{ $fixture->slug }}')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Update
                                                </button>
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

                        @if(!$knockoutFixtures->has('quarter_final') && !$knockoutFixtures->has('semi_final') && !$knockoutFixtures->has('final'))
                            <div class="bg-white border border-dashed border-gray-200 rounded-lg p-4 sm:p-6">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Knockout placeholder</h3>
                                        <p class="text-sm text-gray-600 mt-1">No knockout fixtures yet. Use the form above with match type set to Quarter / Semi / Final after group standings are locked.</p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold">Plan ahead</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-4">
                                    @foreach(['Quarter Final 1','Quarter Final 2','Quarter Final 3','Quarter Final 4','Semi Final 1','Semi Final 2','Final'] as $slot)
                                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">{{ $slot }}</p>
                                            <p class="text-sm font-semibold text-gray-800">TBD vs TBD</p>
                                            <p class="text-xs text-gray-500 mt-1">Pick qualified teams later</p>
                                        </div>
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
// Debug logging removed for production

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
        // Debug logging removed for production
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
        // Debug logging removed for production
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

    initDragSorting();
});

function updateFixture(fixtureId, field, value, element = null) {
    const payloadValue = value === '' ? null : value;
    fetch(`/leagues/{{ $league->slug }}/fixtures/${fixtureId}/update`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            [field]: payloadValue
        })
    })
    .then(async response => {
        const data = await response.json().catch(() => null);
        if (!response.ok || !data || !data.success) {
            const message = data?.message || 'Unable to update fixture.';
            throw new Error(message);
        }
        if (element) {
            const originalBorder = element.className;
            element.className = element.className.replace('border-gray-300', 'border-green-500');
            setTimeout(() => {
                element.className = originalBorder;
            }, 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'An error occurred while updating the fixture.');
    });
}

function autoScheduleGroup(groupId) {
    const startDate = document.getElementById(`start-date-${groupId}`).value;
    const startTime = document.getElementById(`start-time-${groupId}`).value;
    const duration = parseInt(document.getElementById(`duration-${groupId}`).value || '0', 10);
    const gap = parseInt(document.getElementById(`gap-${groupId}`).value || '0', 10);
    const matchesPerDay = parseInt(document.getElementById(`matches-per-day-${groupId}`).value || '1', 10);

    if (!startDate || !startTime) {
        alert('Please set a start date and time.');
        return;
    }
    if (duration <= 0 || gap < 0 || matchesPerDay <= 0) {
        alert('Please enter valid duration, gap, and matches per day values.');
        return;
    }

    const fixtures = document.querySelectorAll(`[data-group-id="${groupId}"]`);
    if (fixtures.length === 0) return;

    const [startHours, startMinutes] = startTime.split(':').map(Number);
    let current = new Date(startDate);
    current.setHours(startHours);
    current.setMinutes(startMinutes);
    current.setSeconds(0);
    current.setMilliseconds(0);

    fixtures.forEach((fixture, index) => {
        const dayOffset = Math.floor(index / matchesPerDay);
        const slotInDay = index % matchesPerDay;

        const scheduled = new Date(current);
        scheduled.setDate(current.getDate() + dayOffset);
        const minutesToAdd = slotInDay * (duration + gap);
        scheduled.setMinutes(scheduled.getMinutes() + minutesToAdd);

        const dateInputs = Array.from(fixture.querySelectorAll('input[type="date"]'));
        const timeInputs = Array.from(fixture.querySelectorAll('input[type="time"]'));

        const isoDate = scheduled.toISOString().slice(0, 10);
        const hh = String(scheduled.getHours()).padStart(2, '0');
        const mm = String(scheduled.getMinutes()).padStart(2, '0');
        const timeVal = `${hh}:${mm}`;

        if (dateInputs.length) {
            dateInputs.forEach(input => input.value = isoDate);
            updateFixture(fixture.dataset.fixtureId, 'match_date', isoDate, dateInputs[0]);
        }
        if (timeInputs.length) {
            timeInputs.forEach(input => input.value = timeVal);
            updateFixture(fixture.dataset.fixtureId, 'match_time', timeVal, timeInputs[0]);
        }
    });
}

function saveFixtureRow(fixtureSlug) {
    const row = document.querySelector(`[data-fixture-id="${fixtureSlug}"]`);
    if (!row) return;

    const dateInputs = Array.from(row.querySelectorAll('input.flatpickr'));
    const timeInputs = Array.from(row.querySelectorAll('input[type="time"]'));
    const venueInputs = Array.from(row.querySelectorAll('input[list="venue-options"]'));

    const match_date = (dateInputs.find(input => input.value)?.value) || null;
    const match_time = (timeInputs.find(input => input.value)?.value) || null;
    const venue = (venueInputs.find(input => input.value)?.value) || null;

    fetch(`/leagues/{{ $league->slug }}/fixtures/${fixtureSlug}/update`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            match_date,
            match_time,
            venue
        })
    })
    .then(async response => {
        const data = await response.json().catch(() => null);
        if (!response.ok || !data || !data.success) {
            const message = data?.message || 'Unable to update fixture.';
            throw new Error(message);
        }
        row.classList.add('ring', 'ring-emerald-300', 'ring-offset-2', 'ring-offset-white');
        setTimeout(() => {
            row.classList.remove('ring', 'ring-emerald-300', 'ring-offset-2', 'ring-offset-white');
        }, 1200);
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'An error occurred while updating the fixture.');
    });
}

function initDragSorting() {
    const lists = document.querySelectorAll('[data-fixture-group-list]');
    lists.forEach(list => setupDragList(list));
}

function setupDragList(list) {
    const items = list.querySelectorAll('[data-draggable-fixture]');
    items.forEach(item => {
        item.addEventListener('dragstart', () => {
            item.classList.add('dragging-fixture', 'opacity-70', 'ring', 'ring-indigo-200');
        });
        item.addEventListener('dragend', () => {
            item.classList.remove('dragging-fixture', 'opacity-70', 'ring', 'ring-indigo-200');
            persistFixtureOrder(list);
        });
    });

    list.addEventListener('dragover', event => {
        event.preventDefault();
        const dragging = document.querySelector('.dragging-fixture');
        if (!dragging) return;
        const afterElement = getDragAfterElement(list, event.clientY);
        if (afterElement == null) {
            list.appendChild(dragging);
        } else {
            list.insertBefore(dragging, afterElement);
        }
    });
}

function getDragAfterElement(list, yPosition) {
    const elements = [...list.querySelectorAll('[data-draggable-fixture]:not(.dragging-fixture)')];
    return elements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = yPosition - box.top - box.height / 2;
        if (offset < 0 && offset > closest.offset) {
            return { offset, element: child };
        }
        return closest;
    }, { offset: Number.NEGATIVE_INFINITY, element: null }).element;
}

function persistFixtureOrder(list) {
    const orders = Array.from(list.querySelectorAll('[data-draggable-fixture]')).map((item, index) => ({
        fixture: item.dataset.fixtureId,
        sort_order: index + 1
    }));

    if (orders.length === 0) {
        return;
    }

    fetch(`{{ route('leagues.fixtures.reorder', $league) }}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        credentials: 'same-origin',
        body: JSON.stringify({ orders })
    })
    .then(async response => {
        const data = await response.json().catch(() => null);
        if (!response.ok || !data?.success) {
            const message = data?.message || 'Unable to reorder fixtures.';
            throw new Error(message);
        }
        list.classList.add('ring', 'ring-emerald-200', 'ring-offset-2', 'ring-offset-gray-50');
        setTimeout(() => list.classList.remove('ring', 'ring-emerald-200', 'ring-offset-2', 'ring-offset-gray-50'), 800);
    })
    .catch(error => {
        console.error('Order update failed:', error);
        alert(error.message || 'An error occurred while reordering fixtures.');
    });
}
</script>
@endsection
