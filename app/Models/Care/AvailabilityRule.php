<?php

namespace App\Models\Care;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/** A weekly working window, e.g. Monday 08:00 to 17:00. */
class AvailabilityRule extends Model
{
    use HasUuids;

    public const DAYS = [1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun'];

    protected $guarded = ['id'];

    protected $casts = ['weekday' => 'integer'];

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }
}
