<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'chat_notifications_enabled',
        'trip_reminders_enabled',
        'nearby_gems_enabled',
        'local_activities_enabled',
        'quiet_hours_enabled',
        'quiet_hours_start',
        'quiet_hours_end',
        'cluster_cooldown_hours',
        'max_proximity_distance_km',
    ];

    protected $casts = [
        'chat_notifications_enabled' => 'boolean',
        'trip_reminders_enabled' => 'boolean',
        'nearby_gems_enabled' => 'boolean',
        'local_activities_enabled' => 'boolean',
        'quiet_hours_enabled' => 'boolean',
        'cluster_cooldown_hours' => 'decimal:2',
        'max_proximity_distance_km' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
