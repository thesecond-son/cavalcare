<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A booking made on the website, shaped so the app can pre-fill
 * "Add parent" (step 1 details, step 2 health) and show the plan.
 * Money is in kobo (integer), as everywhere in the API.
 */
class WebsiteBookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $intake  = $this->booking;
        $patient = $intake?->patient_details ?? [];
        $details = $intake?->booking_details ?? [];
        $booker  = $this->booker_details ?? [];
        $status  = $this->status instanceof \BackedEnum ? $this->status->value : $this->status;

        return [
            'reference'   => $this->reference,
            'patient_id'  => $this->patient_id,   // the client record, once the intake form is in
            'status'      => match ($status) {
                'converted' => 'intake_received',
                'paid'      => 'awaiting_intake',
                default     => 'not_paid',
            },
            'booked_at'   => $this->created_at?->toIso8601String(),
            'amount_paid_kobo' => $this->amount_paid,
            'care_type'   => $this->careType ? ['id' => $this->careType->id, 'name' => $this->careType->name, 'slug' => $this->careType->slug] : null,
            'care_plan'   => $this->carePlan ? [
                'id'            => $this->carePlan->id,
                'name'          => $this->carePlan->name,
                'service_level' => $this->carePlan->service_level,       // caretaker | nursing
                'level_label'   => $this->carePlan->service_level_label, // Caretaker | Licensed Nurse
                'price_ngn'     => $this->carePlan->price,                // naira, as stored on the website
                'billing_cycle' => $this->carePlan->billing_cycle,
            ] : null,
            'add_ons'     => $this->addOns->map(fn ($a) => [
                'id' => $a->id, 'name' => $a->name, 'price_ngn' => (int) $a->pivot->price_at_booking,
            ])->values(),
            'booker'      => [
                'name'  => $booker['name'] ?? null,
                'email' => $booker['email'] ?? null,
                'phone' => $booker['phone'] ?? null,
            ],
            // Pre-fill for the app's "Add parent" flow. Null until the intake form is sent.
            'parent'      => $intake ? [
                'full_name'         => $patient['full_name'] ?? null,
                'age'               => isset($patient['age']) ? (int) $patient['age'] : null,
                'sex'               => isset($patient['gender']) ? strtolower($patient['gender']) : null,
                'address'           => $patient['address'] ?? null,
                'mobility_level'    => $patient['mobility_level'] ?? null,
                'conditions'        => array_values(array_filter([$patient['diagnosis'] ?? null])),
                'allergies'         => collect($patient['allergies'] ?? [])->pluck('allergy')->filter()->values(),
                'medications'       => collect($patient['medications'] ?? [])->pluck('medication')->filter()->values(),
                'daily_tasks'       => collect($patient['daily_tasks'] ?? [])->pluck('task')->filter()->values(),
                'emergency_contact' => $patient['emergency_contact'] ?? ($booker['emergency_number'] ?? null),
            ] : null,
            'schedule'    => $intake ? [
                'start_date' => $details['start_date'] ?? null,
                'end_date'   => $details['end_date'] ?? null,
            ] : null,
        ];
    }
}
