<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LeaguePlayer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'league_team_id',
        'user_id',
        'slug',
        'retention',
        'status',
        'base_price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'retention' => 'boolean',
        'base_price' => 'double',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug before creating a new record
        static::creating(function ($leaguePlayer) {
            if (empty($leaguePlayer->slug)) {
                $leaguePlayer->slug = $leaguePlayer->generateUniqueSlug();
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
        if (!$this->relationLoaded('user')) {
            $this->load('user');
        }
        
        $user = $this->user;
        $leagueTeam = null;
        
        if ($this->league_team_id) {
            if (!$this->relationLoaded('leagueTeam')) {
                $this->load('leagueTeam.team');
            }
            $leagueTeam = $this->leagueTeam;
        }
        
        if (!$user) {
            // Fallback: load by IDs if relationships still aren't available
            $user = \App\Models\User::find($this->user_id);
        }
        
        if (!$user) {
            // Final fallback slug if relationships aren't available
            $slug = 'league-player-' . ($this->id ?? uniqid());
        } else {
            if ($leagueTeam && $leagueTeam->team) {
                // Player assigned to a team
                $teamName = $leagueTeam->team->name;
                $slug = Str::slug($user->name . '-' . $teamName);
            } else {
                // Player available for auction (no team)
                $slug = Str::slug($user->name . '-available');
            }
        }
        
        $count = static::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")
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
            'league_team_id' => 'nullable|exists:league_teams,id',
            'user_id' => 'required|exists:users,id',
            'retention' => 'boolean',
            'status' => 'required|in:pending,available,sold,unsold,skip',
            'base_price' => 'numeric|min:0',
        ];
    }

    /**
     * Get the league team that owns this player.
     */
    public function leagueTeam(): BelongsTo
    {
        return $this->belongsTo(LeagueTeam::class);
    }

    /**
     * Get the user (player) for this league player.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the player for this league player.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope to get players for a specific league team.
     */
    public function scopeForLeagueTeam($query, $leagueTeamId)
    {
        return $query->where('league_team_id', $leagueTeamId);
    }

    /**
     * Scope to get players with retention status.
     */
    public function scopeRetention($query, $retention = true)
    {
        return $query->where('retention', $retention);
    }

    /**
     * Scope to get players by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get available players.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope to get sold players.
     */
    public function scopeSold($query)
    {
        return $query->where('status', 'sold');
    }

    /**
     * Scope to get unsold players.
     */
    public function scopeUnsold($query)
    {
        return $query->where('status', 'unsold');
    }

    /**
     * Scope to get pending players.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    /**
     * Get the auction bids for this league player.
     */
    public function auctionBids(): HasMany
    {
        return $this->hasMany(\App\Models\AuctionBid::class);
    }
}
