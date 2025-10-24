// Role-based error handling for auction actions
function handleAuctionError(xhr) {
    let errorMessage = 'An error occurred. Please try again.';
    
    if (xhr.status === 403) {
        try {
            const errorData = JSON.parse(xhr.responseText);
            const parsedMessage = JSON.parse(errorData.message || '{}');
            errorMessage = parsedMessage.details || parsedMessage.message || 'Access denied.';
        } catch (e) {
            errorMessage = 'Access denied. You do not have permission for this action.';
        }
    } else {
        try {
            const errorData = JSON.parse(xhr.responseText);
            errorMessage = errorData.message || errorMessage;
        } catch (e) {}
    }
    
    return errorMessage;
}

// Pre-auction validation
function showPreAuctionValidation(leagueId, callback) {
    const userRole = document.getElementById('user-role')?.value;
    
    if (userRole !== 'organizer' && userRole !== 'both') {
        alert('Only organizers can start auctions');
        return;
    }
    
    if (confirm('Start auction for this player?')) {
        callback();
    }
}

// Check user role for UI updates
function getUserRole() {
    return document.getElementById('user-role')?.value || 'none';
}

function canPlaceBid() {
    const role = getUserRole();
    return role === 'auctioneer' || role === 'both';
}

function canManageAuction() {
    const role = getUserRole();
    return role === 'organizer' || role === 'both';
}

// Update UI based on role
document.addEventListener('DOMContentLoaded', function() {
    const role = getUserRole();
    
    // Hide/show elements based on role
    if (role === 'organizer') {
        document.querySelectorAll('.auctioneer-only').forEach(el => el.style.display = 'none');
    } else if (role === 'auctioneer') {
        document.querySelectorAll('.organizer-only').forEach(el => el.style.display = 'none');
    }
});
