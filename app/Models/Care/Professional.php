<?php

namespace App\Models\Care;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A nurse (RN) or carer using CavalPro. Shown as a caregiver in Caval OS.
 * A professional can take shifts only when every required credential is
 * verified and not expired (backend spec, section 3.2).
 */
class Professional extends Model
{
    use HasUuids, SoftDeletes;

    public const TITLES = ['rn' => 'Registered nurse', 'carer' => 'Caregiver'];

    public const STATUSES = [
        'applicant' => 'Applicant',
        'verifying' => 'Verifying',
        'active'    => 'Active',
        'suspended' => 'Suspended',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'languages'            => 'array',
        'skills'               => 'array',
        'specialties'          => 'array',
        'care_types'           => 'array',
        'lat'                  => 'float',
        'lng'                  => 'float',
        'rating_avg'           => 'float',
        'open_to_extra_shifts' => 'boolean',
        'data_saver'           => 'boolean',
        'status_changed_at'    => 'datetime',
    ];

    /** Credentials that must be verified before this professional can take shifts. */
    public function requiredCredentialTypes(): array
    {
        return $this->title === 'rn'
            ? ['nmcn', 'background', 'training']
            : ['nin', 'background', 'training'];
    }

    /** [verified count, required count] for the required credentials. */
    public function verificationProgress(): array
    {
        $required = $this->requiredCredentialTypes();
        $done = $this->credentials
            ->whereIn('type', $required)
            ->filter(fn (Credential $c) => $c->countsAsVerified())
            ->count();

        return [$done, count($required)];
    }

    public function isFullyVerified(): bool
    {
        [$done, $total] = $this->verificationProgress();

        return $done === $total;
    }

    /**
     * Moves applicant/verifying/active to the state the credentials justify.
     * Never lifts a suspension: only a person does that.
     */
    public function syncStatusWithCredentials(): void
    {
        if ($this->status === 'suspended') {
            return;
        }

        $this->load('credentials');
        $next = $this->isFullyVerified()
            ? 'active'
            // "Verifying" once anything has actually been sent for review; a typed number alone isn't.
            : ($this->credentials->where('status', '!=', 'to_do')->isNotEmpty() ? 'verifying' : 'applicant');

        if ($next !== $this->status) {
            $this->forceFill(['status' => $next, 'status_changed_at' => now()])->save();
        }
    }

    public function getNameAttribute(): string
    {
        return $this->user?->name ?: 'Unnamed';
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', trim($this->name)))->filter()
            ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->take(2)->join('');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function credentials()
    {
        return $this->hasMany(Credential::class);
    }

    public function availabilityRules()
    {
        return $this->hasMany(AvailabilityRule::class)->orderBy('weekday')->orderBy('start_time');
    }
}
