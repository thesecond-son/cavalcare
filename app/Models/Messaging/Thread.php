<?php

namespace App\Models\Messaging;

use App\Models\Care\CareBooking;
use App\Models\Care\Patient;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * A conversation. care_team = family + caregiver + coordinators for one
 * booking; coordinator = one person and CavalCare; support = help desk.
 */
class Thread extends Model
{
    use HasUuids;

    protected $guarded = ['id'];

    protected $casts = ['last_message_at' => 'datetime'];

    public function participants() { return $this->hasMany(ThreadParticipant::class); }
    public function messages()     { return $this->hasMany(Message::class)->orderBy('sent_at'); }
    public function patient()      { return $this->belongsTo(Patient::class); }
    public function booking()      { return $this->belongsTo(CareBooking::class, 'care_booking_id'); }
}
