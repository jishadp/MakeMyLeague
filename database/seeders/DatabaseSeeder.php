<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\League;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default user
        $user = User::create([
            'name'  => 'Niyas KM',
            'email'  => 'niyas@gmail.com',
            'mobile'  => '9876567876',
            'pin'   => bcrypt('4334')
        ]);

        // Create default game (Cricket)
        $game = Game::create([
            'name' => 'Cricket',
            'description' => 'Twenty20 cricket leagues with auction-based team formation',
            'active' => true
        ]);

        // Create a default league
        // League::create([
        //     'name' => 'IPL 2025',
        //     'slug' => 'ipl-2025',
        //     'game_id' => $game->id,
        //     'user_id' => $user->id,
        //     'season' => 1,
        //     'start_date' => '2025-09-01',
        //     'end_date' => '2025-11-30',
        //     'max_teams' => 8,
        //     'max_team_players' => 15,
        //     'team_reg_fee' => 1000,
        //     'player_reg_fee' => 200,
        //     'retention' => true,
        //     'retention_players' => 3,
        //     'team_wallet_limit' => 10000,
        //     'is_default' => true,
        //     'status' => 'active'
        // ]);

        // Run location seeder
        $this->call(LocationSeeder::class);

        // Run ground seeder
        //$this->call(GroundSeeder::class);

        // Run team seeder
        //$this->call(TeamSeeder::class);

        // Run game role seeder
        $this->call(GameRoleSeeder::class);

        // Run player seeder
        // $this->call(PlayerSeeder::class);

        // Run league team seeder
        // $this->call(LeagueTeamSeeder::class);

        // Run league player seeder
        // $this->call(LeaguePlayerSeeder::class);

        // Add grounds to the default league (after grounds are seeded)
        // $defaultLeague = League::where('is_default', true)->first();
        // if ($defaultLeague) {
        //     // Get some random Wayanad grounds
        //     $groundIds = \App\Models\Ground::where('district_id', function($query) {
        //         $query->select('id')
        //             ->from('districts')
        //             ->where('name', 'Wayanad')
        //             ->first();
        //     })->take(3)->pluck('id')->toArray();

        //     // Update the league with ground_ids and local body
        //     if (!empty($groundIds)) {
        //         $firstGround = \App\Models\Ground::find($groundIds[0]);
        //         $defaultLeague->update([
        //             'ground_ids' => $groundIds,
        //             'localbody_id' => $firstGround->localbody_id,
        //             'venue_details' => 'Main Stadium Area, Near Bus Stand'
        //         ]);
        //     }
        // }
    }
}
