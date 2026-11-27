<?php

namespace App\Models\Care;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasUuids;

    protected $guarded = ['id'];

    protected $hidden = ['raw'];

    protected $casts = [
        'raw'         => 'array',
        'paid_at'     => 'datetime',
        'amount_kobo' => 'integer',
    ];

    public function booking()
    {
        return $this->belongsTo(CareBooking::class, 'care_booking_id');
    }
}
