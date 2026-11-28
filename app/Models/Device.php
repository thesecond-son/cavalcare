<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/** A phone that can receive push notifications (one row per app install). */
class Device extends Model
{
    use HasUuids;

    protected $guarded = ['id'];

    protected $hidden = ['fcm_token', 'fcm_token_hash'];

    protected $casts = [
        'fcm_token'      => 'encrypted',
        'last_active_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
