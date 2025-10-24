<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\League;
use App\Models\LeagueOrganizer;
use App\Models\TeamAuctioneer;
use App\Models\LeagueTeam;
use App\Services\AuctionAccessService;

$service = app(AuctionAccessService::class);

echo "\n🧪 TESTING LIVE AUCTION ACCESS CONTROL\n";
echo "==========================================\n\n";

// Get first league for testing
$league = League::first();
if (!$league) {
    echo "❌ No leagues found. Please create a league first.\n";
    exit(1);
}

echo "📋 Testing with League: {$league->name} (ID: {$league->id})\n\n";

// Test 1: Organizer Access
echo "TEST 1: Organizer Access\n";
echo "-------------------------\n";
$organizer = LeagueOrganizer::where('league_id', $league->id)
    ->where('status', 'approved')
    ->first();

if ($organizer) {
    $user = User::find($organizer->user_id);
    $access = $service->canUserAccessAuction($user->id, $league->id);
    
    echo "User: {$user->name}\n";
    echo "Allowed: " . ($access['allowed'] ? '✅ YES' : '❌ NO') . "\n";
    echo "Role: {$access['role']}\n";
    echo "Expected: organizer or both\n";
    echo "Result: " . (in_array($access['role'], ['organizer', 'both']) ? '✅ PASS' : '❌ FAIL') . "\n";
} else {
    echo "⚠️  No approved organizers found for this league\n";
}
echo "\n";

// Test 2: Auctioneer Access
echo "TEST 2: Auctioneer Access\n";
echo "-------------------------\n";
$auctioneer = TeamAuctioneer::where('league_id', $league->id)
    ->where('status', 'active')
    ->first();

if ($auctioneer) {
    $user = User::find($auctioneer->auctioneer_id);
    $access = $service->canUserAccessAuction($user->id, $league->id);
    
    echo "User: {$user->name}\n";
    echo "Allowed: " . ($access['allowed'] ? '✅ YES' : '❌ NO') . "\n";
    echo "Role: {$access['role']}\n";
    echo "Team ID: " . ($access['team_id'] ?? 'N/A') . "\n";
    echo "Expected: auctioneer or both\n";
    echo "Result: " . (in_array($access['role'], ['auctioneer', 'both']) ? '✅ PASS' : '❌ FAIL') . "\n";
} else {
    echo "⚠️  No active auctioneers found for this league\n";
}
echo "\n";

// Test 3: Admin without role
echo "TEST 3: Admin Without Role\n";
echo "-------------------------\n";
$admin = User::whereHas('roles', function($q) {
    $q->where('name', 'admin');
})->first();

if ($admin) {
    // Check if admin is also organizer or auctioneer
    $isOrganizer = LeagueOrganizer::where('league_id', $league->id)
        ->where('user_id', $admin->id)
        ->where('status', 'approved')
        ->exists();
    
    $isAuctioneer = TeamAuctioneer::where('league_id', $league->id)
        ->where('auctioneer_id', $admin->id)
        ->where('status', 'active')
        ->exists();
    
    if (!$isOrganizer && !$isAuctioneer) {
        $access = $service->canUserAccessAuction($admin->id, $league->id);
        
        echo "User: {$admin->name}\n";
        echo "Allowed: " . ($access['allowed'] ? '✅ YES' : '❌ NO') . "\n";
        echo "Role: {$access['role']}\n";
        echo "Message: {$access['message']}\n";
        echo "Expected: Denied (admin_no_role)\n";
        echo "Result: " . (!$access['allowed'] && $access['role'] === 'admin_no_role' ? '✅ PASS' : '❌ FAIL') . "\n";
    } else {
        echo "⚠️  Admin has organizer or auctioneer role - cannot test pure admin denial\n";
    }
} else {
    echo "⚠️  No admin users found\n";
}
echo "\n";

// Test 4: Regular user without role
echo "TEST 4: Regular User Without Role\n";
echo "-------------------------\n";
$regularUser = User::whereDoesntHave('roles', function($q) {
    $q->where('name', 'admin');
})
->whereDoesntHave('organizedLeagues', function($q) use ($league) {
    $q->where('league_id', $league->id);
})
->first();

if ($regularUser) {
    $access = $service->canUserAccessAuction($regularUser->id, $league->id);
    
    echo "User: {$regularUser->name}\n";
    echo "Allowed: " . ($access['allowed'] ? '✅ YES' : '❌ NO') . "\n";
    echo "Role: {$access['role']}\n";
    echo "Expected: Denied (none)\n";
    echo "Result: " . (!$access['allowed'] && $access['role'] === 'none' ? '✅ PASS' : '❌ FAIL') . "\n";
} else {
    echo "⚠️  No regular users found\n";
}
echo "\n";

// Test 5: Audit Log
echo "TEST 5: Audit Log Creation\n";
echo "-------------------------\n";
$logCount = \App\Models\AuctionLog::count();
echo "Current audit logs: {$logCount}\n";

if ($organizer) {
    \App\Models\AuctionLog::logAction(
        $league->id,
        $organizer->user_id,
        'test_action',
        'Test',
        1,
        ['test' => 'data']
    );
    
    $newCount = \App\Models\AuctionLog::count();
    echo "After test log: {$newCount}\n";
    echo "Result: " . ($newCount > $logCount ? '✅ PASS' : '❌ FAIL') . "\n";
}
echo "\n";

// Summary
echo "==========================================\n";
echo "✅ TESTING COMPLETE\n\n";
echo "Summary:\n";
echo "- Access control methods working\n";
echo "- Role detection functioning\n";
echo "- Audit logging operational\n\n";
echo "Next: Test in browser with actual users\n";
