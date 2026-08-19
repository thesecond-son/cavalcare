<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CareType;
use App\Models\CarePlan;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\BookingStatus;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference',
        'pre_booking_id',
        'care_plan_id',
        'care_type_id',
        'status',
        'booker_details',
        'patient_details',
        'booking_details',
    ];

    protected $casts = [
        'booker_details'  => 'array',
        'patient_details' => 'array',
        'booking_details' => 'array',
        'status' => BookingStatus::class,
    ];
    
        public function getCareTypeAttribute()
    {
        return CareType::find(data_get($this->booking_details, 'care_type_id'));
    }

    public function getCarePlanAttribute()
    {
        return CarePlan::find(data_get($this->booking_details, 'care_plan_id'));
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function carePlan()
    {
        return $this->belongsTo(CarePlan::class);
    }

    public function careType()
    {
        return $this->belongsTo(CareType::class);
    }

    public function preBooking()
    {
        return $this->belongsTo(PreBooking::class);
    }

     // ── Token generation ───────────────────────────────────
 
    /**
     * Generate a fresh secure token and assign it.
     * Call this when creating the booking after payment.
     */
    public function generateToken(): static
    {
        $this->token = Str::random(48);
        return $this;
    }

    // ── Convenience helpers ────────────────────────────────
 
    public function hasSubmittedIntake(): bool
    {
        return $this->intake_submitted_at !== null;
    }
 
    public function getIntakeUrlAttribute(): string
    {
        return route('booking.intake', $this->token);
    }
 
    // // ── Route model binding by token ───────────────────────
 
    // public function getRouteKeyName(): string
    // {
    //     return 'token';
    // }

    protected static function booted()
    {
        static::saving(function ($booking) {

            $startDate = data_get($booking->booking_details, 'start_date');
            $carePlanId = data_get($booking->booking_details, 'care_plan_id');

            if ($startDate && $carePlanId) {
                $plan = \App\Models\CarePlan::find($carePlanId);

                if (!empty($plan?->meta['duration_days'])) {
                    $details = $booking->booking_details;

                    data_set(
                        $details,
                        'end_date',
                        Carbon::parse($startDate)
                            ->addDays($plan->meta['duration_days'])
                            ->toDateString()
                    );

                    $booking->booking_details = $details;
                }
            }
        });
    }

}
