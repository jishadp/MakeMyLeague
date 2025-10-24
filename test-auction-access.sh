#!/bin/bash

echo "🔍 Testing Live Auction Access Control Implementation"
echo "=================================================="
echo ""

# Check if migration ran
echo "✓ Checking auction_logs table..."
php artisan db:table auction_logs --columns 2>/dev/null && echo "  ✅ auction_logs table exists" || echo "  ❌ auction_logs table missing"
echo ""

# Check middleware registration
echo "✓ Checking middleware registration..."
php artisan route:list --columns=uri,middleware 2>/dev/null | grep -q "live.auction" && echo "  ✅ live.auction middleware registered" || echo "  ❌ live.auction middleware not found"
echo ""

# Check routes
echo "✓ Checking protected routes..."
php artisan route:list 2>/dev/null | grep -q "auction.*place-bid" && echo "  ✅ Auction routes exist" || echo "  ❌ Auction routes missing"
echo ""

# Check files exist
echo "✓ Checking created files..."
[ -f "app/Http/Middleware/CheckLiveAuctionAccess.php" ] && echo "  ✅ CheckLiveAuctionAccess middleware" || echo "  ❌ Middleware missing"
[ -f "app/Policies/AuctionPolicy.php" ] && echo "  ✅ AuctionPolicy" || echo "  ❌ Policy missing"
[ -f "app/Models/AuctionLog.php" ] && echo "  ✅ AuctionLog model" || echo "  ❌ Model missing"
[ -f "public/js/auction-access.js" ] && echo "  ✅ auction-access.js" || echo "  ❌ JavaScript missing"
echo ""

# Check service methods
echo "✓ Checking AuctionAccessService methods..."
grep -q "isApprovedOrganizer" app/Services/AuctionAccessService.php && echo "  ✅ isApprovedOrganizer()" || echo "  ❌ Method missing"
grep -q "canUserAccessAuction" app/Services/AuctionAccessService.php && echo "  ✅ canUserAccessAuction()" || echo "  ❌ Method missing"
grep -q "validateAuctionStart" app/Services/AuctionAccessService.php && echo "  ✅ validateAuctionStart()" || echo "  ❌ Method missing"
echo ""

# Check blade templates
echo "✓ Checking Blade template updates..."
grep -q "@can('selectPlayer'" resources/views/auction/index.blade.php && echo "  ✅ @can directives added" || echo "  ❌ @can directives missing"
grep -q "user-role" resources/views/auction/index.blade.php && echo "  ✅ Role inputs added" || echo "  ❌ Role inputs missing"
echo ""

# Check WebSocket channels
echo "✓ Checking WebSocket channel authorization..."
grep -q "auction.{leagueId}" routes/channels.php && echo "  ✅ Auction channel registered" || echo "  ❌ Channel missing"
echo ""

echo "=================================================="
echo "✅ Implementation verification complete!"
echo ""
echo "Next steps:"
echo "1. Clear caches: php artisan optimize:clear"
echo "2. Test as organizer user"
echo "3. Test as auctioneer user"
echo "4. Test as admin without role"
echo "5. Check audit logs in auction_logs table"
echo ""
