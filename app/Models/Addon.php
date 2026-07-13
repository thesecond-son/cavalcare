<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddOn extends Model
{
    protected $table = 'add_ons';
    protected $fillable = ['name', 'price', 'is_active'];
 
    protected $casts = ['is_active' => 'boolean'];
 
    public function preBookings()
{
    return $this->belongsToMany(
       PreBooking::class,
        'add_on_pre_booking',
        'add_on_id',
        'pre_booking_id'
    )->withPivot('price_at_booking');
}
}
