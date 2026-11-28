<?php

namespace App\Models\Care;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * One visit or one day of a shift (live-in = one visit per day), spec 4.6.
 * state and status are changed only by the server.
 */
class Visit extends Model
{
    use HasUuids;

    public const STATES = [
        'scheduled'   => 'Scheduled',
        'en_route'    => 'On the way',
        'checked_in'  => 'Checked in',
        'checked_out' => 'Visit done',
        'missed'      => 'Missed',
        'cancelled'   => 'Cancelled',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'scheduled_start'     => 'datetime',
        'scheduled_end'       => 'datetime',
        'en_route_at'         => 'datetime',
        'checked_in_at'       => 'datetime',
        'checked_out_at'      => 'datetime',
        'report_submitted_at' => 'datetime',
        'family_price_kobo'   => 'integer',
        'pay_kobo'            => 'integer',
    ];

    public function booking()      { return $this->belongsTo(CareBooking::class, 'care_booking_id'); }
    public function patient()      { return $this->belongsTo(Patient::class); }
    public function professional() { return $this->belongsTo(Professional::class); }
    public function checkIn()      { return $this->hasOne(VisitCheckIn::class); }
    public function checkOut()     { return $this->hasOne(VisitCheckOut::class); }
    public function tasks()        { return $this->hasMany(TaskEvent::class)->orderBy('recorded_at'); }
    public function vitals()       { return $this->hasMany(VitalReading::class)->orderBy('recorded_at'); }
    public function medicationEvents() { return $this->hasMany(MedicationEvent::class)->orderBy('recorded_at'); }
    public function observations() { return $this->hasMany(Observation::class)->orderBy('recorded_at'); }
    public function concerns()     { return $this->hasMany(Concern::class)->orderBy('recorded_at'); }
    public function careNotes()    { return $this->hasMany(CareNote::class)->orderBy('recorded_at'); }
    public function amendments()   { return $this->hasMany(Amendment::class)->orderBy('at'); }

    public function isSubmitted(): bool
    {
        return $this->report_submitted_at !== null;
    }

    /** The check-in window: 30 minutes before the start until the scheduled end (spec 13.3). */
    public function inCheckInWindow(?\DateTimeInterface $at = null): bool
    {
        $at = $at ? \Illuminate\Support\Carbon::instance($at) : now();

        return $at->between($this->scheduled_start->copy()->subMinutes(30), $this->scheduled_end);
    }
}
