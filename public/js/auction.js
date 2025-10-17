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
        var callBidAction = $(this).closest('[call-bid-action]').attr('call-bid-action');
        var token = $(this).closest('[token]').attr('token');

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
                $('.markSold').attr('league-player-id',leaguePlayerId);
                $('.markSold').attr('call-team-id',response.call_team_id);
            }
        });
    });

    $('.markSold').click(function(){
        var token = $(this).closest('[mark-sold-action]').attr('token');
        var markSoldAction = $(this).closest('[mark-sold-action]').attr('mark-sold-action');
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
                console.log("Player sold successfully:", response);
            }
        });
    });
    
    $('.markUnSold').click(function(){
        var token = $(this).closest('[mark-unsold-action]').attr('token');
        var markUnSoldAction = $(this).closest('[mark-unsold-action]').attr('mark-unsold-action');
        var leaguePlayerId = $(this).attr('league-player-id');
        
        $(".bidMain").addClass('hidden');
        $(".availPlayers").removeClass('hidden');
        $.ajax({
            url: markUnSoldAction,   // Laravel route
            type: "post",
            headers: {'X-CSRF-TOKEN':token},
            data: {
                league_player_id: leaguePlayerId,
            },
            success: function (response) {
                console.log("Player marked as unsold:", response);
            }
        });
    });
});