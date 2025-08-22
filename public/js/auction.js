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
});

// Simple function to view player details
function viewPlayerDetails(playerId) {
    showMessage('Player details for ID: ' + playerId + ' - Feature coming soon!', 'info');
}

// Show message
function showMessage(message, type = 'info') {
    const messageContainer = document.getElementById('messageContainer');
    if (!messageContainer) return;

    const messageElement = document.createElement('div');
    messageElement.className = `px-4 py-2 rounded-lg shadow-lg mb-2 transition-all duration-300 ${type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
                'bg-blue-500 text-white'
        }`;
    messageElement.textContent = message;

    messageContainer.appendChild(messageElement);

    // Remove message after 3 seconds
    setTimeout(() => {
        messageElement.style.opacity = '0';
        setTimeout(() => {
            if (messageElement.parentNode) {
                messageElement.parentNode.removeChild(messageElement);
            }
        }, 300);
    }, 3000);
}
