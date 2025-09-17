<?php

namespace App\Events;

use App\Models\League;
use App\Models\LeaguePlayer;
use App\Models\LeagueTeam;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuctionPlayerBidCall implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $team;
    public $newBid;

    /**
     * Create a new event instance.
     */
    public function __construct($newBid,$bidTeam)
    {
        $this->team = LeagueTeam::withCount('leaguePlayers')->with('team')->find($bidTeam);
        $this->newBid = $newBid;
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
        return 'new-player-bid-call';
    }

    /**
     * Optional: specify data you want to send.
     */
    public function broadcastWith()
    {
         return [
            'league_team' => $this->team,
            'new_bid' => $this->newBid
        ];
    }
}
