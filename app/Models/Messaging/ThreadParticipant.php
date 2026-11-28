<?php

namespace App\Models\Messaging;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ThreadParticipant extends Model
{
    use HasUuids;

    protected $table = 'thread_participants';

    protected $guarded = ['id'];

    protected $casts = ['last_read_at' => 'datetime', 'read_only' => 'boolean', 'muted' => 'boolean'];

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
