// Minimal Auction JavaScript - Static Demo Version

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
        leagueId: leagueId
    });
    
    // Update bidding player info
    updateBiddingPlayer(playerName, position, basePrice);
    
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
                
                // Update base price for next bid if successful
                if (response.success && response.new_bid) {
                    $('.callBid').attr('base-price', response.new_bid);
                    
                    // Update current bid display
                    $('.currentBid').text(response.new_bid);
                    
                    // Update team info if available
                    if (response.call_team_id) {
                        $('.markSold').attr('call-team-id', response.call_team_id);
                    }
                    
                    showMessage('Bid placed: ₹' + response.new_bid, 'success');
                }
            },
            error: function(xhr) {
                console.error("Error placing bid:", xhr.responseText);
                showMessage('Error placing bid', 'error');
            }
        });
    });

    $('.markSold').click(function(){
        var token = $(this).closest('.mb-6').attr('token');
        var markSoldAction = $(this).closest('.mb-6').attr('mark-sold-action');
        var leaguePlayerId = $(this).attr('league-player-id');
        var callTeamId = $(this).attr('call-team-id');

        $(".bidMain").addClass('hidden');
        $(".availPlayers").removeClass('hidden');
        $("#availablePlayersSection").removeClass('hidden');
        $.ajax({
            url: markSoldAction,   // Laravel route
            type: "post",
            headers: {'X-CSRF-TOKEN':token},
            data: {
                league_player_id: leaguePlayerId,
                team_id: callTeamId,
            },
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
    
    $('.markUnSold').click(function(){
        var token = $(this).closest('.mb-6').attr('token');
        var markUnSoldAction = $(this).closest('.mb-6').attr('mark-unsold-action');
        var leaguePlayerId = $(this).attr('league-player-id');

        $(".bidMain").addClass('hidden');
        $(".availPlayers").removeClass('hidden');
        $("#availablePlayersSection").removeClass('hidden');
        
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