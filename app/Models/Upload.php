<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasUuids;

    protected $guarded = ['id'];

    protected $casts = ['attached_at' => 'datetime', 'size_bytes' => 'integer'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
