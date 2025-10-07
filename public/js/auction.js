// Minimal Auction JavaScript - Static Demo Version
// Simple function to start bidding and show bidding section
function startBidding(playerId, playerName, basePrice, playerRole) {
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
    var action=$('.players-container').attr('url');

}

$(document).ready(function(){
    // Search functionality for available players
    $('#playerSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        const playerCards = $('.player-card');
        let visibleCount = 0;
        
        console.log('Search term:', searchTerm);
        console.log('Total player cards found:', playerCards.length);
        
        playerCards.each(function() {
            const playerName = $(this).find('h3').text().toLowerCase();
            const playerMobile = $(this).find('.flex.items-center.space-x-2 .text-gray-600').first().text().toLowerCase();
            const playerPosition = $(this).find('.bg-blue-100').text().toLowerCase();
            
            console.log('Player:', playerName, 'Mobile:', playerMobile, 'Position:', playerPosition);
            
            if (searchTerm === '') {
                // If no search term, show only first 3 players (initial state)
                const playerIndex = $(this).data('player-index');
                if (playerIndex < 3) {
                    $(this).removeClass('hidden');
                    visibleCount++;
                } else {
                    $(this).addClass('hidden');
                }
            } else if (playerName.includes(searchTerm) || playerMobile.includes(searchTerm) || playerPosition.includes(searchTerm)) {
                // If searching, show all matching players
                $(this).removeClass('hidden');
                visibleCount++;
            } else {
                $(this).addClass('hidden');
            }
        });
        
        // Update visible players count
        $('#visiblePlayersCount').text(visibleCount + ' on this page');
        console.log('Visible count:', visibleCount);
    });
    
    // Role filter functionality
    $('#roleFilter').on('change', function() {
        const selectedRole = $(this).val().toLowerCase();
        const playerCards = $('.player-card');
        let visibleCount = 0;
        
        playerCards.each(function() {
            const playerPosition = $(this).find('.bg-blue-100').text().toLowerCase();
            
            if (selectedRole === '' || playerPosition.includes(selectedRole)) {
                $(this).removeClass('hidden');
                visibleCount++;
            } else {
                $(this).addClass('hidden');
            }
        });
        
        // Update visible players count
        $('#visiblePlayersCount').text(visibleCount + ' on this page');
    });
    
    // Price filter functionality
    $('#priceFilter').on('change', function() {
        const selectedPrice = $(this).val();
        const playerCards = $('.player-card');
        let visibleCount = 0;
        
        playerCards.each(function() {
            const basePrice = parseInt($(this).find('.text-2xl').text().replace('₹', ''));
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
                $(this).removeClass('hidden');
                visibleCount++;
            } else {
                $(this).addClass('hidden');
            }
        });
        
        // Update visible players count
        $('#visiblePlayersCount').text(visibleCount + ' on this page');
    });
    
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
