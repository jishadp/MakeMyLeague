<?php

namespace Database\Seeders;

use App\Models\League;
use App\Models\LeagueTeam;
use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeagueTeamSeeder extends Seeder
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

        // Get all teams
        $teams = Team::all();

        if ($teams->isEmpty()) {
            $this->command->error('No teams found. Please run TeamSeeder first.');
            return;
        }

        // Take only the first 8 teams for the league (as max_teams is 8)
        $teamsForLeague = $teams->take($league->max_teams);

        foreach ($teamsForLeague as $index => $team) {
            LeagueTeam::create([
                'league_id' => $league->id,
                'team_id' => $team->id,
                'status' => $index < 6 ? 'available' : 'pending', // First 6 teams available, rest pending
                'wallet_balance' => $league->team_wallet_limit, // Start with full wallet
            ]);
        }

        $this->command->info('League teams seeded successfully!');
        $this->command->info("Added {$teamsForLeague->count()} teams to league: {$league->name}");
    }
}
