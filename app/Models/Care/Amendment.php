<?php

namespace App\Models\Care;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Amendment extends Model
{
    use HasUuids;

    protected $table = 'amendments';

    protected $guarded = ['id'];

    protected $casts = ['at' => 'datetime'];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
