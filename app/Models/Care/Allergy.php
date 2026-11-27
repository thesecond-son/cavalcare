<?php

namespace App\Models\Care;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Allergy extends Model
{
    use HasUuids;

    protected $table = 'allergies';

    protected $guarded = ['id'];

    protected $casts = ['severe' => 'boolean'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
