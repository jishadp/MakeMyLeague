<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamAuctioneer extends Model
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the league that this auctioneer assignment belongs to.
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * Get the team that this auctioneer is assigned to.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the league team that this auctioneer is assigned to.
     */
    public function leagueTeam(): BelongsTo
    {
        return $this->belongsTo(LeagueTeam::class);
    }

    /**
     * Get the user who is the auctioneer.
     */
    public function auctioneer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auctioneer_id');
    }

    /**
     * Scope to get active auctioneers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get auctioneers for a specific league.
     */
    public function scopeForLeague($query, $leagueId)
    {
        return $query->where('league_id', $leagueId);
    }

    /**
     * Scope to get auctioneers for a specific team.
     */
    public function scopeForTeam($query, $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    /**
     * Scope to get teams that a specific user is auctioneer for.
     */
    public function scopeForAuctioneer($query, $auctioneerId)
    {
        return $query->where('auctioneer_id', $auctioneerId);
    }

    /**
     * Check if the auctioneer assignment is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Activate the auctioneer assignment.
     */
    public function activate(): bool
    {
        return $this->update(['status' => 'active']);
    }

    /**
     * Deactivate the auctioneer assignment.
     */
    public function deactivate(): bool
    {
        return $this->update(['status' => 'inactive']);
    }

    /**
     * Check if a user is already an auctioneer in a league.
     *
     * @param int $leagueId
     * @param int $auctioneerId
     * @return bool
     */
    public static function isAuctioneerInLeague($leagueId, $auctioneerId): bool
    {
        return self::where('league_id', $leagueId)
            ->where('auctioneer_id', $auctioneerId)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Get the team that an auctioneer is assigned to in a league.
     *
     * @param int $leagueId
     * @param int $auctioneerId
     * @return TeamAuctioneer|null
     */
    public static function getAuctioneerAssignment($leagueId, $auctioneerId)
    {
        return self::where('league_id', $leagueId)
            ->where('auctioneer_id', $auctioneerId)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Assign an auctioneer to a team in a league.
     *
     * @param int $leagueId
     * @param int $teamId
     * @param int $leagueTeamId
     * @param int $auctioneerId
     * @param string|null $notes
     * @return TeamAuctioneer
     * @throws \Exception
     */
    public static function assignAuctioneer($leagueId, $teamId, $leagueTeamId, $auctioneerId, $notes = null)
    {
        // Check if user is already an auctioneer in this league
        if (self::isAuctioneerInLeague($leagueId, $auctioneerId)) {
            throw new \Exception('This user is already assigned as an auctioneer in this league.');
        }

        return self::create([
            'league_id' => $leagueId,
            'team_id' => $teamId,
            'league_team_id' => $leagueTeamId,
            'auctioneer_id' => $auctioneerId,
            'status' => 'active',
            'notes' => $notes,
        ]);
    }

    /**
     * Remove an auctioneer assignment.
     *
     * @param int $leagueTeamId
     * @return bool
     */
    public static function removeAuctioneer($leagueTeamId): bool
    {
        return self::where('league_team_id', $leagueTeamId)
            ->where('status', 'active')
            ->delete();
    }

    /**
     * Update auctioneer assignment for a team.
     *
     * @param int $leagueTeamId
     * @param int $newAuctioneerId
     * @return TeamAuctioneer
     * @throws \Exception
     */
    public static function updateAuctioneer($leagueTeamId, $newAuctioneerId)
    {
        $assignment = self::where('league_team_id', $leagueTeamId)
            ->where('status', 'active')
            ->first();

        if (!$assignment) {
            throw new \Exception('No active auctioneer assignment found for this team.');
        }

        // Check if new auctioneer is already assigned in this league
        if (self::isAuctioneerInLeague($assignment->league_id, $newAuctioneerId)) {
            throw new \Exception('This user is already assigned as an auctioneer in this league.');
        }

        $assignment->update(['auctioneer_id' => $newAuctioneerId]);

        return $assignment;
    }
}
