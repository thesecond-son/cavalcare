<?php

namespace App\Http\Resources\V1;

use App\Models\Care\Credential;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** The signed-in caregiver's own profile in CavalPro. Never pay splits or family prices. */
class ProfessionalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        [$done, $total] = $this->verificationProgress();

        return [
            'id'                   => $this->id,
            'name'                 => $this->user?->name,
            'phone'                => $this->user?->phone_e164,
            'email'                => $this->user?->email,
            'title'                => $this->title,                 // rn | carer
            'title_label'          => $this->title === 'rn' ? 'Registered nurse' : 'Caregiver',
            'status'               => $this->status,                // applicant | verifying | active | suspended
            'can_take_shifts'      => $this->status === 'active',
            'verification'         => ['verified' => $done, 'required' => $total],
            'bio'                  => $this->bio,
            'years_experience'     => $this->years_experience,
            'sex'                  => $this->sex,
            'languages'            => $this->languages ?? [],
            'skills'               => $this->skills ?? [],
            'specialties'          => $this->specialties ?? [],
            'home_area'            => $this->home_area,
            'lat'                  => $this->lat,
            'lng'                  => $this->lng,
            'max_travel_km'        => $this->max_travel_km,
            'employment'           => $this->employment,
            'open_to_extra_shifts' => $this->open_to_extra_shifts,
            'data_saver'           => $this->data_saver,
            'rating'               => $this->rating_avg,
            'reviews_count'        => $this->reviews_count,
            'visits_count'         => $this->visits_count,
            'availability'         => $this->whenLoaded('availabilityRules', fn () => $this->availabilityRules->map(fn ($r) => [
                'weekday' => $r->weekday, 'start_time' => substr($r->start_time, 0, 5), 'end_time' => substr($r->end_time, 0, 5),
            ])),
        ];
    }

    /** One checklist row per credential type (required first). */
    public static function checklist($pro): array
    {
        $required = $pro->requiredCredentialTypes();
        $byType = $pro->credentials->keyBy('type');
        $optional = $pro->title === 'rn' ? ['bls', 'reference'] : ['bls', 'reference'];

        return collect(array_merge($required, $optional))->unique()->map(function ($type) use ($byType, $required) {
            $c = $byType->get($type);
            $expired = $c?->valid_to && $c->valid_to->endOfDay()->isPast();

            return [
                'type'             => $type,
                'label'            => Credential::TYPES[$type],
                'required'         => in_array($type, $required, true),
                'status'           => $c ? ($expired ? 'expired' : $c->status) : 'to_do',
                'valid_to'         => $c?->valid_to?->toDateString(),
                'number_last4'     => $c?->number ? mb_substr($c->number, -4) : null,
                'has_document'     => (bool) $c?->file_path,
                'rejection_reason' => $c?->status === 'rejected' ? $c->rejection_reason : null,
            ];
        })->values()->all();
    }
}
