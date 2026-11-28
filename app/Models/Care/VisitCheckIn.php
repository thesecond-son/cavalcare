<?php

namespace App\Models\Care;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class VisitCheckIn extends Model
{
    use HasUuids;

    protected $table = 'visit_check_ins';

    protected $guarded = ['id'];

    protected $casts = ['at' => 'datetime', 'confirmation_asked_at' => 'datetime', 'confirmation_answered_at' => 'datetime', 'coordinator_called_at' => 'datetime', 'resolved_at' => 'datetime', 'disputed' => 'boolean', 'code_valid' => 'boolean', 'lat' => 'float', 'lng' => 'float'];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
