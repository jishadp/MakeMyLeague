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

// Function to update team balance in UI
function updateTeamBalanceInUI(teamId, balance, teamName) {
    console.log('Updating team balance:', teamId, balance, teamName);
    
    // Format the balance
    const formattedBalance = new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        maximumFractionDigits: 0
    }).format(balance);
    
    // Find and update the team balance in the teams section
    // Look for team cards by data attribute or text content
    $('.teamBalance').each(function() {
        const teamCard = $(this).closest('.team-card, .border, .glacier-card');
        const teamNameEl = teamCard.find('h3, .team-name, .font-bold');
        
        if (teamNameEl.text().trim().includes(teamName)) {
            $(this).html(formattedBalance.replace('₹', '₹'));
            console.log('Updated balance for', teamName);
        }
    });
    
    // Also update in any other balance displays
    $('[data-team-id="' + teamId + '"]').find('.wallet-balance, .team-balance, .balance').each(function() {
        $(this).text(formattedBalance);
    });
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
                    
                    // Update bidding team name 
                    if (response.team_name) {
                        $('.bidTeam').text(response.team_name);
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
        confirmationPopup.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
        confirmationPopup.id = 'soldConfirmationPopup';
        
        // Check if user is organizer or admin
        var isOrganizerOrAdmin = document.getElementById('is-organizer-or-admin') ? 
                               document.getElementById('is-organizer-or-admin').value === 'true' : false;
        
        // Create popup content with override option for organizers/admins - styled like the ownership modal
        var popupContent = `
            <div class="relative top-2 sm:top-8 lg:top-20 mx-auto p-3 sm:p-4 lg:p-6 border w-11/12 sm:w-10/12 lg:w-4/5 xl:w-3/4 max-w-6xl shadow-lg rounded-lg bg-white mb-20 sm:mb-24 lg:mb-32">
                <div class="mt-2 sm:mt-3">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="bg-green-100 rounded-lg p-2">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900">Confirm Player Sale</h3>
                        </div>
                        <button id="cancelSoldBtn" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-2 transition-colors">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Sale Content -->
                    <div class="space-y-4 sm:space-y-6">
                        <!-- Player Information -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 sm:p-6">
                            <div class="flex items-center mb-4">
                                <div class="bg-green-100 rounded-lg p-3 mr-4">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg sm:text-xl font-bold text-green-900">${playerName}</h4>
                                    <p class="text-sm sm:text-base text-green-700">Sold to ${bidTeam}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="bg-white rounded-lg p-3 border border-green-100">
                                    <div class="flex items-center mb-2">
                                        <svg class="w-4 h-4 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-xs font-medium text-green-700 uppercase tracking-wide">Bid Amount</span>
                                    </div>
                                    <p class="text-lg font-semibold text-green-900">₹${currentBid}</p>
                                </div>
                            </div>
                        </div>

                        ${isOrganizerOrAdmin ? `
                        <!-- Override Options -->
                        <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6">
                            <h4 class="text-lg font-bold text-gray-900 mb-4">Organizer Options</h4>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="flex items-center text-sm text-gray-700 mb-2">
                                        <input type="checkbox" id="override-team" class="mr-2 h-5 w-5 text-blue-600 rounded">
                                        <span class="text-base">Override team selection</span>
                                    </label>
                                    <div id="override-team-section" class="hidden ml-7">
                                        <select id="override-team-select" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-base focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="">Select team...</option>
                                            <!-- Teams will be loaded dynamically -->
                                        </select>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="flex items-center text-sm text-gray-700 mb-2">
                                        <input type="checkbox" id="override-amount" class="mr-2 h-5 w-5 text-blue-600 rounded">
                                        <span class="text-base">Override bid amount</span>
                                    </label>
                                    <div id="override-amount-section" class="hidden ml-7">
                                        <input type="number" id="override-amount-input" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-base focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter bid amount">
                                    </div>
                                </div>
                            </div>
                        </div>
                        ` : ''}

                        <!-- Confirmation Button -->
                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <button id="confirmSoldBtn" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                                Confirm Sale
                            </button>
                        </div>
                    </div>
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
                    // Get league slug
                    var leagueSlug = document.getElementById('league-slug').value;
                    
                    // Fetch teams - use the league-teams.index route with slug
                    $.ajax({
                        url: '/leagues/' + leagueSlug + '/teams',
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
                    
                    // Update team balance in UI if returned
                    if (response.team_balance !== undefined && response.team_id) {
                        // Update balance in teams section
                        updateTeamBalanceInUI(response.team_id, response.team_balance, response.team_name);
                    }
                    
                    // Reload teams data to reflect changes
                    if (typeof Livewire !== 'undefined') {
                        try {
                            Livewire.emit('refreshComponent');
                        } catch (e) {
                            console.log('Livewire refresh not available');
                        }
                    }
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
        var bidTeam = $('.bidTeam').text().trim();
        var hasBid = !(bidTeam === 'Awaiting new bids..' || bidTeam === 'No player selected');
        
        // Create the confirmation popup
        var confirmationPopup = document.createElement('div');
        confirmationPopup.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
        confirmationPopup.id = 'unsoldConfirmationPopup';
        
        // Create popup content with same style as sold popup
        var popupContent = `
            <div class="relative top-2 sm:top-8 lg:top-20 mx-auto p-3 sm:p-4 lg:p-6 border w-11/12 sm:w-10/12 lg:w-4/5 xl:w-3/4 max-w-6xl shadow-lg rounded-lg bg-white mb-20 sm:mb-24 lg:mb-32">
                <div class="mt-2 sm:mt-3">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="bg-yellow-100 rounded-lg p-2">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900">Confirm Unsold Status</h3>
                        </div>
                        <button id="cancelUnsoldBtn" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-2 transition-colors">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Player Information -->
                    <div class="space-y-4 sm:space-y-6">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 sm:p-6">
                            <div class="flex items-center mb-4">
                                <div class="bg-yellow-100 rounded-lg p-3 mr-4">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg sm:text-xl font-bold text-yellow-900">${playerName}</h4>
                                    <p class="text-sm sm:text-base text-yellow-700">Will be marked as unsold</p>
                                </div>
                            </div>
                            
                            <div class="bg-white rounded-lg p-4 border border-yellow-100 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <span class="text-yellow-800">This will refund all bids and make the player available for future auctions.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Confirmation Button -->
                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <div class="flex space-x-3">
                                ${hasBid ? 
                                `<button id="confirmUnsoldBtn" class="px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                                    Confirm Unsold
                                </button>` : ''}
                            </div>
                        </div>
                    </div>
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