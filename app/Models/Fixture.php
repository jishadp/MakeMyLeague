<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Fixture extends Model
{
    protected $guarded = [];

    protected $casts = [
        'match_date' => 'date',
        'match_time' => 'datetime:H:i',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($fixture) {
            $fixture->slug = $fixture->generateUniqueSlug();
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(LeagueTeam::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(LeagueTeam::class, 'away_team_id');
    }

    public function leagueGroup(): BelongsTo
    {
        return $this->belongsTo(LeagueGroup::class);
    }

    private function generateUniqueSlug()
    {
        if (!$this->league) {
            $this->load('league');
        }
        
        $base = $this->league->slug . '-fixture-' . uniqid();
        return Str::slug($base);
    }
}