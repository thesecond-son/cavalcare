<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppLinkCode extends Model
{
    protected $fillable = [
        'pre_booking_id', 'code_hash', 'attempts', 'expires_at', 'sent_at',
        'redeemed_at', 'redeemed_by', 'revoked_at', 'issued_by',
    ];

    protected $hidden = ['code_hash'];

    protected $casts = [
        'expires_at'  => 'datetime',
        'sent_at'     => 'datetime',
        'redeemed_at' => 'datetime',
        'revoked_at'  => 'datetime',
        'attempts'    => 'integer',
    ];

    public function preBooking()
    {
        return $this->belongsTo(PreBooking::class);
    }

    public function isUsable(): bool
    {
        return ! $this->redeemed_at && ! $this->revoked_at && $this->expires_at->isFuture();
    }

    public function scopeActive($query)
    {
        return $query->whereNull('redeemed_at')->whereNull('revoked_at')->where('expires_at', '>', now());
    }
}
