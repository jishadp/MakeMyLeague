// Minimal Auction JavaScript - Static Demo Version

// Initialize page when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeViewToggle();
});

// Initialize view toggle functionality
function initializeViewToggle() {
    const cardViewBtn = document.getElementById('cardViewBtn');
    const listViewBtn = document.getElementById('listViewBtn');
    const cardView = document.getElementById('cardView');
    const listView = document.getElementById('listView');

    if (cardViewBtn && listViewBtn) {
        cardViewBtn.addEventListener('click', () => {
            cardView.classList.remove('hidden');
            listView.classList.add('hidden');
            cardViewBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
            cardViewBtn.classList.remove('text-gray-600');
            listViewBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
            listViewBtn.classList.add('text-gray-600');
        });

        listViewBtn.addEventListener('click', () => {
            listView.classList.remove('hidden');
            cardView.classList.add('hidden');
            listViewBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
            listViewBtn.classList.remove('text-gray-600');
            cardViewBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
            cardViewBtn.classList.add('text-gray-600');
        });
    }
}

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
    $.ajax({
        url: action,   // Laravel route
        type: "GET",
        data: {
            player_id: playerId
        },
        success: function (response) {
            console.log("Bidding started and broadcasted:", response);
        },
        error: function (xhr) {
            console.error("Error starting bidding:", xhr.responseText);
        }
    });
}

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
