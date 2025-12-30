<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Fixture extends Model
{
    protected $guarded = [];

    public function fixturePlayers()
    {
        return $this->hasMany(FixturePlayer::class);
    }

    public function players()
    {
        return $this->belongsToMany(LeaguePlayer::class, 'fixture_players', 'fixture_id', 'player_id')
                    ->withPivot('status', 'is_active')
                    ->withTimestamps();
    }

    // Match States
    const STATE_NOT_STARTED = 'NOT_STARTED';
    const STATE_FIRST_HALF = 'FIRST_HALF';
    const STATE_HALF_TIME = 'HALF_TIME';
    const STATE_SECOND_HALF = 'SECOND_HALF';
    const STATE_EXTRA_TIME_FIRST = 'EXTRA_TIME_FIRST';
    const STATE_EXTRA_TIME_BREAK = 'EXTRA_TIME_BREAK';
    const STATE_EXTRA_TIME_SECOND = 'EXTRA_TIME_SECOND';
    const STATE_FULL_TIME = 'FULL_TIME';

    protected $casts = [
        'match_date' => 'date',
        'match_time' => 'datetime:H:i',
        'is_running' => 'boolean',
        'is_knockout' => 'boolean',
        'has_penalties' => 'boolean',
        'last_tick_at' => 'datetime',
        'toss_conducted' => 'boolean',
        'toss_conducted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($fixture) {
            if (!$fixture->slug) {
                $fixture->slug = $fixture->generateUniqueSlug();
            }
            if (!$fixture->match_state) {
                $fixture->match_state = self::STATE_NOT_STARTED;
            }
        });
    }

    // ... (rest of methods)

    public function getMatchTimeDisplayAttribute()
    {
        $min = $this->current_minute;
        $added = 0;

        // Helper to format
        $format = function($m, $add) {
            return $add > 0 ? "{$m}+{$add}" : "{$m}:00"; // Simplified for attributes, but Scorer Console does detailed logic
        };

        if ($this->match_state === self::STATE_HALF_TIME) return 'HT';
        if ($this->match_state === self::STATE_FULL_TIME) return 'FT';
        
        return $this->current_minute . "'";
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

    public function scorer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scorer_id');
    }

    public function events()
    {
        return $this->hasMany(MatchEvent::class);
    }

    public function penalties()
    {
        return $this->hasMany(FixturePenalty::class);
    }

    public function penaltyWinnerTeam()
    {
        return $this->belongsTo(LeagueTeam::class, 'penalty_winner_team_id');
    }

    public function tossWinnerTeam()
    {
        return $this->belongsTo(LeagueTeam::class, 'toss_winner_team_id');
    }

    public function getHomePenaltyScoreAttribute()
    {
        return $this->penalties()->where('team_id', $this->home_team_id)->where('scored', true)->count();
    }

    public function getAwayPenaltyScoreAttribute()
    {
        return $this->penalties()->where('team_id', $this->away_team_id)->where('scored', true)->count();
    }

    public function scopeLive($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeFinished($query)
    {
        return $query->where('status', 'completed');
    }

    private function generateUniqueSlug()
    {
        try {
            // Get league slug directly from attribute or load if needed
            $leagueSlug = $this->league_id ? 
                League::find($this->league_id)?->slug : 
                ($this->league?->slug ?? 'fixture');
            
            $base = $leagueSlug . '-fixture-' . uniqid();
            return Str::slug($base);
        } catch (\Exception $e) {
            // Fallback slug if something goes wrong
            return 'fixture-' . uniqid();
        }
    }
}