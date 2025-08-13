<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Team extends Model
{
    protected $fillable = [
        'name',
        'logo_url',
        'country'
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

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function leagues(): BelongsToMany
    {
        return $this->belongsToMany(League::class)->withTimestamps();
    }
}
