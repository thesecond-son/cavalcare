<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Http\Resources\V1\WebsiteBookingResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MeController extends Controller
{
    /** GET /api/v1/me */
    public function show(Request $request)
    {
        return response()->json(['data' => new UserResource($request->user())]);
    }

    /**
     * PATCH /api/v1/me: name, time zone, currency, country.
     * Email and phone are changed through their own verified flows (later phase).
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'name'               => ['sometimes', 'required', 'string', 'max:255'],
            'time_zone'          => ['sometimes', 'required', 'timezone:all'],
            'preferred_currency' => ['sometimes', 'required', Rule::in(['NGN', 'GBP', 'USD'])],
            'country_code'       => ['sometimes', 'nullable', 'string', 'size:2'],
        ]);

        if (isset($data['country_code'])) {
            $data['country_code'] = strtoupper($data['country_code']);
        }

        $request->user()->fill($data)->save();

        return response()->json(['data' => new UserResource($request->user()->fresh())]);
    }

    /** GET /api/v1/me/website-bookings: bookings made on the website and linked to this account. */
    public function websiteBookings(Request $request)
    {
        $bookings = $request->user()->preBookings()
            ->with(['carePlan', 'careType', 'addOns', 'booking'])
            ->latest()
            ->get();

        return response()->json(['data' => WebsiteBookingResource::collection($bookings)]);
    }
}
