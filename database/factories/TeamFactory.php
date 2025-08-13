<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $iplTeams = [
            'Mumbai Indians',
            'Chennai Super Kings', 
            'Royal Challengers Bangalore',
            'Kolkata Knight Riders',
            'Delhi Capitals',
            'Punjab Kings',
            'Rajasthan Royals',
            'Sunrisers Hyderabad'
        ];

        return [
            'name' => fake()->randomElement($iplTeams),
            'country' => 'India',
            'logo_url' => fake()->imageUrl(200, 200, 'sports')
        ];
    }
}
