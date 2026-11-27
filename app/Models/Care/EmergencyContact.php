<?php

namespace App\Models\Care;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    use HasUuids;

    protected $table = 'emergency_contacts';

    protected $guarded = ['id'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
