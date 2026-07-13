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
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
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

    // ─── Route model binding uses slug instead of id ──────────────────────

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ─── Relationships ────────────────────────────────────────────────────

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
}