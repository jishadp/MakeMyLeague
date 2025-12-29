@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="matchCreator()">
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('scorer.dashboard') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Create New Match</h1>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden p-6">
            <form @submit.prevent="submitMatch">
                <!-- LEAGUE SELECTION -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">League</label>
                    <select x-model="form.league_id" @change="fetchLeagueDetails()" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Select League</option>
                        @foreach($leagues as $league)
                            <option value="{{ $league->id }}">{{ $league->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="loading" class="text-center py-4 text-gray-500">
                    Loading teams and groups...
                </div>

                <div x-show="form.league_id && !loading">
                    <!-- MATCH TYPE -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Match Type</label>
                        <select x-model="form.match_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="group_stage">Group Stage</option>
                            <option value="qualifier">Qualifier</option>
                            <option value="eliminator">Eliminator</option>
                            <option value="quarter_final">Quarter Final</option>
                            <option value="semi_final">Semi Final</option>
                            <option value="final">Final</option>
                        </select>
                    </div>

                    <!-- GROUP SELECTION (Visible only for Group Stage) -->
                    <div class="mb-4" x-show="form.match_type === 'group_stage'">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Group</label>
                        <select x-model="form.league_group_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Select Group</option>
                            <template x-for="group in groups" :key="group.id">
                                <option :value="group.id" x-text="group.name"></option>
                            </template>
                        </select>
                    </div>

                    <!-- TEAMS SELECTION -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Home Team</label>
                            <select x-model="form.home_team_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Select Team</option>
                                <template x-for="team in filteredTeams" :key="team.id">
                                    <option :value="team.id" x-text="team.team.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Away Team</label>
                            <select x-model="form.away_team_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Select Team</option>
                                <template x-for="team in filteredTeams" :key="team.id">
                                    <option :value="team.id" x-text="team.team.name" x-show="team.id != form.home_team_id"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- DATE & TIME -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Date</label>
                            <input type="date" x-model="form.match_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Time</label>
                            <input type="time" x-model="form.match_time" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                    </div>

                    <!-- VENUE -->
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Venue</label>
                        <input type="text" x-model="form.venue" placeholder="Stadium or Ground Name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" :disabled="submitting" :class="{'opacity-50 cursor-not-allowed': submitting}">
                            <span x-show="!submitting">Create Match</span>
                            <span x-show="submitting">Creating...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function matchCreator() {
    return {
        loading: false,
        submitting: false,
        leagues: @json($leagues),
        groups: [],
        allTeams: [],
        form: {
            league_id: '',
            match_type: 'group_stage',
            league_group_id: '',
            home_team_id: '',
            away_team_id: '',
            match_date: '',
            match_time: '',
            venue: ''
        },
        init() {
            // Check if league_id is pre-selected (if passed via query param or generic)
        },
        get filteredTeams() {
            if (this.form.match_type === 'group_stage' && this.form.league_group_id) {
                // Filter teams by the selected group
                // Note: The API returns teams and groups. We need to know which team belongs to which group.
                // However, the `getLeagueTeams` API I defined returns `leagueTeams` and `groups`. 
                // `leagueTeams` usually might not have `pivot` data for groups easily accessible unless eager loaded properly or structure is flat.
                // Let's check `LeagueMatchController` logic. Groups have `leagueTeams`.
                // So I should iterate groups to find the team IDs in that group.
                
                let group = this.groups.find(g => g.id == this.form.league_group_id);
                if (group && group.league_teams) {
                    let teamIds = group.league_teams.map(t => t.id);
                     return this.allTeams.filter(t => teamIds.includes(t.id));
                }
                return []; // Group selected but no teams found or structure mismatch?
            }
            // For Knockouts or no group selected yet: Show ALL teams
            return this.allTeams;
        },
        async fetchLeagueDetails() {
            if (!this.form.league_id) return;
            this.loading = true;
            try {
                // Fetch teams and groups for the selected league
                // We need a route for this: route('scorer.leagues.teams-groups', id)
                let url = `/scorer/leagues/${this.form.league_id}/teams-groups`;
                let response = await fetch(url);
                let data = await response.json();
                this.allTeams = data.teams;
                // data.groups should include the leagueTeams relationship as I defined in controller: `leagueGroups()->with('leagueTeams')`?
                // Wait, ScorerDashboardController: ` 'groups' => $league->leagueGroups ` - I didn't eager load `leagueTeams` there!
                // I need to update the controller to eager load `leagueTeams` in groups.
                
                // Let's assume I fix the controller next.
                this.groups = data.groups; 
            } catch (e) {
                console.error(e);
                alert('Failed to load league details.');
            } finally {
                this.loading = false;
            }
        },
        async submitMatch() {
            if (!this.form.league_id || !this.form.home_team_id || !this.form.away_team_id) {
                alert('Please fill in all required fields.');
                return;
            }
            this.submitting = true;
            try {
                let response = await fetch("{{ route('scorer.matches.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.form)
                });
                let result = await response.json();
                if (result.success) {
                    window.location.href = result.redirect;
                } else {
                    alert(result.message || 'Error creating match');
                }
            } catch (e) {
                console.error(e);
                alert('An error occurred.');
            } finally {
                this.submitting = false;
            }
        }
    }
}
</script>
@endsection
