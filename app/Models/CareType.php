<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CareType extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function caregivers()
    {
        return $this->belongsToMany(Caregiver::class,
        'caregiver_care_type',
        'care_type_id',
        'caregiver_id'
    );
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
