<?php

namespace App\Models\Care;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasUuids;

    protected $guarded = ['id'];

    protected $casts = [
        'schedule' => 'array',
        'active'   => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
