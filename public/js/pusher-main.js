var pusher = new Pusher('a1afbae37f05666fb5b6', { cluster: 'ap2' });
var channel = pusher.subscribe('auctions');
channel.bind('new-player-started', function(data) {
    console.log(data);

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
    var scrollPos =  $("#biddingSection").offset().top - 50;
    $(window).scrollTop(scrollPos);

});

channel.bind('new-player-bid-call', function(data) {
    console.log(data);

    $('.currentBid').html(data.new_bid);
    $('.bidStatus').html('Current Bid');
    $('.callBid').attr('base-price',data.new_bid);
    $('.bidTeam').html(data.league_team.team.name);
    $('.teamBidDetls').removeClass('hidden');
    $('.teamBalance').html(data.league_team.wallet_balance);
    $('.leageTeamPlayers').html(data.league_team.league_players_count);

});
