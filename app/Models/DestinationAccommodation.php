<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DestinationAccommodation extends Model
{
    use HasUuids;

    protected $fillable = [
        'destination_id', 'name', 'type',
        'rating', 'room_type', 'bed_configuration',
        'amenities', 'image_url',
    ];

    protected $casts = [
        'amenities' => 'array',
        'rating'    => 'decimal:2',
    ];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }
}
