@extends('layouts.app')

@section('title', 'League Match Setup - ' . $league->name)

@section('styles')
<style>
    .league-match-page {
        background:
            radial-gradient(circle at 20% 20%, rgba(34, 211, 238, 0.12), transparent 30%),
            radial-gradient(circle at 80% 0%, rgba(14, 165, 233, 0.08), transparent 28%),
            #0b1224;
        color: #e2e8f0;
    }

    .league-match-shell {
        background: rgba(15, 23, 42, 0.85);
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 24px;
        box-shadow: 0 25px 60px rgba(2, 6, 23, 0.45);
        backdrop-filter: blur(10px);
    }

    .section-card {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 16px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
    }

    .section-card.soft {
        background: rgba(255, 255, 255, 0.06);
    }

    .league-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 0.45rem 0.9rem;
        border-radius: 9999px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(255, 255, 255, 0.06);
        color: #e2e8f0;
        letter-spacing: 0.01em;
        font-weight: 700;
        font-size: 0.8rem;
    }

    .league-avatar {
        width: 3rem;
        height: 3rem;
        border-radius: 14px;
        background: linear-gradient(135deg, #22d3ee, #0ea5e9);
        color: #0b1224;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 15px 35px rgba(14, 165, 233, 0.35);
    }

    .step-pill {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        background: linear-gradient(135deg, #22d3ee, #0ea5e9);
        color: #0b1224;
        box-shadow: 0 10px 25px rgba(14, 165, 233, 0.35);
    }

    .step-pill.is-muted {
        background: rgba(255, 255, 255, 0.06);
        color: #cbd5e1;
        box-shadow: none;
        border: 1px solid rgba(148, 163, 184, 0.25);
    }

    .step-rail {
        flex: 1;
        height: 2px;
        background: linear-gradient(90deg, rgba(34, 211, 238, 0.6), rgba(14, 165, 233, 0.08));
    }

    .team-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 14px;
        color: #e2e8f0;
        transition: all 0.15s ease;
    }

    .team-card:hover {
        border-color: rgba(34, 211, 238, 0.6);
        transform: translateY(-2px);
        background: rgba(255, 255, 255, 0.08);
    }

    .group-dropzone {
        background: rgba(255, 255, 255, 0.03);
        border: 2px dashed rgba(148, 163, 184, 0.3);
        border-radius: 14px;
    }

    .modal-card {
        background: #0f172a;
        border: 1px solid rgba(148, 163, 184, 0.35);
        border-radius: 16px;
    }
</style>
@endsection

@section('content')
@php
    $existingGroupCount = $existingGroups->count();
    $assignedTeams = $existingGroups->sum(fn($group) => $group->leagueTeams->count());
    $teamCount = $assignedTeams + $availableTeams->count();
@endphp
<div class="league-match-page min-h-screen py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
        <div class="league-match-shell rounded-3xl shadow-2xl overflow-hidden">
            <div class="p-4 sm:p-6 lg:p-10">
                <!-- Header Section -->
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6 sm:mb-8">
                    <div class="flex items-start gap-4">
                        <div class="league-avatar">
                            {{ strtoupper(substr($league->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.18em] text-cyan-300 font-semibold">League match setup</p>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">{{ $league->name }}</h1>
                            <p class="text-slate-300 mt-1 sm:mt-2 text-sm sm:text-base">
                                {{ $league->season ? 'Season ' . $league->season . ' · ' : '' }}{{ $league->game->name ?? 'Game' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 sm:gap-3">
                        <span class="league-pill">
                            <i class="fa-solid fa-circle-check text-emerald-300"></i> Auction completed
                        </span>
                        <span class="league-pill">
                            <i class="fa-solid fa-people-group text-cyan-300"></i> Teams: {{ $teamCount }}
                        </span>
                        <span class="league-pill">
                            <i class="fa-solid fa-layer-group text-cyan-300"></i> Groups: {{ $existingGroupCount }}
                        </span>
                    </div>
                </div>
                
                <!-- Progress Steps -->
                <div class="mb-6 sm:mb-8">
                    <div class="section-card soft p-4 sm:p-5">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-3">
                                <div class="step-pill step-indicator" data-step="1">1</div>
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.12em] text-cyan-300 font-semibold">Step 1</p>
                                    <p class="text-sm sm:text-base font-semibold text-white step-text" data-step="1">Create Groups</p>
                                </div>
                            </div>
                            <div class="step-rail"></div>
                            <div class="flex items-center gap-3">
                                <div class="step-pill step-indicator is-muted" data-step="2">2</div>
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.12em] text-slate-400 font-semibold">Step 2</p>
                                    <p class="text-sm sm:text-base font-semibold text-slate-300 step-text" data-step="2">Generate Fixtures</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="step-content">
                    <!-- Step 1: Create Groups -->
                    <div id="step-1" class="step-content">
                        <div class="section-card p-4 sm:p-6 rounded-2xl border border-white/10">
                            <h3 class="text-lg sm:text-xl font-semibold text-white mb-3 sm:mb-4">Create League Match Groups</h3>
                            <p class="text-slate-300 mb-4 sm:mb-6 text-sm sm:text-base">
                                <span class="hidden sm:inline">Organize teams into groups for league matches. Drag and drop teams to assign them to groups.</span>
                                <span class="sm:hidden">Organize teams into groups. Click "Add" button next to each team to assign them.</span>
                            </p>
                            
                            @if($existingGroups->count() > 0)
                                <!-- Existing Groups Display -->
                                <div id="existing-groups-display" class="mb-6 p-4 section-card soft border border-white/10 rounded-xl">
                                    <h4 class="text-base font-semibold text-cyan-200 mb-3">Current Groups</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach($existingGroups as $group)
                                            <div class="section-card p-3 rounded-lg border border-white/10">
                                                <h5 class="font-medium text-white mb-2">{{ $group->name }}</h5>
                                                <div class="space-y-1">
                                                    @foreach($group->leagueTeams as $leagueTeam)
                                                        <div class="text-sm text-slate-300">• {{ $leagueTeam->team->name }}</div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-4 flex flex-col sm:flex-row gap-3">
                                        <a href="{{ route('leagues.league-match.fixture-setup', $league) }}" 
                                           class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-cyan-500 text-white font-semibold rounded-lg hover:from-emerald-400 hover:to-cyan-400 transition-colors text-sm shadow-lg shadow-emerald-500/20">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            Setup Fixtures
                                        </a>
                                        <button onclick="editGroups()" class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-semibold rounded-lg hover:from-cyan-500 hover:to-blue-500 transition-colors text-sm shadow-lg shadow-cyan-500/20">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit Groups
                                        </a>
                                    </div>
                                </div>
                            @endif
                            
                            <div id="group-setup-section" class="space-y-4 lg:space-y-0 lg:grid lg:grid-cols-2 lg:gap-6">
                                <!-- Available Teams Section -->
                                <div class="section-card p-3 sm:p-4 rounded-xl border border-white/10">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3 space-y-2 sm:space-y-0">
                                        <h4 class="font-semibold text-white text-sm sm:text-base">Available Teams</h4>
                                        <div class="relative">
                                            <input type="text" 
                                                   id="team-search" 
                                                   placeholder="Search teams..." 
                                                   class="pl-9 pr-3 py-1.5 bg-slate-900/60 border border-white/10 rounded-lg text-sm focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 w-full sm:w-44 text-white placeholder-slate-400">
                                            <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div id="available-teams" class="space-y-2 max-h-64 sm:max-h-80 overflow-y-auto">
                                        @foreach($availableTeams as $leagueTeam)
                                        <div class="team-card p-2.5 sm:p-3 bg-slate-900/50 border border-white/10 rounded-xl cursor-pointer hover:border-cyan-400/60 hover:bg-slate-900/70 transition-all duration-150" 
                                             data-team-id="{{ $leagueTeam->id }}" 
                                             data-team-name="{{ $leagueTeam->team->name }}">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center min-w-0 flex-1">
                                                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full flex items-center justify-center mr-2 sm:mr-3 flex-shrink-0 shadow-md shadow-cyan-500/30">
                                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                        </svg>
                                                    </div>
                                                    <span class="font-medium text-white text-sm sm:text-base truncate">{{ $leagueTeam->team->name }}</span>
                                                </div>
                                                <button onclick="showGroupPopup('{{ $leagueTeam->id }}', '{{ $leagueTeam->team->name }}', event)" 
                                                        class="sm:hidden bg-indigo-600 text-white px-2 py-1 rounded text-xs hover:bg-indigo-700 ml-2 flex-shrink-0">
                                                    Add
                                                </button>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-3 text-xs sm:text-sm text-slate-400">
                                        <span class="hidden md:inline">💡 Drag teams to groups</span>
                                        <span class="md:hidden">💡 Click "Add" button to assign team to group</span>
                                    </div>
                                </div>
                                
                                <!-- Tournament Groups Section -->
                                <div class="section-card p-3 sm:p-4 rounded-xl border border-white/10">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3 space-y-2 sm:space-y-0">
                                        <h4 class="font-semibold text-white text-sm sm:text-base">Tournament Groups</h4>
                                        <button onclick="addGroup()" class="text-sm bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-3 py-1.5 sm:py-2 rounded-lg hover:from-cyan-400 hover:to-blue-500 font-semibold self-start shadow-md shadow-cyan-500/25">Add Group</button>
                                    </div>
                                    
                                    <div id="groups-container" class="space-y-3 sm:space-y-4 max-h-64 sm:max-h-80 overflow-y-auto"></div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex justify-end mt-4 sm:mt-6">
                                <button onclick="saveGroups()" class="bg-gradient-to-r from-emerald-500 to-cyan-500 text-white px-4 sm:px-6 py-2 rounded-lg hover:from-emerald-400 hover:to-cyan-400 font-semibold text-sm sm:text-base shadow-lg shadow-emerald-500/20">Save Groups & Continue</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 2: Generate Fixtures -->
                    <div id="step-2" class="step-content hidden">
                        <div class="section-card p-4 sm:p-6 rounded-2xl border border-white/10">
                            <h3 class="text-lg sm:text-xl font-semibold text-white mb-3 sm:mb-4">Generate Fixtures</h3>
                            <p class="text-slate-300 mb-4 sm:mb-6 text-sm sm:text-base">Choose the tournament format to generate match fixtures.</p>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-200 mb-2">Tournament Format</label>
                                    <select id="tournament-format" class="w-full border border-white/15 rounded-lg px-3 py-2 text-sm sm:text-base bg-slate-900/50 text-white focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                                        <option value="single_round_robin">Single Round Robin</option>
                                        <option value="double_round_robin">Double Round Robin</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row sm:justify-between mt-4 sm:mt-6 space-y-2 sm:space-y-0 sm:space-x-3">
                                <button onclick="showStep(1)" class="bg-white/10 text-slate-200 px-4 sm:px-6 py-2 rounded-lg hover:bg-white/15 border border-white/15 font-semibold text-sm sm:text-base">Back</button>
                                <button onclick="generateFixtures()" class="bg-gradient-to-r from-emerald-500 to-cyan-500 text-white px-4 sm:px-6 py-2 rounded-lg hover:from-emerald-400 hover:to-cyan-400 font-semibold text-sm sm:text-base shadow-lg shadow-emerald-500/20">Generate Fixtures</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Group Selection Popup -->
<div id="group-popup" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="modal-card shadow-2xl max-w-sm w-full">
            <div class="p-4">
                <h3 class="text-lg font-semibold text-white mb-2">Add Team to Group</h3>
                <p id="popup-team-name" class="text-sm text-slate-300 mb-4"></p>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-200 mb-1">Select Group</label>
                        <select id="popup-group-select" class="w-full border border-white/15 rounded-lg px-3 py-2 text-sm bg-slate-900/60 text-white focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                            <option value="">Choose a group...</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex space-x-3 mt-6">
                    <button onclick="closeGroupPopup()" class="flex-1 bg-white/10 text-slate-200 px-4 py-2 rounded-lg hover:bg-white/15 border border-white/15 font-semibold text-sm">Cancel</button>
                    <button onclick="addTeamToSelectedGroup()" class="flex-1 bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-4 py-2 rounded-lg hover:from-cyan-400 hover:to-blue-500 font-semibold text-sm shadow-md shadow-cyan-500/25">Add Team</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let groupCount = 0;
let currentTeamId = null;
let currentTeamName = null;
const teamCardBaseClass = 'team-card p-2.5 sm:p-3 bg-slate-900/50 border border-white/10 rounded-xl cursor-pointer hover:border-cyan-400/60 hover:bg-slate-900/70 transition-all duration-150';
const teamCardAssignedClass = 'team-card p-2.5 sm:p-3 bg-emerald-500/10 border border-emerald-300/40 rounded-xl cursor-pointer hover:border-emerald-300/60 hover:bg-emerald-500/15 transition-all duration-150';
const groupContainerClass = 'group-container rounded-xl border border-white/15 p-3 sm:p-4 bg-slate-900/40 shadow-inner';
const groupTeamsClass = 'group-teams group-dropzone min-h-[80px] sm:min-h-[100px] border-2 border-dashed border-white/20 rounded-xl p-2 sm:p-3 bg-slate-900/30';
const groupPlaceholderClass = 'text-slate-400 text-xs sm:text-sm text-center py-4';

function addGroup() {
    groupCount++;
    const groupsContainer = document.getElementById('groups-container');
    const groupHtml = `
        <div class="${groupContainerClass}" data-group-id="${groupCount}">
            <div class="flex items-center justify-between mb-3">
                <input type="text" placeholder="Group Name" class="group-name font-medium text-white border-0 bg-transparent focus:ring-0 p-0 text-sm sm:text-base flex-1 mr-2 placeholder-slate-500" value="Group ${String.fromCharCode(64 + groupCount)}">
                <button onclick="removeGroup(${groupCount})" class="text-slate-300 hover:text-white flex-shrink-0 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="${groupTeamsClass}" ondrop="drop(event)" ondragover="allowDrop(event)">
                <p class="${groupPlaceholderClass}">
                    <span class="hidden sm:inline">Drop teams here</span>
                    <span class="sm:hidden">Teams will appear here</span>
                </p>
            </div>
        </div>
    `;
    groupsContainer.insertAdjacentHTML('beforeend', groupHtml);
    updatePopupGroupOptions();
}

function removeGroup(groupId) {
    const groupElement = document.querySelector(`[data-group-id="${groupId}"]`);
    if (groupElement) {
        const teams = groupElement.querySelectorAll('.team-card');
        const availableTeams = document.getElementById('available-teams');
        teams.forEach(team => {
            team.className = teamCardBaseClass;
            // Re-add the Add button for mobile
            const addButton = team.querySelector('button');
            if (!addButton) {
                const teamName = team.dataset.teamName;
                const teamId = team.dataset.teamId;
                team.querySelector('.flex').innerHTML += `
                    <button onclick="showGroupPopup('${teamId}', '${teamName}', event)" 
                            class="sm:hidden bg-indigo-600 text-white px-2 py-1 rounded text-xs hover:bg-indigo-700 ml-2 flex-shrink-0">
                        Add
                    </button>
                `;
            }
            availableTeams.appendChild(team);
        });
        groupElement.remove();
        updatePopupGroupOptions();
    }
}

function updatePopupGroupOptions() {
    const popupSelect = document.getElementById('popup-group-select');
    const groups = document.querySelectorAll('.group-container');
    
    popupSelect.innerHTML = '<option value="">Choose a group...</option>';
    groups.forEach(group => {
        const groupName = group.querySelector('.group-name').value;
        const groupId = group.dataset.groupId;
        popupSelect.innerHTML += `<option value="${groupId}">${groupName}</option>`;
    });
}

function showGroupPopup(teamId, teamName, event) {
    event.stopPropagation();
    currentTeamId = teamId;
    currentTeamName = teamName;
    
    document.getElementById('popup-team-name').textContent = `Team: ${teamName}`;
    document.getElementById('group-popup').classList.remove('hidden');
    updatePopupGroupOptions();
}

function closeGroupPopup() {
    document.getElementById('group-popup').classList.add('hidden');
    currentTeamId = null;
    currentTeamName = null;
}

function addTeamToSelectedGroup() {
    const selectedGroupId = document.getElementById('popup-group-select').value;
    if (!selectedGroupId) {
        alert('Please select a group');
        return;
    }
    
    const targetGroup = document.querySelector(`[data-group-id="${selectedGroupId}"] .group-teams`);
    const totalTeams = document.querySelectorAll('.team-card').length;
    const totalGroups = document.querySelectorAll('.group-container').length;
    const maxTeamsPerGroup = Math.ceil(totalTeams / totalGroups);
    const currentTeamsInGroup = targetGroup.querySelectorAll('.team-card').length;
    
    if (currentTeamsInGroup >= maxTeamsPerGroup) {
        alert(`This group already has the maximum number of teams (${maxTeamsPerGroup}). Please remove a team first or choose another group.`);
        return;
    }
    
    const teamElement = document.querySelector(`[data-team-id="${currentTeamId}"]`);
    
    // Update team styling and remove Add button
    teamElement.className = teamCardAssignedClass;
    const addButton = teamElement.querySelector('button');
    if (addButton) addButton.remove();
    
    targetGroup.appendChild(teamElement);
    
    const placeholder = targetGroup.querySelector('p');
    if (placeholder) placeholder.remove();
    
    closeGroupPopup();
    
    // Auto-assign remaining teams if only 2 groups and one is half full
    autoAssignRemainingTeams();
}

function searchTeams() {
    const searchTerm = document.getElementById('team-search').value.toLowerCase();
    const teamCards = document.querySelectorAll('#available-teams .team-card');
    
    teamCards.forEach(card => {
        const teamName = card.dataset.teamName.toLowerCase();
        if (teamName.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function allowDrop(ev) {
    ev.preventDefault();
}

function drop(ev) {
    ev.preventDefault();
    const data = ev.dataTransfer.getData("text");
    const draggedElement = document.getElementById(data);
    
    if (!draggedElement) return;
    
    if (ev.target.classList.contains('group-teams') || ev.target.closest('.group-teams')) {
        const dropZone = ev.target.classList.contains('group-teams') ? ev.target : ev.target.closest('.group-teams');
        
        const totalTeams = document.querySelectorAll('.team-card').length;
        const totalGroups = document.querySelectorAll('.group-container').length;
        const maxTeamsPerGroup = Math.ceil(totalTeams / totalGroups);
        const currentTeamsInGroup = dropZone.querySelectorAll('.team-card').length;
        
        if (currentTeamsInGroup >= maxTeamsPerGroup) {
            alert(`This group already has the maximum number of teams (${maxTeamsPerGroup}). Please remove a team first.`);
            return;
        }
        
        // Update styling and remove Add button
        draggedElement.className = teamCardAssignedClass;
        const addButton = draggedElement.querySelector('button');
        if (addButton) addButton.remove();
        
        dropZone.appendChild(draggedElement);
        
        const placeholder = dropZone.querySelector('p');
        if (placeholder) placeholder.remove();
        
        // Auto-assign remaining teams if only 2 groups and one is half full
        autoAssignRemainingTeams();
    }
}

function autoAssignRemainingTeams() {
    const totalGroups = document.querySelectorAll('.group-container').length;
    if (totalGroups !== 2) return;
    
    const totalTeams = document.querySelectorAll('.team-card').length;
    const halfTeams = Math.floor(totalTeams / 2);
    const groups = document.querySelectorAll('.group-container');
    
    // Check if one group has exactly half the teams
    let groupWithHalf = null;
    let emptyGroup = null;
    
    groups.forEach(group => {
        const teamsInGroup = group.querySelectorAll('.team-card').length;
        if (teamsInGroup === halfTeams) {
            groupWithHalf = group;
        } else if (teamsInGroup === 0) {
            emptyGroup = group;
        }
    });
    
    if (groupWithHalf && emptyGroup) {
        const availableTeams = document.querySelectorAll('#available-teams .team-card');
        const emptyGroupTeams = emptyGroup.querySelector('.group-teams');
        
        availableTeams.forEach(team => {
            team.className = teamCardAssignedClass;
            const addButton = team.querySelector('button');
            if (addButton) addButton.remove();
            emptyGroupTeams.appendChild(team);
        });
        
        const placeholder = emptyGroupTeams.querySelector('p');
        if (placeholder) placeholder.remove();
    }
}

function saveGroups() {
    const groups = [];
    const groupContainers = document.querySelectorAll('.group-container');
    
    groupContainers.forEach(container => {
        const groupName = container.querySelector('.group-name').value.trim();
        const teamCards = container.querySelectorAll('.team-card');
        const teamIds = Array.from(teamCards).map(card => card.dataset.teamId);
        
        if (groupName && teamIds.length > 0) {
            groups.push({
                name: groupName,
                team_ids: teamIds
            });
        }
    });
    
    if (groups.length < 1) {
        alert('Please create at least 1 group with teams assigned.');
        return;
    }
    
    const availableTeams = document.querySelectorAll('#available-teams .team-card');
    if (availableTeams.length > 0) {
        if (!confirm(`${availableTeams.length} teams are not assigned to any group. Continue anyway?`)) {
            return;
        }
    }
    
    fetch('{{ route("leagues.league-match.groups", $league) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ groups: groups })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showStep(2);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving groups. Please try again.');
    });
}

function generateFixtures() {
    const format = document.getElementById('tournament-format').value;
    
    const generateBtn = document.querySelector('button[onclick="generateFixtures()"]');
    const originalText = generateBtn.innerHTML;
    generateBtn.innerHTML = `
        <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        Generating Fixtures...
    `;
    generateBtn.disabled = true;
    
    fetch('{{ route("leagues.league-match.fixtures", $league) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ format: format })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            generateBtn.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Fixtures Generated Successfully!
            `;
            generateBtn.classList.remove('bg-gradient-to-r', 'from-emerald-500', 'to-cyan-500', 'hover:from-emerald-400', 'hover:to-cyan-400', 'shadow-lg', 'shadow-emerald-500/20');
            generateBtn.classList.add('bg-emerald-600', 'hover:bg-emerald-600', 'shadow-md', 'shadow-emerald-500/30');
            
            // Redirect to fixture setup page
            setTimeout(() => {
                window.location.href = '{{ route("leagues.league-match.fixture-setup", $league) }}';
            }, 1500);
        } else {
            alert('Error: ' + data.message);
            generateBtn.innerHTML = originalText;
            generateBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while generating fixtures. Please try again.');
        generateBtn.innerHTML = originalText;
        generateBtn.disabled = false;
    });
}

function showStep(stepNumber) {
    document.querySelectorAll('.step-content').forEach(step => step.classList.add('hidden'));
    document.getElementById(`step-${stepNumber}`).classList.remove('hidden');
    
    document.querySelectorAll('.step-indicator').forEach(indicator => {
        const step = parseInt(indicator.dataset.step);
        const stepText = document.querySelector(`.step-text[data-step="${step}"]`);
        
        indicator.classList.toggle('is-muted', step > stepNumber);
        indicator.classList.toggle('shadow-none', step > stepNumber);
        indicator.classList.toggle('text-slate-900', step <= stepNumber);
        indicator.classList.toggle('text-slate-300', step > stepNumber);

        if (stepText) {
            stepText.classList.remove('text-slate-300', 'text-slate-400', 'text-white');
            stepText.classList.add(step <= stepNumber ? 'text-white' : 'text-slate-400');
        }
    });
}

function editGroups() {
    // Hide existing groups display
    const existingGroupsDiv = document.getElementById('existing-groups-display');
    if (existingGroupsDiv) {
        existingGroupsDiv.style.display = 'none';
    }
    
    // Load existing groups into the editor
    loadExistingGroupsForEditing();
}

function loadExistingGroupsForEditing() {
    const existingGroups = @json($existingGroups);
    const groupsContainer = document.getElementById('groups-container');
    
    // Clear existing groups in editor
    groupsContainer.innerHTML = '';
    groupCount = 0;
    
    // Load each existing group
    existingGroups.forEach((group, index) => {
        groupCount++;
        const groupHtml = `
            <div class="${groupContainerClass}" data-group-id="${groupCount}">
                <div class="flex items-center justify-between mb-3">
                    <input type="text" placeholder="Group Name" class="group-name font-medium text-white border-0 bg-transparent focus:ring-0 p-0 text-sm sm:text-base flex-1 mr-2 placeholder-slate-500" value="${group.name}">
                    <button onclick="removeGroup(${groupCount})" class="text-slate-300 hover:text-white flex-shrink-0 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="${groupTeamsClass}" ondrop="drop(event)" ondragover="allowDrop(event)">
                </div>
            </div>
        `;
        groupsContainer.insertAdjacentHTML('beforeend', groupHtml);
        
        // Create team elements for this group
        const groupContainer = document.querySelector(`[data-group-id="${groupCount}"] .group-teams`);
        group.league_teams.forEach(leagueTeam => {
            // Create team element if it doesn't exist in available teams
            let teamElement = document.querySelector(`[data-team-id="${leagueTeam.id}"]`);
            if (!teamElement) {
                const teamHtml = `
                    <div class="${teamCardAssignedClass}" 
                         data-team-id="${leagueTeam.id}" 
                         data-team-name="${leagueTeam.team.name}" 
                         draggable="true" 
                         id="team-existing-${leagueTeam.id}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center min-w-0 flex-1">
                                <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full flex items-center justify-center mr-2 sm:mr-3 flex-shrink-0 shadow-md shadow-cyan-500/30">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                                <span class="font-medium text-white text-sm sm:text-base truncate">${leagueTeam.team.name}</span>
                            </div>
                        </div>
                    </div>
                `;
                groupContainer.insertAdjacentHTML('beforeend', teamHtml);
                teamElement = document.querySelector(`#team-existing-${leagueTeam.id}`);
                
                // Add drag event listener
                teamElement.addEventListener('dragstart', function(e) {
                    e.dataTransfer.setData('text/plain', e.target.id);
                });
            } else {
                // Move existing team element
                teamElement.className = teamCardAssignedClass;
                const addButton = teamElement.querySelector('button');
                if (addButton) addButton.remove();
                groupContainer.appendChild(teamElement);
            }
        });
    });
    
    updatePopupGroupOptions();
}

document.addEventListener('DOMContentLoaded', function() {
    const teamCards = document.querySelectorAll('.team-card');
    teamCards.forEach((card, index) => {
        card.draggable = true;
        card.id = `team-${index}`;
        
        card.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('text/plain', e.target.id);
        });
    });
    
    document.getElementById('team-search').addEventListener('input', searchTeams);
    
    // Only add default groups if no existing groups
    const existingGroups = {{ $existingGroups->count() }};
    if (existingGroups === 0) {
        addGroup();
    } else {
        // Show existing groups display by default
        const existingGroupsDiv = document.getElementById('existing-groups-display');
        if (existingGroupsDiv) {
            existingGroupsDiv.style.display = 'block';
        }
    }
});
</script>
@endsection
