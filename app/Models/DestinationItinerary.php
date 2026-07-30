<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DestinationItinerary extends Model
{
    use HasUuids;

    protected $table = 'destination_itinerary';

    protected $fillable = [
        'destination_id', 'day_number', 'title', 'description',
        'activities', 'meals',
    ];

    protected $casts = [
        'activities' => 'array',
        'meals'      => 'array',
    ];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }
}
