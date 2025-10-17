var pusher = new Pusher('b62b3b015a81d2d28278', { 
    cluster: 'ap2',
    forceTLS: true,
    enabledTransports: ['ws', 'wss']
});

// Enable Pusher logging for debugging
Pusher.logToConsole = true;

console.log('Pusher initialized with key: b62b3b015a81d2d28278');

// Subscribe to the general auctions channel
var channel = pusher.subscribe('auctions');
console.log('Subscribed to general auctions channel');

// Subscribe to the league-specific channel if we're in a league context
const leagueIdElement = document.getElementById('league-id');
var leagueChannel = null;

if (leagueIdElement && leagueIdElement.value) {
    const leagueId = leagueIdElement.value;
    leagueChannel = pusher.subscribe(`auctions.league.${leagueId}`);
    console.log(`✅ Subscribed to league-specific channel: auctions.league.${leagueId}`);
} else {
    console.warn('⚠️ No league-id element found, only using general channel');
}

// Define event handlers separately so we can reuse them
function handlePlayerStarted(data) {
    console.log('🎯 NEW PLAYER STARTED EVENT RECEIVED:', data);
    console.log('Player:', data.player?.name, 'League:', data.league?.id);
    
    $('.playerName').html(data.player.name);
    
    // Update position only if primary game role exists
    if (data.player.primary_game_role && data.player.primary_game_role.game_position) {
        $('.position').html(data.player.primary_game_role.game_position.name);
    } else {
        $('.position').html('');
    }
    
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
    
    var scrollPos = $("#biddingSection").offset().top - 50;
    $(window).scrollTop(scrollPos);
}

function handleNewBid(data) {
    console.log('💰 NEW BID EVENT RECEIVED:', data);
    console.log('Bid Amount:', data.new_bid, 'Team:', data.team?.team?.name);
    
    // Ensure we have the team data
    const teamData = data.team || data.league_team;
    if (!teamData) {
        console.error('No team data received in bid event');
        return;
    }
    
    // Update bid information
    $('.currentBid').html(data.new_bid);
    $('.bidStatus').html('Current Bid');
    $('.callBid').attr('base-price', data.new_bid);
    $('.bidTeam').html(teamData.team.name);
    $('.teamBidDetls').removeClass('hidden');
    $('.teamBalance').html(teamData.wallet_balance);
    $('.leageTeamPlayers').html(teamData.league_players_count);
    $('.markSold').attr('call-team-id', teamData.id);
    
    // Update player card elements if player data is available
    if (data.league_player) {
        updatePlayerCard(data.league_player, data.new_bid);
    }
    
    // Update bid button increments
    updateBidButtonIncrements(data.new_bid);
    
    // Refresh Livewire components - but only do it once and with try/catch
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
}

function handlePlayerSold(data) {
    console.log('✅ PLAYER SOLD EVENT RECEIVED:', data);
    console.log('Player:', data.league_player?.player?.name, 'Team:', data.team?.team?.name);
    console.log('Updated Team Data:', data.team);
    
    // Update team balance immediately in UI if function exists
    if (typeof updateTeamBalanceInUI === 'function' && data.team) {
        updateTeamBalanceInUI(data.team.id, data.team.wallet_balance, data.team.team?.name);
    }
    
    // Show available players section again
    $('.availPlayers').removeClass('hidden');
    $('#availablePlayersSection').removeClass('hidden');
    $('.bidMain').addClass('hidden');
    
    // Clear the current bidding info
    $('.teamBidDetls').addClass('hidden');
    $('.currentBid').html('0');
    $('.bidTeam').html('No bids yet');
    $('.bidStatus').html('Starting bid');
    
    // Refresh Livewire components immediately - Teams first since player was sold
    try {
        if (window.Livewire) {
            console.log('🔄 Refreshing Teams and Bids after player sold...');
            
            // Refresh Teams immediately to show updated wallet and player count
            if (typeof Livewire.dispatch === 'function') {
                Livewire.dispatch('refreshTeams');
            } else if (typeof Livewire.emit === 'function') {
                Livewire.emit('refreshTeams');
            }
            
            // Then refresh Bids after a short delay
            setTimeout(function() {
                try {
                    if (typeof Livewire.dispatch === 'function') {
                        Livewire.dispatch('refreshBids');
                    } else if (typeof Livewire.emit === 'function') {
                        Livewire.emit('refreshBids');
                    }
                } catch (e) {
                    console.warn("Error refreshing bids:", e);
                }
            }, 500);
        }
    } catch (e) {
        console.warn("Error with Livewire refresh:", e);
    }
    
    // Show success message with player and team info
    if (typeof showMessage === 'function') {
        const playerName = data.league_player?.player?.name || 'Player';
        const teamName = data.team?.team?.name || 'Team';
        showMessage(`${playerName} sold to ${teamName}!`, 'success');
    }
}

function handlePlayerUnsold(data) {
    console.log('❌ PLAYER UNSOLD EVENT RECEIVED:', data);
    console.log('Player:', data.league_player?.player?.name);
    
    // Show available players section again
    $('.availPlayers').removeClass('hidden');
    $('#availablePlayersSection').removeClass('hidden');
    $('.bidMain').addClass('hidden');
    
    // Clear the current bidding info
    $('.teamBidDetls').addClass('hidden');
    $('.currentBid').html('0');
    $('.bidTeam').html('No bids yet');
    $('.bidStatus').html('Starting bid');
    
    // Refresh Livewire components immediately - Teams first to show refunded wallets
    try {
        if (window.Livewire) {
            console.log('🔄 Refreshing Teams and Bids after player unsold...');
            
            // Refresh Teams immediately to show refunded wallet balances
            if (typeof Livewire.dispatch === 'function') {
                Livewire.dispatch('refreshTeams');
            } else if (typeof Livewire.emit === 'function') {
                Livewire.emit('refreshTeams');
            }
            
            // Then refresh Bids after a short delay
            setTimeout(function() {
                try {
                    if (typeof Livewire.dispatch === 'function') {
                        Livewire.dispatch('refreshBids');
                    } else if (typeof Livewire.emit === 'function') {
                        Livewire.emit('refreshBids');
                    }
                } catch (e) {
                    console.warn("Error refreshing bids:", e);
                }
            }, 500);
        }
    } catch (e) {
        console.warn("Error with Livewire refresh:", e);
    }
    
    // Show success message with player info
    if (typeof showMessage === 'function') {
        const playerName = data.league_player?.player?.name || 'Player';
        showMessage(`${playerName} marked as unsold - bids refunded!`, 'success');
    }
}

// Bind events to the general channel
console.log('📡 Binding events to general channel...');
channel.bind('new-player-started', handlePlayerStarted);
channel.bind('new-player-bid-call', handleNewBid);
channel.bind('player-sold', handlePlayerSold);
channel.bind('player-unsold', handlePlayerUnsold);
console.log('✅ General channel events bound');

// Bind events to the league-specific channel if it exists
if (leagueChannel) {
    console.log('📡 Binding events to league-specific channel...');
    leagueChannel.bind('new-player-started', handlePlayerStarted);
    leagueChannel.bind('new-player-bid-call', handleNewBid);
    leagueChannel.bind('player-sold', handlePlayerSold);
    leagueChannel.bind('player-unsold', handlePlayerUnsold);
    console.log('✅ League-specific channel events bound');
}

// Log connection state
pusher.connection.bind('connected', function() {
    console.log('Pusher connected successfully!');
});

pusher.connection.bind('error', function(err) {
    console.error('Pusher connection error:', err);
});

pusher.connection.bind('disconnected', function() {
    console.warn('Pusher disconnected');
});

// Function to update player card elements
function updatePlayerCard(playerData, newBid) {
    console.log('🔄 Updating player card with new bid:', newBid);
    
    // Update player name if available
    if (playerData.player && playerData.player.name) {
        $('.playerName').text(playerData.player.name);
    }
    
    // Update player position if available
    if (playerData.player && playerData.player.primary_game_role && playerData.player.primary_game_role.game_position) {
        $('.position').text(playerData.player.primary_game_role.game_position.name);
    }
    
    // Update base price
    if (playerData.base_price) {
        $('.basePrice').text(playerData.base_price);
    }
    
    // Update current bid display with currency symbol
    $('.currentBid').html('₹' + parseInt(newBid).toLocaleString());
    
    // Update custom bid modal current bid display
    $('#currentBidDisplay').html('₹' + parseInt(newBid).toLocaleString());
    
    // Update bid status
    $('.bidStatus').text('Current Bid');
}

// Function to update bid button increments
function updateBidButtonIncrements(newBid) {
    console.log('🔄 Updating bid button increments for bid:', newBid);
    
    // Calculate next bid amount (assuming standard increment logic)
    // You might need to adjust this based on your league's increment rules
    const currentBid = parseFloat(newBid);
    let nextBid = currentBid;
    
    // Simple increment logic - you can customize this based on your league rules
    if (currentBid < 1000) {
        nextBid = currentBid + 50;
    } else if (currentBid < 5000) {
        nextBid = currentBid + 100;
    } else if (currentBid < 10000) {
        nextBid = currentBid + 250;
    } else {
        nextBid = currentBid + 500;
    }
    
    const increment = nextBid - currentBid;
    
    // Update organizer bid buttons (if they exist)
    $('.callBid').each(function() {
        const $button = $(this);
        const $container = $button.closest('.grid, .flex');
        
        if ($container.hasClass('grid')) {
            // Organizer view - update only the increment display
            $button.find('p').html(parseInt(increment).toLocaleString());
        } else {
            // Team owner/auctioneer view - update only the increment display
            $button.find('p').html(parseInt(increment).toLocaleString());
        }
        
        // Update the increment attribute
        $button.attr('increment', increment);
        $button.attr('base-price', currentBid);
    });
}
