# Live Auction Access Control - Quick Reference

## Middleware Usage

### Protect Routes
```php
// View access (any authorized user)
Route::get('/auction/{league}')->middleware('live.auction:view');

// Organizer only
Route::post('/auction/{league}/sold')->middleware('live.auction:organizer');

// Auctioneer only
Route::post('/auction/{league}/place-bid')->middleware('live.auction:auctioneer');
```

## Controller Usage

### Check Authorization
```php
// Using policy
$this->authorize('selectPlayer', $league);
$this->authorize('placeBid', $league);
$this->authorize('startAuction', $league);

// Get user's role from request
$role = $request->input('auction_role'); // organizer|auctioneer|both
$teamId = $request->input('auction_team_id'); // null for organizers
```

### Validate Auction Start
```php
use App\Services\AuctionAccessService;

$service = app(AuctionAccessService::class);
$validation = $service->validateAuctionStart($league->id);

if (!$validation['valid']) {
    return response()->json([
        'errors' => $validation['errors'],
        'warnings' => $validation['warnings'],
        'data' => $validation['data']
    ], 422);
}
```

### Log Actions
```php
use App\Models\AuctionLog;

AuctionLog::logAction(
    $league->id,
    auth()->id(),
    'player_sold',
    'LeaguePlayer',
    $player->id,
    ['amount' => 5000, 'team_id' => $teamId]
);
```

## Blade Templates

### Conditional Rendering
```php
@can('selectPlayer', $league)
    <!-- Organizer controls: player selection, sold/unsold buttons -->
@endcan

@can('placeBid', $league)
    <!-- Bidding panel: bid input, place bid button -->
@endcan

@if(auth()->user()->can('selectPlayer', $league) && auth()->user()->can('placeBid', $league))
    <!-- Dual role badge -->
@endif
```

## Service Methods

### Check Access
```php
use App\Services\AuctionAccessService;

$service = app(AuctionAccessService::class);

// Check if user is organizer
$isOrganizer = $service->isApprovedOrganizer($userId, $leagueId);

// Check if user is auctioneer
[$isAuctioneer, $teamId] = $service->isActiveAuctioneer($userId, $leagueId);

// Get user's role
$role = $service->getUserAuctionRole($userId, $leagueId);
// Returns: 'organizer', 'auctioneer', 'both', or 'none'

// Master access check
$access = $service->canUserAccessAuction($userId, $leagueId);
// Returns: ['allowed' => bool, 'role' => string, 'team_id' => int|null, 'message' => string]
```

### Get Auctioneers List
```php
$auctioneers = $service->getAuctioneersList($leagueId);
// Returns collection with team_name, auctioneer_name, access_type
```

## Access Rules Summary

| Role | Select Player | Mark Sold/Unsold | Place Bid | View Auction |
|------|--------------|------------------|-----------|--------------|
| Organizer | ✓ | ✓ | Only if auctioneer | ✓ |
| Auctioneer | ✗ | ✗ | ✓ (own team) | ✓ |
| Both | ✓ | ✓ | ✓ (own team) | ✓ |
| Admin (no role) | ✗ | ✗ | ✗ | ✗ |

## Common Patterns

### Controller Action with Access Check
```php
public function selectPlayer(Request $request, League $league)
{
    $this->authorize('selectPlayer', $league);
    
    // Your logic here
    
    AuctionLog::logAction(
        $league->id,
        auth()->id(),
        'player_selected',
        'LeaguePlayer',
        $playerId
    );
}
```

### AJAX Request with Error Handling
```javascript
fetch('/auction/' + leagueId + '/place-bid', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({ amount: bidAmount })
})
.then(response => {
    if (response.status === 403) {
        return response.json().then(data => {
            alert(JSON.parse(data.message).details);
        });
    }
    return response.json();
});
```

## Migration Command
```bash
php artisan migrate
```

## Clear Cache After Changes
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```
