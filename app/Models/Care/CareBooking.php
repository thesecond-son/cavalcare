<?php

namespace App\Models\Care;

use App\Models\CarePlan;
use App\Models\CareType;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/** A booking made in the Cavalcare app (spec 4.5). Prices are snapshots, in kobo. */
class CareBooking extends Model
{
    use HasUuids, SoftDeletes;

    public const STATUSES = [
        'pending_payment' => 'Waiting for payment',
        'confirmed'       => 'Paid',
        'matching'        => 'Finding a caregiver',
        'matched'         => 'Caregiver confirmed',
        'active'          => 'Care in progress',
        'completed'       => 'Completed',
        'cancelled'       => 'Cancelled',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'start_date'   => 'date',
        'end_date'     => 'date',
        'preferences'  => 'array',
        'add_ons'      => 'array',
        'paid_at'      => 'datetime',
        'matched_at'   => 'datetime',
        'cancelled_at' => 'datetime',
        'units'        => 'integer',
        'start_hour'   => 'integer',
        'price_per_unit_kobo' => 'integer',
        'add_ons_total_kobo'  => 'integer',
        'total_kobo'   => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (CareBooking $b) {
            if (! $b->reference) {
                do {
                    $ref = 'CVL-' . random_int(10000, 99999);
                } while (static::withTrashed()->where('reference', $ref)->exists());
                $b->reference = $ref;
            }
        });
    }

    public function patient()       { return $this->belongsTo(Patient::class); }
    public function booker()        { return $this->belongsTo(User::class, 'booked_by'); }
    public function carePlan()      { return $this->belongsTo(CarePlan::class)->withTrashed(); }
    public function careType()      { return $this->belongsTo(CareType::class); }
    public function professional()  { return $this->belongsTo(Professional::class); }
    public function requestedProfessional() { return $this->belongsTo(Professional::class, 'requested_professional_id'); }
    public function payments()      { return $this->hasMany(Payment::class)->latest(); }
    public function offers()        { return $this->hasMany(ShiftOffer::class)->latest(); }

    /** The dates care happens on (one per visit/day; a week or month plan lists its first day only). */
    public function visitDates(): array
    {
        if (in_array($this->billing_cycle, ['per_day', 'per_night', 'per_24h'], true)) {
            return collect(range(0, max(0, $this->units - 1)))
                ->map(fn ($i) => $this->start_date->copy()->addDays($i)->toDateString())->all();
        }

        return [$this->start_date->toDateString()];
    }
}
