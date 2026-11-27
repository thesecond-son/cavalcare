<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A patient as the family app sees it. Load "members" so my_role and
 * can_edit tell the app what this person may do (spec 3.1).
 */
class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $member = $this->relationLoaded('members') ? $this->members->firstWhere('user_id', $request->user()?->id) : null;
        $role = $member?->role;

        return [
            'id'             => $this->id,
            'full_name'      => $this->full_name,
            'display_name'   => $this->display_name,
            'address_as'     => $this->address_as,
            'pronoun'        => $this->pronoun,
            'date_of_birth'  => $this->date_of_birth?->toDateString(),
            'age'            => $this->age,
            'sex'            => $this->sex,
            'phone'          => $this->phone_e164,
            'address'        => [
                'line'     => $this->address_line,
                'note'     => $this->address_note,
                'area'     => $this->area,
                'city'     => $this->city,
                'state'    => $this->state,
                'lat'      => $this->lat,
                'lng'      => $this->lng,
                'place_id' => $this->place_id,
            ],
            'languages'      => $this->languages ?? [],
            'mobility_level' => $this->mobility_level,
            'consent_given'  => $this->consent_given_at !== null,
            'consent_given_at' => $this->consent_given_at?->toIso8601String(),
            'source'         => $this->source,
            'my_role'        => $role,
            'can_edit'       => in_array($role, ['owner', 'caregiver'], true),
            'allergies'      => $this->whenLoaded('allergies', fn () => $this->allergies->map(fn ($a) => [
                'id' => $a->id, 'name' => $a->name, 'reaction' => $a->reaction, 'severe' => $a->severe,
            ])),
            'conditions'     => $this->whenLoaded('conditions', fn () => $this->conditions->map(fn ($c) => [
                'id' => $c->id, 'name' => $c->name, 'notes' => $c->notes,
            ])),
            'medications'    => $this->whenLoaded('medications', fn () => $this->medications->map(fn ($m) => [
                'id' => $m->id, 'name' => $m->name, 'dose' => $m->dose, 'instructions' => $m->instructions,
                'times' => $m->schedule['times'] ?? [], 'with_food' => $m->schedule['with_food'] ?? null, 'active' => $m->active,
            ])),
            'emergency_contacts' => $this->whenLoaded('emergencyContacts', fn () => $this->emergencyContacts->map(fn ($e) => [
                'id' => $e->id, 'name' => $e->name, 'relation' => $e->relation,
                'phone' => $e->phone_e164 ?? $e->phone_raw, 'location' => $e->location,
            ])),
        ];
    }
}
