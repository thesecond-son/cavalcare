<?php

namespace App\Models\Care;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class VitalReading extends Model
{
    use HasUuids;

    protected $table = 'vital_readings';

    protected $guarded = ['id'];

    protected $casts = ['recorded_at' => 'datetime', 'synced_at' => 'datetime', 'clock_drift_flag' => 'boolean', 'out_of_range' => 'boolean'];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
