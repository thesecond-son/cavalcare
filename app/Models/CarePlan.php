<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarePlan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function caregivers()
    {
        return $this->belongsToMany(Caregiver::class,
        'caregiver_care_plan',
        'care_plan_id',
        'caregiver_id'
    );
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
