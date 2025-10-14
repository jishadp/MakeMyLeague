<?php

namespace App\Events;

use App\Models\LeaguePlayer;
use App\Models\LeagueTeam;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerSold implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $leaguePlayer;
    public $teamId;
    public $team;

    /**
     * Create a new event instance.
     */
    public function __construct($leaguePlayerId, $teamId, $team = null)
    {
        $this->leaguePlayer = LeaguePlayer::with(['player', 'leagueTeam.team'])->find($leaguePlayerId);
        $this->teamId = $teamId;
        
        // Use provided team if available, otherwise fetch it
        if ($team) {
            $this->team = $team;
        } else {
            $this->team = LeagueTeam::withCount('leaguePlayers')->with('team')->find($teamId);
        }
    }

    /**
     * The channel the event should broadcast on.
     */
    public function broadcastOn()
    {
        $channels = [new Channel('auctions')];
        
        // Also broadcast to league-specific channel
        if ($this->leaguePlayer && $this->leaguePlayer->league_id) {
            $channels[] = new Channel('auctions.league.' . $this->leaguePlayer->league_id);
        }
        
        return $channels;
    }

    /**
     * Custom event name.
     */
    public function broadcastAs()
    {
        return 'player-sold';
    }

    /**
     * Optional: specify data you want to send.
     */
    public function broadcastWith()
    {
        return [
            'league_player' => $this->leaguePlayer,
            'team_id' => $this->teamId,
            'team' => $this->team,
            'message' => 'Player sold successfully!'
        ];
    }
}
