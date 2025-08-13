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
        $playerIndex = 0;

        // Base prices array
        $basePrices = [1000, 1500, 2000, 2500, 3000, 3500, 4000, 5000];

        foreach ($leagueTeams as $leagueTeam) {
            // Add players to each team (max_team_players limit)
            $playersPerTeam = min($league->max_team_players, 
                                 ceil($shuffledPlayers->count() / $leagueTeams->count()));
            
            for ($i = 0; $i < $playersPerTeam && $playerIndex < $shuffledPlayers->count(); $i++) {
                $player = $shuffledPlayers[$playerIndex];
                
                // Determine if player is retained (only if league has retention enabled)
                $isRetention = $league->retention && $i < $league->retention_players && rand(0, 1);
                
                // Determine status
                $status = 'pending';
                if ($leagueTeam->status === 'available') {
                    $statusOptions = ['pending', 'available', 'sold', 'unsold'];
                    $status = $statusOptions[array_rand($statusOptions)];
                }

                LeaguePlayer::create([
                    'league_team_id' => $leagueTeam->id,
                    'user_id' => $player->id,
                    'retention' => $isRetention,
                    'status' => $status,
                    'base_price' => $basePrices[array_rand($basePrices)],
                ]);

                $playerIndex++;
            }
        }

        $this->command->info('League players seeded successfully!');
        $this->command->info("Added players to {$leagueTeams->count()} teams in league: {$league->name}");
    }
}
