<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

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
        'slug',
        'owner_id',
        'logo',
        'home_ground_id',
        'local_body_id',
        'created_by',
    ];
    
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($team) {
            // Generate slug from name if slug is not explicitly set
            if (empty($team->slug)) {
                $team->slug = self::generateUniqueSlug($team->name);
            }
        });
        
        static::updating(function ($team) {
            // Update slug if name has changed and slug hasn't been manually set
            if ($team->isDirty('name') && !$team->isDirty('slug')) {
                $team->slug = self::generateUniqueSlug($team->name, $team->id);
            }
        });
    }
    
    /**
     * Generate a unique slug based on the given name.
     * 
     * @param string $name
     * @param int|null $exceptId
     * @return string
     */
    protected static function generateUniqueSlug($name, $exceptId = null)
    {
        $slug = Str::slug($name);
        $count = 1;
        
        // Check if the slug already exists
        $query = static::where('slug', $slug);
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        
        // If slug exists, append a number to make it unique
        while ($query->exists()) {
            $slug = Str::slug($name) . '-' . $count++;
            $query = static::where('slug', $slug);
            if ($exceptId) {
                $query->where('id', '!=', $exceptId);
            }
        }
        
        return $slug;
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

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
