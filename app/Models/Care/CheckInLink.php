<?php

namespace App\Models\Care;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/** The patient's private check-in page link. The secret never leaves the server. */
class CheckInLink extends Model
{
    use HasUuids;

    protected $guarded = ['id'];

    protected $hidden = ['secret'];

    protected $casts = [
        'secret'     => 'encrypted',
        'sent_at'    => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function url(): string
    {
        return url('/c/' . $this->code);
    }
}
