<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreBooking extends Model
{
    protected $fillable = [
        'reference',
        'care_plan_id',
        'care_type_id',
        'booker_details',
        'status',
        'payment_meta',
    ];

    protected $casts = [
        'booker_details' => 'array',
        'payment_meta'   => 'array',
    ];

    public function carePlan()
    {
        return $this->belongsTo(CarePlan::class);
    }

    public function careType()
    {
        return $this->belongsTo(CareType::class);
    }
}
