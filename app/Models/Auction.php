<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Validation rules for the model.
     */
    public static function rules()
    {
        return [
            'league_player_id' => 'required|exists:league_players,id',
            'league_team_id' => 'required|exists:league_teams,id',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:won,ask',
        ];
    }

    /**
     * Get the league player for this bid.
     */
    public function leaguePlayer(): BelongsTo
    {
        return $this->belongsTo(LeaguePlayer::class);
    }

    /**
     * Get the league team for this bid.
     */
    public function leagueTeam(): BelongsTo
    {
        return $this->belongsTo(LeagueTeam::class);
    }

    /**
     * Scope to get bids for a specific league player.
     */
    public function scopeForLeaguePlayer($query, $leaguePlayerId)
    {
        return $query->where('league_player_id', $leaguePlayerId);
    }

    /**
     * Scope to get bids for a specific league team.
     */
    public function scopeForLeagueTeam($query, $leagueTeamId)
    {
        return $query->where('league_team_id', $leagueTeamId);
    }

    /**
     * Scope to get won bids.
     */
    public function scopeWon($query)
    {
        return $query->where('status', 'won');
    }

    /**
     * Scope to get ask bids.
     */
    public function scopeAsk($query)
    {
        return $query->where('status', 'ask');
    }
}
