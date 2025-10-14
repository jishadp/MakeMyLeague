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
    public $leaguePlayer;

    /**
     * Create a new event instance.
     */
    public function __construct($newBid, $bidTeam, $leaguePlayerId = null)
    {
        $this->team = LeagueTeam::withCount('leaguePlayers')->with('team')->find($bidTeam);
        $this->newBid = $newBid;
        
        if ($leaguePlayerId) {
            $this->leaguePlayer = LeaguePlayer::with(['player.position', 'player.primaryGameRole.gamePosition'])->find($leaguePlayerId);
        }
    }

    /**
     * The channel the event should broadcast on.
     */
    public function broadcastOn()
    {
        $channels = [new Channel('auctions')];
        
        // Also broadcast to league-specific channel
        if ($this->team && isset($this->team->league_id)) {
            $channels[] = new Channel('auctions.league.' . $this->team->league_id);
        }
        
        return $channels;
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
            'new_bid' => $this->newBid,
            'league_player' => $this->leaguePlayer ?? null,
            'timestamp' => now()->timestamp
        ];
    }
}
