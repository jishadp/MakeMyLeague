<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Player>
 */
class PlayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $iplPlayers = [
            'Rohit Sharma', 'Virat Kohli', 'MS Dhoni', 'KL Rahul', 'Hardik Pandya',
            'Jasprit Bumrah', 'Ravindra Jadeja', 'Shikhar Dhawan', 'Rishabh Pant',
            'Yuzvendra Chahal', 'Mohammed Shami', 'Bhuvneshwar Kumar', 'Suryakumar Yadav',
            'Ishan Kishan', 'Shreyas Iyer', 'Prithvi Shaw', 'Axar Patel', 'Kuldeep Yadav'
        ];
        
        $positions = ['Batsman', 'Bowler', 'All-rounder', 'Wicket-keeper'];

        return [
            'name' => fake()->randomElement($iplPlayers),
            'position' => fake()->randomElement($positions),
            'age' => fake()->numberBetween(20, 38),
            'stats_json' => [
                'matches' => fake()->numberBetween(10, 200),
                'runs' => fake()->numberBetween(100, 6000),
                'wickets' => fake()->numberBetween(0, 150),
                'average' => fake()->randomFloat(2, 15, 55)
            ]
        ];
    }
}
