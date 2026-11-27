<?php

namespace App\Models\Care;

use App\Models\Booking;
use App\Models\PreBooking;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * The person being cared for (the "parent" in the family app).
 * Shown as a client in Caval OS.
 */
class Patient extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'date_of_birth'    => 'date',
        'consent_given_at' => 'datetime',
        'languages'        => 'array',
        'lat'              => 'float',
        'lng'              => 'float',
        'age_years'        => 'integer',
    ];

    // ── Derived ─────────────────────────────────────────────────────────

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age ?? $this->age_years;
    }

    /** "Woman, 78" style line used in lists and on shift offers. */
    public function getAgeSexAttribute(): ?string
    {
        $sex = match ($this->sex) { 'female' => 'Woman', 'male' => 'Man', default => null };

        return collect([$sex, $this->age])->filter()->join(', ') ?: null;
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', trim($this->full_name)))->filter()
            ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->take(2)->join('');
    }

    // ── Relationships ───────────────────────────────────────────────────

    public function members()
    {
        return $this->hasMany(PatientMember::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'patient_members')
            ->withPivot(['role', 'relationship', 'city', 'can_view_documents', 'invite_status', 'joined_at']);
    }

    public function allergies()
    {
        return $this->hasMany(Allergy::class)->orderByDesc('severe')->orderBy('name');
    }

    public function conditions()
    {
        return $this->hasMany(Condition::class)->orderBy('name');
    }

    public function medications()
    {
        return $this->hasMany(Medication::class)->orderByDesc('active')->orderBy('name');
    }

    public function emergencyContacts()
    {
        return $this->hasMany(EmergencyContact::class);
    }

    public function preBookings()
    {
        return $this->hasMany(PreBooking::class);
    }

    /** The website intake form this client was created from, if any. */
    public function sourceBooking()
    {
        return $this->belongsTo(Booking::class, 'source_booking_id');
    }
}
