<?php

namespace App\Models\Care;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class VisitCheckOut extends Model
{
    use HasUuids;

    protected $table = 'visit_check_outs';

    protected $guarded = ['id'];

    protected $casts = ['at' => 'datetime', 'lat' => 'float', 'lng' => 'float'];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
