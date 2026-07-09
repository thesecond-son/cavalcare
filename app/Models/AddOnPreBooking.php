<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddOnPreBooking extends Model
{
    protected $table = 'add_on_pre_booking';
    protected $fillable = ['pre_booking_id', 'add_on_id', 'price_at_booking'];
}
