<?php

namespace Database\Seeders;

use App\Models\League;
use App\Models\LeaguePlayer;
use App\Models\LeagueTeam;
use App\Models\User;
use App\Models\GameRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeaguePlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the default league
        $league = League::where('is_default', true)->first();
        
        if (!$league) {
            $this->command->error('No default league found. Please run the main seeder first.');
            return;
        }

        // Get league teams
        $leagueTeams = LeagueTeam::where('league_id', $league->id)->get();

        if ($leagueTeams->isEmpty()) {
            $this->command->error('No league teams found. Please run LeagueTeamSeeder first.');
            return;
        }

        // Get all players (users with role_id not null)
        $players = User::whereNotNull('role_id')->get();

        if ($players->isEmpty()) {
            $this->command->error('No players found. Please run PlayerSeeder first.');
            return;
        }

        // Shuffle players for random distribution
        $shuffledPlayers = $players->shuffle();

        // Base prices array
        $basePrices = [1000, 1500, 2000, 2500, 3000, 3500, 4000, 5000];
        
        // First, create available players without team assignment for auction
        foreach ($shuffledPlayers as $player) {
            // Create league player without team assignment (for auction)
            LeaguePlayer::create([
                'league_team_id' => null, // No team assigned initially
                'user_id' => $player->id,
                'retention' => false, // Default retention is false
                'status' => 'available', // All players are available
                'base_price' => $basePrices[array_rand($basePrices)],
            ]);
        }
        
        // If retention is enabled, assign retention players to teams
        if ($league->retention) {
            $this->command->info("Assigning retention players to teams...");
            
            // Reset player index
            $playerIndex = 0;
            
            foreach ($leagueTeams as $leagueTeam) {
                // Number of retention players per team
                $retentionCount = min($league->retention_players ?? 2, 
                                    ceil($shuffledPlayers->count() / $leagueTeams->count()));
                
                for ($i = 0; $i < $retentionCount && $playerIndex < $shuffledPlayers->count(); $i++) {
                    $player = $shuffledPlayers[$playerIndex];
                    
                    // Find the player in the league players table
                    $leaguePlayer = LeaguePlayer::where('user_id', $player->id)
                                               ->where('league_team_id', null)
                                               ->first();
                    
                    if ($leaguePlayer) {
                        // Update to make this a retention player
                        $leaguePlayer->update([
                            'league_team_id' => $leagueTeam->id,
                            'retention' => true, // Mark as retention player
                            'status' => 'sold', // Player is already sold to this team
                        ]);
                    }
                    
                    $playerIndex++;
                }
            }
        }

        $this->command->info('League players seeded successfully!');
        $this->command->info("Created " . $shuffledPlayers->count() . " league players for auction");
        if ($league->retention) {
            $this->command->info("Assigned retention players to {$leagueTeams->count()} teams in league: {$league->name}");
        }
    }
}
