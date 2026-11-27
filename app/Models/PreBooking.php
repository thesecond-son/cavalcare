<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\PreBookingStatus;

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
        'status'         => PreBookingStatus::class,
        'amount_paid'    => 'integer',
    ];

    //  /**
    //  * Use the private token for route model binding.
    //  */
    // public function getRouteKeyName()
    // {
    //     return 'id';
    // }

    public function carePlan()
    {
        // Include retired (soft-deleted) plans: a booking keeps the plan it was bought on.
        return $this->belongsTo(CarePlan::class)->withTrashed();
    }

    /** The Cavalcare app account this booking is linked to (after the app code is used). */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appLinkCodes()
    {
        return $this->hasMany(AppLinkCode::class);
    }

    public function careType()
    {
        return $this->belongsTo(CareType::class);
    }

    public function booking()
    {
        return $this->hasOne(Booking::class);
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

    public function generateToken(): self
    {
        $this->token = Str::random(64);

        return $this;
    }
}
