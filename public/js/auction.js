// Function to start bidding and show player card
function startBidding(playerId, leaguePlayerId, playerName, basePrice, playerRole, leagueId, startAuctionUrl) {
    // Get CSRF token
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const resolvedLeagueId = leagueId || document.getElementById('league-id')?.value;
    const resolvedStartUrl = startAuctionUrl || document.getElementById('auction-start-url')?.value;

    if (!resolvedLeagueId || !resolvedStartUrl) {
        console.error('Unable to resolve auction configuration', { leagueId: resolvedLeagueId, startAuctionUrl: resolvedStartUrl });
        alert('Unable to start auction. Missing required configuration.');
        return;
    }
    
    // Make AJAX call to start auction
    $.ajax({
        url: resolvedStartUrl,
        type: "post",
        headers: {'X-CSRF-TOKEN': token},
        data: {
            league_id: resolvedLeagueId,
            player_id: playerId,
            league_player_id: leaguePlayerId
        },
        success: function (response) {
            console.log("Auction started successfully:", response);
            
            if (response.success) {
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
                if (typeof showMessage === 'function') {
                    showMessage(playerName + ' auction started!', 'success');
                }
                
                // Refresh the page to show the updated player card
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                alert('Error: ' + (response.message || 'Failed to start auction'));
            }
        },
        error: function (xhr) {
            console.error("Error starting auction:", xhr.responseText);
            let errorMessage = 'Error starting auction. Please try again.';
            
            try {
                const errorData = JSON.parse(xhr.responseText);
                errorMessage = errorData.message || errorMessage;
            } catch (e) {
                // Use default error message
            }
            
            alert(errorMessage);
        }
    });
}

$(document).ready(function(){
    $('.startAuction').click(function(){
        $(".availPlayers").addClass('hidden');
        $(".bidMain").removeClass('hidden');
        var scrollPos =  $("#biddingSection").offset().top - 30;
        $(window).scrollTop(scrollPos);

        var actionStartAction = $(this).attr('start-bid-action');
        var playerId = $(this).attr('player-id');
        var leagueId = $(this).attr('league-id');
        var leaguePlayerId = $(this).attr('league-player-id');
        var token = $(this).attr('token') || $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: actionStartAction,
            type: "post",
            headers: {'X-CSRF-TOKEN': token},
            data: {
                league_id: leagueId,
                player_id: playerId,
                league_player_id: leaguePlayerId
            },
            success: function (response) {
                console.log("Bidding started and broadcasted:", response);
                // Refresh page to show updated player card
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            },
            error: function (xhr) {
                console.error("Error starting bidding:", xhr.responseText);
                let errorMessage = 'Error starting auction. Please try again.';
                
                try {
                    const errorData = JSON.parse(xhr.responseText);
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    // Use default error message
                }
                
                alert(errorMessage);
            }
        });
    });

    $('.callBid').click(function(){
        var leagueId = $(this).attr('league-id');
        var playerId = $(this).attr('player-id');
        var basePrice = $(this).attr('base-price');
        var increment = $(this).attr('increment');
        var leaguePlayerId = $(this).attr('league-player-id');
        var callBidAction = $(this).closest('[call-bid-action]').attr('call-bid-action');
        var token = $(this).closest('[token]').attr('token');

        // Disable button to prevent double clicks
        $(this).prop('disabled', true).addClass('opacity-50');
        
        $.ajax({
            url: callBidAction,
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
                console.log("Bid placed successfully:", response);
                
                if (response.success) {
                    $('.markSold').attr('league-player-id',leaguePlayerId);
                    $('.markSold').attr('call-team-id',response.call_team_id);
                    
                    // Update current bid display
                    $('.currentBid').text(response.new_bid);
                    $('.bidStatus').text('Current Bid');
                    
                    // Show success message
                    if (typeof showMessage === 'function') {
                        showMessage('Bid placed successfully!', 'success');
                    }
                } else {
                    alert('Error: ' + (response.message || 'Failed to place bid'));
                }
            },
            error: function (xhr) {
                console.error("Error placing bid:", xhr.responseText);
                let errorMessage = 'Error placing bid. Please try again.';
                
                try {
                    const errorData = JSON.parse(xhr.responseText);
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    // Use default error message
                }
                
                alert(errorMessage);
            },
            complete: function() {
                // Re-enable button
                $('.callBid').prop('disabled', false).removeClass('opacity-50');
            }
        });
    });

    $('.markSold').click(function(){
        var token = $(this).closest('[mark-sold-action]').attr('token');
        var markSoldAction = $(this).closest('[mark-sold-action]').attr('mark-sold-action');
        var leaguePlayerId = $(this).attr('league-player-id');
        var callTeamId = $(this).attr('call-team-id');

        if (!leaguePlayerId || !callTeamId) {
            alert('Please place a bid first before marking as sold.');
            return;
        }

        if (!confirm('Are you sure you want to mark this player as SOLD?')) {
            return;
        }

        $.ajax({
            url: markSoldAction,
            type: "post",
            headers: {'X-CSRF-TOKEN':token},
            data: {
                league_player_id: leaguePlayerId,
                team_id: callTeamId,
            },
            success: function (response) {
                console.log("Player sold successfully:", response);
                
                if (response.success) {
                    $(".bidMain").addClass('hidden');
                    $(".availPlayers").removeClass('hidden');
                    
                    // Show success message
                    if (typeof showMessage === 'function') {
                        showMessage('Player marked as sold!', 'success');
                    }
                    
                    // Refresh page to update player lists
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    alert('Error: ' + (response.message || 'Failed to mark player as sold'));
                }
            },
            error: function (xhr) {
                console.error("Error marking player as sold:", xhr.responseText);
                let errorMessage = 'Error marking player as sold. Please try again.';
                
                try {
                    const errorData = JSON.parse(xhr.responseText);
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    // Use default error message
                }
                
                alert(errorMessage);
            }
        });
    });
    
    $('.markUnSold').click(function(){
        var token = $(this).closest('[mark-unsold-action]').attr('token');
        var markUnSoldAction = $(this).closest('[mark-unsold-action]').attr('mark-unsold-action');
        var leaguePlayerId = $(this).attr('league-player-id');
        
        if (!leaguePlayerId) {
            alert('No player selected for auction.');
            return;
        }

        if (!confirm('Are you sure you want to mark this player as UNSOLD?')) {
            return;
        }
        
        $.ajax({
            url: markUnSoldAction,
            type: "post",
            headers: {'X-CSRF-TOKEN':token},
            data: {
                league_player_id: leaguePlayerId,
            },
            success: function (response) {
                console.log("Player marked as unsold:", response);
                
                if (response.success) {
                    $(".bidMain").addClass('hidden');
                    $(".availPlayers").removeClass('hidden');
                    
                    // Show success message
                    if (typeof showMessage === 'function') {
                        showMessage('Player marked as unsold!', 'success');
                    }
                    
                    // Refresh page to update player lists
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    alert('Error: ' + (response.message || 'Failed to mark player as unsold'));
                }
            },
            error: function (xhr) {
                console.error("Error marking player as unsold:", xhr.responseText);
                let errorMessage = 'Error marking player as unsold. Please try again.';
                
                try {
                    const errorData = JSON.parse(xhr.responseText);
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    // Use default error message
                }
                
                alert(errorMessage);
            }
        });
    });

    // Handle image error for player photos
    window.handleImageError = function(img) {
        if (img && img.nextElementSibling) {
            img.style.display = 'none';
            img.nextElementSibling.style.display = 'flex';
        }
    };
});
