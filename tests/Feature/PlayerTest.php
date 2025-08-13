<?php

namespace Tests\Feature;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_player(): void
    {
        $team = Team::factory()->create();
        
        $playerData = [
            'name' => 'Virat Kohli',
            'position' => 'Batsman',
            'age' => 35,
            'team_id' => $team->id
        ];

        $player = Player::create($playerData);

        $this->assertDatabaseHas('players', $playerData);
        $this->assertEquals('Virat Kohli', $player->name);
    }

    public function test_player_belongs_to_team(): void
    {
        $team = Team::factory()->create();
        $player = Player::factory()->create(['team_id' => $team->id]);

        $this->assertEquals($team->id, $player->team->id);
    }
}
