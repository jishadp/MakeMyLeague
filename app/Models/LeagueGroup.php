<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LeagueGroup extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($group) {
            $group->slug = $group->generateUniqueSlug();
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

    public function leagueTeams(): BelongsToMany
    {
        return $this->belongsToMany(LeagueTeam::class, 'league_group_teams');
    }

    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }

    private function generateUniqueSlug()
    {
        if (!$this->league) {
            $this->load('league');
        }
        
        $slug = Str::slug($this->league->slug . '-' . $this->name);
        $count = static::where('league_id', $this->league_id)
                      ->whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")
                      ->count();
        return $count ? "{$slug}-{$count}" : $slug;
    }
}