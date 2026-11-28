<?php

namespace App\Models\Care;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SafetyAlert extends Model
{
    use HasUuids;

    protected $table = 'safety_alerts';

    protected $guarded = ['id'];

    protected $casts = ['call_me' => 'boolean', 'acknowledged_at' => 'datetime', 'lat' => 'float', 'lng' => 'float'];

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
