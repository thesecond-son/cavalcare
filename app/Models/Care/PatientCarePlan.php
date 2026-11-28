<?php

namespace App\Models\Care;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PatientCarePlan extends Model
{
    use HasUuids;

    protected $table = 'patient_care_plans';

    protected $guarded = ['id'];

    protected $casts = ['tasks' => 'array', 'vitals_required' => 'array', 'carer_allowed_vitals' => 'array'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
