<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

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
        return $query->whereNotNull('role_id');
    }
    
    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        // For now, let's assume users with ID 1 are admins
        // In a production app, you would use a proper role system
        return $this->id === 1;
    }
    
    /**
     * Get the auction bids where this user is the player.
     */
    public function auctions(): HasMany
    {
        return $this->hasMany(Auction::class, 'player_id');
    }
    
    /**
     * Get the league players for this user.
     */
    public function leaguePlayers(): HasMany
    {
        return $this->hasMany(LeaguePlayer::class);
    }
    
    /**
     * Get the teams owned by this user.
     */
    public function ownedTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'owner_id');
    }
}
