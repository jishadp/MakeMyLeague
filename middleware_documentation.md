# MakeMyLeague Middleware Documentation

## Overview
The application uses custom middleware to control access to different features based on user roles and permissions.

## Middleware Classes

### 1. CheckLiveAuctionAccess
**Purpose:** Controls access to live auction functionality with granular role-based permissions
**Location:** `app/Http/Middleware/CheckLiveAuctionAccess.php`

**Access Levels:**
1. **Organizer:** Full auction control (select players, mark sold/unsold, start/end auction)
2. **Auctioneer:** Bidding access for assigned team only
3. **Both:** Users who are both organizer and auctioneer
4. **Admin:** Must have organizer or auctioneer role assigned

**How it works:**
- Uses AuctionAccessService for centralized access checks
- Validates user role against required permission parameter
- Stores auction_role and auction_team_id in request for controllers
- Returns detailed error messages for unauthorized access

**Permission Parameters:**
- `view`: Basic auction panel access (organizer or auctioneer)
- `organizer`: Organizer-only actions (player selection, sold/unsold)
- `auctioneer`: Bidding actions (place bids)

**Usage:**
```php
Route::middleware(['live.auction:organizer'])->group(function () {
    Route::post('/auction/{league}/sold', [AuctionController::class, 'sold']);
});

Route::middleware(['live.auction:auctioneer'])->group(function () {
    Route::post('/auction/{league}/place-bid', [AuctionController::class, 'placeBid']);
});

Route::middleware(['live.auction:view'])->group(function () {
    Route::get('/auction/{league}', [AuctionController::class, 'index']);
});
```

**Access Control Matrix:**
```
Organizer Permissions:
✓ Select player for bidding
✓ Mark player as Sold/Unsold
✓ Start/End auction
✓ View all bids
✓ Can bid IF assigned as auctioneer
✗ Cannot bid without auctioneer assignment

Auctioneer Permissions:
✓ Place bids for assigned team only
✓ View auction status
✓ View own team wallet
✗ Cannot select players
✗ Cannot mark Sold/Unsold
✗ Cannot bid for other teams

Admin Restrictions:
✗ Must be assigned as organizer OR auctioneer
✗ No blanket access without role
```

### 2. CheckAdminAccess
**Purpose:** Restricts access to admin-only features
**Location:** `app/Http/Middleware/CheckAdminAccess.php`

**How it works:**
- Checks if user is authenticated
- Verifies user has admin role via `isAdmin()` method
- Redirects to login if not authenticated
- Returns 403 error if not admin

**Usage:**
```php
Route::middleware(['auth', 'admin'])->group(function () {
    // Admin routes
});
```

### 3. CheckAuctionAccess (Legacy)
**Purpose:** Basic auction access control (being replaced by CheckLiveAuctionAccess)
**Location:** `app/Http/Middleware/CheckAuctionAccess.php`
**Status:** Legacy - Use CheckLiveAuctionAccess for new implementations

**Access Levels:**
1. **Full Access:** League organizers and admins
2. **Bidding Access:** Team owners in the league
3. **Auctioneer Access:** Users assigned as auctioneers for teams

**Usage:**
```php
Route::middleware(['auction.access'])->group(function () {
    Route::get('/auction/{league}', [AuctionController::class, 'index']);
});
```

### 4. CheckLeagueOrganizer
**Purpose:** Restricts league management to organizers
**Location:** `app/Http/Middleware/CheckLeagueOrganizer.php`

**Access Control:**
- League creation: Any authenticated user
- League management: Only organizers and admins

**How it works:**
- Allows access to index/create routes for all users
- For specific league routes, checks organizer status
- Uses `isOrganizerForLeague()` method

**Usage:**
```php
Route::middleware(['league.organizer'])->group(function () {
    Route::resource('leagues', LeagueController::class)->except(['index', 'show']);
});
```

### 5. CheckLeagueViewer
**Purpose:** Basic authentication for league viewing
**Location:** `app/Http/Middleware/CheckLeagueViewer.php`

**How it works:**
- Simple authentication check
- Allows any authenticated user to view leagues
- Public access to league information

**Usage:**
```php
Route::middleware(['league.viewer'])->group(function () {
    Route::get('/leagues', [LeagueController::class, 'index']);
    Route::get('/leagues/{league}', [LeagueController::class, 'show']);
});
```

### 6. CheckTeamOwner
**Purpose:** Restricts team-related actions to team owners
**Location:** `app/Http/Middleware/CheckTeamOwner.php`

**Access Control:**
- Team owners in the specific league
- League organizers (override)
- Admins (override)

**How it works:**
- Checks if user owns a team in the league
- Queries through `league_teams → team → team_owners` relationship
- Allows organizers and admins as fallback

**Usage:**
```php
Route::middleware(['team.owner'])->group(function () {
    Route::post('/leagues/{league}/register-team', [TeamController::class, 'register']);
});
```

## User Role Methods

### isAdmin()
```php
public function isAdmin(): bool
{
    return $this->userRoles()->whereHas('role', function($query) {
        $query->where('name', 'admin');
    })->exists();
}
```

### isOrganizerForLeague($leagueId)
```php
public function isOrganizerForLeague($leagueId): bool
{
    return $this->organizedLeagues()
        ->where('league_id', $leagueId)
        ->where('status', 'approved')
        ->exists();
}
```

## Middleware Registration

In `bootstrap/app.php`:
```php
protected $middlewareAliases = [
    'admin' => \App\Http\Middleware\CheckAdminAccess::class,
    'auction.access' => \App\Http\Middleware\CheckAuctionAccess::class,
    'live.auction' => \App\Http\Middleware\CheckLiveAuctionAccess::class,
    'league.organizer' => \App\Http\Middleware\CheckLeagueOrganizer::class,
    'league.viewer' => \App\Http\Middleware\CheckLeagueViewer::class,
    'team.owner' => \App\Http\Middleware\CheckTeamOwner::class,
];
```

## Permission Hierarchy

```
Admin (Highest)
├── Full system access
├── Can manage all leagues
└── Must be assigned organizer/auctioneer for auction access

League Organizer
├── Can manage their leagues
├── Full auction control (select players, mark sold/unsold)
├── Can bid ONLY if assigned as auctioneer
└── Cannot bid without auctioneer assignment

Team Owner / Auctioneer
├── Can bid for assigned team only
├── View auction status and own team wallet
├── Cannot select players or mark sold/unsold
└── Cannot bid for other teams

Regular User (Lowest)
├── Can view public league information
├── Can register as player
└── Cannot access management features
```

## Error Handling

**Authentication Errors:**
- Redirects to login page with error message
- Preserves intended URL for post-login redirect

**Authorization Errors:**
- Returns 403 HTTP status
- Shows descriptive error message
- Prevents unauthorized access attempts

**Missing Resource Errors:**
- Returns 404 HTTP status
- Handles cases where league/team not found

## Services

### AuctionAccessService
**Purpose:** Centralized auction access control logic
**Location:** `app/Services/AuctionAccessService.php`

**Key Methods:**
- `isApprovedOrganizer($userId, $leagueId)`: Check organizer status
- `isActiveAuctioneer($userId, $leagueId)`: Check auctioneer assignment
- `isTeamOwnerWithBiddingRights($userId, $leagueId)`: Check team ownership
- `getUserAuctionRole($userId, $leagueId)`: Get user's role (organizer|auctioneer|both|none)
- `canUserAccessAuction($userId, $leagueId)`: Master access check method
- `validateAuctionStart($leagueId)`: Pre-auction validation
- `getAuctioneersList($leagueId)`: Get all auctioneers for league

## Policies

### AuctionPolicy
**Purpose:** Laravel authorization for auction actions
**Location:** `app/Policies/AuctionPolicy.php`

**Methods:**
- `selectPlayer(User, League)`: Authorize player selection
- `markSoldUnsold(User, League)`: Authorize sold/unsold actions
- `placeBid(User, League)`: Authorize bidding
- `startAuction(User, League)`: Authorize auction start with validation
- `viewAuctionPanel(User, League)`: Authorize panel access

**Usage in Controllers:**
```php
$this->authorize('selectPlayer', $league);
$this->authorize('placeBid', $league);
```

## Audit Trail

### AuctionLog Model
**Purpose:** Track all auction actions for security and debugging
**Location:** `app/Models/AuctionLog.php`
**Table:** `auction_logs`

**Logged Actions:**
- Auction access attempts (success/failure)
- Player selection changes
- Sold/Unsold actions
- Bid placements
- Auction start/end events
- Access denials

**Usage:**
```php
AuctionLog::logAction(
    $leagueId,
    $userId,
    'player_sold',
    'LeaguePlayer',
    $playerId,
    ['amount' => $bidAmount, 'team_id' => $teamId]
);
```