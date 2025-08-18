<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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
        'slug',
        'status',
        'wallet_balance',
        'created_by',
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
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug before creating a new record
        static::creating(function ($leagueTeam) {
            if (empty($leagueTeam->slug)) {
                $leagueTeam->slug = $leagueTeam->generateUniqueSlug();
            }
        });
    }

    /**
     * Generate a unique slug.
     *
     * @return string
     */
    protected function generateUniqueSlug()
    {
        // Load relationships if not already loaded
        if (!$this->relationLoaded('league')) {
            $this->load('league');
        }
        if (!$this->relationLoaded('team')) {
            $this->load('team');
        }
        
        $league = $this->league;
        $team = $this->team;
        
        if (!$league || !$team) {
            // Fallback: load by IDs if relationships still aren't available
            $league = \App\Models\League::find($this->league_id);
            $team = \App\Models\Team::find($this->team_id);
        }
        
        if (!$league || !$team) {
            // Final fallback slug if relationships aren't available
            $slug = 'league-team-' . ($this->id ?? uniqid());
        } else {
            $slug = Str::slug($league->name . '-' . $team->name);
        }
        
        $count = static::where('league_id', $this->league_id)
                      ->whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")
                      ->where('id', '!=', $this->id ?? 0)
                      ->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

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
     * Get the auction bids for this league team.
     */
    public function auctions(): HasMany
    {
        return $this->hasMany(\App\Models\Auction::class);
    }
    
    /**
     * Get the user who created this league team.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
