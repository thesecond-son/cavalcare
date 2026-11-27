<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** A care plan as the family app shows it. Money in kobo plus a display string. */
class CarePlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'service_level' => $this->service_level,          // caretaker | nursing
            'level_label'   => $this->service_level_label,    // Caretaker | Licensed Nurse
            'description'   => $this->description,
            'billing_cycle' => $this->billing_cycle,          // per_day | per_night | per_24h | per_week | per_month | per_10_visits | flat
            'price_kobo'    => (int) $this->price * 100,
            'display'       => $this->formatted_price,        // "₦25,000/day"
            'per_unit'      => in_array($this->billing_cycle, ['per_day', 'per_night', 'per_24h'], true),
            'duration_days' => $this->meta['duration_days'] ?? null,
            'daily_hours'   => $this->meta['daily_hours'] ?? null,
        ];
    }
}
