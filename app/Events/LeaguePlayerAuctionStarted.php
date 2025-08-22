<?php

namespace App\Events;

use App\Models\League;
use App\Models\LeaguePlayer;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaguePlayerAuctionStarted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $player;
    public $league;
    public $leaguePlayer;

    /**
     * Create a new event instance.
     */
    public function __construct($request)
    {
        $this->player = User::with('position')->find($request['player_id']);
        $this->league = League::find($request['league_id']);
        $this->leaguePlayer = LeaguePlayer::find($request['league_player_id']);
    }

    /**
     * The channel the event should broadcast on.
     */
    public function broadcastOn()
    {
        return new Channel('auctions');
    }

    /**
     * Custom event name.
     */
    public function broadcastAs()
    {
        return 'new-player-started';
    }

    /**
     * Optional: specify data you want to send.
     */
    public function broadcastWith()
    {
         return [
            'player' => $this->player,
            'league' => $this->league,
            'league_player' => $this->leaguePlayer
        ];
    }
}
