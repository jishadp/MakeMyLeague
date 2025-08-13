<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaguePlayer extends Model
{
    protected $fillable = [
        'league_id',
        'player_id',
        'league_team_id',
        'bid_amount',
        'auction_status'
    ];

    protected $casts = [
        'bid_amount' => 'decimal:2'
    ];

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function leagueTeam(): BelongsTo
    {
        return $this->belongsTo(LeagueTeam::class);
    }
}
