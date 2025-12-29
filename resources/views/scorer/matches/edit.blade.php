@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('scorer.dashboard', ['league_id' => $fixture->league_id]) }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fa-solid fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Match</h1>
                    <p class="text-sm text-gray-600">{{ $fixture->league->name }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <form @submit.prevent="submitForm" x-data="matchForm()" x-init="init()">
            <div x-show="message" :class="`mb-6 p-4 rounded-lg border ${message?.type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'}`" style="display: none;">
                <div class="flex items-center gap-2">
                    <i :class="`fa-solid ${message?.type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}`"></i>
                    <span x-text="message?.text"></span>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 space-y-6">
                <div class="border-b pb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">League Details</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">League <span class="text-red-500">*</span></label>
                            <select x-model="form.league_id" @change="onLeagueChange()" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Choose a League...</option>
                                @foreach($leagues as $league)
                                    <option value="{{ $league->id }}">{{ $league->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-2" x-data="{ showMatchTypes: false }">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Match Stage <span class="text-red-500">*</span></label>
                            <button type="button" @click="showMatchTypes = true" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-left flex items-center justify-between bg-white hover:bg-gray-50">
                                <span x-text="form.match_type ? form.match_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Select Match Stage'"></span>
                                <i class="fa-solid fa-chevron-down text-gray-400"></i>
                            </button>

                            <div x-show="showMatchTypes" x-transition @click.self="showMatchTypes = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" style="display: none;">
                                <div @click.away="showMatchTypes = false" class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[80vh] overflow-y-auto">
                                    <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-xl">
                                        <h3 class="text-lg font-bold text-gray-900">Select Match Stage</h3>
                                        <button @click="showMatchTypes = false" class="text-gray-400 hover:text-gray-600">
                                            <i class="fa-solid fa-times text-xl"></i>
                                        </button>
                                    </div>
                                    <div class="p-6 space-y-2">
                                        <button type="button" @click="form.match_type = 'qualifier'; showMatchTypes = false" :class="form.match_type === 'qualifier' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50'" class="w-full px-4 py-3 border-2 rounded-lg text-left font-medium transition-all flex items-center justify-between">
                                            <span>Qualifier</span>
                                            <i x-show="form.match_type === 'qualifier'" class="fa-solid fa-check text-blue-600"></i>
                                        </button>
                                        <button type="button" @click="form.match_type = 'eliminator'; showMatchTypes = false" :class="form.match_type === 'eliminator' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50'" class="w-full px-4 py-3 border-2 rounded-lg text-left font-medium transition-all flex items-center justify-between">
                                            <span>Eliminator</span>
                                            <i x-show="form.match_type === 'eliminator'" class="fa-solid fa-check text-blue-600"></i>
                                        </button>
                                        <button type="button" @click="form.match_type = 'quarter_final'; showMatchTypes = false" :class="form.match_type === 'quarter_final' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50'" class="w-full px-4 py-3 border-2 rounded-lg text-left font-medium transition-all flex items-center justify-between">
                                            <span>Quarter Final</span>
                                            <i x-show="form.match_type === 'quarter_final'" class="fa-solid fa-check text-blue-600"></i>
                                        </button>
                                        <button type="button" @click="form.match_type = 'semi_final'; showMatchTypes = false" :class="form.match_type === 'semi_final' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50'" class="w-full px-4 py-3 border-2 rounded-lg text-left font-medium transition-all flex items-center justify-between">
                                            <span>Semi Final</span>
                                            <i x-show="form.match_type === 'semi_final'" class="fa-solid fa-check text-blue-600"></i>
                                        </button>
                                        <button type="button" @click="form.match_type = 'final'; showMatchTypes = false" :class="form.match_type === 'final' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50'" class="w-full px-4 py-3 border-2 rounded-lg text-left font-medium transition-all flex items-center justify-between">
                                            <span>Final</span>
                                            <i x-show="form.match_type === 'final'" class="fa-solid fa-check text-blue-600"></i>
                                        </button>
                                        <button type="button" @click="form.match_type = 'third_place'; showMatchTypes = false" :class="form.match_type === 'third_place' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50'" class="w-full px-4 py-3 border-2 rounded-lg text-left font-medium transition-all flex items-center justify-between">
                                            <span>Third Place</span>
                                            <i x-show="form.match_type === 'third_place'" class="fa-solid fa-check text-blue-600"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-b pb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Select Teams</h2>
                    <div class="grid md:grid-cols-2 gap-4 mb-6">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                            <p class="text-xs font-semibold text-gray-500 mb-3 uppercase">Home Team</p>
                            <div x-show="form.home_team_id">
                                <p class="text-lg font-bold text-gray-900" x-text="getTeamName(form.home_team_id)"></p>
                                <button type="button" @click="form.home_team_id = ''" class="mt-2 text-xs text-red-600 hover:text-red-800 underline">Change</button>
                            </div>
                            <div x-show="!form.home_team_id" class="text-gray-500">
                                <i class="fa-solid fa-plus text-2xl mb-2 block opacity-50"></i>
                                <p class="text-sm">Select from teams below</p>
                            </div>
                        </div>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                            <p class="text-xs font-semibold text-gray-500 mb-3 uppercase">Away Team</p>
                            <div x-show="form.away_team_id">
                                <p class="text-lg font-bold text-gray-900" x-text="getTeamName(form.away_team_id)"></p>
                                <button type="button" @click="form.away_team_id = ''" class="mt-2 text-xs text-red-600 hover:text-red-800 underline">Change</button>
                            </div>
                            <div x-show="!form.away_team_id" class="text-gray-500">
                                <i class="fa-solid fa-plus text-2xl mb-2 block opacity-50"></i>
                                <p class="text-sm">Select from teams below</p>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <template x-for="team in teams" :key="team.id">
                            <button type="button" @click.prevent="selectTeam(team.id)" :class="`p-4 rounded-lg border-2 transition-all text-center cursor-pointer ${Number(form.home_team_id) === Number(team.id) ? 'border-green-500 bg-green-50 ring-2 ring-green-300' : Number(form.away_team_id) === Number(team.id) ? 'border-orange-500 bg-orange-50 ring-2 ring-orange-300' : 'border-gray-200 hover:border-blue-400 hover:bg-blue-50 bg-white'}`">
                                <div x-show="Number(form.home_team_id) === Number(team.id)" class="inline-block px-2 py-1 rounded-full bg-green-500 text-white text-xs font-bold mb-2">HOME</div>
                                <div x-show="Number(form.away_team_id) === Number(team.id)" class="inline-block px-2 py-1 rounded-full bg-orange-500 text-white text-xs font-bold mb-2">AWAY</div>
                                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                                    <i class="fa-solid fa-shield text-gray-400"></i>
                                </div>
                                <p class="font-semibold text-gray-900 text-sm" x-text="team.team.name"></p>
                            </button>
                        </template>
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Schedule Details</h2>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Match Date</label>
                            <input type="date" x-model="form.match_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Match Time</label>
                            <input type="time" x-model="form.match_time" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Venue</label>
                            <input type="text" x-model="form.venue" placeholder="Stadium/Ground Name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex items-center gap-4">
                <a href="{{ route('scorer.dashboard', ['league_id' => $fixture->league_id]) }}" class="px-6 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md font-medium transition-colors">Cancel</a>
                <button type="submit" :disabled="loading" :class="`flex-1 px-6 py-2 rounded-md font-medium transition-colors ${loading ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 text-white'}`">
                    <span x-show="!loading">Update Match</span>
                    <span x-show="loading" class="flex items-center justify-center gap-2"><i class="fa-solid fa-spinner fa-spin"></i> Updating...</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function matchForm() {
    return {
        loading: false,
        message: null,
        leagues: @json($leagues),
        teams: [],
        form: {
            league_id: '{{ $fixture->league_id }}',
            match_type: '{{ $fixture->match_type }}',
            league_group_id: '{{ $fixture->league_group_id }}',
            home_team_id: '{{ $fixture->home_team_id }}',
            away_team_id: '{{ $fixture->away_team_id }}',
            match_date: '{{ $fixture->match_date }}',
            match_time: '{{ $fixture->match_time ? $fixture->match_time->format("H:i") : "" }}',
            venue: '{{ $fixture->venue }}'
        },

        async init() {
            await this.onLeagueChange();
        },

        async onLeagueChange() {
            if (!this.form.league_id) return;
            try {
                const league = this.leagues.find(l => String(l.id) === String(this.form.league_id));
                const identifier = league.slug || league.id;
                const response = await fetch(`/scorer/leagues/${identifier}/teams-groups`);
                const data = await response.json();
                this.teams = data.teams || [];
            } catch (error) {
                this.showMessage('Failed to load league data', 'error');
            }
        },

        selectTeam(teamId) {
            const teamIdNum = Number(teamId);
            const homeTeamNum = Number(this.form.home_team_id) || null;
            const awayTeamNum = Number(this.form.away_team_id) || null;
            
            if (homeTeamNum === teamIdNum) {
                this.form.home_team_id = '';
                return;
            }
            if (awayTeamNum === teamIdNum) {
                this.form.away_team_id = '';
                return;
            }
            if (!homeTeamNum) {
                this.form.home_team_id = teamIdNum;
                return;
            }
            if (!awayTeamNum && homeTeamNum !== teamIdNum) {
                this.form.away_team_id = teamIdNum;
                return;
            }
            if (homeTeamNum && awayTeamNum) {
                this.form.away_team_id = teamIdNum;
            }
        },

        getTeamName(teamId) {
            if (!teamId) return '';
            const team = this.teams.find(t => Number(t.id) === Number(teamId));
            return team ? team.team.name : '';
        },

        async submitForm() {
            if (!this.form.league_id || !this.form.home_team_id || !this.form.away_team_id) {
                this.showMessage('Please select league and both teams', 'error');
                return;
            }

            this.loading = true;
            try {
                const response = await fetch('{{ route('scorer.matches.update', $fixture->slug) }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.form)
                });

                const result = await response.json();
                if (result.success) {
                    this.showMessage('Match updated successfully!', 'success');
                    setTimeout(() => window.location.href = result.redirect, 1000);
                } else {
                    this.showMessage(result.message || 'Failed to update match', 'error');
                }
            } catch (error) {
                this.showMessage('An error occurred', 'error');
            } finally {
                this.loading = false;
            }
        },

        showMessage(text, type = 'info') {
            this.message = { text, type };
            setTimeout(() => this.message = null, 5000);
        }
    };
}
</script>
@endsection
