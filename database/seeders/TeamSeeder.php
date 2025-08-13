<?php

namespace Database\Seeders;

use App\Models\Ground;
use App\Models\LocalBody;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the default user for owner_id and created_by
        $user = User::where('email', 'niyas@gmail.com')->first();

        if (!$user) {
            $this->command->error('Default user not found. Please run DatabaseSeeder first.');
            return;
        }

        // Get all grounds and local bodies for distribution
        $grounds = Ground::all();
        $localBodies = LocalBody::all();

        if ($grounds->isEmpty()) {
            $this->command->error('No grounds found. Please run GroundSeeder first.');
            return;
        }

        if ($localBodies->isEmpty()) {
            $this->command->error('No local bodies found. Please run LocationSeeder first.');
            return;
        }

        // Define team names with their colors
        $teams = [
            'Wayanad Warriors' => '#1e40af', // Blue
            'Kalpetta Kings' => '#b91c1c', // Red
            'Sulthan Bathery Strikers' => '#15803d', // Green
            'Mananthavady Mavericks' => '#7e22ce', // Purple
            'Meenangadi Marauders' => '#ea580c', // Orange
            'Pulpally Panthers' => '#0f766e', // Teal
            'Vythiri Vipers' => '#fbbf24', // Yellow
            'Ambalavayal Aces' => '#6b7280', // Gray
            'Meppadi Monarchs' => '#9d174d', // Pink
            'Padinjarathara Patriots' => '#991b1b', // Brown
        ];

        $index = 0;
        foreach ($teams as $name => $color) {
            // Cycle through grounds and local bodies if there are more teams than grounds
            $ground = $grounds[$index % count($grounds)];
            $localBody = $localBodies[$index % count($localBodies)];

            Team::create([
                'name' => $name,
                'owner_id' => $user->id,
                'logo' => null, // We don't have logos yet
                'home_ground_id' => $ground->id,
                'local_body_id' => $localBody->id,
                'created_by' => $user->id,
            ]);

            $this->command->info("Created team: {$name}");
            $index++;
        }
    }
}
