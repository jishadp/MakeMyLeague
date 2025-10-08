var pusher = new Pusher('a1afbae37f05666fb5b6', { cluster: 'ap2' });
var channel = pusher.subscribe('auctions');
channel.bind('new-player-started', function(data) {
    $('.playerName').html(data.player.name);
    $('.position').html(data.player.position.name);
    $('.basePrice,.currentBid').html(data.league_player.base_price);
    $('.bidTeam').html('Awaiting new bids..');
    $('.bidStatus').html('Base Price');

    $('.callBid').attr('league-id',data.league.id);
    $('.callBid').attr('player-id',data.player.id);
    $('.callBid').attr('league-player-id',data.league_player.id);
    $('.callBid').attr('base-price',data.league_player.base_price);

    $('.bidMain').removeClass('hidden');
    $('.availPlayers').addClass('hidden');
    $('#availablePlayersSection').addClass('hidden');
    var scrollPos =  $("#biddingSection").offset().top - 50;
    $(window).scrollTop(scrollPos);

});

channel.bind('new-player-bid-call', function(data) {
    $('.currentBid').html(data.new_bid);
    $('.bidStatus').html('Current Bid');
    $('.callBid').attr('base-price',data.new_bid);
    $('.bidTeam').html(data.league_team.team.name);
    $('.teamBidDetls').removeClass('hidden');
    $('.teamBalance').html(data.league_team.wallet_balance);
    $('.leageTeamPlayers').html(data.league_team.league_players_count);
    $('.markSold').attr('call-team-id',data.league_team.id);
    
    // Refresh Livewire components
    if (window.Livewire) {
        Livewire.emit('refreshBids');
        Livewire.emit('refreshTeams');
    }
    
    // Also refresh the page data after a short delay
    setTimeout(function() {
        if (window.Livewire) {
            Livewire.emit('refreshBids');
            Livewire.emit('refreshTeams');
        }
    }, 1000);

});

// Listen for player sold event
channel.bind('player-sold', function(data) {
    // Show available players section again
    $('.availPlayers').removeClass('hidden');
    $('#availablePlayersSection').removeClass('hidden');
    $('.bidMain').addClass('hidden');
    
    // Refresh Livewire components
    if (window.Livewire) {
        Livewire.emit('refreshBids');
        Livewire.emit('refreshTeams');
    }
    
    // Show success message
    if (typeof showMessage === 'function') {
        showMessage('Player sold successfully!', 'success');
    }
});

// Listen for player unsold event
channel.bind('player-unsold', function(data) {
    // Show available players section again
    $('.availPlayers').removeClass('hidden');
    $('#availablePlayersSection').removeClass('hidden');
    $('.bidMain').addClass('hidden');
    
    // Refresh Livewire components
    if (window.Livewire) {
        Livewire.emit('refreshBids');
        Livewire.emit('refreshTeams');
    }
    
    // Show success message
    if (typeof showMessage === 'function') {
        showMessage('Player marked as unsold!', 'success');
    }
});
