<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\League;
use App\Models\LeagueTeam;
use App\Models\LeaguePlayer;
use App\Models\Team;
use App\Models\LeagueOrganizer;
use App\Models\TeamAuctioneer;
use App\Models\Role;
use App\Services\AuctionAccessService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuctionAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected $auctionAccessService;
    protected $league;
    protected $leagueTeam;
    protected $leaguePlayer;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->auctionAccessService = app(AuctionAccessService::class);
        
        // Create test league
        $this->league = League::factory()->create([
            'name' => 'Test League',
            'status' => 'active'
        ]);
        
        // Create test team
        $team = Team::factory()->create(['name' => 'Test Team']);
        
        $this->leagueTeam = LeagueTeam::create([
            'league_id' => $this->league->id,
            'team_id' => $team->id,
            'wallet_balance' => 10000,
            'status' => 'available'
        ]);
        
        // Create test player
        $player = User::factory()->create(['name' => 'Test Player']);
        
        $this->leaguePlayer = LeaguePlayer::create([
            'league_id' => $this->league->id,
            'user_id' => $player->id,
            'base_price' => 1000,
            'status' => 'auctioning'
        ]);
    }

    /** @test */
    public function organizer_can_access_auction_panel()
    {
        $organizer = User::factory()->create();
        
        LeagueOrganizer::create([
            'league_id' => $this->league->id,
            'user_id' => $organizer->id,
            'status' => 'approved'
        ]);
        
        $accessCheck = $this->auctionAccessService->canUserAccessAuction($organizer->id, $this->league->id);
        
        $this->assertTrue($accessCheck['allowed']);
        $this->assertEquals('organizer', $accessCheck['role']);
        
        echo "✅ Test 1: Organizer can access auction panel\n";
    }

    /** @test */
    public function organizer_cannot_bid_without_auctioneer_role()
    {
        $organizer = User::factory()->create();
        
        LeagueOrganizer::create([
            'league_id' => $this->league->id,
            'user_id' => $organizer->id,
            'status' => 'approved'
        ]);
        
        $role = $this->auctionAccessService->getUserAuctionRole($organizer->id, $this->league->id);
        
        $this->assertEquals('organizer', $role);
        
        // Organizer should not be able to bid
        $this->actingAs($organizer)
            ->post(route('auction.call'), [
                'league_id' => $this->league->id,
                'player_id' => $this->leaguePlayer->user_id,
                'base_price' => 1000,
                'increment' => 100,
                'league_player_id' => $this->leaguePlayer->id
            ])
            ->assertStatus(403);
        
        echo "✅ Test 2: Organizer cannot bid without auctioneer assignment\n";
    }

    /** @test */
    public function auctioneer_can_bid_but_cannot_manage()
    {
        $auctioneer = User::factory()->create();
        
        TeamAuctioneer::create([
            'league_team_id' => $this->leagueTeam->id,
            'auctioneer_id' => $auctioneer->id,
            'league_id' => $this->league->id,
            'status' => 'active'
        ]);
        
        $accessCheck = $this->auctionAccessService->canUserAccessAuction($auctioneer->id, $this->league->id);
        
        $this->assertTrue($accessCheck['allowed']);
        $this->assertEquals('auctioneer', $accessCheck['role']);
        $this->assertEquals($this->leagueTeam->id, $accessCheck['team_id']);
        
        // Auctioneer should not be able to mark sold
        $this->actingAs($auctioneer)
            ->post(route('auction.sold'), [
                'league_player_id' => $this->leaguePlayer->id,
                'team_id' => $this->leagueTeam->id
            ])
            ->assertStatus(403);
        
        echo "✅ Test 3: Auctioneer can access but cannot manage auction\n";
    }

    /** @test */
    public function user_with_both_roles_has_full_access()
    {
        $user = User::factory()->create();
        
        // Assign as organizer
        LeagueOrganizer::create([
            'league_id' => $this->league->id,
            'user_id' => $user->id,
            'status' => 'approved'
        ]);
        
        // Assign as auctioneer
        TeamAuctioneer::create([
            'league_team_id' => $this->leagueTeam->id,
            'auctioneer_id' => $user->id,
            'league_id' => $this->league->id,
            'status' => 'active'
        ]);
        
        $accessCheck = $this->auctionAccessService->canUserAccessAuction($user->id, $this->league->id);
        
        $this->assertTrue($accessCheck['allowed']);
        $this->assertEquals('both', $accessCheck['role']);
        $this->assertEquals($this->leagueTeam->id, $accessCheck['team_id']);
        
        echo "✅ Test 4: User with both roles has full access\n";
    }

    /** @test */
    public function admin_without_role_is_denied()
    {
        $admin = User::factory()->create();
        
        // Create admin role if not exists
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $admin->roles()->attach($adminRole->id);
        
        $accessCheck = $this->auctionAccessService->canUserAccessAuction($admin->id, $this->league->id);
        
        $this->assertFalse($accessCheck['allowed']);
        $this->assertEquals('admin_no_role', $accessCheck['role']);
        $this->assertStringContainsString('must be assigned', $accessCheck['message']);
        
        echo "✅ Test 5: Admin without role assignment is denied\n";
    }

    /** @test */
    public function regular_user_without_role_is_denied()
    {
        $user = User::factory()->create();
        
        $accessCheck = $this->auctionAccessService->canUserAccessAuction($user->id, $this->league->id);
        
        $this->assertFalse($accessCheck['allowed']);
        $this->assertEquals('none', $accessCheck['role']);
        
        echo "✅ Test 6: Regular user without role is denied\n";
    }

    /** @test */
    public function audit_log_is_created_on_actions()
    {
        $organizer = User::factory()->create();
        
        LeagueOrganizer::create([
            'league_id' => $this->league->id,
            'user_id' => $organizer->id,
            'status' => 'approved'
        ]);
        
        \App\Models\AuctionLog::logAction(
            $this->league->id,
            $organizer->id,
            'test_action',
            'LeaguePlayer',
            $this->leaguePlayer->id,
            ['test' => 'data']
        );
        
        $this->assertDatabaseHas('auction_logs', [
            'league_id' => $this->league->id,
            'user_id' => $organizer->id,
            'action_type' => 'test_action'
        ]);
        
        echo "✅ Test 7: Audit log is created successfully\n";
    }
}
