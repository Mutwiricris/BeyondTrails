<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id', 'destination_id', 'operator_id',
        'status', 'adults', 'children', 'total_participants',
        'total_price_kes', 'deposit_paid_kes',
        'booking_date', 'payment_method', 'payment_status',
        'confirmation_code', 'special_requests',
        'operator_notes', 'cancelled_reason',
    ];

    protected $casts = [
        'booking_date'      => 'date',
        'total_price_kes'   => 'integer',
        'deposit_paid_kes'  => 'integer',
        'adults'            => 'integer',
        'children'          => 'integer',
        'total_participants'=> 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($booking) {
            if (empty($booking->confirmation_code)) {
                $booking->confirmation_code = strtoupper('BT-' . substr(str_replace('-', '', (string)\Str::uuid()), 0, 8));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class)->withDefault();
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class)->withDefault();
    }
}
