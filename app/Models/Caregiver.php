<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caregiver extends Model
{
    protected $casts = [
        'profile' => 'array',
        'professional' => 'array',
        'availability' => 'array',
        'performance' => 'array',
    ];

    public function careTypes()
    {
        return $this->belongsToMany(CareType::class,
        'caregiver_care_type', // 👈 explicitly define pivot table
        'caregiver_id',
        'care_type_id'
    );
    }

    public function carePlans()
    {
        return $this->belongsToMany(CarePlan::class,
        'caregiver_care_plan', // 👈 explicitly define pivot table
        'caregiver_id',
        'care_plan_id');
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class);
    }
}
