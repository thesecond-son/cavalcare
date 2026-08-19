<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CareType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description', // optional intro shown on the booking page
        'meta',
        'sort_order',
    ];

    protected $casts = [
        'meta' => 'array',
        'sort_order' => 'integer',
    ];

    // ─── Auto-generate slug on create ────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (CareType $type) {
            if (empty($type->slug)) {
                $type->slug = Str::slug($type->name) . '-' . Str::lower(Str::random(5));
            }
        });
    }

    // ─── Route model binding uses slug ───────────────────────────────────

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ─── Relationships ────────────────────────────────────────────────────

    /**
     * All pricing plans that belong to this care type.
     * e.g. CareType "Elderly Care" → plans: Day Care, Night Care, 24hr Live-in, etc.
     */
    public function carePlans()
    {
        return $this->hasMany(CarePlan::class)->orderBy('price');
    }

    public function caregivers()
    {
        return $this->belongsToMany(
            Caregiver::class,
            'caregiver_care_type',
            'care_type_id',
            'caregiver_id'
        );
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