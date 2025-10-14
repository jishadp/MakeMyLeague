// Minimal Auction JavaScript - Static Demo Version

// Function to update bid increments based on current bid amount
function updateBidIncrements(currentBid) {
    // Convert to number if it's a string
    currentBid = Number(currentBid);
    
    // Get league bid increment settings if they exist
    let incrementType = 'predefined'; // Default to predefined
    let customIncrement = 0;
    let predefinedIncrements = [];
    
    // Safely check if these elements exist before trying to access their values
    const bidIncrementTypeEl = document.getElementById('bid-increment-type');
    const customBidIncrementEl = document.getElementById('custom-bid-increment');
    const predefinedIncrementsEl = document.getElementById('predefined-increments');
    
    if (bidIncrementTypeEl) {
        incrementType = bidIncrementTypeEl.value;
    }
    
    if (customBidIncrementEl) {
        customIncrement = Number(customBidIncrementEl.value);
    }
    
    try {
        if (predefinedIncrementsEl && predefinedIncrementsEl.value) {
            predefinedIncrements = JSON.parse(predefinedIncrementsEl.value);
        }
    } catch (e) {
        console.error('Error parsing predefined increments:', e);
    }
    
    // Determine the base increment based on current bid and league settings
    let baseIncrement = 10; // Default value
    
    // If using custom increment from league settings
    if (incrementType === 'custom' && customIncrement > 0) {
        baseIncrement = customIncrement;
    }
    // If using predefined increments based on bid ranges
    else if (incrementType === 'predefined') {
        // Default increment rules if no specific predefined increments
        if (currentBid < 100) {
            baseIncrement = 10;
        } else if (currentBid < 500) {
            baseIncrement = 25;
        } else if (currentBid < 1000) {
            baseIncrement = 50;
        } else if (currentBid < 5000) {
            baseIncrement = 100;
        } else {
            baseIncrement = 500;
        }
        
        // Calculate a percentage-based increment (approximately 10% of current bid)
        const percentIncrement = Math.ceil(currentBid * 0.1 / 10) * 10; // Round to nearest 10
        
        // Use percentage-based increment if it's larger than the default tier
        if (percentIncrement > baseIncrement) {
            baseIncrement = percentIncrement;
        }
    }
    
    // Calculate rounded increment that makes sense (e.g., 33 for a bid of 1695)
    // For bids over 1000, round to nearest 10
    if (currentBid > 1000) {
        baseIncrement = Math.max(10, Math.round(currentBid * 0.02 / 10) * 10);
    }
    // For bids over 5000, round to nearest 50
    if (currentBid > 5000) {
        baseIncrement = Math.max(50, Math.round(currentBid * 0.02 / 50) * 50);
    }
    // For bids over 10000, round to nearest 100
    if (currentBid > 10000) {
        baseIncrement = Math.max(100, Math.round(currentBid * 0.02 / 100) * 100);
    }
    
    // Apply the simple 1x, 2x, 4x rule
    const secondIncrement = Math.round(baseIncrement * 2);
    const thirdIncrement = Math.round(baseIncrement * 4);
    
    // Update the bid buttons if they exist
    const callBidButtons = $('.callBid');
    if (callBidButtons.length > 0) {
        callBidButtons.eq(0).attr('increment', baseIncrement).find('p').text('+ ₹' + baseIncrement);
        
        if (callBidButtons.length > 1) {
            callBidButtons.eq(1).attr('increment', secondIncrement).find('p').text('+ ₹' + secondIncrement);
        }
        
        if (callBidButtons.length > 2) {
            callBidButtons.eq(2).attr('increment', thirdIncrement).find('p').text('+ ₹' + thirdIncrement);
        }
    }
}

// Function to handle image loading errors
function handleImageError(img) {
    if (img && img.nextElementSibling) {
        img.style.display = 'none';
        img.nextElementSibling.style.display = 'flex';
    }
}

// Function to show messages
function showMessage(message, type = 'info') {
    const messageContainer = document.getElementById('messageContainer');
    if (!messageContainer) return;
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `p-4 rounded-lg shadow-lg mb-4 transform transition-all duration-300 translate-x-full`;
    
    // Set colors based on type
    switch(type) {
        case 'success':
            messageDiv.className += ' bg-green-500 text-white';
            break;
        case 'error':
            messageDiv.className += ' bg-red-500 text-white';
            break;
        case 'warning':
            messageDiv.className += ' bg-yellow-500 text-white';
            break;
        default:
            messageDiv.className += ' bg-blue-500 text-white';
    }
    
    messageDiv.innerHTML = `
        <div class="flex items-center justify-between">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    messageContainer.appendChild(messageDiv);
    
    // Animate in
    setTimeout(() => {
        messageDiv.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        messageDiv.classList.add('translate-x-full');
        setTimeout(() => {
            if (messageDiv.parentElement) {
                messageDiv.remove();
            }
        }, 300);
    }, 5000);
}

// Simple function to start bidding and show bidding section
function startBidding(userId, leaguePlayerId, playerName, basePrice, position, leagueId, startBidAction) {
    console.log('Starting bidding for player:', {
        userId: userId,
        leaguePlayerId: leaguePlayerId,
        playerName: playerName,
        basePrice: basePrice,
        position: position,
        leagueId: leagueId,
        startBidAction: startBidAction
    });
    
    // Update bidding player info
    updateBiddingPlayer(playerName, position, basePrice);
    
    // Initialize bid increments based on base price
    updateBidIncrements(basePrice);
    
    // Important: Update the callBid buttons with correct data
    $('.callBid').each(function() {
        $(this).attr('player-id', userId);
        $(this).attr('league-player-id', leaguePlayerId);
        $(this).attr('base-price', basePrice);
        $(this).attr('league-id', leagueId);
    });
    
    // Update markSold and markUnSold buttons
    $('.markSold').attr('league-player-id', leaguePlayerId);
    $('.markUnSold').attr('league-player-id', leaguePlayerId);
    
    // Critical: Send AJAX request to set this player's status to 'auctioning' in the database
    if (startBidAction && leaguePlayerId && leagueId) {
        $.ajax({
            url: startBidAction,
            type: "post",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {
                league_id: leagueId,
                player_id: userId,
                league_player_id: leaguePlayerId
            },
            success: function(response) {
                console.log("Player auction status updated:", response);
                if (!response.success) {
                    showMessage('Error: ' + response.message, 'error');
                }
            },
            error: function(xhr) {
                console.error("Error setting player auction status:", xhr.responseText);
                try {
                    var response = JSON.parse(xhr.responseText);
                    showMessage('Error: ' + (response.message || 'Could not start auction'), 'error');
                } catch (e) {
                    showMessage('Error starting auction. Please try again.', 'error');
                }
            }
        });
    } else {
        console.warn("Missing data required to start auction:", { startBidAction, leaguePlayerId, leagueId });
    }
    
    // Show the bidding section
    const bidMain = document.querySelector('.bidMain');
    if (bidMain) {
        bidMain.classList.remove('hidden');
        
        // Better mobile scroll handling
        setTimeout(() => {
            const isMobile = window.innerWidth < 768;
            if (isMobile) {
                // On mobile, scroll to show the top of the bidding section with more offset
                const rect = bidMain.getBoundingClientRect();
                const offsetTop = window.pageYOffset + rect.top - 60; // 60px offset from top to clear header
                window.scrollTo({ top: offsetTop, behavior: 'smooth' });
            } else {
                // On desktop, use standard scrollIntoView
                bidMain.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 100);
    }
    
    // Hide available players on mobile
    const availableSection = document.getElementById('availablePlayersSection');
    if (availableSection && window.innerWidth < 1024) {
        availableSection.classList.add('hidden');
    }
    
    // Show success message
    showMessage(playerName + ' selected for bidding!', 'success');
    
    // Store current bidding player info for later use
    window.currentBiddingPlayer = {
        userId: userId,
        leaguePlayerId: leaguePlayerId,
        playerName: playerName,
        basePrice: basePrice,
        position: position,
        leagueId: leagueId,
        startBidAction: startBidAction
    };
}

// Function to update the bidding player information
function updateBiddingPlayer(playerName, position, basePrice) {
    // Update player name
    const playerNameElement = document.querySelector('.playerName');
    if (playerNameElement) {
        playerNameElement.textContent = playerName;
    }
    
    // Update position
    const positionElement = document.querySelector('.position');
    if (positionElement) {
        positionElement.textContent = position;
    }
    
    // Update base price
    const basePriceElement = document.querySelector('.basePrice');
    if (basePriceElement) {
        basePriceElement.textContent = basePrice;
    }
    
    // Update current bid to base price initially
    const currentBidElement = document.querySelector('.currentBid');
    if (currentBidElement) {
        currentBidElement.textContent = basePrice;
    }
    
    // Update bid status
    const bidStatusElement = document.querySelector('.bidStatus');
    if (bidStatusElement) {
        bidStatusElement.textContent = 'Starting bid';
    }
    
    // Clear team info
    const bidTeamElement = document.querySelector('.bidTeam');
    if (bidTeamElement) {
        bidTeamElement.textContent = 'No bids yet';
    }
}

$(document).ready(function(){
    // Simple search functionality for available players
    const searchInput = document.getElementById('playerSearch');
    const visibleCountElement = document.getElementById('visiblePlayersCount');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const playerCards = document.querySelectorAll('.player-card');
            let visibleCount = 0;
            
            playerCards.forEach(function(card) {
                const playerName = card.querySelector('h3').textContent.toLowerCase();
                const playerMobile = card.querySelector('.text-gray-600').textContent.toLowerCase();
                const playerPosition = card.querySelector('.bg-blue-100').textContent.toLowerCase();
                
                if (searchTerm === '') {
                    // If no search term, show only first 3 players
                    const playerIndex = parseInt(card.getAttribute('data-player-index'));
                    if (playerIndex < 3) {
                        card.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        card.classList.add('hidden');
                    }
                } else {
                    // If searching, show matching players
                    if (playerName.includes(searchTerm) || playerMobile.includes(searchTerm) || playerPosition.includes(searchTerm)) {
                        card.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        card.classList.add('hidden');
                    }
                }
            });
            
            // Update visible players count
            if (visibleCountElement) {
                visibleCountElement.textContent = visibleCount + ' on this page';
            }
        });
    }
    
    // Simple role filter functionality
    const roleFilter = document.getElementById('roleFilter');
    if (roleFilter) {
        roleFilter.addEventListener('change', function() {
            const selectedRole = this.value.toLowerCase();
            const playerCards = document.querySelectorAll('.player-card');
            let visibleCount = 0;
            
            playerCards.forEach(function(card) {
                const playerPosition = card.querySelector('.bg-blue-100').textContent.toLowerCase();
                
                if (selectedRole === '' || playerPosition.includes(selectedRole)) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });
            
            // Update visible players count
            if (visibleCountElement) {
                visibleCountElement.textContent = visibleCount + ' on this page';
            }
        });
    }
    
    // Simple price filter functionality
    const priceFilter = document.getElementById('priceFilter');
    if (priceFilter) {
        priceFilter.addEventListener('change', function() {
            const selectedPrice = this.value;
            const playerCards = document.querySelectorAll('.player-card');
            let visibleCount = 0;
            
            playerCards.forEach(function(card) {
                const basePrice = parseInt(card.querySelector('.text-2xl').textContent.replace('₹', ''));
                let showPlayer = false;
                
                if (selectedPrice === '') {
                    showPlayer = true;
                } else if (selectedPrice === '0-100') {
                    showPlayer = basePrice >= 0 && basePrice <= 100;
                } else if (selectedPrice === '101-200') {
                    showPlayer = basePrice >= 101 && basePrice <= 200;
                } else if (selectedPrice === '201-500') {
                    showPlayer = basePrice >= 201 && basePrice <= 500;
                } else if (selectedPrice === '500+') {
                    showPlayer = basePrice > 500;
                }
                
                if (showPlayer) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });
            
            // Update visible players count
            if (visibleCountElement) {
                visibleCountElement.textContent = visibleCount + ' on this page';
            }
        });
    }
    
    $('.startAuction').click(function(){
        $(".availPlayers").addClass('hidden');
        $("#availablePlayersSection").addClass('hidden');
        $(".bidMain").removeClass('hidden');
        
        // Better mobile scroll handling
        setTimeout(() => {
            const isMobile = window.innerWidth < 768;
            var scrollPos = $("#biddingSection").offset().top;
            
            if (isMobile) {
                // On mobile, use larger offset to clear header and smooth scrolling
                scrollPos = scrollPos - 60;
                $('html, body').animate({ scrollTop: scrollPos }, 300);
            } else {
                // On desktop, use original offset
                scrollPos = scrollPos - 30;
                $(window).scrollTop(scrollPos);
            }
        }, 100);

        var actionStartAction = $(this).attr('start-bid-action');
        var playerId = $(this).attr('player-id');
        var leagueId = $(this).attr('league-id');
        var leaguePlayerId = $(this).attr('league-player-id');

        $.ajax({
            url: actionStartAction,   // Laravel route
            type: "post",
            data: {
                league_id: leagueId,
                player_id: playerId,
                league_player_id: leaguePlayerId
            },
            success: function (response) {
                $('.markSold').attr('league-player-id',leaguePlayerId);
                console.log("Bidding started and broadcasted:", response);
            },
            error: function (xhr) {
                console.error("Error starting bidding:", xhr.responseText);
            }
        });
    });

    $('.callBid').click(function(){
        // Get this button element
        var $this = $(this);
        
        // Get attributes
        var leagueId = $this.attr('league-id');
        var playerId = $this.attr('player-id');
        var basePrice = $this.attr('base-price');
        var increment = $this.attr('increment');
        var leaguePlayerId = $this.attr('league-player-id');
        var callBidAction = $this.closest('.grid').attr('call-bid-action');
        var token = $this.closest('.grid').attr('token');
        
        // Log data for debugging
        console.log('Bid data:', {
            leagueId: leagueId,
            playerId: playerId,
            basePrice: basePrice,
            increment: increment,
            leaguePlayerId: leaguePlayerId
        });
        
        // Check for missing data
        if (!leaguePlayerId || leaguePlayerId === '' || !playerId || playerId === '') {
            showMessage('Error: Player data missing. Please select a player first.', 'error');
            return;
        }
        
        // Prevent double-clicking by adding a processing class
        if ($this.hasClass('processing')) {
            return false;
        }
        
        // Disable all bid buttons while processing to prevent race conditions
        $('.callBid').prop('disabled', true).addClass('opacity-75');
        $this.addClass('processing');

        $.ajax({
            url: callBidAction,   // Laravel route
            type: "post",
            headers: {'X-CSRF-TOKEN':token},
            data: {
                league_id: leagueId,
                player_id: playerId,
                base_price: basePrice,
                increment: increment,
                league_player_id: leaguePlayerId,
            },
            success: function (response) {
                console.log("Bid placed:", response);
                // Add a small delay before re-enabling buttons to prevent rapid clicks
                setTimeout(function() {
                    // Re-enable all bid buttons
                    $('.callBid').prop('disabled', false).removeClass('opacity-75');
                    $this.removeClass('processing');
                }, 500);
                
                // Update base price for next bid if successful
                if (response.success && response.new_bid) {
                    // Update all bid buttons with the new base price
                    $('.callBid').each(function() {
                        $(this).attr('base-price', response.new_bid);
                    });
                    
                    // Update the bid increment rule based on the new bid amount and league settings
                    // Update any league settings if provided in the response
                    if (response.bid_increment_type) {
                        document.getElementById('bid-increment-type').value = response.bid_increment_type;
                    }
                    if (response.custom_bid_increment) {
                        document.getElementById('custom-bid-increment').value = response.custom_bid_increment;
                    }
                    if (response.predefined_increments) {
                        document.getElementById('predefined-increments').value = JSON.stringify(response.predefined_increments);
                    }
                    
                    // Update bid increments based on new bid amount and settings
                    updateBidIncrements(response.new_bid);
                    
                    // Update current bid display
                    $('.currentBid').text(response.new_bid);
                    
                    // Update team info if available
                    if (response.call_team_id) {
                        $('.markSold').attr('call-team-id', response.call_team_id);
                    }
                    
                    // Refresh Livewire components - but safely
                    try {
                        if (window.Livewire) {
                            // Use a single timeout to avoid multiple requests
                            clearTimeout(window.livewireRefreshTimeout);
                            window.livewireRefreshTimeout = setTimeout(function() {
                                try {
                                    // Use the correct Livewire dispatch method based on version
                                    if (typeof Livewire.dispatch === 'function') {
                                        // Livewire 3.x
                                        Livewire.dispatch('refreshBids');
                                    } else if (typeof Livewire.emit === 'function') {
                                        // Livewire 2.x
                                        Livewire.emit('refreshBids');
                                    } else {
                                        console.warn("Livewire dispatch methods not available");
                                    }
                                    
                                    // Wait a moment before refreshing teams to avoid simultaneous requests
                                    setTimeout(function() {
                                        try {
                                            if (typeof Livewire.dispatch === 'function') {
                                                // Livewire 3.x
                                                Livewire.dispatch('refreshTeams');
                                            } else if (typeof Livewire.emit === 'function') {
                                                // Livewire 2.x
                                                Livewire.emit('refreshTeams');
                                            }
                                        } catch (e) {
                                            console.warn("Error refreshing teams:", e);
                                        }
                                    }, 500);
                                } catch (e) {
                                    console.warn("Error refreshing bids:", e);
                                }
                            }, 1000);
                        }
                    } catch (e) {
                        console.warn("Error with Livewire refresh:", e);
                    }
                    
                    showMessage('Bid placed: ₹' + response.new_bid, 'success');
                } else if (!response.success) {
                    showMessage('Error: ' + (response.message || 'Failed to place bid'), 'error');
                }
            },
            error: function(xhr) {
                console.error("Error placing bid:", xhr.responseText);
                // Add a small delay before re-enabling buttons
                setTimeout(function() {
                    // Re-enable all bid buttons
                    $('.callBid').prop('disabled', false).removeClass('opacity-75');
                    $this.removeClass('processing');
                }, 500);
                
                // Try to parse and show specific error message
                try {
                    var response = JSON.parse(xhr.responseText);
                    showMessage('Error: ' + (response.message || 'Failed to place bid'), 'error');
                } catch (e) {
                    showMessage('Error placing bid. Server issue or player status changed.', 'error');
                }
            }
        });
    });

    $('.markSold').click(function(){
        var token = $(this).closest('.mb-6').attr('token');
        var markSoldAction = $(this).closest('.mb-6').attr('mark-sold-action');
        var leaguePlayerId = $(this).attr('league-player-id');
        var callTeamId = $(this).attr('call-team-id');
        
        // Get the current bid information
        var currentBid = $('.currentBid').text();
        var bidTeam = $('.bidTeam').text();
        var playerName = $('.playerName').text().trim();
        
        // Create the confirmation popup
        var confirmationPopup = document.createElement('div');
        confirmationPopup.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        confirmationPopup.id = 'soldConfirmationPopup';
        
        // Check if user is organizer or admin
        var isOrganizerOrAdmin = document.getElementById('is-organizer-or-admin') ? 
                               document.getElementById('is-organizer-or-admin').value === 'true' : false;
        
        // Create popup content with override option for organizers/admins
        var popupContent = `
            <div class="bg-white rounded-xl shadow-2xl p-4 sm:p-6 max-w-md w-full mx-2 sm:mx-4 text-black">
                <div class="text-center mb-4">
                    <h3 class="text-xl font-bold text-black">Confirm Player Sale</h3>
                </div>
                <div class="mb-6">
                    <p class="text-black mb-4">Are you sure you want to mark <span class="font-bold">${playerName}</span> as sold?</p>
                    <div class="bg-gray-100 rounded-lg p-4 mb-4">
                        <div class="flex justify-between mb-2">
                            <span class="text-black">Current Bid:</span>
                            <span class="font-bold text-green-600">₹${currentBid}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-black">Team:</span>
                            <span class="font-bold text-black">${bidTeam}</span>
                        </div>
                    </div>
                    ${isOrganizerOrAdmin ? `
                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <label class="flex items-center text-sm text-black mb-2">
                            <input type="checkbox" id="override-team" class="mr-2 h-5 w-5 text-blue-600">
                            <span class="text-base">Override team selection</span>
                        </label>
                        <div id="override-team-section" class="hidden mb-4">
                            <select id="override-team-select" class="w-full border border-gray-300 rounded-lg px-3 py-3 text-base focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select team...</option>
                                <!-- Teams will be loaded dynamically -->
                            </select>
                        </div>
                        <label class="flex items-center text-sm text-black mb-2">
                            <input type="checkbox" id="override-amount" class="mr-2 h-5 w-5 text-blue-600">
                            <span class="text-base">Override bid amount</span>
                        </label>
                        <div id="override-amount-section" class="hidden">
                            <input type="number" id="override-amount-input" class="w-full border border-gray-300 rounded-lg px-3 py-3 text-base focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter bid amount">
                        </div>
                    </div>
                    ` : ''}
                </div>
                <div class="flex justify-end space-x-3">
                    <button id="cancelSoldBtn" class="px-4 py-3 bg-gray-200 hover:bg-gray-300 text-black rounded-lg transition-colors text-base font-medium">
                        Cancel
                    </button>
                    <button id="confirmSoldBtn" class="px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors text-base font-medium">
                        Confirm Sale
                    </button>
                </div>
            </div>
        `;
        
        confirmationPopup.innerHTML = popupContent;
        document.body.appendChild(confirmationPopup);
        
        // If organizer or admin, load teams for override
        if (isOrganizerOrAdmin) {
            // Show/hide override team section
            document.getElementById('override-team').addEventListener('change', function() {
                document.getElementById('override-team-section').classList.toggle('hidden', !this.checked);
                
                // Load teams if checked and not already loaded
                if (this.checked && document.getElementById('override-team-select').options.length <= 1) {
                    // Get league ID
                    var leagueId = document.getElementById('league-id').value;
                    
                    // Fetch teams
                    $.ajax({
                        url: '/api/leagues/' + leagueId + '/teams',
                        type: 'GET',
                        success: function(response) {
                            var select = document.getElementById('override-team-select');
                            if (response.teams && response.teams.length > 0) {
                                response.teams.forEach(function(team) {
                                    var option = document.createElement('option');
                                    option.value = team.id;
                                    option.textContent = team.name;
                                    select.appendChild(option);
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error('Error loading teams:', xhr.responseText);
                        }
                    });
                }
            });
            
            // Show/hide override amount section
            document.getElementById('override-amount').addEventListener('change', function() {
                document.getElementById('override-amount-section').classList.toggle('hidden', !this.checked);
            });
        }
        
        // Cancel button event
        document.getElementById('cancelSoldBtn').addEventListener('click', function() {
            document.getElementById('soldConfirmationPopup').remove();
        });
        
        // Confirm button event
        document.getElementById('confirmSoldBtn').addEventListener('click', function() {
            // Prepare data
            var data = {
                league_player_id: leaguePlayerId,
                team_id: callTeamId
            };
            
            // If organizer/admin and override options are checked, use those values
            if (isOrganizerOrAdmin) {
                if (document.getElementById('override-team').checked && 
                    document.getElementById('override-team-select').value) {
                    data.team_id = document.getElementById('override-team-select').value;
                }
                
                if (document.getElementById('override-amount').checked && 
                    document.getElementById('override-amount-input').value) {
                    data.override_amount = document.getElementById('override-amount-input').value;
                }
            }
            
            // Remove popup
            document.getElementById('soldConfirmationPopup').remove();
            
            // Hide bidding section, show available players
            $(".bidMain").addClass('hidden');
            $(".availPlayers").removeClass('hidden');
            $("#availablePlayersSection").removeClass('hidden');
            
            // Send AJAX request
            $.ajax({
                url: markSoldAction,
                type: "post",
                headers: {'X-CSRF-TOKEN': token},
                data: data,
                success: function (response) {
                    console.log("Player marked as sold:", response);
                    showMessage('Player marked as sold successfully!', 'success');
                },
                error: function (xhr) {
                    console.error("Error marking player as sold:", xhr.responseText);
                    showMessage('Error marking player as sold', 'error');
                }
            });
        });
    });
    
    $('.markUnSold').click(function(){
        var token = $(this).closest('.mb-6').attr('token');
        var markUnSoldAction = $(this).closest('.mb-6').attr('mark-unsold-action');
        var leaguePlayerId = $(this).attr('league-player-id');
        var playerName = $('.playerName').text().trim();
        
        // Create the confirmation popup
        var confirmationPopup = document.createElement('div');
        confirmationPopup.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        confirmationPopup.id = 'unsoldConfirmationPopup';
        
        // Create popup content
        var popupContent = `
            <div class="bg-white rounded-xl shadow-2xl p-6 max-w-md w-full mx-4">
                <div class="text-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Confirm Unsold Status</h3>
                </div>
                <div class="mb-6">
                    <p class="text-gray-700 mb-4">Are you sure you want to mark <span class="font-bold">${playerName}</span> as unsold?</p>
                    <div class="bg-yellow-50 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span class="text-yellow-800">This will refund all bids and make the player available for future auctions.</span>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <button id="cancelUnsoldBtn" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button id="confirmUnsoldBtn" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">
                        Mark as Unsold
                    </button>
                </div>
            </div>
        `;
        
        confirmationPopup.innerHTML = popupContent;
        document.body.appendChild(confirmationPopup);
        
        // Cancel button event
        document.getElementById('cancelUnsoldBtn').addEventListener('click', function() {
            document.getElementById('unsoldConfirmationPopup').remove();
        });
        
        // Confirm button event
        document.getElementById('confirmUnsoldBtn').addEventListener('click', function() {
            // Remove popup
            document.getElementById('unsoldConfirmationPopup').remove();
            
            // Hide bidding section, show available players
            $(".bidMain").addClass('hidden');
            $(".availPlayers").removeClass('hidden');
            $("#availablePlayersSection").removeClass('hidden');
            
            // Send AJAX request
            $.ajax({
                url: markUnSoldAction,
                type: "post",
                headers: {'X-CSRF-TOKEN': token},
                data: {
                    league_player_id: leaguePlayerId,
                },
                success: function (response) {
                    console.log("Player marked as unsold:", response);
                    showMessage('Player marked as unsold successfully!', 'success');
                },
                error: function (xhr) {
                    console.error("Error marking player as unsold:", xhr.responseText);
                    showMessage('Error marking player as unsold', 'error');
                }
            });
        });
    });
});