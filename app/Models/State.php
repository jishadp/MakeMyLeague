<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
