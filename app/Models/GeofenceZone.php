<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GeofenceZone extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'description',
        'center_lat',
        'center_lng',
        'radius_meters',
        'category',
        'trigger_on_entry',
        'trigger_on_dwell',
        'dwell_seconds',
        'trigger_on_exit',
        'throttle_hours',
        'notification_title',
        'notification_body',
        'notification_icon',
        'linked_model_type',
        'linked_model_id',
        'is_active',
    ];

    protected $casts = [
        'center_lat' => 'decimal:7',
        'center_lng' => 'decimal:7',
        'trigger_on_entry' => 'boolean',
        'trigger_on_dwell' => 'boolean',
        'trigger_on_exit' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function linkedModel(): MorphTo
    {
        return $this->morphTo();
    }
}
