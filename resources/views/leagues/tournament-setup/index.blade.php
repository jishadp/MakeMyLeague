@extends('layouts.app')

@section('title', 'Tournament Setup - ' . $league->name)

@section('content')
<div class="py-2 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl overflow-hidden">
            <div class="p-3 sm:p-6 lg:p-10">
                <!-- Header Section -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 sm:mb-8">
                    <div class="mb-3 sm:mb-0">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Tournament Setup</h1>
                        <p class="text-gray-600 mt-1 sm:mt-2 text-sm sm:text-base">{{ $league->name }} - Season {{ $league->season }}</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 rounded-full text-xs sm:text-sm font-semibold bg-green-100 text-green-800 self-start">
                        Auction Completed
                    </span>
                </div>
                
                <!-- Progress Steps -->
                <div class="mb-6 sm:mb-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2 sm:space-x-4">
                            <div class="flex items-center justify-center w-6 h-6 sm:w-8 sm:h-8 bg-indigo-600 text-white rounded-full text-xs sm:text-sm font-semibold step-indicator" data-step="1">1</div>
                            <span class="text-xs sm:text-sm font-medium text-indigo-600 step-text" data-step="1">Create Groups</span>
                        </div>
                        <div class="flex-1 h-px bg-gray-300 mx-2 sm:mx-4"></div>
                        <div class="flex items-center space-x-2 sm:space-x-4">
                            <div class="flex items-center justify-center w-6 h-6 sm:w-8 sm:h-8 bg-gray-300 text-gray-600 rounded-full text-xs sm:text-sm font-semibold step-indicator" data-step="2">2</div>
                            <span class="text-xs sm:text-sm font-medium text-gray-600 step-text" data-step="2">Generate Fixtures</span>
                        </div>
                    </div>
                </div>
                
                <div id="step-content">
                    <!-- Step 1: Create Groups -->
                    <div id="step-1" class="step-content">
                        <div class="bg-gray-50 p-3 sm:p-6 rounded-xl">
                            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3 sm:mb-4">Create Tournament Groups</h3>
                            <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base">
                                <span class="hidden sm:inline">Organize teams into groups for the tournament. Drag and drop teams to assign them to groups.</span>
                                <span class="sm:hidden">Organize teams into groups. Click "Add" button next to each team to assign them.</span>
                            </p>
                            
                            <div class="space-y-4 lg:space-y-0 lg:grid lg:grid-cols-2 lg:gap-6">
                                <!-- Available Teams Section -->
                                <div class="bg-white p-3 sm:p-4 rounded-lg border border-gray-200">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3 space-y-2 sm:space-y-0">
                                        <h4 class="font-semibold text-gray-900 text-sm sm:text-base">Available Teams</h4>
                                        <div class="relative">
                                            <input type="text" 
                                                   id="team-search" 
                                                   placeholder="Search teams..." 
                                                   class="pl-8 pr-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-40">
                                            <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div id="available-teams" class="space-y-2 max-h-64 sm:max-h-80 overflow-y-auto">
                                        @foreach($leagueTeams as $leagueTeam)
                                        <div class="team-card p-2.5 sm:p-3 bg-blue-50 border border-blue-200 rounded-lg cursor-pointer hover:bg-blue-100 transition-colors" 
                                             data-team-id="{{ $leagueTeam->id }}" 
                                             data-team-name="{{ $leagueTeam->team->name }}">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center min-w-0 flex-1">
                                                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-blue-600 rounded-full flex items-center justify-center mr-2 sm:mr-3 flex-shrink-0">
                                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                        </svg>
                                                    </div>
                                                    <span class="font-medium text-gray-900 text-sm sm:text-base truncate">{{ $leagueTeam->team->name }}</span>
                                                </div>
                                                <button onclick="showGroupPopup('{{ $leagueTeam->id }}', '{{ $leagueTeam->team->name }}', event)" 
                                                        class="sm:hidden bg-indigo-600 text-white px-2 py-1 rounded text-xs hover:bg-indigo-700 ml-2 flex-shrink-0">
                                                    Add
                                                </button>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-3 text-xs sm:text-sm text-gray-600">
                                        <span class="hidden md:inline">💡 Drag teams to groups</span>
                                        <span class="md:hidden">💡 Click "Add" button to assign team to group</span>
                                    </div>
                                </div>
                                
                                <!-- Tournament Groups Section -->
                                <div class="bg-white p-3 sm:p-4 rounded-lg border border-gray-200">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3 space-y-2 sm:space-y-0">
                                        <h4 class="font-semibold text-gray-900 text-sm sm:text-base">Tournament Groups</h4>
                                        <button onclick="addGroup()" class="text-sm bg-indigo-600 text-white px-3 py-1.5 sm:py-2 rounded-lg hover:bg-indigo-700 font-medium self-start">Add Group</button>
                                    </div>
                                    
                                    <div id="groups-container" class="space-y-3 sm:space-y-4 max-h-64 sm:max-h-80 overflow-y-auto"></div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex justify-end mt-4 sm:mt-6">
                                <button onclick="saveGroups()" class="bg-indigo-600 text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-indigo-700 font-medium text-sm sm:text-base">Save Groups & Continue</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 2: Generate Fixtures -->
                    <div id="step-2" class="step-content hidden">
                        <div class="bg-gray-50 p-3 sm:p-6 rounded-xl">
                            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3 sm:mb-4">Generate Fixtures</h3>
                            <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base">Choose the tournament format to generate match fixtures.</p>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tournament Format</label>
                                    <select id="tournament-format" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm sm:text-base bg-white">
                                        <option value="single_round_robin">Single Round Robin</option>
                                        <option value="double_round_robin">Double Round Robin</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row sm:justify-between mt-4 sm:mt-6 space-y-2 sm:space-y-0 sm:space-x-3">
                                <button onclick="showStep(1)" class="bg-gray-300 text-gray-700 px-4 sm:px-6 py-2 rounded-lg hover:bg-gray-400 font-medium text-sm sm:text-base">Back</button>
                                <button onclick="generateFixtures()" class="bg-indigo-600 text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-indigo-700 font-medium text-sm sm:text-base">Generate Fixtures</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Group Selection Popup -->
<div id="group-popup" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full">
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Add Team to Group</h3>
                <p id="popup-team-name" class="text-sm text-gray-600 mb-4"></p>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Group</label>
                        <select id="popup-group-select" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">Choose a group...</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex space-x-3 mt-6">
                    <button onclick="closeGroupPopup()" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 font-medium text-sm">Cancel</button>
                    <button onclick="addTeamToSelectedGroup()" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 font-medium text-sm">Add Team</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let groupCount = 0;
let currentTeamId = null;
let currentTeamName = null;

function addGroup() {
    groupCount++;
    const groupsContainer = document.getElementById('groups-container');
    const groupHtml = `
        <div class="group-container border border-gray-300 rounded-lg p-3 sm:p-4 bg-gray-50" data-group-id="${groupCount}">
            <div class="flex items-center justify-between mb-3">
                <input type="text" placeholder="Group Name" class="group-name font-medium text-gray-900 border-0 bg-transparent focus:ring-0 p-0 text-sm sm:text-base flex-1 mr-2" value="Group ${String.fromCharCode(64 + groupCount)}">
                <button onclick="removeGroup(${groupCount})" class="text-red-600 hover:text-red-800 flex-shrink-0 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="group-teams min-h-[80px] sm:min-h-[100px] border-2 border-dashed border-gray-300 rounded-lg p-2 sm:p-3 bg-white" ondrop="drop(event)" ondragover="allowDrop(event)">
                <p class="text-gray-500 text-xs sm:text-sm text-center py-4">
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
            team.className = 'team-card p-2.5 sm:p-3 bg-blue-50 border border-blue-200 rounded-lg cursor-pointer hover:bg-blue-100 transition-colors';
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
    teamElement.className = 'team-card p-2.5 sm:p-3 bg-green-50 border border-green-200 rounded-lg cursor-pointer hover:bg-green-100 transition-colors';
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
        draggedElement.className = 'team-card p-2.5 sm:p-3 bg-green-50 border border-green-200 rounded-lg cursor-pointer hover:bg-green-100 transition-colors';
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
            team.className = 'team-card p-2.5 sm:p-3 bg-green-50 border border-green-200 rounded-lg cursor-pointer hover:bg-green-100 transition-colors';
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
    
    if (groups.length < 2) {
        alert('Please create at least 2 groups with teams assigned.');
        return;
    }
    
    const availableTeams = document.querySelectorAll('#available-teams .team-card');
    if (availableTeams.length > 0) {
        if (!confirm(`${availableTeams.length} teams are not assigned to any group. Continue anyway?`)) {
            return;
        }
    }
    
    fetch('{{ route("leagues.tournament-setup.groups", $league) }}', {
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
    
    fetch('{{ route("leagues.tournament-setup.fixtures", $league) }}', {
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
                Tournament Setup Complete!
            `;
            generateBtn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
            generateBtn.classList.add('bg-green-600');
            
            // Redirect after showing success
            setTimeout(() => {
                window.location.href = '{{ route("leagues.show", $league) }}';
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
        
        if (step === stepNumber) {
            indicator.className = 'flex items-center justify-center w-6 h-6 sm:w-8 sm:h-8 bg-indigo-600 text-white rounded-full text-xs sm:text-sm font-semibold step-indicator';
            stepText.className = 'text-xs sm:text-sm font-medium text-indigo-600 step-text';
        } else {
            indicator.className = 'flex items-center justify-center w-6 h-6 sm:w-8 sm:h-8 bg-gray-300 text-gray-600 rounded-full text-xs sm:text-sm font-semibold step-indicator';
            stepText.className = 'text-xs sm:text-sm font-medium text-gray-600 step-text';
        }
    });
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
    
    addGroup();
    addGroup();
});
</script>
@endsection