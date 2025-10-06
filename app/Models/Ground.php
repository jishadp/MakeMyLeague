<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ground extends Model
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
        'is_available' => 'boolean',
        'capacity' => 'integer',
    ];

    /**
     * Get the local body that the ground belongs to.
     */
    public function localBody(): BelongsTo
    {
        return $this->belongsTo(LocalBody::class, 'localbody_id');
    }

    /**
     * Get the district that the ground belongs to.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the state that the ground belongs to.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the teams that use this ground as their home ground.
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class, 'home_ground_id');
    }
}
