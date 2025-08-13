<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Player extends Model
{
    protected $fillable = [
        'name',
        'position',
        'age',
        'stats_json',
        'team_id'
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
        'stats_json' => 'array'
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function leagues(): BelongsToMany
    {
        return $this->belongsToMany(League::class)->withTimestamps();
    }
}
