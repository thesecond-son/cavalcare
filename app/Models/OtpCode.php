<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    protected $fillable = [
        'channel', 'destination', 'code_hash', 'purpose', 'attempts', 'expires_at', 'consumed_at', 'ip',
    ];

    protected $hidden = ['code_hash'];

    protected $casts = [
        'expires_at'  => 'datetime',
        'consumed_at' => 'datetime',
        'attempts'    => 'integer',
    ];
}
