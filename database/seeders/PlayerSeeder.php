<?php

namespace Database\Seeders;

use App\Models\GamePosition;
use App\Models\LocalBody;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all game roles for cricket
        $GamePositions = GamePosition::all();

        if ($GamePositions->isEmpty()) {
            $this->command->error('No game roles found. Please run GamePositionSeeder first.');
            return;
        }

        // Get all local bodies for distribution
        $localBodies = LocalBody::all();

        if ($localBodies->isEmpty()) {
            $this->command->error('No local bodies found. Please run LocationSeeder first.');
            return;
        }

        // Define player names and their roles
        $players = [
            // Batters
            ['name' => 'Virat Kohli', 'position_id' => 'Batter'],
            ['name' => 'Rohit Sharma', 'position_id' => 'Batter'],
            ['name' => 'KL Rahul', 'position_id' => 'Batter'],
            ['name' => 'Shubman Gill', 'position_id' => 'Batter'],
            ['name' => 'Shreyas Iyer', 'position_id' => 'Batter'],

            // Bowlers
            ['name' => 'Jasprit Bumrah', 'position_id' => 'Bowler'],
            ['name' => 'Mohammed Siraj', 'position_id' => 'Bowler'],
            ['name' => 'Yuzvendra Chahal', 'position_id' => 'Bowler'],
            ['name' => 'Ravichandran Ashwin', 'position_id' => 'Bowler'],
            ['name' => 'Kuldeep Yadav', 'position_id' => 'Bowler'],

            // All-Rounders
            ['name' => 'Hardik Pandya', 'position_id' => 'All-Rounder'],
            ['name' => 'Ravindra Jadeja', 'position_id' => 'All-Rounder'],
            ['name' => 'Axar Patel', 'position_id' => 'All-Rounder'],
            ['name' => 'Washington Sundar', 'position_id' => 'All-Rounder'],
            ['name' => 'Shardul Thakur', 'position_id' => 'All-Rounder'],

            // Wicket-Keeper Batters
            ['name' => 'Rishabh Pant', 'position_id' => 'Wicket-Keeper Batter'],
            ['name' => 'MS Dhoni', 'position_id' => 'Wicket-Keeper Batter'],
            ['name' => 'Ishan Kishan', 'position_id' => 'Wicket-Keeper Batter'],
            ['name' => 'Sanju Samson', 'position_id' => 'Wicket-Keeper Batter'],
            ['name' => 'Dinesh Karthik', 'position_id' => 'Wicket-Keeper Batter'],
        ];

        foreach ($players as $index => $playerData) {
            // Find the role ID
            $role = $GamePositions->where('name', $playerData['position_id'])->first();

            if (!$role) {
                $this->command->error("Role {$playerData['position_id']} not found. Skipping player {$playerData['name']}.");
                continue;
            }

            // Cycle through local bodies for distribution
            $localBody = $localBodies[$index % count($localBodies)];

            // Generate a unique email based on the player's name
            $email = strtolower(str_replace(' ', '.', $playerData['name'])) . '@example.com';

            // Generate a unique mobile number
            $mobile = '98765' . str_pad($index + 10, 5, '0', STR_PAD_LEFT);

            // Create the player
            $player = User::create([
                'name' => $playerData['name'],
                'email' => $email,
                'mobile' => $mobile,
                'pin' => Hash::make('1234'), // Simple PIN for all players
                'position_id' => $role->id,
                'local_body_id' => $localBody->id,
            ]);

            $this->command->info("Created player: {$player->name} ({$role->name}) - {$localBody->name}");
        }
    }
}
