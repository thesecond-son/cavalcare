<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    protected $table = 'add_ons';
    protected $fillable = ['name', 'price', 'is_active'];
 
    protected $casts = ['is_active' => 'boolean'];
 
    public function preBookings()
    {
        return $this->belongsToMany(PreBooking::class)
                    ->withPivot('price_at_booking');
    }
}
