<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddOn extends Model
{
    protected $table = 'add_ons';

    protected $fillable = [
        'name',
        'description', // ← new
        'price',
        'price_from',  // ← new: true = "from ₦X"
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'price_from' => 'boolean',
        'price'      => 'integer',
    ];

    // ─── Formatted price string ───────────────────────────────────────────
    // e.g. "₦25,000"  or  "from ₦40,000"

    public function getFormattedPriceAttribute(): string
    {
        $amount = '₦' . number_format($this->price);
        return $this->price_from ? 'from ' . $amount : $amount;
    }

    // ─── Relationships ────────────────────────────────────────────────────

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