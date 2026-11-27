<?php

namespace App\Models\Care;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasUuids;

    protected $table = 'conditions';

    protected $guarded = ['id'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
