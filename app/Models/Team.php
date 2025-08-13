<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Team extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'owner_id',
        'logo',
        'home_ground_id',
        'local_body_id',
        'created_by',
    ];

    /**
     * Get the owner of the team.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the home ground of the team.
     */
    public function homeGround(): BelongsTo
    {
        return $this->belongsTo(Ground::class, 'home_ground_id');
    }

    /**
     * Get the local body of the team.
     */
    public function localBody(): BelongsTo
    {
        return $this->belongsTo(LocalBody::class, 'local_body_id');
    }

    /**
     * Get the user who created the team.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
