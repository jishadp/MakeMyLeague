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
    protected $guarded = [];

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
        'auction_active' => 'boolean',
        'auction_started_at' => 'datetime',
        'auction_ended_at' => 'datetime',
        'team_reg_fee' => 'double',
        'player_reg_fee' => 'double',
        'team_wallet_limit' => 'double',
        'winner_prize' => 'decimal:2',
        'runner_prize' => 'decimal:2',
        'custom_bid_increment' => 'decimal:2',
        'predefined_increments' => 'array',

    ];

    /**
     * Get the finance records for this league.
     */
    public function finances(): HasMany
    {
        return $this->hasMany(LeagueFinance::class);
    }

    /**
     * Get the income records for this league.
     */
    public function incomes(): HasMany
    {
        return $this->hasMany(LeagueFinance::class)->where('type', 'income');
    }

    /**
     * Get the expense records for this league.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(LeagueFinance::class)->where('type', 'expense');
    }

    /**
     * Get total income for this league.
     */
    public function getTotalIncomeAttribute()
    {
        return $this->incomes()->sum('amount');
    }

    /**
     * Get total expenses for this league.
     */
    public function getTotalExpensesAttribute()
    {
        return $this->expenses()->sum('amount');
    }

    /**
     * Get net profit/loss for this league.
     */
    public function getNetProfitAttribute()
    {
        return $this->total_income - $this->total_expenses;
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
            'winner_prize' => 'nullable|numeric|min:0',
            'runner_prize' => 'nullable|numeric|min:0',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
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
     * Get all organizers for this league.
     */
    public function organizers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'league_organizers')
            ->withPivot('status', 'message', 'admin_notes')
            ->withTimestamps();
    }

    /**
     * Get only approved organizers for this league.
     */
    public function approvedOrganizers(): BelongsToMany
    {
        return $this->organizers()->wherePivot('status', 'approved');
    }

    /**
     * Get organizer requests for this league.
     */
    public function organizerRequests(): HasMany
    {
        return $this->hasMany(OrganizerRequest::class);
    }

    /**
     * Get the local body that the league is located in.
     */
    public function localBody(): BelongsTo
    {
        return $this->belongsTo(LocalBody::class, 'localbody_id');
    }

    /**
     * Get all associated Ground models
     */
     public function grounds()
    {
        return $this->belongsToMany(Ground::class, 'league_grounds');
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

    /**
     * Get the league groups for this league.
     */
    public function leagueGroups(): HasMany
    {
        return $this->hasMany(\App\Models\LeagueGroup::class);
    }

    /**
     * Get the fixtures for this league.
     */
    public function fixtures(): HasMany
    {
        return $this->hasMany(\App\Models\Fixture::class);
    }

    /**
     * Get the next minimum bid amount based on current bid and increment structure.
     */
    public function getNextMinimumBid($currentBid)
    {
        if ($this->bid_increment_type === 'custom') {
            return $currentBid + ($this->custom_bid_increment ?? 10);
        }

        // Predefined structure
        $increments = $this->predefined_increments ?? [
            ['min' => 0, 'max' => 100, 'increment' => 5],
            ['min' => 101, 'max' => 500, 'increment' => 10],
            ['min' => 501, 'max' => 1000, 'increment' => 25],
            ['min' => 1001, 'max' => null, 'increment' => 50],
        ];

        foreach ($increments as $rule) {
            if ($currentBid >= $rule['min'] && ($rule['max'] === null || $currentBid <= $rule['max'])) {
                return $currentBid + $rule['increment'];
            }
        }

        return $currentBid + 50; // Default increment
    }

    /**
     * Check if auction is active.
     */
    public function isAuctionActive()
    {
        return $this->auction_active && $this->auction_started_at && !$this->auction_ended_at;
    }

    /**
     * Get available players for auction.
     */
    public function getAvailablePlayers()
    {
        return $this->leaguePlayers()
            ->where('status', 'available')
            ->where('retention', false) // Exclude retention players
            ->with(['user', 'user.position', 'user.localBody'])
            ->orderBy('base_price', 'desc');
    }

    /**
     * Get sold players.
     */
    public function getSoldPlayers()
    {
        return $this->leaguePlayers()
            ->where('status', 'sold')
            ->with(['user', 'user.position', 'leagueTeam.team']);
    }

    /**
     * Get auction statistics.
     */
    public function getAuctionStats()
    {
        $totalPlayers = $this->leaguePlayers()->count();
        $availablePlayers = $this->leaguePlayers()->where('status', 'available')->count();
        $soldPlayers = $this->leaguePlayers()->where('status', 'sold')->count();
        $totalRevenue = $this->leaguePlayers()
            ->where('status', 'sold')
            ->whereNotNull('bid_price')
            ->sum('bid_price');

        return [
            'total_players' => $totalPlayers,
            'available_players' => $availablePlayers,
            'sold_players' => $soldPlayers,
            'total_revenue' => $totalRevenue,
            'completion_percentage' => $totalPlayers > 0 ? round(($soldPlayers / $totalPlayers) * 100, 2) : 0,
        ];
    }

    /**
     * Get total teams count for this league.
     */
    public function getTeamsCount()
    {
        return $this->leagueTeams()->count();
    }

    /**
     * Get total players count for this league.
     */
    public function getPlayersCount()
    {
        return $this->leaguePlayers()->count();
    }

    /**
     * Get total players capacity (teams × max_team_players).
     */
    public function getTotalPlayersCapacity()
    {
        return $this->getTeamsCount() * $this->max_team_players;
    }

    /**
     * Get league teams with their auctioneers.
     */
    public function leagueTeamsWithAuctioneers()
    {
        return $this->leagueTeams()->with(['team', 'auctioneer']);
    }

    /**
     * Get teams that don't have auctioneers assigned.
     */
    public function getTeamsWithoutAuctioneers()
    {
        return $this->leagueTeams()->whereNull('auctioneer_id')->with('team');
    }

    /**
     * Get teams that have auctioneers assigned.
     */
    public function getTeamsWithAuctioneers()
    {
        return $this->leagueTeams()->whereNotNull('auctioneer_id')->with(['team', 'auctioneer']);
    }

    /**
     * Check if all teams have auctioneers assigned.
     */
    public function allTeamsHaveAuctioneers()
    {
        return $this->leagueTeams()->whereNull('auctioneer_id')->count() === 0;
    }

    /**
     * Get available users who can be assigned as auctioneers for this league.
     * Excludes users who are already auctioneers for other teams in this league.
     */
    public function getAvailableAuctioneers()
    {
        $assignedAuctioneerIds = $this->leagueTeams()
            ->whereNotNull('auctioneer_id')
            ->pluck('auctioneer_id')
            ->toArray();

        return User::whereNotIn('id', $assignedAuctioneerIds)
            ->where('id', '!=', 1) // Exclude admin user
            ->orderBy('name')
            ->get();
    }
}
