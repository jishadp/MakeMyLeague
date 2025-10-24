# Live Auction Access Control - Implementation Summary

## Overview
Professional implementation of granular access control for live auction functionality with strict role separation and comprehensive audit trail.

## Components Implemented

### 1. Middleware
**File:** `app/Http/Middleware/CheckLiveAuctionAccess.php`
- Granular permission checking (view, organizer, auctioneer)
- Integrates with AuctionAccessService
- Stores role and team info in request
- Detailed error responses

### 2. Service Layer
**File:** `app/Services/AuctionAccessService.php`
**New Methods Added:**
- `isApprovedOrganizer($userId, $leagueId)` - Organizer verification
- `isActiveAuctioneer($userId, $leagueId)` - Auctioneer verification
- `isTeamOwnerWithBiddingRights($userId, $leagueId)` - Owner verification
- `getUserAuctionRole($userId, $leagueId)` - Role determination
- `canUserAccessAuction($userId, $leagueId)` - Master access check
- `validateAuctionStart($leagueId)` - Pre-auction validation
- `getAuctioneersList($leagueId)` - Auctioneer list for UI

### 3. Policy
**File:** `app/Policies/AuctionPolicy.php`
- `selectPlayer()` - Organizer only
- `markSoldUnsold()` - Organizer only
- `placeBid()` - Auctioneer/Both only
- `startAuction()` - Organizer with validation
- `viewAuctionPanel()` - Any authorized role

### 4. Audit Trail
**Migration:** `database/migrations/2025_10_22_125817_create_auction_logs_table.php`
**Model:** `app/Models/AuctionLog.php`
- Tracks all auction actions
- Stores user, action type, target, metadata
- IP address and user agent logging

### 5. Configuration
**File:** `bootstrap/app.php`
- Registered `live.auction` middleware alias

**File:** `app/Providers/AppServiceProvider.php`
- Registered AuctionPolicy

## Access Control Matrix

### Organizer Permissions
✓ Select player for bidding
✓ Mark player as Sold/Unsold
✓ Start/End auction
✓ View all bids
✓ Can bid IF assigned as auctioneer
✗ Cannot bid without auctioneer assignment

### Auctioneer Permissions
✓ Place bids for assigned team only
✓ View auction status
✓ View own team wallet
✗ Cannot select players
✗ Cannot mark Sold/Unsold
✗ Cannot bid for other teams

### Admin Restrictions
✗ Must be assigned as organizer OR auctioneer
✗ No blanket access without role assignment

## Route Protection

### Updated Routes (web.php)
```php
// Organizer-only routes
Route::post('accept-bid')->middleware('live.auction:organizer');
Route::post('complete')->middleware('live.auction:organizer');
Route::post('skip-player')->middleware('live.auction:organizer');

// Auctioneer routes
Route::post('place-bid')->middleware('live.auction:auctioneer');

// View routes (any authorized role)
Route::get('/')->middleware('live.auction:view');
Route::get('recent-bids/{league}')->middleware('live.auction:view');
```

## Usage Examples

### In Controllers
```php
// Check authorization using policy
$this->authorize('selectPlayer', $league);
$this->authorize('placeBid', $league);

// Access role from request
$role = $request->input('auction_role');
$teamId = $request->input('auction_team_id');

// Log actions
AuctionLog::logAction(
    $league->id,
    auth()->id(),
    'player_sold',
    'LeaguePlayer',
    $player->id,
    ['amount' => $amount, 'team_id' => $teamId]
);
```

### In Blade Templates
```php
@can('selectPlayer', $league)
    <!-- Organizer controls -->
@endcan

@can('placeBid', $league)
    <!-- Bidding panel -->
@endcan
```

### Validation Before Auction Start
```php
$validation = app(AuctionAccessService::class)->validateAuctionStart($league->id);

if (!$validation['valid']) {
    return response()->json([
        'errors' => $validation['errors'],
        'warnings' => $validation['warnings']
    ], 422);
}
```

## Error Responses

### Organizer Access Required
```json
{
    "message": "Organizer Access Required",
    "details": "Only approved league organizers can perform this action.",
    "required_role": "organizer",
    "your_role": "auctioneer"
}
```

### Auctioneer Access Required
```json
{
    "message": "Auctioneer Access Required",
    "details": "You must be assigned as an auctioneer or be a team owner to place bids.",
    "required_role": "auctioneer",
    "your_role": "organizer"
}
```

### Admin Without Role
```json
{
    "message": "Admin Role Assignment Required",
    "details": "Admins must be explicitly assigned as organizer or auctioneer to access live auctions.",
    "reason": "Prevents unauthorized interference in league operations"
}
```

## Testing Scenarios

### Scenario 1: Organizer Only
- ✓ Can select players
- ✓ Can mark sold/unsold
- ✓ Can start auction
- ✗ Cannot bid (unless also auctioneer)

### Scenario 2: Auctioneer Only
- ✓ Can bid for assigned team
- ✗ Cannot bid for other teams
- ✗ Cannot select players
- ✗ Cannot see organizer controls

### Scenario 3: Organizer + Auctioneer (Dual Role)
- ✓ Can do both
- ✓ UI shows both sections
- ✓ Can bid for assigned team only

### Scenario 4: Admin Edge Cases
- Admin + Organizer → Full organizer access ✓
- Admin + Auctioneer → Bidding access only ✓
- Admin alone → Denied ✓

## Database Schema

### auction_logs Table
```sql
id                  BIGINT UNSIGNED PRIMARY KEY
league_id           BIGINT UNSIGNED (FK to leagues)
user_id             BIGINT UNSIGNED (FK to users)
action_type         VARCHAR(255)
target_type         VARCHAR(255) NULLABLE
target_id           BIGINT UNSIGNED NULLABLE
metadata            JSON NULLABLE
ip_address          VARCHAR(45) NULLABLE
user_agent          TEXT NULLABLE
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX (league_id, created_at)
INDEX (user_id, action_type)
```

## Migration Steps

1. Run migration:
```bash
php artisan migrate
```

2. Clear cache:
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

3. Test access control:
- Test as organizer
- Test as auctioneer
- Test as admin without role
- Test dual role scenarios

## Key Principles Applied

✅ **Separation of Concerns** - Middleware, Service, Policy layers
✅ **DRY Principle** - Centralized access checks
✅ **Security First** - Multiple validation layers
✅ **Audit Trail** - Complete action logging
✅ **Clear Error Messages** - Helpful user feedback
✅ **Scalability** - Easy to add new roles
✅ **Maintainability** - Well-documented code

## Next Steps

1. Update frontend to use new access control
2. Add validation popup before auction start
3. Implement WebSocket channel authorization
4. Add real-time role-based UI updates
5. Create admin panel for access management
6. Add comprehensive test suite

## Documentation

See `middleware_documentation.md` for complete middleware reference.
