<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'category',
        'type',
        'title',
        'description',
        'location_type',
        'general_area',
        'location_name',
        'latitude',
        'longitude',
        'date',
        'time_type',
        'specific_time',
        'duration_hours',
        'min_age',
        'max_age',
        'privacy',
        'max_capacity',
        'join_approval',
        'tags',
        'is_host_verified',
        'status',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'date' => 'date',
        'tags' => 'array',
        'is_host_verified' => 'boolean',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'activity_user')
                    ->withPivot('status')
                    ->withTimestamps();
    }
}
