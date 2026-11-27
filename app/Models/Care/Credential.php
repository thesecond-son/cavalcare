<?php

namespace App\Models\Care;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/** A licence, ID, check or training record for a professional. */
class Credential extends Model
{
    use HasUuids;

    public const TYPES = [
        'nmcn'       => 'NMCN licence',
        'nin'        => 'National ID (NIN)',
        'bls'        => 'Basic Life Support',
        'background' => 'Background check',
        'reference'  => 'References',
        'training'   => 'CavalCare training',
    ];

    public const STATUSES = [
        'to_do'       => 'To do',
        'in_progress' => 'Awaiting review',
        'verified'    => 'Verified',
        'expiring'    => 'Expiring soon',
        'expired'     => 'Expired',
        'rejected'    => 'Rejected',
    ];

    protected $guarded = ['id'];

    protected $hidden = ['number'];

    protected $casts = [
        'number'      => 'encrypted',
        'valid_to'    => 'date',
        'verified_at' => 'datetime',
    ];

    /** Verified (or verified and expiring soon) and not past its expiry date. */
    public function countsAsVerified(): bool
    {
        return in_array($this->status, ['verified', 'expiring'], true)
            && (! $this->valid_to || $this->valid_to->endOfDay()->isFuture());
    }

    /** Last 4 characters of the number, for display. Full numbers are never shown in lists. */
    public function getMaskedNumberAttribute(): ?string
    {
        return $this->number ? '•••• ' . mb_substr($this->number, -4) : null;
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
