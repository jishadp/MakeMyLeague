<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    protected $fillable = ['name'];

    /**
     * Get all districts for the state.
     */
    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }
}
