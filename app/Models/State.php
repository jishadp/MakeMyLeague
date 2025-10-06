<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class State extends Model
{
    protected $guarded = [];

    /**
     * Get all districts for the state.
     */
    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    /**
     * Get all local bodies for the state through districts.
     */
    public function localBodies(): HasManyThrough
    {
        return $this->hasManyThrough(LocalBody::class, District::class);
    }
}
