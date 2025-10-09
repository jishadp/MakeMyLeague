@extends('layouts.app')

@section('title', 'Auction Panel Management | ' . config('app.name'))

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Auction Panel Management</h1>
                    <p class="text-gray-600 mt-2">Manage auction access for leagues and handle auction requests</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="refreshStats()" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh Stats
                    </button>
                    <a href="{{ route('admin.analytics.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Analytics
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Pending Requests -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending Requests</p>
                        <p class="text-2xl font-semibold text-gray-900" id="pending-requests-count">{{ $stats['pending_requests'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Granted Access -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Granted Access</p>
                        <p class="text-2xl font-semibold text-gray-900" id="granted-access-count">{{ $stats['granted_access'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Leagues -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Leagues</p>
                        <p class="text-2xl font-semibold text-gray-900" id="total-leagues-count">{{ $stats['total_leagues'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Auction Enabled % -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Auction Enabled %</p>
                        <p class="text-2xl font-semibold text-gray-900" id="auction-enabled-percentage">{{ $stats['auction_enabled_percentage'] ?? 0 }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests Section -->
        <div class="bg-white rounded-lg shadow-sm mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Pending Auction Access Requests</h3>
                    <div class="flex gap-2">
                        <button onclick="selectAllPendingRequests()" 
                                class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Select All
                        </button>
                        <button onclick="bulkGrantAccess()" 
                                class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Bulk Grant
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                @if(isset($pendingRequests) && $pendingRequests->count() > 0)
                    <div class="space-y-4" id="pending-requests-list">
                        @foreach($pendingRequests as $league)
                            <div class="flex items-center justify-between p-4 bg-orange-50 border border-orange-200 rounded-lg">
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           class="pending-request-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500" 
                                           value="{{ $league->id }}"
                                           data-slug="{{ $league->slug }}">
                                    <div class="ml-4">
                                        <h4 class="font-medium text-gray-900">{{ $league->name }}</h4>
                                        <p class="text-sm text-gray-600">
                                            Game: {{ $league->game->name ?? 'N/A' }} • 
                                            Organizers: {{ $league->approvedOrganizers->pluck('name')->join(', ') ?: 'None' }} •
                                            Requested: {{ $league->auction_access_requested_at ? $league->auction_access_requested_at->format('M d, Y H:i') : 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button onclick="viewLeagueDetails('{{ $league->slug }}')" 
                                            class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-200 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </button>
                                    <button onclick="grantAccess('{{ $league->slug }}', '{{ $league->name }}')" 
                                            class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Grant
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No pending requests</h3>
                        <p class="text-gray-600">All auction access requests have been processed</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Granted Access Section -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Leagues with Auction Access</h3>
                    <div class="flex gap-2">
                        <button onclick="selectAllGrantedAccess()" 
                                class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Select All
                        </button>
                        <button onclick="bulkRevokeAccess()" 
                                class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Bulk Revoke
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                @if(isset($grantedAccess) && $grantedAccess->count() > 0)
                    <div class="space-y-4" id="granted-access-list">
                        @foreach($grantedAccess as $league)
                            <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           class="granted-access-checkbox rounded border-gray-300 text-red-600 focus:ring-red-500" 
                                           value="{{ $league->id }}"
                                           data-slug="{{ $league->slug }}">
                                    <div class="ml-4">
                                        <h4 class="font-medium text-gray-900">{{ $league->name }}</h4>
                                        <p class="text-sm text-gray-600">
                                            Game: {{ $league->game->name ?? 'N/A' }} • 
                                            Organizers: {{ $league->approvedOrganizers->pluck('name')->join(', ') ?: 'None' }} •
                                            Granted: {{ $league->updated_at->format('M d, Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button onclick="viewLeagueDetails('{{ $league->slug }}')" 
                                            class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-200 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </button>
                                    <button onclick="revokeAccess('{{ $league->slug }}', '{{ $league->name }}')" 
                                            class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Revoke
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No leagues with auction access</h3>
                        <p class="text-gray-600">No leagues have been granted auction access yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- League Details Modal -->
<div id="leagueDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">League Details</h3>
                <button onclick="closeLeagueDetailsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="leagueDetailsContent" class="space-y-4">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- Notes Modal -->
<div id="notesModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 id="notesModalTitle" class="text-lg font-medium text-gray-900">Add Notes</h3>
                <button onclick="closeNotesModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="notesForm">
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                    <textarea id="notes" name="notes" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Add any notes about this action..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeNotesModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="notesSubmitButton"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentAction = null;
let currentLeagueId = null;
let currentLeagueName = null;

// Grant access to a single league
function grantAccess(leagueSlug, leagueName) {
    currentAction = 'grant';
    currentLeagueId = leagueSlug;
    currentLeagueName = leagueName;
    
    document.getElementById('notesModalTitle').textContent = `Grant Auction Access - ${leagueName}`;
    document.getElementById('notesSubmitButton').textContent = 'Grant Access';
    document.getElementById('notesSubmitButton').className = 'px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors';
    
    document.getElementById('notesModal').classList.remove('hidden');
}

// Revoke access from a single league
function revokeAccess(leagueSlug, leagueName) {
    currentAction = 'revoke';
    currentLeagueId = leagueSlug;
    currentLeagueName = leagueName;
    
    document.getElementById('notesModalTitle').textContent = `Revoke Auction Access - ${leagueName}`;
    document.getElementById('notesSubmitButton').textContent = 'Revoke Access';
    document.getElementById('notesSubmitButton').className = 'px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors';
    
    document.getElementById('notesModal').classList.remove('hidden');
}

// View league details
function viewLeagueDetails(leagueSlug) {
    fetch(`/admin/auction-panel/league/${leagueSlug}/details`)
        .then(response => response.json())
        .then(data => {
            const content = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Basic Information</h4>
                        <div class="space-y-2 text-sm">
                            <p><span class="font-medium">Name:</span> ${data.name}</p>
                            <p><span class="font-medium">Game:</span> ${data.game}</p>
                            <p><span class="font-medium">Status:</span> <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">${data.status}</span></p>
                            <p><span class="font-medium">Created:</span> ${data.created_at}</p>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">League Details</h4>
                        <div class="space-y-2 text-sm">
                            <p><span class="font-medium">Teams:</span> ${data.teams_count}/${data.max_teams}</p>
                            <p><span class="font-medium">Players:</span> ${data.players_count}/${data.max_team_players * data.max_teams}</p>
                            <p><span class="font-medium">Wallet Limit:</span> ₹${data.team_wallet_limit.toLocaleString()}</p>
                            <p><span class="font-medium">Duration:</span> ${data.start_date} - ${data.end_date}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <h4 class="font-medium text-gray-900 mb-2">Organizers</h4>
                    <p class="text-sm text-gray-600">${data.organizers || 'No organizers'}</p>
                </div>
                <div class="mt-4">
                    <h4 class="font-medium text-gray-900 mb-2">Auction Access Status</h4>
                    <div class="space-y-2 text-sm">
                        <p><span class="font-medium">Access Granted:</span> 
                            <span class="px-2 py-1 rounded-full text-xs ${data.auction_access_granted ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                ${data.auction_access_granted ? 'Yes' : 'No'}
                            </span>
                        </p>
                        ${data.auction_access_requested_at ? `<p><span class="font-medium">Requested:</span> ${new Date(data.auction_access_requested_at).toLocaleString()}</p>` : ''}
                        ${data.auction_access_notes ? `<p><span class="font-medium">Notes:</span> ${data.auction_access_notes}</p>` : ''}
                    </div>
                </div>
            `;
            
            document.getElementById('leagueDetailsContent').innerHTML = content;
            document.getElementById('leagueDetailsModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load league details');
        });
}

// Bulk grant access
function bulkGrantAccess() {
    const checkedBoxes = document.querySelectorAll('.pending-request-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Please select at least one league');
        return;
    }
    
    currentAction = 'bulk_grant';
    currentLeagueId = Array.from(checkedBoxes).map(cb => cb.value); // Still use IDs for bulk operations
    
    document.getElementById('notesModalTitle').textContent = `Bulk Grant Auction Access (${checkedBoxes.length} leagues)`;
    document.getElementById('notesSubmitButton').textContent = 'Grant Access';
    document.getElementById('notesSubmitButton').className = 'px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors';
    
    document.getElementById('notesModal').classList.remove('hidden');
}

// Bulk revoke access
function bulkRevokeAccess() {
    const checkedBoxes = document.querySelectorAll('.granted-access-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Please select at least one league');
        return;
    }
    
    currentAction = 'bulk_revoke';
    currentLeagueId = Array.from(checkedBoxes).map(cb => cb.value); // Still use IDs for bulk operations
    
    document.getElementById('notesModalTitle').textContent = `Bulk Revoke Auction Access (${checkedBoxes.length} leagues)`;
    document.getElementById('notesSubmitButton').textContent = 'Revoke Access';
    document.getElementById('notesSubmitButton').className = 'px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors';
    
    document.getElementById('notesModal').classList.remove('hidden');
}

// Select all pending requests
function selectAllPendingRequests() {
    const checkboxes = document.querySelectorAll('.pending-request-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(cb => {
        cb.checked = !allChecked;
    });
}

// Select all granted access
function selectAllGrantedAccess() {
    const checkboxes = document.querySelectorAll('.granted-access-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(cb => {
        cb.checked = !allChecked;
    });
}

// Refresh statistics
function refreshStats() {
    fetch('/admin/auction-panel/stats')
        .then(response => response.json())
        .then(data => {
            document.getElementById('pending-requests-count').textContent = data.pending_requests;
            document.getElementById('granted-access-count').textContent = data.granted_access;
            document.getElementById('total-leagues-count').textContent = data.total_leagues;
            document.getElementById('auction-enabled-percentage').textContent = data.auction_enabled_percentage + '%';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to refresh statistics');
        });
}

// Close modals
function closeLeagueDetailsModal() {
    document.getElementById('leagueDetailsModal').classList.add('hidden');
}

function closeNotesModal() {
    document.getElementById('notesModal').classList.add('hidden');
    document.getElementById('notesForm').reset();
    currentAction = null;
    currentLeagueId = null;
    currentLeagueName = null;
}

// Handle notes form submission
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('notesForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const notes = document.getElementById('notes').value;
        const submitButton = document.getElementById('notesSubmitButton');
        const originalText = submitButton.textContent;
        
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';
        
        let url, data;
        
        if (currentAction === 'grant') {
            url = `/admin/auction-panel/league/${currentLeagueId}/grant`;
            data = { notes: notes };
        } else if (currentAction === 'revoke') {
            url = `/admin/auction-panel/league/${currentLeagueId}/revoke`;
            data = { notes: notes };
        } else if (currentAction === 'bulk_grant') {
            url = '/admin/auction-panel/bulk-grant';
            data = { league_ids: currentLeagueId, notes: notes };
        } else if (currentAction === 'bulk_revoke') {
            url = '/admin/auction-panel/bulk-revoke';
            data = { league_ids: currentLeagueId, notes: notes };
        }
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showNotification(result.message, 'success');
                closeNotesModal();
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showNotification(result.message || 'Action failed', 'error');
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred', 'error');
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        });
    });
});

// Show notification
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
        type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success' 
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                }
            </svg>
            <span class="font-medium">${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Close modals when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('leagueDetailsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLeagueDetailsModal();
        }
    });

    document.getElementById('notesModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeNotesModal();
        }
    });
});
</script>
@endsection

