<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class League extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'manager_id',
        'status',
        'purse_balance',
        'min_players_needed',
        'min_bid_amount',
        'auction_started'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'purse_balance' => 'decimal:2',
        'min_bid_amount' => 'decimal:2',
        'auction_started' => 'boolean'
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'league_team')
                    ->withPivot(['name', 'owner_id', 'purse_balance', 'initial_purse_balance'])
                    ->withTimestamps();
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class)->withTimestamps();
    }

    public function leagueTeams()
    {
        return $this->teams();
    }

    public function leaguePlayers()
    {
        return $this->belongsToMany(Player::class, 'league_player')
                    ->withPivot(['bid_amount', 'auction_status', 'league_team_id'])
                    ->withTimestamps();
    }

    public function availablePlayersForAuction()
    {
        return Player::whereNotIn('id', 
            $this->leaguePlayers()->wherePivot('auction_status', 'sold')->pluck('players.id')
        );
    }
    
    public function leagueAvailablePlayers()
    {
        return $this->leaguePlayers()
                    ->whereIn('league_player.auction_status', ['unsold', 'skip'])
                    ->orWhereNull('league_player.auction_status');
    }
}
