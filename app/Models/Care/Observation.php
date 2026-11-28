<?php

namespace App\Models\Care;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{
    use HasUuids;

    protected $table = 'observations';

    protected $guarded = ['id'];

    protected $casts = ['recorded_at' => 'datetime', 'synced_at' => 'datetime', 'clock_drift_flag' => 'boolean', 'skin' => 'array', 'water_glasses' => 'integer', 'pain_score' => 'integer'];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
