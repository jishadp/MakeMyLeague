<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Role constants for better maintainability
     */
    public const ROLE_ADMIN = 'Admin';
    public const ROLE_ORGANISER = 'Organiser';
    public const ROLE_OWNER = 'Owner';
    public const ROLE_PLAYER = 'Player';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];
    
    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Boot the model and add event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->slug)) {
                $user->slug = $user->generateUniqueSlug();
            }
        });

        static::updating(function ($user) {
            if ($user->isDirty('name') && empty($user->slug)) {
                $user->slug = $user->generateUniqueSlug();
            }
        });
    }

    /**
     * Generate unique slug for the user.
     */
    public function generateUniqueSlug()
    {
        $baseSlug = Str::slug($this->name);
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the game role of the user.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(GamePosition::class, 'position_id');
    }

    /**
     * Get all game roles for the user.
     */
    public function gameRoles(): HasMany
    {
        return $this->hasMany(UserGameRole::class);
    }

    /**
     * Get the primary game role for the user.
     */
    public function primaryGameRole(): HasOne
    {
        return $this->hasOne(UserGameRole::class)->where('is_primary', true);
    }

    /**
     * Get the local body of the user.
     */
    public function localBody(): BelongsTo
    {
        return $this->belongsTo(LocalBody::class, 'local_body_id');
    }

    /**
     * Scope a query to only include players.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePlayers($query)
    {
        return $query->whereNotNull('position_id');
    }

    /**
     * Check if the user is an organizer for a specific league.
     *
     * @param int $leagueId
     * @return bool
     */
    public function isOrganizerForLeague($leagueId): bool
    {
        return $this->organizedLeagues()
            ->where('league_id', $leagueId)
            ->wherePivot('status', 'approved')
            ->exists();
    }

    /**
     * Check if the user is an owner of a specific team.
     *
     * @param int $teamId
     * @return bool
     */
    public function isOwnerOfTeam($teamId): bool
    {
        return $this->ownedTeams()
            ->where('team_id', $teamId)
            ->wherePivot('role', 'owner')
            ->exists();
    }

    /**
     * Check if the user is a co-owner of a specific team.
     *
     * @param int $teamId
     * @return bool
     */
    public function isCoOwnerOfTeam($teamId): bool
    {
        return $this->ownedTeams()
            ->where('team_id', $teamId)
            ->wherePivot('role', 'co_owner')
            ->exists();
    }

    /**
     * Check if the user has any ownership (owner or co-owner) of a specific team.
     *
     * @param int $teamId
     * @return bool
     */
    public function hasOwnershipOfTeam($teamId): bool
    {
        return $this->ownedTeams()->where('team_id', $teamId)->exists();
    }

    /**
     * Check if the user is a player (has Player role).
     *
     * @return bool
     */
    public function isPlayer(): bool
    {
        return $this->hasRole(self::ROLE_PLAYER);
    }

    /**
     * Check if the user is a team owner (has Owner role).
     *
     * @return bool
     */
    public function isTeamOwner(): bool
    {
        return $this->hasRole(self::ROLE_OWNER);
    }

    /**
     * Check if the user is an organizer (has Organiser role).
     *
     * @return bool
     */
    public function isOrganizer(): bool
    {
        return $this->hasRole(self::ROLE_ORGANISER);
    }

    /**
     * Check if the user is an admin (has Admin role).
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if the user has any of the given roles.
     *
     * @param array $roleNames
     * @return bool
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }

    /**
     * Check if the user has all of the given roles.
     *
     * @param array $roleNames
     * @return bool
     */
    public function hasAllRoles(array $roleNames): bool
    {
        $userRoles = $this->roles()->pluck('name')->toArray();
        return count(array_intersect($roleNames, $userRoles)) === count($roleNames);
    }

    /**
     * Get the auction bids where this user is the player.
     */
    public function auctions(): hasManyThrough
    {
        return $this->hasManyThrough(Auction::class, LeaguePlayer::class, 'user_id', 'league_player_id');
    }

    /**
     * Get the league players for this user.
     */
    public function leaguePlayers(): HasMany
    {
        return $this->hasMany(LeaguePlayer::class);
    }

    /**
     * Get the leagues organized by this user.
     */
    public function organizedLeagues(): BelongsToMany
    {
        return $this->belongsToMany(League::class, 'league_organizers')
            ->withPivot('status', 'message', 'admin_notes')
            ->withTimestamps();
    }

    /**
     * Get only approved organized leagues.
     */
    public function approvedOrganizedLeagues(): BelongsToMany
    {
        return $this->organizedLeagues()->wherePivot('status', 'approved');
    }

    /**
     * Get the teams owned by this user (including co-owned).
     */
    public function ownedTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_owners')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get only primary owned teams.
     */
    public function primaryOwnedTeams(): BelongsToMany
    {
        return $this->ownedTeams()->wherePivot('role', 'owner');
    }

    /**
     * Get only co-owned teams.
     */
    public function coOwnedTeams(): BelongsToMany
    {
        return $this->ownedTeams()->wherePivot('role', 'co_owner');
    }

    /**
     * Get organizer requests made by this user.
     */
    public function organizerRequests(): HasMany
    {
        return $this->hasMany(OrganizerRequest::class);
    }

    /**
     * Get organizer requests reviewed by this user (admin).
     */
    public function reviewedOrganizerRequests(): HasMany
    {
        return $this->hasMany(OrganizerRequest::class, 'reviewed_by');
    }

    /**
     * Get the roles assigned to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Get the user roles pivot records.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    /**
     * Get the full phone number with country code.
     *
     * @return string
     */
    public function getFullPhoneNumberAttribute(): string
    {
        return $this->country_code . $this->mobile;
    }

    /**
     * Get the formatted phone number for display.
     *
     * @return string
     */
    public function getFormattedPhoneNumberAttribute(): string
    {
        return $this->country_code . ' ' . $this->mobile;
    }
}
