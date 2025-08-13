<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $teams = [
            ['name' => 'Mumbai Indians', 'country' => 'India'],
            ['name' => 'Chennai Super Kings', 'country' => 'India'],
            ['name' => 'Royal Challengers Bangalore', 'country' => 'India'],
            ['name' => 'Kolkata Knight Riders', 'country' => 'India'],
            ['name' => 'Delhi Capitals', 'country' => 'India'],
            ['name' => 'Punjab Kings', 'country' => 'India'],
            ['name' => 'Rajasthan Royals', 'country' => 'India'],
            ['name' => 'Sunrisers Hyderabad', 'country' => 'India']
        ];

        foreach ($teams as $team) {
            Team::create($team);
        }
    }
}
