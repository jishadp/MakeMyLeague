<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaguePlayer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'league_team_id',
        'user_id',
        'retention',
        'status',
        'base_price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'retention' => 'boolean',
        'base_price' => 'double',
    ];

    /**
     * Validation rules for the model.
     */
    public static function rules()
    {
        return [
            'league_team_id' => 'required|exists:league_teams,id',
            'user_id' => 'required|exists:users,id',
            'retention' => 'boolean',
            'status' => 'required|in:pending,available,sold,unsold,skip',
            'base_price' => 'numeric|min:0',
        ];
    }

    /**
     * Get the league team that owns this player.
     */
    public function leagueTeam(): BelongsTo
    {
        return $this->belongsTo(LeagueTeam::class);
    }

    /**
     * Get the user (player) for this league player.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the player for this league player.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope to get players for a specific league team.
     */
    public function scopeForLeagueTeam($query, $leagueTeamId)
    {
        return $query->where('league_team_id', $leagueTeamId);
    }

    /**
     * Scope to get players with retention status.
     */
    public function scopeRetention($query, $retention = true)
    {
        return $query->where('retention', $retention);
    }

    /**
     * Scope to get players by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get available players.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope to get sold players.
     */
    public function scopeSold($query)
    {
        return $query->where('status', 'sold');
    }

    /**
     * Scope to get unsold players.
     */
    public function scopeUnsold($query)
    {
        return $query->where('status', 'unsold');
    }

    /**
     * Scope to get pending players.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    /**
     * Get the auction bids for this league player.
     */
    public function auctionBids(): HasMany
    {
        return $this->hasMany(\App\Models\AuctionBid::class);
    }
}
