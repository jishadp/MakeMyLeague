<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LeagueTeam extends Model
{
    protected $fillable = [
        'league_id',
        'name',
        'owner_id',
        'purse_balance',
        'initial_purse_balance'
    ];

    protected $casts = [
        'purse_balance' => 'decimal:2',
        'initial_purse_balance' => 'decimal:2'
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

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function leaguePlayers(): HasMany
    {
        return $this->hasMany(LeaguePlayer::class);
    }

    public function soldPlayers(): HasMany
    {
        return $this->leaguePlayers()->where('auction_status', 'sold');
    }
}
