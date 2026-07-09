<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreBooking extends Model
{
    protected $fillable = [
        'reference',
        'token',          // ← ADD
        'care_plan_id',
        'care_type_id',
        'booker_details',
        'status',
        'payment_meta',
        'paystack_reference',  // ← ADD (was missing, controller sets it)
        'amount_paid',         // ← ADD (was missing, controller sets it)
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

    public function addOns()
    {
        return $this->belongsToMany(
            AddOn::class,
            'add_on_pre_booking',   // explicit pivot table — matches your migration
            'pre_booking_id',       // this model's foreign key on the pivot
            'add_on_id'             // related model's foreign key on the pivot
        )->withPivot('price_at_booking');
    }
}
