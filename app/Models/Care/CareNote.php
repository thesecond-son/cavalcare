<?php

namespace App\Models\Care;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CareNote extends Model
{
    use HasUuids;

    protected $table = 'care_notes';

    protected $guarded = ['id'];

    protected $casts = ['recorded_at' => 'datetime', 'synced_at' => 'datetime', 'clock_drift_flag' => 'boolean'];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
