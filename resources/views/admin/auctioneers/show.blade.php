@extends('layouts.app')

@section('title', $league->name . ' - Auctioneer Management | ' . config('app.name'))

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <div class="flex items-center mb-2">
                        <a href="{{ route('admin.auctioneers.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $league->name }}</h1>
                    </div>
                    <p class="text-gray-600">Manage auctioneer assignments for this league</p>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="text-sm text-gray-600 mb-1">Total Teams</div>
                <div class="text-3xl font-bold text-blue-600">{{ $leagueTeams->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="text-sm text-gray-600 mb-1">Assigned</div>
                <div class="text-3xl font-bold text-green-600">{{ $assignedTeams->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="text-sm text-gray-600 mb-1">Unassigned</div>
                <div class="text-3xl font-bold text-orange-600">{{ $unassignedTeams->count() }}</div>
            </div>
        </div>

        <!-- Teams with Assignments -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Teams & Auctioneers</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Team</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Auctioneer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($leagueTeams as $leagueTeam)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $leagueTeam->team->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($leagueTeam->teamAuctioneer && $leagueTeam->teamAuctioneer->auctioneer)
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $leagueTeam->teamAuctioneer->auctioneer->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $leagueTeam->teamAuctioneer->auctioneer->email }}</div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400 italic">Not assigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($leagueTeam->teamAuctioneer)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Assigned
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Unassigned
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="openAssignModal({{ $leagueTeam->id }}, '{{ $leagueTeam->team->name }}')" 
                                            class="text-blue-600 hover:text-blue-900 mr-3">
                                        {{ $leagueTeam->teamAuctioneer ? 'Change' : 'Assign' }}
                                    </button>
                                    @if($leagueTeam->teamAuctioneer)
                                        <button onclick="removeAuctioneer({{ $leagueTeam->id }})" 
                                                class="text-red-600 hover:text-red-900">
                                            Remove
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Assignment Modal -->
<div id="assignModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4" id="modalTitle">Assign Auctioneer</h3>
            <select id="auctioneerSelect" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Select an auctioneer...</option>
                @foreach($availableUsers as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
            <div class="flex justify-end gap-3 mt-6">
                <button onclick="closeAssignModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button onclick="confirmAssign()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Assign
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentLeagueTeamId = null;

function openAssignModal(leagueTeamId, teamName) {
    currentLeagueTeamId = leagueTeamId;
    document.getElementById('modalTitle').textContent = `Assign Auctioneer for ${teamName}`;
    document.getElementById('assignModal').classList.remove('hidden');
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
    document.getElementById('auctioneerSelect').value = '';
}

function confirmAssign() {
    const auctioneerId = document.getElementById('auctioneerSelect').value;
    if (!auctioneerId) {
        alert('Please select an auctioneer');
        return;
    }

    fetch(`/admin/auctioneers/leagues/{{ $league->id }}/teams/${currentLeagueTeamId}/assign`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ auctioneer_id: auctioneerId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

function removeAuctioneer(leagueTeamId) {
    if (!confirm('Are you sure you want to remove this auctioneer?')) {
        return;
    }

    fetch(`/admin/auctioneers/leagues/{{ $league->id }}/teams/${leagueTeamId}/remove`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}
</script>
@endsection

