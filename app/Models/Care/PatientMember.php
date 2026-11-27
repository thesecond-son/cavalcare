<?php

namespace App\Models\Care;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/** One person in a patient's family circle, with their role (spec 3.1). */
class PatientMember extends Model
{
    use HasUuids;

    public const ROLES = ['owner' => 'Owner', 'caregiver' => 'Caregiver', 'viewer' => 'Viewer'];

    protected $guarded = ['id'];

    protected $casts = [
        'can_view_documents' => 'boolean',
        'joined_at'          => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
