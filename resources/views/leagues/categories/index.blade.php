@extends('layouts.app')

@section('title', 'Manage Categories - MakeMyLeague')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/auction.css') }}">
<style>
    .cat-card {
        transition: all 0.2s ease;
    }
    .cat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .player-row {
        transition: background-color 0.15s ease;
    }
    .player-row:hover {
        background-color: #f8fafc;
    }
    /* Bulk selection styles */
    .bulk-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    .bulk-action-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        transform: translateY(100%);
        transition: transform 0.3s ease;
        z-index: 9999;
    }
    .bulk-action-bar.visible {
        transform: translateY(0);
    }
    #bulk-assign-modal {
        z-index: 10000 !important;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen auction-bg py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold glacier-text-primary">Manage Categories</h1>
                <p class="mt-1 text-sm text-gray-500">Create categories and manage player assignments.</p>
            </div>
            <a href="{{ route('auction.manage-players', $league) }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Players
            </a>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-10">
            <!-- Create New Card -->
            <div onclick="openCreateCategoryModal()" class="cat-card bg-white rounded-lg border-2 border-dashed border-gray-300 p-6 flex flex-col items-center justify-center cursor-pointer hover:border-indigo-500 hover:bg-indigo-50 group min-h-[200px]">
                <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center mb-3 group-hover:bg-indigo-200 transition-colors">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Create New Category</h3>
                <p class="text-sm text-gray-500 text-center mt-1">Define limits and requirements</p>
            </div>

            <!-- Category Cards -->
            @foreach($categories as $category)
            <div class="cat-card glacier-card flex flex-col justify-between min-h-[200px]">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-bold text-gray-900 truncate" title="{{ $category->name }}">{{ $category->name }}</h3>
                        <div class="flex items-center space-x-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $category->players_count }} Players
                            </span>
                        </div>
                    </div>
                    <div class="text-sm text-gray-500 mb-4">
                        Requires: <span class="font-medium text-gray-700">{{ $category->min_requirement }}</span> 
                        <span class="mx-1 text-gray-300">|</span>
                        Max: <span class="font-medium text-gray-700">{{ $category->max_requirement ?? '∞' }}</span>
                    </div>
                </div>
                <div class="border-t border-gray-100 bg-gray-50 p-4 rounded-b-lg grid grid-cols-2 gap-2">
                    <button onclick="openAutoAssignModal({{ $category->id }}, '{{ addslashes($category->name) }}')" 
                            class="col-span-1 text-xs font-medium text-indigo-700 hover:bg-indigo-100 bg-white border border-indigo-200 rounded py-2 transition-colors">
                        Auto Assign
                    </button>
                    <button onclick="openManualAddModal({{ $category->id }}, '{{ addslashes($category->name) }}')"
                            class="col-span-1 text-xs font-medium text-emerald-700 hover:bg-emerald-100 bg-white border border-emerald-200 rounded py-2 transition-colors">
                        + Add Player
                    </button>
                    <button onclick="filterByCategory({{ $category->id }})" 
                            class="col-span-1 text-xs font-medium text-gray-700 hover:bg-gray-100 bg-white border border-gray-200 rounded py-2 transition-colors">
                        View List
                    </button>
                    <button onclick="deleteCategory({{ $category->id }})" 
                            class="col-span-1 text-xs font-medium text-red-700 hover:bg-red-100 bg-white border border-red-200 rounded py-2 transition-colors">
                        Delete
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Players Management Section -->
        <div id="players-section" class="glacier-card">
            <div class="px-6 py-4 border-b glacier-border flex flex-col md:flex-row justify-between items-center gap-4">
                <h3 class="text-lg font-bold text-gray-900">Categorized Players</h3>
                
                <div class="flex items-center space-x-2 w-full md:w-auto">
                    <div class="relative w-full md:w-64">
                         <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" id="table-search" oninput="debounceSearch()" 
                               class="block w-full pl-10 sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" 
                               placeholder="Search players...">
                    </div>
                </div>
            </div>
            
            <!-- Category Tabs -->
            <div class="border-b border-gray-200 bg-gray-50 overflow-x-auto">
                <nav class="-mb-px flex space-x-6 px-6" aria-label="Tabs" id="category-tabs">
                    <button onclick="filterByCategory('all')" 
                            class="cat-tab active border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors" data-id="all">
                        All Players
                    </button>
                     <button onclick="filterByCategory('uncategorized')" 
                            class="cat-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors" data-id="uncategorized">
                        Uncategorized
                    </button>
                    @foreach($categories as $category)
                    <button onclick="filterByCategory({{ $category->id }})" 
                            class="cat-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors" data-id="{{ $category->id }}">
                        {{ $category->name }}
                    </button>
                    @endforeach
                </nav>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto min-h-[400px]">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left">
                                <input type="checkbox" id="select-all-checkbox" class="bulk-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" onclick="toggleSelectAll()">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Player</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="players-table-body">
                        <!-- Ajax Content -->
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between" id="pagination-container">
                <!-- Pagination -->
            </div>
        </div>

    </div>
</div>

<!-- Modals -->

<!-- Create Category Modal -->
<div id="create-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeCreateCategoryModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Create New Category</h3>
            <form id="create-category-form" onsubmit="event.preventDefault(); submitCreateCategory();">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Category Name</label>
                        <input type="text" name="name" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Min Players</label>
                            <input type="number" name="min_requirement" min="0" value="0" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Max Players</label>
                            <input type="number" name="max_requirement" min="0" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="flex justify-end pt-4 space-x-3">
                         <button type="button" onclick="closeCreateCategoryModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Cancel</button>
                         <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manual Add Player Modal -->
<div id="manual-add-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-start justify-center min-h-screen px-4 pt-20 pb-20 text-center">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeManualAddModal()"></div>
        <div class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-lg shadow-xl">
             <h3 class="text-lg font-medium leading-6 text-gray-900">Add Player to <span id="manual-add-cat-name" class="text-indigo-600">Category</span></h3>
             <input type="hidden" id="manual-add-cat-id">
             
             <div class="mt-4">
                 <label class="block text-sm font-medium text-gray-700 mb-1">Search Player</label>
                 <div class="relative">
                     <input type="text" id="manual-player-search" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Type name..." oninput="searchForManualAdd(this.value)">
                     <div id="manual-search-results" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
                 </div>
             </div>
             
             <div class="mt-5 flex justify-end">
                 <button type="button" onclick="closeManualAddModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Close</button>
             </div>
        </div>
    </div>
</div>

<!-- Change Category Modal (for table action) -->
<div id="change-cat-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeChangeCatModal()"></div>
        <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-sm sm:w-full sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Change Category</h3>
            <input type="hidden" id="change-cat-player-id">
            <div class="space-y-2 max-h-60 overflow-y-auto mb-4">
                <button onclick="confirmChangeCategory(null)" class="w-full text-left px-4 py-2 rounded hover:bg-red-50 text-red-700 font-medium">Remove Category</button>
                @foreach($categories as $cat)
                <button onclick="confirmChangeCategory({{ $cat->id }})" class="w-full text-left px-4 py-2 rounded hover:bg-gray-100 text-gray-700">{{ $cat->name }}</button>
                @endforeach
            </div>
             <div class="flex justify-end">
                 <button type="button" onclick="closeChangeCatModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Cancel</button>
             </div>
        </div>
    </div>
</div>

<!-- Auto Assign Tool Modal (Existing) -->
<!-- ... Reusing code from previous step or rewriting ... -->
<div id="auto-assign-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="closeAutoAssignModal()"></div>
         <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Auto Assign to <span id="auto-assign-cat-name" class="text-indigo-600"></span></h3>
            <input type="hidden" id="auto-assign-cat-id">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">District</label>
                    <select id="auto-assign-district" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                        <option value="">Select District</option>
                        @foreach($districts as $district)
                        <option value="{{ $district->id }}">{{ $district->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="text-center text-sm text-gray-500">- OR -</div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Local Body</label>
                    <input type="text" id="local-body-search-input" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" placeholder="Search..." oninput="searchLocalBodies(this.value)">
                    <div id="local-body-results" class="hidden mt-1 border rounded-md max-h-40 overflow-y-auto"></div>
                    <input type="hidden" id="auto-assign-local">
                     <div id="selected-local-body" class="hidden mt-2 p-2 bg-indigo-50 border border-indigo-100 rounded text-sm text-indigo-700 flex justify-between">
                         <span id="selected-lb-name"></span>
                         <button type="button" onclick="clearLocalBody()" class="text-indigo-400">&times;</button>
                     </div>
                </div>
            </div>
            
             <div class="mt-5 flex justify-end space-x-3">
                 <button type="button" onclick="closeAutoAssignModal()" class="px-4 py-2 border rounded-md text-gray-700">Cancel</button>
                 <button type="button" onclick="submitAutoAssign()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Execute</button>
             </div>
         </div>
    </div>
</div>

<div id="loading-overlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-[100] hidden flex items-center justify-center">
    <div class="bg-white p-5 rounded-lg flex items-center space-x-4">
        <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-lg font-medium text-gray-900">Processing...</span>
    </div>
</div>

<!-- Bulk Action Bar -->
<div id="bulk-action-bar" class="bulk-action-bar bg-indigo-600 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <span class="text-white font-medium">
                    <span id="selected-count">0</span> players selected
                </span>
                <button onclick="clearSelection()" class="text-indigo-200 hover:text-white text-sm underline">
                    Clear selection
                </button>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="openBulkAssignModal()" 
                        class="inline-flex items-center px-4 py-2 bg-white text-indigo-600 font-semibold text-sm rounded-md hover:bg-indigo-50 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-5 5a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 10V5a2 2 0 012-2zm0 0v.01"/>
                    </svg>
                    Assign to Category
                </button>
                <button onclick="bulkRemoveCategory()" 
                        class="inline-flex items-center px-4 py-2 bg-red-500 text-white font-semibold text-sm rounded-md hover:bg-red-600 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Remove Category
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Assign Modal -->
<div id="bulk-assign-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeBulkAssignModal()"></div>
        <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Assign Players to Category</h3>
            <p class="text-sm text-gray-500 mb-4">
                <span id="bulk-modal-count">0</span> players selected
            </p>
            
            <div class="space-y-2 max-h-60 overflow-y-auto mb-4">
                @foreach($categories as $cat)
                <button onclick="submitBulkAssign({{ $cat->id }})" 
                        class="w-full text-left px-4 py-3 rounded-lg hover:bg-indigo-50 text-gray-700 border border-gray-200 hover:border-indigo-300 transition flex items-center justify-between group">
                    <span class="font-medium">{{ $cat->name }}</span>
                    <span class="text-xs text-gray-400 group-hover:text-indigo-500">{{ $cat->players_count }} players</span>
                </button>
                @endforeach
            </div>
            
            <div class="flex justify-end pt-4 border-t">
                <button type="button" onclick="closeBulkAssignModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';
    let currentCategoryFilter = 'all';
    let searchTimeout;

    document.addEventListener('DOMContentLoaded', () => {
        loadPlayers();
    });

    function showLoading(show = true) {
        document.getElementById('loading-overlay').classList.toggle('hidden', !show);
    }
    
    // --- Categories Handlers ---
    function openCreateCategoryModal() {
        document.getElementById('create-modal').classList.remove('hidden');
    }
    
    function closeCreateCategoryModal() {
        document.getElementById('create-modal').classList.add('hidden');
    }
    
    function submitCreateCategory() {
        const form = document.getElementById('create-category-form');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        showLoading(true);
        fetch('{{ route("leagues.categories.store", $league) }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) location.reload();
            else alert(res.message);
        })
        .finally(() => showLoading(false));
    }
    
    function deleteCategory(id) {
        if(!confirm('Delete this category? Assigned players will be uncategorized.')) return;
        showLoading(true);
        fetch(`{{ url("leagues/{$league->id}/categories") }}/${id}`, {
            method: 'DELETE',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken}
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) location.reload();
        })
        .finally(() => showLoading(false));
    }

    // --- Players Ajax Table ---
    function filterByCategory(catId) {
        currentCategoryFilter = catId;
        
        // Update Tabs
        document.querySelectorAll('.cat-tab').forEach(tab => {
            if(tab.dataset.id == catId) {
                tab.classList.add('active', 'border-indigo-500', 'text-indigo-600');
                tab.classList.remove('border-transparent', 'text-gray-500');
            } else {
                tab.classList.remove('active', 'border-indigo-500', 'text-indigo-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            }
        });
        
        loadPlayers(1);
        
        // Scroll to table if not near
        const table = document.getElementById('players-section');
        if(table.getBoundingClientRect().top > window.innerHeight) {
            table.scrollIntoView({behavior: 'smooth'});
        }
    }
    
    function debounceSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => loadPlayers(1), 300);
    }
    
    function loadPlayers(page = 1) {
        const search = document.getElementById('table-search').value;
        const tbody = document.getElementById('players-table-body');
        
        tbody.innerHTML = '<tr><td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Loading...</td></tr>';
        
        const url = new URL('{{ route("leagues.categories.players", $league) }}');
        url.searchParams.append('page', page);
        url.searchParams.append('category_id', currentCategoryFilter);
        if(search) url.searchParams.append('search', search);
        
        fetch(url)
        .then(res => res.json())
        .then(data => {
            renderTable(data.data);
            renderPagination(data);
        });
    }
    
    // Bulk selection state
    let selectedPlayers = new Set();
    
    function renderTable(players) {
        const tbody = document.getElementById('players-table-body');
        tbody.innerHTML = '';
        
        if(players.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No players found.</td></tr>';
            return;
        }
        
        players.forEach(p => {
             const catName = p.category ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${p.category.name}</span>` : '<span class="text-gray-400 italic">Uncategorized</span>';
             const isChecked = selectedPlayers.has(p.id) ? 'checked' : '';
             
             const tr = document.createElement('tr');
             tr.className = 'player-row';
             tr.innerHTML = `
                <td class="px-4 py-4">
                    <input type="checkbox" class="bulk-checkbox player-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" 
                           data-id="${p.id}" ${isChecked} onclick="togglePlayerSelect(${p.id})">
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${p.player ? p.player.name : 'Unknown'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${p.player && p.player.mobile ? p.player.mobile : '-'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${catName}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button onclick="openChangeCatModal(${p.id})" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</button>
                    ${p.league_player_category_id ? `<button onclick="assignCategory(${p.id}, null)" class="text-red-600 hover:text-red-900" title="Remove Category">&times;</button>` : ''}
                </td>
             `;
             tbody.appendChild(tr);
        });
        
        updateSelectAllState();
        updateBulkActionBar();
    }
    
    function renderPagination(data) {
        const container = document.getElementById('pagination-container');
        if(data.last_page <= 1) {
            container.innerHTML = '';
            return;
        }
        
        container.innerHTML = `
            <div class="text-sm text-gray-700">Page ${data.current_page} of ${data.last_page}</div>
            <div class="space-x-1">
                <button onclick="loadPlayers(${data.current_page - 1})" ${data.prev_page_url ? '' : 'disabled'} class="px-3 py-1 border rounded disabled:opacity-50">Prev</button>
                <button onclick="loadPlayers(${data.current_page + 1})" ${data.next_page_url ? '' : 'disabled'} class="px-3 py-1 border rounded disabled:opacity-50">Next</button>
            </div>
        `;
    }

    // --- Manual Add Modal ---
    function openManualAddModal(catId, catName) {
        document.getElementById('manual-add-cat-id').value = catId;
        document.getElementById('manual-add-cat-name').textContent = catName;
        document.getElementById('manual-add-modal').classList.remove('hidden');
        document.getElementById('manual-player-search').value = '';
        document.getElementById('manual-search-results').classList.add('hidden');
        document.getElementById('manual-player-search').focus();
    }
    
    function closeManualAddModal() {
        document.getElementById('manual-add-modal').classList.add('hidden');
    }
    
    let manualSearchTimeout;
    function searchForManualAdd(query) {
        clearTimeout(manualSearchTimeout);
        const resultsDiv = document.getElementById('manual-search-results');
        
        if(query.length < 2) {
            resultsDiv.classList.add('hidden');
            return;
        }
        
        manualSearchTimeout = setTimeout(() => {
            fetch(`{{ route("leagues.categories.search-players", $league) }}?search=${query}`)
            .then(res => res.json())
            .then(data => {
                resultsDiv.innerHTML = '';
                if(data.length > 0) {
                    data.forEach(p => {
                        const div = document.createElement('div');
                        div.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer flex justify-between items-center';
                        div.innerHTML = `<span>${p.name}</span> <span class="text-xs text-gray-400">${p.category_name}</span>`;
                        div.onclick = () => {
                            const catId = document.getElementById('manual-add-cat-id').value;
                            assignCategory(p.id, catId, true);
                        };
                        resultsDiv.appendChild(div);
                    });
                    resultsDiv.classList.remove('hidden');
                } else {
                    resultsDiv.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500">No players found</div>';
                    resultsDiv.classList.remove('hidden');
                }
            });
        }, 300);
    }

    // --- Assign Category Logic (Shared) ---
    function assignCategory(playerId, catId, reload = false) {
        showLoading(true);
        fetch('{{ route("leagues.categories.assign-player", $league) }}', {
             method: 'POST',
             headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
             body: JSON.stringify({ league_player_id: playerId, category_id: catId })
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                if(reload) {
                    location.reload(); 
                } else {
                    closeChangeCatModal();
                    loadPlayers(1); // Refresh table
                }
            } else {
                alert(res.message);
            }
        })
        .finally(() => showLoading(false));
    }
    
    function openChangeCatModal(playerId) {
        document.getElementById('change-cat-player-id').value = playerId;
        document.getElementById('change-cat-modal').classList.remove('hidden');
    }
    
    function closeChangeCatModal() {
        document.getElementById('change-cat-modal').classList.add('hidden');
    }
    
    function confirmChangeCategory(catId) {
        const playerId = document.getElementById('change-cat-player-id').value;
        assignCategory(playerId, catId);
    }
    
    // --- Auto Assign Logic ---
    // (Similar to previous implementation, kept simple here for brevity but fully functional in real implementation)
    function openAutoAssignModal(catId, catName) {
        document.getElementById('auto-assign-cat-id').value = catId;
        document.getElementById('auto-assign-cat-name').textContent = catName;
        document.getElementById('auto-assign-modal').classList.remove('hidden');
    }
    function closeAutoAssignModal() {
        document.getElementById('auto-assign-modal').classList.add('hidden');
    }
    
    let lbTimeout;
    function searchLocalBodies(query) {
         clearTimeout(lbTimeout);
         const res = document.getElementById('local-body-results');
         if(query.length < 2) { res.classList.add('hidden'); return; }
         lbTimeout = setTimeout(() => {
             fetch(`{{ route("auction.search-local-bodies") }}?query=${query}`)
             .then(r => r.json())
             .then(data => {
                 res.innerHTML = '';
                 if(data.length) {
                     data.forEach(lb => {
                         const div = document.createElement('div');
                         div.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm';
                         div.textContent = lb.name;
                         div.onclick = () => {
                             document.getElementById('auto-assign-local').value = lb.id;
                             document.getElementById('selected-local-body').classList.remove('hidden');
                             document.getElementById('selected-lb-name').textContent = lb.name;
                             document.getElementById('local-body-search-input').value = '';
                             res.classList.add('hidden');
                         }
                         res.appendChild(div);
                     });
                     res.classList.remove('hidden');
                 }
             })
         }, 300);
    }
    
    function clearLocalBody() {
        document.getElementById('auto-assign-local').value = '';
        document.getElementById('selected-local-body').classList.add('hidden');
    }
    
    function submitAutoAssign() {
        const catId = document.getElementById('auto-assign-cat-id').value;
        const distId = document.getElementById('auto-assign-district').value;
        const locId = document.getElementById('auto-assign-local').value;
        
        if(!confirm('Assign players matching location?')) return;
        
        showLoading(true);
        fetch('{{ route("leagues.categories.auto-assign", $league) }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
            body: JSON.stringify({ category_id: catId, district_id: distId, local_body_id: locId })
        })
        .then(r => r.json())
        .then(r => {
            if(r.success) location.reload();
            else alert(r.message);
        })
        .finally(() => showLoading(false));
    }
    
    // --- Bulk Selection Logic ---
    function toggleSelectAll() {
        const selectAllCheckbox = document.getElementById('select-all-checkbox');
        const checkboxes = document.querySelectorAll('.player-checkbox');
        
        checkboxes.forEach(cb => {
            const playerId = parseInt(cb.dataset.id);
            cb.checked = selectAllCheckbox.checked;
            if(selectAllCheckbox.checked) {
                selectedPlayers.add(playerId);
            } else {
                selectedPlayers.delete(playerId);
            }
        });
        
        updateBulkActionBar();
    }
    
    function togglePlayerSelect(playerId) {
        if(selectedPlayers.has(playerId)) {
            selectedPlayers.delete(playerId);
        } else {
            selectedPlayers.add(playerId);
        }
        
        updateSelectAllState();
        updateBulkActionBar();
    }
    
    function updateSelectAllState() {
        const selectAllCheckbox = document.getElementById('select-all-checkbox');
        const checkboxes = document.querySelectorAll('.player-checkbox');
        const allChecked = checkboxes.length > 0 && [...checkboxes].every(cb => cb.checked);
        selectAllCheckbox.checked = allChecked;
    }
    
    function updateBulkActionBar() {
        const bar = document.getElementById('bulk-action-bar');
        const countSpan = document.getElementById('selected-count');
        const count = selectedPlayers.size;
        
        countSpan.textContent = count;
        
        if(count > 0) {
            bar.classList.add('visible');
        } else {
            bar.classList.remove('visible');
        }
    }
    
    function clearSelection() {
        selectedPlayers.clear();
        document.querySelectorAll('.player-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('select-all-checkbox').checked = false;
        updateBulkActionBar();
    }
    
    function openBulkAssignModal() {
        document.getElementById('bulk-modal-count').textContent = selectedPlayers.size;
        document.getElementById('bulk-assign-modal').classList.remove('hidden');
    }
    
    function closeBulkAssignModal() {
        document.getElementById('bulk-assign-modal').classList.add('hidden');
    }
    
    function submitBulkAssign(categoryId) {
        if(selectedPlayers.size === 0) {
            alert('No players selected');
            return;
        }
        
        showLoading(true);
        closeBulkAssignModal();
        
        fetch('{{ route("leagues.categories.bulk-assign", $league) }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
            body: JSON.stringify({ 
                player_ids: [...selectedPlayers], 
                category_id: categoryId 
            })
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                clearSelection();
                loadPlayers(1);
                // Show success message
                alert(res.message);
            } else {
                alert(res.message);
            }
        })
        .finally(() => showLoading(false));
    }
    
    function bulkRemoveCategory() {
        if(selectedPlayers.size === 0) {
            alert('No players selected');
            return;
        }
        
        if(!confirm(`Remove category from ${selectedPlayers.size} selected players?`)) return;
        
        submitBulkAssign(null);
    }
    
</script>
@endsection
