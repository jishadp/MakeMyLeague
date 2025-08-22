<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerViewedBroadcastEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $player; 

    /**
     * Create a new event instance.
     */
    public function __construct($player)
    {
        $this->player = $player;
    }

    /**
     * The channel the event should broadcast on.
     */
    public function broadcastOn()
    {
        return new Channel('order'); 
    }

    /**
     * Custom event name.
     */
    public function broadcastAs()
    {
        return 'player-binding'; 
    }

    /**
     * Optional: specify data you want to send.
     */
    public function broadcastWith()
    {
         return [
            'player' => $this->player,
            'message' => "Player  was viewed",
        ];
    }
}
