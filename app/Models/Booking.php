<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CareType;
use App\Models\CarePlan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_details',
        'booker_details',
        'patient_details',
        'payment_details',
    ];

    protected $casts = [
        'booking_details' => 'array',
        'booker_details' => 'array',
        'patient_details' => 'array',
        'payment_details' => 'array',
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
