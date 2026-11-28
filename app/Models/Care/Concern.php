<?php

namespace App\Models\Care;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Concern extends Model
{
    use HasUuids;

    protected $table = 'concerns';

    protected $guarded = ['id'];

    protected $casts = ['recorded_at' => 'datetime', 'synced_at' => 'datetime', 'clock_drift_flag' => 'boolean', 'photo_paths' => 'array', 'acknowledged_at' => 'datetime', 'resolved_at' => 'datetime'];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
