<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** The signed-in user, as the apps see them. No roles, no security fields. */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name ?: null,
            'email'              => $this->email,
            'email_verified'     => $this->email_verified_at !== null,
            'phone'              => $this->phone_e164,
            'phone_verified'     => $this->phone_verified_at !== null,
            'country_code'       => $this->country_code,
            'time_zone'          => $this->time_zone,
            'preferred_currency' => $this->preferred_currency,
            'needs_profile'      => blank($this->name),
        ];
    }
}
