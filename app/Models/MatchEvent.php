<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchEvent extends Model
{
    protected $guarded = [];

    public function fixture()
    {
        return $this->belongsTo(Fixture::class);
    }

    public function team()
    {
        return $this->belongsTo(LeagueTeam::class, 'team_id');
    }

    public function player()
    {
        return $this->belongsTo(LeaguePlayer::class, 'player_id');
    }

    public function relatedPlayer()
    {
        return $this->belongsTo(LeaguePlayer::class, 'related_player_id');
    }

    public function assistPlayer()
    {
        return $this->belongsTo(LeaguePlayer::class, 'assist_player_id');
    }
}
