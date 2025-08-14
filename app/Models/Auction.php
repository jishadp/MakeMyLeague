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
    protected $fillable = [
        'user_id',
        'league_team_id',
        'amount',
        'created_by',
    ];

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
            'user_id' => 'required|exists:users,id',
            'league_team_id' => 'required|exists:league_teams,id',
            'amount' => 'required|numeric|min:0',
            'created_by' => 'required|exists:users,id',
        ];
    }

    /**
     * Get the player (user) for this auction.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user for this auction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the league team for this auction.
     */
    public function leagueTeam(): BelongsTo
    {
        return $this->belongsTo(LeagueTeam::class);
    }

    /**
     * Get the user who created this auction.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to get auctions for a specific league team.
     */
    public function scopeForLeagueTeam($query, $leagueTeamId)
    {
        return $query->where('league_team_id', $leagueTeamId);
    }

    /**
     * Scope to get auctions for a specific player.
     */
    public function scopeForPlayer($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
