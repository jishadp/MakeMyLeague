<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
}
