<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CarePlan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'care_type_id',   // ← new: parent care type
        'name',
        'slug',
        'price',
        'description',    // ← new: shown on booking page
        'billing_cycle',  // ← new: per_day | per_night | per_24h | per_week | per_month | flat
        'meta',
    ];

    protected $casts = [
        'meta'       => 'array',
        'price'      => 'integer',
        'price_from' => 'boolean',
    ];

    // ─── Auto-generate slug on create ────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (CarePlan $plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name) . '-' . Str::lower(Str::random(5));
            }
        });
    }

    // ─── Route model binding — uses slug ─────────────────────────────────

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ─── Billing cycle label ──────────────────────────────────────────────
    // Returns a human-readable suffix like "/day", "/week", etc.

    public function getBillingLabelAttribute(): string
    {
        return match ($this->billing_cycle) {
            'per_day'   => '/day',
            'per_night' => '/night',
            'per_24h'   => '/24 hours',
            'per_week'  => '/week',
            'per_month' => '/month',
            'flat'      => ' (one-time)',
            default     => '',
        };
    }

    // ─── Formatted price string ───────────────────────────────────────────
    // e.g. "₦35,000/day"  or  "from ₦40,000"

    public function getFormattedPriceAttribute(): string
    {
        $amount = '₦' . number_format($this->price);
        $label  = $this->billing_label;

        return $amount . $label;
    }

    // ─── Convenience: features from meta ─────────────────────────────────

    public function getFeaturesAttribute(): array
    {
        return $this->meta['features'] ?? [];
    }

    // ─── Relationships ────────────────────────────────────────────────────

    public function careType()
    {
        return $this->belongsTo(CareType::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function preBookings()
    {
        return $this->hasMany(PreBooking::class);
    }
}