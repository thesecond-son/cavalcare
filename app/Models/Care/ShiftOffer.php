<?php

namespace App\Models\Care;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * The privacy-safe view of a booking that reaches caregivers (spec 4.5).
 * It never holds the patient's name, address or the family's price.
 */
class ShiftOffer extends Model
{
    use HasUuids;

    protected $guarded = ['id'];

    protected $casts = [
        'dates'              => 'array',
        'skills_required'    => 'array',
        'expires_at'         => 'datetime',
        'responded_at'       => 'datetime',
        'distance_km'        => 'float',
        'pay_per_visit_kobo' => 'integer',
        'visits_count'       => 'integer',
    ];

    public function booking()
    {
        return $this->belongsTo(CareBooking::class, 'care_booking_id');
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function isLive(): bool
    {
        return $this->status === 'open' && (! $this->expires_at || $this->expires_at->isFuture());
    }
}
