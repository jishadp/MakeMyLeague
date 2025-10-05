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
        // Get all games
        $cricketGame = Game::where('name', 'Cricket')->first();
        $footballGame = Game::where('name', 'Football')->first();
        $badmintonGame = Game::where('name', 'Badminton')->first();
        $tableTennisGame = Game::where('name', 'Table Tennis')->first();
        
        if (!$cricketGame || !$footballGame || !$badmintonGame || !$tableTennisGame) {
            $this->command->error('Games not found. Please run DatabaseSeeder first.');
            return;
        }
        
        // Define cricket positions
        $cricketPositions = [
            'Batter',
            'Bowler',
            'All-Rounder',
            'Wicket-Keeper Batter'
        ];
        
        // Define football positions
        $footballPositions = [
            'Goalkeeper',
            'Defender',
            'Midfielder',
            'Forward',
            'Wing-Back',
            'Striker'
        ];
        
        // Define badminton positions
        $badmintonPositions = [
            'Singles Player',
            'Doubles Player',
            'Mixed Doubles Player',
            'All-Round Player'
        ];
        
        // Define table tennis positions
        $tableTennisPositions = [
            'Singles Player',
            'Doubles Player',
            'Mixed Doubles Player',
            'All-Round Player'
        ];
        
        // Create cricket positions
        foreach ($cricketPositions as $positionName) {
            GamePosition::create([
                'name' => $positionName,
                'game_id' => $cricketGame->id
            ]);
            $this->command->info("Created cricket position: {$positionName}");
        }
        
        // Create football positions
        foreach ($footballPositions as $positionName) {
            GamePosition::create([
                'name' => $positionName,
                'game_id' => $footballGame->id
            ]);
            $this->command->info("Created football position: {$positionName}");
        }
        
        // Create badminton positions
        foreach ($badmintonPositions as $positionName) {
            GamePosition::create([
                'name' => $positionName,
                'game_id' => $badmintonGame->id
            ]);
            $this->command->info("Created badminton position: {$positionName}");
        }
        
        // Create table tennis positions
        foreach ($tableTennisPositions as $positionName) {
            GamePosition::create([
                'name' => $positionName,
                'game_id' => $tableTennisGame->id
            ]);
            $this->command->info("Created table tennis position: {$positionName}");
        }
    }
}
