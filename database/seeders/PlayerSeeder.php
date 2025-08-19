<?php

namespace Database\Seeders;

use App\Models\GameRole;
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
        $gameRoles = GameRole::all();

        if ($gameRoles->isEmpty()) {
            $this->command->error('No game roles found. Please run GameRoleSeeder first.');
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
            ['name' => 'Virat Kohli', 'role' => 'Batter'],
            ['name' => 'Rohit Sharma', 'role' => 'Batter'],
            ['name' => 'KL Rahul', 'role' => 'Batter'],
            ['name' => 'Shubman Gill', 'role' => 'Batter'],
            ['name' => 'Shreyas Iyer', 'role' => 'Batter'],

            // Bowlers
            ['name' => 'Jasprit Bumrah', 'role' => 'Bowler'],
            ['name' => 'Mohammed Siraj', 'role' => 'Bowler'],
            ['name' => 'Yuzvendra Chahal', 'role' => 'Bowler'],
            ['name' => 'Ravichandran Ashwin', 'role' => 'Bowler'],
            ['name' => 'Kuldeep Yadav', 'role' => 'Bowler'],

            // All-Rounders
            ['name' => 'Hardik Pandya', 'role' => 'All-Rounder'],
            ['name' => 'Ravindra Jadeja', 'role' => 'All-Rounder'],
            ['name' => 'Axar Patel', 'role' => 'All-Rounder'],
            ['name' => 'Washington Sundar', 'role' => 'All-Rounder'],
            ['name' => 'Shardul Thakur', 'role' => 'All-Rounder'],

            // Wicket-Keeper Batters
            ['name' => 'Rishabh Pant', 'role' => 'Wicket-Keeper Batter'],
            ['name' => 'MS Dhoni', 'role' => 'Wicket-Keeper Batter'],
            ['name' => 'Ishan Kishan', 'role' => 'Wicket-Keeper Batter'],
            ['name' => 'Sanju Samson', 'role' => 'Wicket-Keeper Batter'],
            ['name' => 'Dinesh Karthik', 'role' => 'Wicket-Keeper Batter'],
        ];

        foreach ($players as $index => $playerData) {
            // Find the role ID
            $role = $gameRoles->where('name', $playerData['role'])->first();

            if (!$role) {
                $this->command->error("Role {$playerData['role']} not found. Skipping player {$playerData['name']}.");
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
                'role_id' => $role->id,
                'local_body_id' => $localBody->id,
            ]);

            $this->command->info("Created player: {$player->name} ({$role->name}) - {$localBody->name}");
        }
    }
}
