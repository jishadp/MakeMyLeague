@extends('layouts.app')

@section('title', 'Auction - ' . $league->name)

@section('content')
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $league->name }} Auction</h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">Manage player auctions and team assignments</p>
            </div>
            <div class="flex flex-wrap gap-3 w-full sm:w-auto">
                <a href="{{ route('leagues.show', $league) }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 transition duration-200 text-sm sm:text-base">Back to League</a>
                @if(!$league->auction_started)
                    <a href="{{ route('auction.setup', $league) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition duration-200 text-sm sm:text-base">Setup Auction</a>
                @else
                    <div class="flex gap-2">
                        <form action="{{ route('auction.reset', $league) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Reset auction? This will clear all bids and restore team purses.')" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200 text-sm sm:text-base">Reset Auction</button>
                        </form>
                        <form action="{{ route('auction.reset', $league) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="restart" value="1">
                            <button type="submit" onclick="return confirm('Restart auction? This will clear all bids and take you back to the setup page.')" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition duration-200 text-sm sm:text-base">Restart Auction</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 flex items-start">
            <div class="flex-shrink-0 mt-0.5">
                <svg class="h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-800 font-medium">{{ session('success') }}</p>
            </div>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-green-50 text-green-600 rounded-lg p-1.5 inline-flex h-8 w-8 hover:bg-green-100" onclick="this.parentElement.style.display='none'">
                <span class="sr-only">Dismiss</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        @endif

        @if(!$league->auction_started)
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 sm:p-6 mb-8 text-center sm:text-left">
                <p class="text-yellow-800 text-sm sm:text-base">Auction not started yet. Please configure auction settings first.</p>
            </div>
        @else
            <!-- Team Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @forelse($leagueTeams as $team)
                    <div class="bg-white rounded-xl border border-gray-200 shadow-md hover:shadow-lg transition-shadow duration-300 p-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $team->pivot->name }}</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Purse Balance:</span>
                                <span class="font-medium text-green-600">₹{{ number_format($team->pivot->purse_balance) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Initial Purse:</span>
                                <span class="text-gray-900">₹{{ number_format($team->pivot->initial_purse_balance) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Players Bought:</span>
                                <span class="text-gray-900">{{ $league->leaguePlayers()->wherePivot('league_team_id', $team->pivot->id)->wherePivot('auction_status', 'sold')->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Remaining Slots:</span>
                                <span class="text-gray-900">{{ $league->min_players_needed - $league->leaguePlayers()->wherePivot('league_team_id', $team->pivot->id)->wherePivot('auction_status', 'sold')->count() }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-red-50 border border-red-200 rounded-xl p-4 sm:p-6 text-center">
                        <p class="text-red-800 text-sm sm:text-base">No teams created yet. Please add teams to the league first.</p>
                    </div>
                @endforelse
            </div>

            <!-- Auction Form -->
            @if($leagueTeams->count() > 0)
                <div class="bg-white rounded-xl border border-gray-200 shadow-md p-4 sm:p-6 mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Auction Player</h2>
                    
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li class="text-red-600 text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('auction.bid', $league) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6" id="auctionForm">
                        @csrf
                        
                        <div>
                            <label for="player_id" class="block text-sm font-medium text-gray-700 mb-2">Select Player</label>
                            <select name="player_id" id="player_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">Choose Player</option>
                                @forelse($availablePlayers as $player)
                                    <option value="{{ $player->id }}" {{ old('player_id') == $player->id ? 'selected' : '' }}>{{ $player->name }} ({{ $player->position }})</option>
                                @empty
                                    <option value="" disabled>No available players in this league</option>
                                @endforelse
                            </select>
                            @if($availablePlayers->isEmpty())
                                <p class="mt-1 text-xs text-amber-600">You need to add players to this league first.</p>
                            @endif
                            @error('player_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="league_team_id" class="block text-sm font-medium text-gray-700 mb-2">Select Team</label>
                            <select name="league_team_id" id="league_team_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">Choose Team</option>
                                @foreach($leagueTeams as $team)
                                    <option value="{{ $team->pivot->id }}" {{ old('league_team_id') == $team->pivot->id ? 'selected' : '' }}>
                                        {{ $team->pivot->name }} (₹{{ number_format($team->pivot->purse_balance) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('league_team_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="bid_amount" class="block text-sm font-medium text-gray-700 mb-2">Bid Amount</label>
                            <input type="number" name="bid_amount" id="bid_amount" 
                                   value="{{ old('bid_amount') }}" min="{{ $league->min_bid_amount }}" step="100"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <p class="text-xs text-gray-500 mt-1">Min: ₹{{ number_format($league->min_bid_amount) }}</p>
                            @error('bid_amount')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2 invisible">Action</label>
                            <button type="submit" name="action" value="sell" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200 text-sm" 
                                    onclick="return validateSoldAction()">Sold</button>
                            <button type="submit" name="action" value="unsold" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200 text-sm"
                                    onclick="return confirmAction('unsold')">Unsold</button>
                            <button type="submit" name="action" value="skip" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition duration-200 text-sm"
                                    onclick="return confirmAction('skip')">Skip</button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Auction Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="bg-green-50 border border-green-200 rounded-xl shadow-md p-4 sm:p-6 text-center transition-shadow duration-300 hover:shadow-lg">
                    <div class="text-3xl font-bold text-green-600">{{ $stats['sold'] }}</div>
                    <div class="text-green-800 font-medium mt-2">Players Sold</div>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-xl shadow-md p-4 sm:p-6 text-center transition-shadow duration-300 hover:shadow-lg">
                    <div class="text-3xl font-bold text-red-600">{{ $stats['unsold'] }}</div>
                    <div class="text-red-800 font-medium mt-2">Players Unsold</div>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl shadow-md p-4 sm:p-6 text-center transition-shadow duration-300 hover:shadow-lg">
                    <div class="text-3xl font-bold text-yellow-600">{{ $stats['skip'] }}</div>
                    <div class="text-yellow-800 font-medium mt-2">Players Skipped</div>
                </div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('auctionForm');
            const playerSelect = document.getElementById('player_id');
            const teamSelect = document.getElementById('league_team_id');
            const bidAmountInput = document.getElementById('bid_amount');
            const actionButtons = document.querySelectorAll('button[name="action"]');
            
            if (form) {
                // Handle action buttons
                actionButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        // Store the action value
                        const action = this.value;
                        
                        // Check if player is selected
                        if (!playerSelect.value) {
                            e.preventDefault();
                            alert('Please select a player');
                            return false;
                        }
                        
                        // For sell action, validate team and bid amount
                        if (action === 'sell') {
                            if (!teamSelect.value) {
                                e.preventDefault();
                                alert('Please select a team for "Sold" action');
                                return false;
                            }
                            
                            if (!bidAmountInput.value || bidAmountInput.value < {{ $league->min_bid_amount }}) {
                                e.preventDefault();
                                alert('Please enter a valid bid amount (minimum ₹{{ number_format($league->min_bid_amount) }})');
                                return false;
                            }
                        }
                        
                        // For unsold and skip actions, team and bid amount are not required
                        if (action === 'unsold' || action === 'skip') {
                            // Set a default value for bid_amount to pass validation
                            bidAmountInput.value = {{ $league->min_bid_amount }};
                        }
                    });
                });
                
                // Show team balance next to team name
                teamSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption.value) {
                        const teamBalance = selectedOption.textContent.match(/₹([\d,]+)/);
                        if (teamBalance && teamBalance[1]) {
                            // Optionally, you could show this information somewhere in the UI
                            console.log('Selected team balance:', teamBalance[1]);
                        }
                    }
                });
            }
        });
        
        // Custom confirmation dialog for auction actions
        function confirmAction(action) {
            // Create and append modal if it doesn't exist
            if (!document.getElementById('confirmationModal')) {
                const modal = document.createElement('div');
                modal.id = 'confirmationModal';
                modal.className = 'fixed inset-0 flex items-center justify-center z-50 hidden';
                modal.innerHTML = `
                    <div class="fixed inset-0 bg-black bg-opacity-50"></div>
                    <div class="bg-white rounded-xl shadow-xl p-6 max-w-md w-full mx-4 relative z-10">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4" id="confirmTitle">Confirm Action</h3>
                        <p class="text-gray-600 mb-6" id="confirmMessage">Are you sure you want to proceed?</p>
                        <div class="flex justify-end space-x-3">
                            <button id="cancelBtn" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-800 transition-colors">
                                Cancel
                            </button>
                            <button id="confirmBtn" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-lg text-white transition-colors">
                                Confirm
                            </button>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
                
                // Close modal on cancel
                document.getElementById('cancelBtn').addEventListener('click', function() {
                    document.getElementById('confirmationModal').classList.add('hidden');
                });
                
                // Close when clicking outside
                modal.querySelector('.fixed.inset-0').addEventListener('click', function() {
                    document.getElementById('confirmationModal').classList.add('hidden');
                });
            }
            
            // Set modal content based on action
            const modal = document.getElementById('confirmationModal');
            const title = document.getElementById('confirmTitle');
            const message = document.getElementById('confirmMessage');
            const confirmBtn = document.getElementById('confirmBtn');
            const form = document.getElementById('auctionForm');
            const playerSelect = document.getElementById('player_id');
            const teamSelect = document.getElementById('league_team_id');
            const bidAmountInput = document.getElementById('bid_amount');
            
            // Get player name
            const playerName = playerSelect.options[playerSelect.selectedIndex]?.text || 'the selected player';
            
            // Configure modal based on action
            if (action === 'sell') {
                // For sell action, check if team and bid amount are selected
                if (!teamSelect.value) {
                    alert('Please select a team for "Sold" action');
                    return false;
                }
                
                if (!bidAmountInput.value || bidAmountInput.value < {{ $league->min_bid_amount }}) {
                    alert('Please enter a valid bid amount (minimum ₹{{ number_format($league->min_bid_amount) }})');
                    return false;
                }
                
                const teamName = teamSelect.options[teamSelect.selectedIndex].text;
                const bidAmount = bidAmountInput.value;
                
                title.textContent = 'Confirm Sale';
                title.className = 'text-xl font-semibold text-green-700 mb-4';
                message.innerHTML = `
                    <p class="mb-3">Are you sure you want to mark <span class="font-semibold">${playerName}</span> as sold to <span class="font-semibold">${teamName}</span> for <span class="font-semibold text-green-600">₹${Number(bidAmount).toLocaleString('en-IN')}</span>?</p>
                    <p class="text-sm text-gray-500">This action will update the team's purse balance.</p>
                `;
                confirmBtn.className = 'px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-white transition-colors';
                confirmBtn.textContent = 'Mark as Sold';
            } else if (action === 'unsold') {
                title.textContent = 'Mark as Unsold';
                title.className = 'text-xl font-semibold text-red-700 mb-4';
                message.innerHTML = `
                    <p class="mb-3">Are you sure you want to mark <span class="font-semibold">${playerName}</span> as unsold?</p>
                    <p class="text-sm text-gray-500">The player will be marked as unsold in this auction.</p>
                `;
                confirmBtn.className = 'px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-white transition-colors';
                confirmBtn.textContent = 'Mark Unsold';
            } else if (action === 'skip') {
                title.textContent = 'Skip Player';
                title.className = 'text-xl font-semibold text-yellow-700 mb-4';
                message.innerHTML = `
                    <p class="mb-3">Are you sure you want to skip <span class="font-semibold">${playerName}</span> for now?</p>
                    <p class="text-sm text-gray-500">The player will remain available for future bidding.</p>
                `;
                confirmBtn.className = 'px-4 py-2 bg-yellow-600 hover:bg-yellow-700 rounded-lg text-white transition-colors';
                confirmBtn.textContent = 'Skip Player';
            }
            
            // Set up confirm button action
            confirmBtn.onclick = function() {
                // For unsold and skip actions, set a default bid amount to pass validation
                if (action === 'unsold' || action === 'skip') {
                    bidAmountInput.value = {{ $league->min_bid_amount }};
                }
                
                // Hide modal
                modal.classList.add('hidden');
                
                // Programmatically trigger the form submission with the correct action
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'action';
                hiddenInput.value = action;
                form.appendChild(hiddenInput);
                
                // Submit the form
                form.submit();
            };
            
            // Show modal
            modal.classList.remove('hidden');
            
            // Return false to prevent default form submission
            return false;
        }
        
        // Function to validate the Sold action without showing confirmation
        function validateSoldAction() {
            const form = document.getElementById('auctionForm');
            const playerSelect = document.getElementById('player_id');
            const teamSelect = document.getElementById('league_team_id');
            const bidAmountInput = document.getElementById('bid_amount');
            
            // Check if player is selected
            if (!playerSelect.value) {
                alert('Please select a player');
                return false;
            }
            
            // Check if team is selected
            if (!teamSelect.value) {
                alert('Please select a team for "Sold" action');
                return false;
            }
            
            // Check if bid amount is valid
            if (!bidAmountInput.value || bidAmountInput.value < {{ $league->min_bid_amount }}) {
                alert('Please enter a valid bid amount (minimum ₹{{ number_format($league->min_bid_amount) }})');
                return false;
            }
            
            // All validations passed, proceed with form submission
            return true;
        }
    </script>
@endsection