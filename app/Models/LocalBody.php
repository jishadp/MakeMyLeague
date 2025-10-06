<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LocalBody extends Model
{
    protected $guarded = [];

    /**
     * Get the district that owns the local body.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the users that belong to this local body.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'local_body_id');
    }

    /**
     * Get the leagues that belong to this local body.
     */
    public function leagues(): HasMany
    {
        return $this->hasMany(League::class, 'local_body_id');
    }
}
