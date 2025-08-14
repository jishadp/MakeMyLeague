@extends('layouts.app')

@section('title', 'Manual Auction - CricBid')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow mb-6 transition-all duration-300 hover:shadow-md">
            <div class="px-4 py-4 sm:px-6 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Manual Auction - {{ $league->name }}</h1>
                        <p class="text-gray-600">Directly assign players to teams through manual auction</p>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 w-full sm:w-auto">
                        <!-- Player Status Summary -->
                        <div class="flex flex-wrap gap-2 text-sm">
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full transition-colors duration-200 hover:bg-green-200">
                                Available: {{ $playerCounts['available'] ?? 0 }}
                            </span>
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full transition-colors duration-200 hover:bg-blue-200">
                                Sold: {{ $playerCounts['sold'] ?? 0 }}
                            </span>
                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full transition-colors duration-200 hover:bg-red-200">
                                Unsold: {{ $playerCounts['unsold'] ?? 0 }}
                            </span>
                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full transition-colors duration-200 hover:bg-yellow-200">
                                Skipped: {{ $playerCounts['skip'] ?? 0 }}
                            </span>
                        </div>
                        <a href="{{ route('auction.bidding', $league->slug) }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 w-full sm:w-auto text-center">
                            Switch to Bidding Auction
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Available Players Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow transition-all duration-300 hover:shadow-md">
                    <div class="px-4 py-4 sm:px-6 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Available Players</h2>
                                <p class="text-sm text-gray-600">Select players to auction to teams</p>
                            </div>
                            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                                <!-- Bulk Action Buttons -->
                                <button onclick="toggleSelectAll()" id="selectAllBtn" class="text-sm bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded transition-colors duration-200">
                                    Select All
                                </button>
                                <div class="relative inline-block text-left w-full sm:w-auto">
                                    <button onclick="toggleBulkActions()" id="bulkActionBtn" disabled class="text-sm bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-3 py-1 rounded transition-colors duration-200 w-full sm:w-auto">
                                        Bulk Actions
                                    </button>
                                    <div id="bulkActionsMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 transition-opacity duration-200 opacity-0">
                                        <div class="py-1">
                                            <button onclick="showBulkModal('sold')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150">Mark as Sold</button>
                                            <button onclick="showBulkModal('unsold')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150">Mark as Unsold</button>
                                            <button onclick="showBulkModal('skip')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150">Skip Players</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 sm:p-6">
                        <!-- Search Bar -->
                        <div class="mb-4">
                            <input type="text" id="playerSearch" placeholder="Search players by name or mobile..." 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>

                        <!-- Players List -->
                        <div class="space-y-3" id="playersList">
                            @forelse($availablePlayers as $key => $player)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-all duration-200 hover:shadow-sm player-card animate-fade-in {{ $key >= 3 ? 'hidden' : '' }}" 
                                 data-player-id="{{ $player->user->id }}"
                                 data-player-name="{{ $player->user->name }}"
                                 data-base-price="{{ $player->base_price }}"
                                 data-league-player-id="{{ $player->id }}">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                    <div class="flex items-center space-x-3 w-full">
                                        <input type="checkbox" class="player-checkbox" value="{{ $player->id }}" onchange="updateBulkActionBtn()">
                                        <div class="flex-1">
                                            <h3 class="font-medium text-gray-900">{{ $player->user->name }}</h3>
                                            <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600 mt-1">
                                                <span>📱 {{ $player->user->mobile }}</span>
                                                <span>💰 Base Price: ₹{{ number_format($player->base_price) }}</span>
                                                @if($player->retention)
                                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">Retention</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <button onclick="selectPlayer('{{ $player->user->id }}', '{{ $player->user->name }}', '{{ $player->base_price }}')"
                                            class="auction-btn bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200 w-full sm:w-auto">
                                        Auction
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8 text-gray-500">
                                <p>No available players for auction</p>
                            </div>
                            @endforelse
                        </div>

                        @if(count($availablePlayers) > 3)
                        <div class="mt-4 text-center" id="showMoreContainer">
                            <button id="showMoreBtn" class="text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
                                Show More Players ({{ count($availablePlayers) - 3 }} remaining)
                            </button>
                        </div>
                        @endif

                        <!-- Pagination -->
                        @if($availablePlayers->hasPages())
                        <div class="mt-6 overflow-x-auto">
                            {{ $availablePlayers->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Auction Form Section -->
            <div class="lg:col-span-1" id="auctionFormSection">
                <div class="bg-white rounded-lg shadow transition-all duration-300 hover:shadow-md">
                    <div class="px-4 py-4 sm:px-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Auction Player</h2>
                    </div>
                    <div class="p-4 sm:p-6">
                        <form id="auctionForm" method="POST" action="{{ route('auction.manual.store', $league->slug) }}">
                            @csrf
                            
                            <!-- Selected Player -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Selected Player</label>
                                <div id="selectedPlayer" class="bg-gray-50 p-3 rounded-lg text-sm text-gray-600 transition-colors duration-200">
                                    No player selected
                                </div>
                                <input type="hidden" name="user_id" id="selectedPlayerId" aria-required="true">
                            </div>

                            <!-- Team Selection -->
                            <div class="mb-4">
                                <label for="league_team_id" class="block text-sm font-medium text-gray-700 mb-2">Select Team</label>
                                <select name="league_team_id" id="league_team_id" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        aria-required="true">
                                    <option value="">Choose a team...</option>
                                    @foreach($leagueTeams as $leagueTeam)
                                    <option value="{{ $leagueTeam->id }}" data-wallet="{{ $leagueTeam->wallet_balance }}">
                                        {{ $leagueTeam->team->name }} (₹{{ number_format($leagueTeam->wallet_balance) }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Team Wallet Info -->
                            <div class="mb-4" id="teamWalletInfo" style="display: none;">
                                <div class="bg-blue-50 p-3 rounded-lg transition-opacity duration-200">
                                    <p class="text-sm text-blue-800">
                                        <strong>Team Wallet:</strong> ₹<span id="teamWallet">0</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Base Price Override Toggle -->
                            <div class="mb-4">
                                <div class="flex items-center">
                                    <input type="checkbox" id="override_base_price" name="override_base_price" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="override_base_price" class="ml-2 block text-sm text-gray-700">
                                        Override Base Price
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Allow amount below base price</p>
                            </div>
                            
                            <!-- Auction Amount -->
                            <div class="mb-6">
                                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Auction Amount</label>
                                <input type="number" name="amount" id="amount" step="0.01" min="0" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       placeholder="Enter auction amount" aria-required="true">
                                <p class="text-xs text-gray-500 mt-1">Minimum base price: ₹<span id="minAmount">0</span></p>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" id="submitBtn" disabled
                                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white py-2 rounded-lg transition-colors duration-200">
                                Complete Auction
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Recent Auctions -->
                <div class="bg-white rounded-lg shadow mt-6 transition-all duration-300 hover:shadow-md">
                    <div class="px-4 py-4 sm:px-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Auctions</h2>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @forelse($recentAuctions as $auction)
                            <div class="border-l-4 border-green-400 pl-4 py-2 transition-all duration-200 hover:pl-6 animate-fade-in">
                                <p class="font-medium text-gray-900">{{ $auction->player->name }}</p>
                                <p class="text-sm text-gray-600">{{ $auction->leagueTeam->team->name }}</p>
                                <p class="text-sm font-semibold text-green-600">₹{{ number_format($auction->amount) }}</p>
                                <p class="text-xs text-gray-500">{{ $auction->created_at->diffForHumans() }}</p>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center py-4">No recent auctions</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Action Modal -->
<div id="bulkActionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 transition-opacity duration-300">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4" id="bulkModalTitle">Bulk Action</h3>
            <form id="bulkActionForm" method="POST" action="{{ route('auction.manual.update-status', $league->slug) }}">
                @csrf
                <div id="bulkModalContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeBulkModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded transition-colors duration-200">Cancel</button>
                    <button type="submit" id="bulkSubmitBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition-colors duration-200">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
<div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in" id="successMessage">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in" id="errorMessage">
    @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
    @endforeach
</div>
@endif

<script>
let selectedPlayers = [];

function selectPlayer(playerId, playerName, basePrice) {
    document.getElementById('selectedPlayerId').value = playerId;
    document.getElementById('selectedPlayer').innerHTML = `
        <strong>${playerName}</strong><br>
        <span class="text-xs">Base Price: ₹${parseFloat(basePrice).toLocaleString()}</span>
    `;
    document.getElementById('minAmount').textContent = parseFloat(basePrice).toLocaleString();
    
    // Set min value only if override is not checked
    if (!document.getElementById('override_base_price').checked) {
        document.getElementById('amount').min = basePrice;
    } else {
        document.getElementById('amount').min = 0;
    }
    
    document.getElementById('amount').value = basePrice;
    
    checkFormValid();
    
    // On mobile screens, scroll to auction form
    if (window.innerWidth < 1024) { // lg breakpoint in Tailwind
        const auctionFormSection = document.getElementById('auctionFormSection');
        if (auctionFormSection) {
            setTimeout(() => {
                auctionFormSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100); // Small delay to allow state to update
        }
    }
}

function toggleSelectAll() {
    const checkboxes = document.querySelectorAll('.player-checkbox');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(cb => {
        cb.checked = !allChecked;
    });
    
    selectAllBtn.textContent = allChecked ? 'Select All' : 'Deselect All';
    updateBulkActionBtn();
}

function updateBulkActionBtn() {
    const checkedBoxes = document.querySelectorAll('.player-checkbox:checked');
    const bulkActionBtn = document.getElementById('bulkActionBtn');
    
    selectedPlayers = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (selectedPlayers.length > 0) {
        bulkActionBtn.disabled = false;
        bulkActionBtn.textContent = `Bulk Actions (${selectedPlayers.length})`;
    } else {
        bulkActionBtn.disabled = true;
        bulkActionBtn.textContent = 'Bulk Actions';
    }
}

function toggleBulkActions() {
    const menu = document.getElementById('bulkActionsMenu');
    menu.classList.toggle('hidden');
    menu.classList.toggle('opacity-0');
}

function showBulkModal(action) {
    const modal = document.getElementById('bulkActionModal');
    const title = document.getElementById('bulkModalTitle');
    const content = document.getElementById('bulkModalContent');
    const form = document.getElementById('bulkActionForm');
    
    // Hide bulk actions menu
    document.getElementById('bulkActionsMenu').classList.add('hidden');
    document.getElementById('bulkActionsMenu').classList.add('opacity-0');
    
    // Clear previous form data
    form.querySelectorAll('input[name="player_ids[]"]').forEach(input => input.remove());
    
    // Add selected player IDs to form
    selectedPlayers.forEach(playerId => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'player_ids[]';
        input.value = playerId;
        form.appendChild(input);
    });
    
    // Add status input
    let statusInput = form.querySelector('input[name="status"]');
    if (!statusInput) {
        statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        form.appendChild(statusInput);
    }
    statusInput.value = action;
    
    // Set modal content based on action
    if (action === 'sold') {
        title.textContent = `Sell ${selectedPlayers.length} Player(s)`;
        content.innerHTML = `
            <p class="mb-4">Mark ${selectedPlayers.length} selected player(s) as sold.</p>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Team</label>
                <select name="league_team_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Choose a team...</option>
                    @foreach($leagueTeams as $leagueTeam)
                    <option value="{{ $leagueTeam->id }}">{{ $leagueTeam->team->name }} (₹{{ number_format($leagueTeam->wallet_balance) }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Auction Amount</label>
                <input type="number" name="amount" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter amount">
            </div>
            <div class="mb-4">
                <div class="flex items-center">
                    <input type="checkbox" id="bulk_override_base_price" name="override_base_price" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="bulk_override_base_price" class="ml-2 block text-sm text-gray-700">
                        Override Base Price
                    </label>
                </div>
                <p class="text-xs text-gray-500 mt-1">Allow amount below base price</p>
            </div>
        `;
        document.getElementById('bulkSubmitBtn').textContent = 'Sell Players';
        document.getElementById('bulkSubmitBtn').className = 'bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition-colors duration-200';
    } else if (action === 'unsold') {
        title.textContent = `Mark ${selectedPlayers.length} Player(s) as Unsold`;
        content.innerHTML = `<p>Mark ${selectedPlayers.length} selected player(s) as unsold. They will not be assigned to any team.</p>`;
        document.getElementById('bulkSubmitBtn').textContent = 'Mark as Unsold';
        document.getElementById('bulkSubmitBtn').className = 'bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition-colors duration-200';
    } else if (action === 'skip') {
        title.textContent = `Skip ${selectedPlayers.length} Player(s)`;
        content.innerHTML = `<p>Skip ${selectedPlayers.length} selected player(s) for now. They will remain available for future auctions.</p>`;
        document.getElementById('bulkSubmitBtn').textContent = 'Skip Players';
        document.getElementById('bulkSubmitBtn').className = 'bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded transition-colors duration-200';
    }
    
    modal.classList.remove('hidden');
}

function closeBulkModal() {
    document.getElementById('bulkActionModal').classList.add('hidden');
}

// Team selection change handler
document.getElementById('league_team_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const walletBalance = selectedOption.getAttribute('data-wallet');
    
    if (walletBalance) {
        document.getElementById('teamWallet').textContent = parseFloat(walletBalance).toLocaleString();
        document.getElementById('teamWalletInfo').style.display = 'block';
    } else {
        document.getElementById('teamWalletInfo').style.display = 'none';
    }
    
    checkFormValid();
});

// Amount input validation
document.getElementById('amount').addEventListener('input', function() {
    const amount = parseFloat(this.value);
    const teamSelect = document.getElementById('league_team_id');
    const selectedOption = teamSelect.options[teamSelect.selectedIndex];
    const walletBalance = parseFloat(selectedOption.getAttribute('data-wallet') || 0);
    const playerId = document.getElementById('selectedPlayerId').value;
    
    if (amount > walletBalance) {
        this.setCustomValidity('Amount exceeds team wallet balance');
    } else {
        this.setCustomValidity('');
    }
    
    checkFormValid();
});

// Base price override toggle handler
document.getElementById('override_base_price').addEventListener('change', function() {
    const basePrice = document.getElementById('minAmount').textContent;
    const amountInput = document.getElementById('amount');
    
    if (this.checked) {
        // Remove base price validation when override is checked
        amountInput.min = 0;
    } else {
        // Restore base price validation when override is unchecked
        amountInput.min = parseFloat(basePrice.replace(/,/g, ''));
        
        // Validate current value against base price
        if (parseFloat(amountInput.value) < parseFloat(basePrice.replace(/,/g, ''))) {
            amountInput.value = basePrice.replace(/,/g, '');
        }
    }
});

function checkFormValid() {
    const playerId = document.getElementById('selectedPlayerId').value;
    const teamId = document.getElementById('league_team_id').value;
    const amount = document.getElementById('amount').value;
    
    const submitBtn = document.getElementById('submitBtn');
    if (playerId && teamId && amount) {
        submitBtn.disabled = false;
    } else {
        submitBtn.disabled = true;
    }
}

// Player search functionality (using event delegation for efficiency)
document.getElementById('playerSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const playerCards = document.querySelectorAll('.player-card');
    const showMoreBtn = document.getElementById('showMoreBtn');
    const showMoreContainer = document.getElementById('showMoreContainer');
    
    // If search is active, show all matching players
    if (searchTerm.length > 0) {
        playerCards.forEach(card => {
            const playerName = card.getAttribute('data-player-name').toLowerCase();
            const playerId = card.getAttribute('data-player-id');
            const showMatch = playerName.includes(searchTerm);
            card.style.display = showMatch ? 'block' : 'none';
            if (showMatch) {
                card.classList.remove('hidden');
            }
        });
        // Hide "Show More" button during search
        if (showMoreContainer) showMoreContainer.style.display = 'none';
    } else {
        // If search is cleared, go back to showing only first 3
        playerCards.forEach((card, index) => {
            if (index < 3) {
                card.style.display = 'block';
                card.classList.remove('hidden');
            } else {
                card.style.display = 'none';
                card.classList.add('hidden');
            }
        });
        // Show "Show More" button again
        if (showMoreContainer) showMoreContainer.style.display = 'block';
    }
});

// "Show More" button functionality
if (document.getElementById('showMoreBtn')) {
    document.getElementById('showMoreBtn').addEventListener('click', function() {
        const hiddenPlayers = document.querySelectorAll('.player-card.hidden');
        hiddenPlayers.forEach(player => {
            player.classList.remove('hidden');
            player.style.display = 'block';
        });
        document.getElementById('showMoreContainer').style.display = 'none';
    });
}

// Auction button click handler for mobile - scroll to auction form
document.querySelectorAll('.auction-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // On mobile screens, scroll to auction form
        if (window.innerWidth < 1024) { // lg breakpoint in Tailwind
            const auctionForm = document.getElementById('auctionForm');
            if (auctionForm) {
                setTimeout(() => {
                    auctionForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100); // Small delay to allow state to update
            }
        }
    });
});

// Close bulk actions menu when clicking outside
document.addEventListener('click', function(e) {
    const bulkActionBtn = document.getElementById('bulkActionBtn');
    const bulkActionsMenu = document.getElementById('bulkActionsMenu');
    
    if (!bulkActionBtn.contains(e.target) && !bulkActionsMenu.contains(e.target)) {
        bulkActionsMenu.classList.add('hidden');
        bulkActionsMenu.classList.add('opacity-0');
    }
});

// Auto-hide messages
setTimeout(() => {
    const successMsg = document.getElementById('successMessage');
    const errorMsg = document.getElementById('errorMessage');
    if (successMsg) successMsg.style.display = 'none';
    if (errorMsg) errorMsg.style.display = 'none';
}, 5000);
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
@keyframes slide-in {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}
</style>
@endsection