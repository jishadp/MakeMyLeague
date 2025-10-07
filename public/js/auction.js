// Minimal Auction JavaScript - Static Demo Version
// Simple function to start bidding and show bidding section
function startBidding(userId, leaguePlayerId, playerName, basePrice, position, leagueId, startBidAction) {
    // Update the bidding section with player information
    updateBiddingPlayer(playerName, position, basePrice);
    
    // Show the bidding section
    const biddingSection = document.getElementById('biddingSection');
    if (biddingSection) {
        biddingSection.classList.remove('hidden');
        biddingSection.scrollIntoView({ behavior: 'smooth' });
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
        $(".bidMain").removeClass('hidden');
        var scrollPos =  $("#biddingSection").offset().top - 30;
        $(window).scrollTop(scrollPos);

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
        var leagueId = $(this).attr('league-id');
        var playerId = $(this).attr('player-id');
        var basePrice = $(this).attr('base-price');
        var increment = $(this).attr('increment');
        var leaguePlayerId = $(this).attr('league-player-id');
        var callBidAction = $(this).closest('.grid').attr('call-bid-action');
        var token = $(this).closest('.grid').attr('token');

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
                console.log("Bidding started and broadcasted:", response);
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
        $.ajax({
            url: markSoldAction,   // Laravel route
            type: "post",
            headers: {'X-CSRF-TOKEN':token},
            data: {
                league_player_id: leaguePlayerId,
                team_id: callTeamId,
            },
            success: function (response) {
                console.log("Bidding started and broadcasted:", response);

            }
        });
    });
    $('.markUnSold').click(function(){
        $(".bidMain").addClass('hidden');
        $(".availPlayers").removeClass('hidden');
    });
});
