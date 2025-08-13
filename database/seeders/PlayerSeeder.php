<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PlayerSeeder extends Seeder
{
    public function run(): void
    {
        $iplPlayers = [
            ['name' => 'Rohit Sharma', 'position' => 'Batsman', 'age' => 36, 'team' => 'Mumbai Indians'],
            ['name' => 'Virat Kohli', 'position' => 'Batsman', 'age' => 35, 'team' => 'Royal Challengers Bangalore'],
            ['name' => 'MS Dhoni', 'position' => 'Wicket-keeper', 'age' => 42, 'team' => 'Chennai Super Kings'],
            ['name' => 'KL Rahul', 'position' => 'Wicket-keeper', 'age' => 31, 'team' => 'Punjab Kings'],
            ['name' => 'Hardik Pandya', 'position' => 'All-rounder', 'age' => 30, 'team' => 'Mumbai Indians'],
            ['name' => 'Jasprit Bumrah', 'position' => 'Bowler', 'age' => 30, 'team' => 'Mumbai Indians'],
            ['name' => 'Ravindra Jadeja', 'position' => 'All-rounder', 'age' => 35, 'team' => 'Chennai Super Kings'],
            ['name' => 'Shikhar Dhawan', 'position' => 'Batsman', 'age' => 38, 'team' => 'Punjab Kings'],
            ['name' => 'Rishabh Pant', 'position' => 'Wicket-keeper', 'age' => 26, 'team' => 'Delhi Capitals'],
            ['name' => 'Yuzvendra Chahal', 'position' => 'Bowler', 'age' => 33, 'team' => 'Rajasthan Royals']
        ];

        $teams = Team::all()->keyBy('name');
        
        foreach ($iplPlayers as $playerData) {
            $team = $teams->get($playerData['team']);
            Player::create([
                'name' => $playerData['name'],
                'position' => $playerData['position'],
                'age' => $playerData['age'],
                'team_id' => $team?->id,
                'stats_json' => [
                    'matches' => rand(50, 200),
                    'runs' => rand(1000, 6000),
                    'wickets' => rand(0, 150),
                    'average' => round(rand(2000, 5500) / 100, 2)
                ]
            ]);
        }
    }
}
