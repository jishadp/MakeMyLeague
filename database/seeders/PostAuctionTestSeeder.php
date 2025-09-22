<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\League;
use App\Models\Team;
use App\Models\LeagueTeam;
use App\Models\LeaguePlayer;
use App\Models\Game;

class PostAuctionTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create organizer user (ID 1)
        $organizer = User::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Test Organizer',
                'email' => 'organizer@test.com',
                'mobile' => '1234567890',
                'pin' => bcrypt('1234'),
                'slug' => 'test-organizer'
            ]
        );

        // Assign organizer role
        \App\Models\UserRole::firstOrCreate([
            'user_id' => $organizer->id,
            'role_id' => 1
        ]);

        // Get or create game
        $game = Game::first() ?? Game::create([
            'name' => 'Cricket',
            'active' => true
        ]);

        // Create test league
        $league = League::create([
            'name' => 'Test Tournament League',
            'slug' => 'test-tournament-league',
            'game_id' => $game->id,
            'user_id' => $organizer->id,
            'season' => 1,
            'start_date' => now()->addDays(30),
            'end_date' => now()->addDays(60),
            'max_teams' => 4,
            'max_team_players' => 5,
            'team_reg_fee' => 1000,
            'player_reg_fee' => 500,
            'team_wallet_limit' => 50000,
            'status' => 'auction_completed'
        ]);

        // Create 4 teams
        $teamNames = ['Mumbai Warriors', 'Delhi Capitals', 'Chennai Kings', 'Bangalore Royals'];
        $teams = [];

        foreach ($teamNames as $teamName) {
            $team = Team::firstOrCreate(
                ['slug' => \Str::slug($teamName)],
                [
                    'name' => $teamName,
                    'owner_id' => $organizer->id,
                    'home_ground_id' => 1,
                    'local_body_id' => 1,
                    'created_by' => $organizer->id
                ]
            );

            // Create league team
            $leagueTeam = LeagueTeam::create([
                'league_id' => $league->id,
                'team_id' => $team->id,
                'status' => 'available',
                'wallet_balance' => 45000
            ]);

            $teams[] = $leagueTeam;
        }

        // Create 20 players (5 per team)
        $playerNames = [
            'Virat Kohli', 'Rohit Sharma', 'MS Dhoni', 'Hardik Pandya', 'Jasprit Bumrah',
            'KL Rahul', 'Rishabh Pant', 'Shikhar Dhawan', 'Bhuvneshwar Kumar', 'Yuzvendra Chahal',
            'Ravindra Jadeja', 'Mohammed Shami', 'Shreyas Iyer', 'Suryakumar Yadav', 'Ishan Kishan',
            'Axar Patel', 'Washington Sundar', 'Deepak Chahar', 'Prithvi Shaw', 'Devdutt Padikkal'
        ];

        foreach ($teams as $index => $leagueTeam) {
            for ($i = 0; $i < 5; $i++) {
                $playerIndex = ($index * 5) + $i;
                
                // Create user for player
                $player = User::firstOrCreate(
                    ['slug' => \Str::slug($playerNames[$playerIndex])],
                    [
                        'name' => $playerNames[$playerIndex],
                        'email' => strtolower(str_replace(' ', '.', $playerNames[$playerIndex])) . '@test.com',
                        'mobile' => '98765' . str_pad($playerIndex, 5, '0', STR_PAD_LEFT),
                        'pin' => bcrypt('1234')
                    ]
                );

                // Create league player
                LeaguePlayer::create([
                    'league_id' => $league->id,
                    'user_id' => $player->id,
                    'league_team_id' => $leagueTeam->id,
                    'base_price' => rand(1000, 5000),
                    'bid_price' => rand(2000, 8000),
                    'status' => 'sold',
                    'retention' => false
                ]);
            }
        }

        $this->command->info('Post-auction test data created successfully!');
        $this->command->info("League: {$league->name} (Status: {$league->status})");
        $this->command->info("Teams: 4 teams with 5 players each");
        $this->command->info("Organizer: {$organizer->name} (ID: {$organizer->id})");
    }
}