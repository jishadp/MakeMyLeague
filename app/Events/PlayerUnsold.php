<?php

namespace App\Events;

use App\Models\LeaguePlayer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerUnsold implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $leaguePlayer;

    /**
     * Create a new event instance.
     */
    public function __construct($leaguePlayerId)
    {
        $this->leaguePlayer = LeaguePlayer::with(['player'])->find($leaguePlayerId);
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
        return 'player-unsold';
    }

    /**
     * Optional: specify data you want to send.
     */
    public function broadcastWith()
    {
        return [
            'league_player' => $this->leaguePlayer
        ];
    }
}
