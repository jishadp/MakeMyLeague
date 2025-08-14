<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeagueTeam extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'league_id',
        'team_id',
        'status',
        'wallet_balance',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'wallet_balance' => 'double',
    ];

    /**
     * Validation rules for the model.
     */
    public static function rules()
    {
        return [
            'league_id' => 'required|exists:leagues,id',
            'team_id' => 'required|exists:teams,id',
            'status' => 'required|in:pending,available',
            'wallet_balance' => 'numeric|min:0',
        ];
    }

    /**
     * Get the league that owns this league team.
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * Get the team that belongs to this league.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the players for this league team.
     */
    public function leaguePlayers(): HasMany
    {
        return $this->hasMany(LeaguePlayer::class);
    }

    /**
     * Get the players for this league team.
     */
    public function players(): HasMany
    {
        return $this->hasMany(LeaguePlayer::class);
    }

    /**
     * Scope to get teams for a specific league.
     */
    public function scopeForLeague($query, $leagueId)
    {
        return $query->where('league_id', $leagueId);
    }

    /**
     * Scope to get available teams.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope to get pending teams.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    /**
     * Get the auctions for this league team.
     */
    public function auctions(): HasMany
    {
        return $this->hasMany(\App\Models\Auction::class);
    }
    
    /**
     * Get the auction bids for this league team.
     */
    public function auctionBids(): HasMany
    {
        return $this->hasMany(\App\Models\AuctionBid::class);
    }
}
