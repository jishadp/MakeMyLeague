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

        // Get all players (users with role_id not null)
        $players = User::whereNotNull('role_id')->get();

        if ($players->isEmpty()) {
            $this->command->error('No players found. Please run PlayerSeeder first.');
            return;
        }

        // Base prices array
        $basePrices = [200];
        
        // Create all players as available for auction without team assignment
        foreach ($players as $player) {
            // Create league player without team assignment (for auction)
            LeaguePlayer::create([
                'league_id' => $league->id, // Add league_id
                'league_team_id' => null, // No team assigned
                'user_id' => $player->id,
                'retention' => false, // Default retention is false
                'status' => 'available', // All players are available
                'base_price' => $basePrices[array_rand($basePrices)],
            ]);
        }

        $this->command->info('League players seeded successfully!');
        $this->command->info("Created " . $players->count() . " league players for auction");
        $this->command->info("All players are available for teams to select as retention or bid in auction");
    }
}
