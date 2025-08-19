<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\GamePosition;
use Illuminate\Database\Seeder;

class GamePositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the Cricket game
        $cricketGame = Game::where('name', 'Cricket')->first();
        
        if (!$cricketGame) {
            $this->command->error('Cricket game not found. Please run DatabaseSeeder first.');
            return;
        }
        
        // Define cricket roles
        $cricketRoles = [
            'Batter',
            'Bowler',
            'All-Rounder',
            'Wicket-Keeper Batter'
        ];
        
        // Create the roles
        foreach ($cricketRoles as $roleName) {
            GamePosition::create([
                'name' => $roleName,
                'game_id' => $cricketGame->id
            ]);
            
            $this->command->info("Created game role: {$roleName}");
        }
    }
}
