<?php

namespace App\Models;

use App\Relations\JsonArrayRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class League extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'game_id',
        'user_id',
        'ground_ids',
        'localbody_id',
        'venue_details',
        'season',
        'start_date',
        'end_date',
        'max_teams',
        'max_team_players',
        'team_reg_fee',
        'player_reg_fee',
        'retention',
        'retention_players',
        'team_wallet_limit',
        'is_default',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'retention' => 'boolean',
        'is_default' => 'boolean',
        'team_reg_fee' => 'double',
        'player_reg_fee' => 'double',
        'team_wallet_limit' => 'double',
        'ground_ids' => 'array',
    ];

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
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug before creating a new record
        static::creating(function ($league) {
            $league->slug = $league->generateUniqueSlug($league->name);
        });

        // Auto-update slug when name changes
        static::updating(function ($league) {
            if ($league->isDirty('name')) {
                $league->slug = $league->generateUniqueSlug($league->name);
            }
        });
    }

    /**
     * Generate a unique slug.
     *
     * @param string $name
     * @return string
     */
    protected function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = static::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }

    /**
     * Validation rules for the season field.
     */
    public static function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'game_id' => 'required|exists:games,id',
            'ground_ids' => 'nullable|array',
            'ground_ids.*' => 'exists:grounds,id',
            'localbody_id' => 'nullable|exists:local_bodies,id',
            'venue_details' => 'nullable|string|max:255',
            'season' => 'required|integer|min:1|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'max_teams' => 'required|integer|min:2',
            'max_team_players' => 'required|integer|min:1',
            'team_reg_fee' => 'required|numeric|min:0',
            'player_reg_fee' => 'required|numeric|min:0',
            'retention' => 'boolean',
            'retention_players' => 'integer|min:0',
            'team_wallet_limit' => 'required|numeric|min:0',
            'status' => 'required|in:pending,active,completed,cancelled',
        ];
    }

    /**
     * Get the game that owns the league.
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Get the organizer who created the league.
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get the local body that the league is located in.
     */
    public function localBody(): BelongsTo
    {
        return $this->belongsTo(LocalBody::class, 'localbody_id');
    }
    
    /**
     * Get the grounds associated with this league.
     * 
     * @return \App\Relations\JsonArrayRelation
     */
    public function grounds()
    {
        return new JsonArrayRelation(
            Ground::query(),
            $this,
            'ground_ids'
        );
    }

    /**
     * Get the league teams for this league.
     */
    public function leagueTeams(): HasMany
    {
        return $this->hasMany(LeagueTeam::class);
    }
    
    /**
     * Get the league players for this league.
     */
    public function leaguePlayers(): HasMany
    {
        return $this->hasMany(LeaguePlayer::class);
    }

    /**
     * Get all teams participating in this league through league teams.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'league_teams')
                    ->withPivot('status', 'wallet_balance')
                    ->withTimestamps();
    }
}
