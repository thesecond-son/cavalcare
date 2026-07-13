<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CarePlan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    // ─── Slug: auto-generate on create ───────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (CarePlan $plan) {
            if (empty($plan->slug)) {
                // Append a short random suffix so slugs stay unique
                // even if two plans have the same name.
                $plan->slug = Str::slug($plan->name) . '-' . Str::lower(Str::random(5));
            }
        });
    }

    // ─── Route model binding — uses slug instead of id ───────────────────
    //
    // Laravel will now resolve {carePlan} in routes by looking up
    // the slug column instead of the primary key. If the slug doesn't
    // exist it automatically returns a 404 — exactly the same safety
    // as before, but with a human-readable URL like /book/full-care-plan-abc12
    // instead of /book/3.

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ─── Convenience: pull features out of meta ──────────────────────────

    public function getFeaturesAttribute(): array
    {
        return $this->meta['features'] ?? [];
    }

    // ─── Relationships ────────────────────────────────────────────────────

    public function caregivers()
    {
        return $this->belongsToMany(
            Caregiver::class,
            'caregiver_care_plan',
            'care_plan_id',
            'caregiver_id'
        );
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}