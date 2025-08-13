<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocalBody extends Model
{
    protected $fillable = ['district_id', 'name'];

    /**
     * Get the district that owns the local body.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
