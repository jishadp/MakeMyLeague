@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('scorer.dashboard') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fa-solid fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create Match</h1>
                    <p class="text-sm text-gray-600">{{ $selectedLeague?->name ?? 'Select a League' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="max-w-4xl mx-auto px-4 py-8">
        <form @submit.prevent="submitForm" x-data="matchForm()" x-init="init()">
            
            <!-- Alert Messages -->
            <div x-show="message" :class="`mb-6 p-4 rounded-lg border ${message?.type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'}`" style="display: none;">
                <div class="flex items-center gap-2">
                    <i :class="`fa-solid ${message?.type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}`"></i>
                    <span x-text="message?.text"></span>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 space-y-6">

                <!-- Step 1: League Selection -->
                <div class="border-b pb-6 last:border-b-0">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold text-sm">1</span>
                        League Details
                    </h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- League Selection -->
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                League <span class="text-red-500">*</span>
                            </label>
                            <select x-model="form.league_id" @change="onLeagueChange()" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Choose a League...</option>
                                @foreach($leagues as $league)
                                    <option value="{{ $league->id }}">{{ $league->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Knockout Type Selection -->
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Match Stage <span class="text-red-500">*</span>
                            </label>
                            <select x-model="form.match_type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="qualifier">Qualifier</option>
                                <option value="eliminator">Eliminator</option>
                                <option value="quarter_final">Quarter Final</option>
                                <option value="semi_final">Semi Final</option>
                                <option value="final">Final</option>
                                <option value="third_place">Third Place</option>
                            </select>
                        </div>
                    </div>
                </div>



                <!-- Step 3: Teams Selection with Cards -->
                <div class="border-b pb-6 last:border-b-0">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold text-sm">3</span>
                        Select Teams
                    </h2>

                    <!-- Help Text -->
                    <div x-show="!form.home_team_id || !form.away_team_id" class="mb-6 p-3 rounded-md bg-blue-50 border border-blue-200 text-blue-700 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-info-circle"></i>
                        <span>Click on team cards below to select home and away teams</span>
                    </div>

                    <!-- Selected Teams Preview -->
                    <div class="grid md:grid-cols-2 gap-4 mb-6">
                        <!-- Home Team Selection Box -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                            <p class="text-xs font-semibold text-gray-500 mb-3 uppercase">Home Team</p>
                            <div x-show="form.home_team_id">
                                <p class="text-lg font-bold text-gray-900" x-text="getTeamName(form.home_team_id)"></p>
                                <button type="button" @click="form.home_team_id = ''" class="mt-2 text-xs text-red-600 hover:text-red-800 underline">
                                    Change
                                </button>
                            </div>
                            <div x-show="!form.home_team_id" class="text-gray-500">
                                <i class="fa-solid fa-plus text-2xl mb-2 block opacity-50"></i>
                                <p class="text-sm">Select from teams below</p>
                            </div>
                        </div>

                        <!-- Away Team Selection Box -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                            <p class="text-xs font-semibold text-gray-500 mb-3 uppercase">Away Team</p>
                            <div x-show="form.away_team_id">
                                <p class="text-lg font-bold text-gray-900" x-text="getTeamName(form.away_team_id)"></p>
                                <button type="button" @click="form.away_team_id = ''" class="mt-2 text-xs text-red-600 hover:text-red-800 underline">
                                    Change
                                </button>
                            </div>
                            <div x-show="!form.away_team_id" class="text-gray-500">
                                <i class="fa-solid fa-plus text-2xl mb-2 block opacity-50"></i>
                                <p class="text-sm">Select from teams below</p>
                            </div>
                        </div>
                    </div>

                    <!-- Teams Grid as Cards -->
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-4">Available Teams</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <template x-for="team in getAvailableTeams()" :key="team.id">
                                <button type="button"
                                    @click.prevent="selectTeam(team.id)"
                                    :class="`p-4 rounded-lg border-2 transition-all text-center cursor-pointer ${
                                        Number(form.home_team_id) === Number(team.id) ? 'border-green-500 bg-green-50 ring-2 ring-green-300' : 
                                        Number(form.away_team_id) === Number(team.id) ? 'border-orange-500 bg-orange-50 ring-2 ring-orange-300' : 
                                        'border-gray-200 hover:border-blue-400 hover:bg-blue-50 bg-white'
                                    }`">
                                    
                                    <!-- Team Badge -->
                                    <div x-show="Number(form.home_team_id) === Number(team.id)" class="inline-block px-2 py-1 rounded-full bg-green-500 text-white text-xs font-bold mb-2">
                                        HOME
                                    </div>
                                    <div x-show="Number(form.away_team_id) === Number(team.id)" class="inline-block px-2 py-1 rounded-full bg-orange-500 text-white text-xs font-bold mb-2">
                                        AWAY
                                    </div>

                                    <!-- Team Logo -->
                                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fa-solid fa-shield text-gray-400"></i>
                                    </div>

                                    <!-- Team Name -->
                                    <p class="font-semibold text-gray-900 text-sm" x-text="team.team.name"></p>
                                </button>
                            </template>
                        </div>

                        <!-- No Teams Message -->
                        <div x-show="getAvailableTeams().length === 0" class="text-center py-12 text-gray-500">
                            <i class="fa-solid fa-inbox text-3xl mb-3 block opacity-50"></i>
                            <p>No teams available. Please select a league first.</p>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Schedule Details -->
                <div class="border-b pb-6 last:border-b-0">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold text-sm">4</span>
                        Schedule Details (Optional)
                    </h2>

                    <div class="grid md:grid-cols-3 gap-4">
                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Match Date</label>
                            <input type="text" x-model="form.match_date" placeholder="Select Date"
                                class="flatpickr w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Match Time</label>
                            <input type="time" x-model="form.match_time"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Venue -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Venue</label>
                            <input type="text" x-model="form.venue" placeholder="Stadium/Ground Name"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Summary Preview -->
                <div x-show="form.league_id && form.home_team_id && form.away_team_id" 
                    class="bg-blue-50 border border-blue-200 rounded-lg p-4" style="display: none;">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Match Preview</h3>
                    <div class="grid grid-cols-3 gap-4 text-center text-sm">
                        <div>
                            <p class="font-semibold text-gray-900" x-text="getTeamName(form.home_team_id) || 'Home'"></p>
                            <p class="text-xs text-gray-600">Home</p>
                        </div>
                        <div>
                            <p class="text-gray-500">vs</p>
                            <p x-show="form.match_time" class="text-xs text-gray-600" x-text="form.match_time || 'TBA'"></p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900" x-text="getTeamName(form.away_team_id) || 'Away'"></p>
                            <p class="text-xs text-gray-600">Away</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center gap-4">
                <a href="{{ route('scorer.dashboard') }}" class="px-6 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md font-medium transition-colors">
                    Cancel
                </a>
                <button type="submit" :disabled="loading"
                    :class="`flex-1 px-6 py-2 rounded-md font-medium transition-colors ${loading ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 text-white'}`">
                    <span x-show="!loading">Create Match</span>
                    <span x-show="loading" class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-spinner fa-spin"></i> Creating...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function matchForm() {
    return {
        loading: false,
        fetchingLeagueData: false,
        message: null,
        leagues: @json($leagues),
        groups: [],
        teams: [],
        match_mode: 'knockout', // Enforce knockout only
        form: {
            league_id: '',
            match_type: 'qualifier', // Default to first knockout option
            league_group_id: '',
            home_team_id: '',
            away_team_id: '',
            match_date: '',
            match_time: '',
            venue: ''
        },

        init() {
            // Auto-populate league if passed via URL
            if ('{{ $selectedLeague?->id }}') {
                this.form.league_id = '{{ $selectedLeague?->id }}';
                this.onLeagueChange();
            }
        },

        async onLeagueChange() {
            this.form.league_group_id = '';
            this.form.home_team_id = '';
            this.form.away_team_id = '';
            this.groups = [];
            this.teams = [];
            this.fetchingLeagueData = false;

            if (!this.form.league_id) return;

            this.fetchingLeagueData = true;

            try {
                const league = this.leagues.find(l => String(l.id) === String(this.form.league_id));
                if (!league) throw new Error('League not found');

                // Use the route helper if possible, otherwise construct manually
                // We prefer slug for the route parameter if available
                const identifier = league.slug || league.id;
                const url = `/scorer/leagues/${identifier}/teams-groups`;
                
                const response = await fetch(url);
                
                if (!response.ok) throw new Error('Failed to fetch league data');
                
                const data = await response.json();
                this.teams = data.teams || [];
                this.groups = data.groups || [];

                console.log('League data loaded:', { teams: this.teams, groups: this.groups });
            } catch (error) {
                console.error('Error loading league:', error);
                this.showMessage('Failed to load league data. Please try again.', 'error');
            } finally {
                this.fetchingLeagueData = false;
            }
        },

        onGroupChange() {
            this.form.home_team_id = '';
            this.form.away_team_id = '';
        },

        onTeamChange() {
            // Reset away team if it's the same as home team
            if (this.form.home_team_id && this.form.away_team_id && this.form.home_team_id === this.form.away_team_id) {
                this.form.away_team_id = '';
            }
        },

        selectTeam(teamId) {
            // Convert to number for consistency
            const teamIdNum = Number(teamId);
            const homeTeamNum = Number(this.form.home_team_id) || null;
            const awayTeamNum = Number(this.form.away_team_id) || null;
            
            console.log('selectTeam called:', { teamIdNum, homeTeamNum, awayTeamNum });
            
            // If this team is already selected as home, deselect it
            if (homeTeamNum === teamIdNum) {
                this.form.home_team_id = '';
                console.log('Deselected home team');
                return;
            }
            
            // If this team is already selected as away, deselect it
            if (awayTeamNum === teamIdNum) {
                this.form.away_team_id = '';
                console.log('Deselected away team');
                return;
            }

            // If home team not selected, select it
            if (!homeTeamNum) {
                this.form.home_team_id = teamIdNum;
                console.log('Selected home team:', teamIdNum);
                return;
            }

            // If away team not selected and it's different from home, select it
            if (!awayTeamNum && homeTeamNum !== teamIdNum) {
                this.form.away_team_id = teamIdNum;
                console.log('Selected away team:', teamIdNum);
                return;
            }

            // If both are selected, replace the away team
            if (homeTeamNum && awayTeamNum) {
                this.form.away_team_id = teamIdNum;
                console.log('Replaced away team:', teamIdNum);
            }
        },

        setMatchMode(mode) {
             // Deprecated functionality, kept strict knockout
             this.match_mode = 'knockout';
        },

        getAvailableTeams() {
            // Always return all teams as we are in strict knockout mode
            return this.teams;
        },

        getTeamName(teamId) {
            if (!teamId) return '';
            const teamIdNum = Number(teamId);
            const team = this.teams.find(t => Number(t.id) === teamIdNum);
            return team ? team.team.name : '';
        },

        isFormValid() {
            if (!this.form.league_id || !this.form.match_type) return false;
            
            if (!this.form.home_team_id || !this.form.away_team_id) return false;
            
            if (this.form.home_team_id === this.form.away_team_id) return false;

            return true;
        },

        async submitForm() {
            if (!this.isFormValid()) {
                this.showMessage('Please select a league and both teams', 'error');
                return;
            }

            this.loading = true;
            try {
                const response = await fetch('{{ route('scorer.matches.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.form)
                });

                const result = await response.json();
                
                if (result.success) {
                    this.showMessage('Match created successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1000);
                } else {
                    this.showMessage(result.message || 'Failed to create match', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showMessage('An error occurred while creating the match', 'error');
            } finally {
                this.loading = false;
            }
        },

        showMessage(text, type = 'info') {
            this.message = { text, type };
            setTimeout(() => {
                this.message = null;
            }, 5000);
        }
    };
}
</script>
@endsection
