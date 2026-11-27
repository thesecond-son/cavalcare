<?php

namespace App\Http\Controllers\Api\V1\Pro;

use App\Http\Controllers\Controller;
use App\Models\Care\CareBooking;
use App\Models\Care\Professional;
use App\Models\Care\ShiftOffer;
use App\Services\Care\MatchingService;
use App\Support\ApiError;
use Illuminate\Http\Request;

/**
 * Shift offers in CavalPro (backend spec 8.3).
 * Offers carry no name, street address or family price. Patient details
 * unlock only after accepting (GET /pro/assignments).
 */
class ShiftOfferController extends Controller
{
    public function __construct(private MatchingService $matching)
    {
    }

    /** GET /pro/shift-offers?source=assigned|open */
    public function index(Request $request)
    {
        $pro = $this->pro($request);
        $this->matching->expireDue();   // never show an offer that has run out

        $source = $request->query('source', 'assigned');

        if ($source === 'open') {
            // Open shifts this caregiver qualifies for (an unverified caregiver can browse, not accept).
            $offers = ShiftOffer::with('booking.patient')->where('source', 'open')->where('status', 'open')->latest()->get()
                ->filter(fn ($o) => $o->booking && ($pro->status !== 'active' || $this->matching->qualifies($pro, $o->booking)))
                ->each(fn ($o) => $o->distance_km = $this->matching->distanceKm($pro, $o->booking))
                ->sortBy(fn ($o) => $o->distance_km ?? 999)->values();
        } else {
            $offers = ShiftOffer::where('professional_id', $pro->id)->where('source', 'assigned')->where('status', 'open')->latest()->get();
        }

        return response()->json(['data' => $offers->map(fn ($o) => $this->offer($o, $pro))]);
    }

    /** GET /pro/shift-offers/{id} */
    public function show(Request $request, string $id)
    {
        $pro = $this->pro($request);
        return response()->json(['data' => $this->offer($this->visible($pro, $id), $pro)]);
    }

    /** POST /pro/shift-offers/{id}/accept: atomic; returns the unlocked assignment. */
    public function accept(Request $request, string $id)
    {
        $pro = $this->pro($request);
        $offer = $this->visible($pro, $id);
        $booking = $this->matching->accept($offer, $pro);

        return response()->json(['data' => $this->assignment($booking->fresh(['patient.allergies', 'patient.conditions', 'patient.medications', 'patient.emergencyContacts', 'careType']))]);
    }

    /** POST /pro/shift-offers/{id}/decline */
    public function decline(Request $request, string $id)
    {
        $pro = $this->pro($request);
        $data = $request->validate(['reason' => ['nullable', 'string', 'max:255']]);
        $this->matching->decline($this->visible($pro, $id), $pro, $data['reason'] ?? null);

        return response()->json(['data' => ['declined' => true]]);
    }

    /** GET /pro/assignments: accepted bookings with the patient brief (allergies first). */
    public function assignments(Request $request)
    {
        $pro = $this->pro($request);
        $bookings = CareBooking::with(['patient.allergies', 'patient.conditions', 'patient.medications', 'patient.emergencyContacts', 'careType'])
            ->where('professional_id', $pro->id)
            ->whereIn('status', ['matched', 'active'])
            ->orderBy('start_date')
            ->get();

        return response()->json(['data' => $bookings->map(fn ($b) => $this->assignment($b))]);
    }

    // ── Shapes ───────────────────────────────────────────────────────────

    /** The privacy-safe offer: what, where (area only), when, and pay. Never the family price. */
    private function offer(ShiftOffer $o, Professional $pro): array
    {
        return [
            'id'                 => $o->id,
            'source'             => $o->source,
            'why_you'            => $o->source === 'assigned' ? $o->why_you : null,
            'care_type'          => $o->care_type,
            'age_sex'            => $o->age_sex,
            'summary'            => $o->summary,
            'area'               => $o->area,
            'distance_km'        => $o->distance_km,
            'dates'              => $o->dates,
            'visits_count'       => $o->visits_count,
            'pay_per_visit_kobo' => $pro->employment === 'agency' ? null : $o->pay_per_visit_kobo,
            'paid_via'           => $pro->employment === 'agency' ? 'Your agency' : null,
            'skills_required'    => $o->skills_required ?? [],
            'expires_at'         => $o->expires_at?->toIso8601String(),
            'status'             => $o->status,
            'can_accept'         => $pro->status === 'active',
        ];
    }

    /** After accepting: who and where, with allergies first. */
    private function assignment(CareBooking $b): array
    {
        $p = $b->patient;

        return [
            'booking_id'   => $b->id,
            'reference'    => $b->reference,
            'care_type'    => $b->careType?->name,
            'plan'         => $b->plan_name,
            'dates'        => $b->visitDates(),
            'start_hour'   => $b->start_hour,
            'notes'        => $b->notes,
            'patient'      => [
                'name'        => $p->display_name ?: $p->full_name,
                'full_name'   => $p->full_name,
                'address_as'  => $p->address_as,
                'age_sex'     => $p->age_sex,
                'phone'       => $p->phone_e164,
                'address'     => ['line' => $p->address_line, 'note' => $p->address_note, 'area' => $p->area, 'city' => $p->city, 'lat' => $p->lat, 'lng' => $p->lng],
                'allergies'   => $p->allergies->map(fn ($a) => ['name' => $a->name, 'reaction' => $a->reaction, 'severe' => $a->severe]),
                'conditions'  => $p->conditions->pluck('name'),
                'medications' => $p->medications->where('active', true)->map(fn ($m) => ['name' => $m->name, 'dose' => $m->dose, 'times' => $m->schedule['times'] ?? []])->values(),
                'mobility'    => $p->mobility_level,
                'emergency_contacts' => $p->emergencyContacts->map(fn ($e) => ['name' => $e->name, 'relation' => $e->relation, 'phone' => $e->phone_e164]),
            ],
        ];
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function pro(Request $request): Professional
    {
        $pro = Professional::with(['user', 'credentials', 'availabilityRules'])->where('user_id', $request->user()->id)->first();
        if (! $pro) {
            abort(ApiError::make('profile_missing', 'Finish "About you" first.', 404));
        }

        return $pro;
    }

    /** An offer this caregiver may see: their own assigned offer, or an open shift. */
    private function visible(Professional $pro, string $id): ShiftOffer
    {
        $offer = ShiftOffer::with('booking.patient')->find($id);
        // Open shifts are visible to everyone (so a late accept gets "shift_taken");
        // an assigned offer only to the caregiver it was sent to.
        abort_unless($offer && ($offer->source === 'open' || $offer->professional_id === $pro->id), 404);

        return $offer;
    }
}
