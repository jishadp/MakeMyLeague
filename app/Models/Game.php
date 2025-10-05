<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
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
        'active' => 'boolean',
    ];

    /**
     * Get the leagues for the game.
     */
    public function leagues(): HasMany
    {
        return $this->hasMany(League::class);
    }
    
    /**
     * Get the roles for the game.
     */
    public function roles(): HasMany
    {
        return $this->hasMany(GamePosition::class);
    }

    /**
     * Get the user game roles for this game.
     */
    public function userGameRoles(): HasMany
    {
        return $this->hasMany(UserGameRole::class);
    }
}
