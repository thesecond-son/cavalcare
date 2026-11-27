<?php

namespace App\Http\Resources\V1;

use App\Models\Care\CareBooking;
use App\Services\Care\BookingPricing;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** An app booking as the family sees it. Never shows caregiver pay or splits (spec 13.2). */
class CareBookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $pro = $this->professional;
        $payment = $this->relationLoaded('payments') ? $this->payments->first() : null;

        return [
            'id'               => $this->id,
            'reference'        => $this->reference,
            'status'           => $this->status,
            'status_label'     => CareBooking::STATUSES[$this->status] ?? $this->status,
            'patient_id'       => $this->patient_id,
            'care_type'        => $this->careType?->name,
            'plan'             => [
                'id' => $this->care_plan_id, 'name' => $this->plan_name, 'service_level' => $this->service_level,
                'billing_cycle' => $this->billing_cycle,
            ],
            'start_date'       => $this->start_date->toDateString(),
            'start_hour'       => $this->start_hour,
            'end_date'         => $this->end_date?->toDateString(),
            'units'            => $this->units,
            'caregiver_choice' => $this->caregiver_choice,
            'preferences'      => $this->preferences ?? (object) [],
            'notes'            => $this->notes,
            'price'            => [
                'per_unit_kobo' => $this->price_per_unit_kobo,
                'add_ons'       => $this->add_ons ?? [],
                'add_ons_kobo'  => $this->add_ons_total_kobo,
                'total_kobo'    => $this->total_kobo,
                'total_display' => BookingPricing::naira($this->total_kobo),
            ],
            'payment'          => $payment ? ['reference' => $payment->reference, 'status' => $payment->status, 'paid_at' => $payment->paid_at?->toIso8601String()] : null,
            'caregiver'        => $pro && in_array($this->status, ['matched', 'active', 'completed'], true) ? [
                'id'          => $pro->id,
                'name'        => $pro->user?->name,
                'title_label' => $pro->title === 'rn' ? 'Registered nurse' : 'Caregiver',
                'rating'      => $pro->rating_avg,
                'verified'    => true,
            ] : null,
            'paid_at'          => $this->paid_at?->toIso8601String(),
            'matched_at'       => $this->matched_at?->toIso8601String(),
            'cancelled_at'     => $this->cancelled_at?->toIso8601String(),
            'created_at'       => $this->created_at->toIso8601String(),
        ];
    }
}
