<?php

namespace App\Http\Controllers\AdminOs;

use App\Http\Controllers\Controller;
use App\Models\Care\CareBooking;
use App\Models\Care\Professional;
use App\Services\Care\MatchingService;
use App\Support\AdminOs\Audit;
use Illuminate\Http\Request;

/**
 * Bookings made in the Cavalcare app: payment, matching progress and the
 * offers sent to caregivers. Coordinators can offer a booking to a chosen
 * caregiver or post it as an open shift (spec 11, "Coordinator" path).
 */
class AppBookingController extends Controller
{
    public function __construct(private MatchingService $matching)
    {
    }

    public function index(Request $request)
    {
        $status = $request->query('status');

        $bookings = CareBooking::with(['patient', 'professional.user', 'booker'])
            ->when($status && isset(CareBooking::STATUSES[$status]), fn ($q) => $q->where('status', $status))
            ->orderByRaw("FIELD(status, 'matching', 'confirmed', 'pending_payment', 'matched', 'active', 'completed', 'cancelled')")
            ->orderBy('start_date')
            ->paginate(20)
            ->withQueryString();

        $counts = CareBooking::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status');

        return view('adminos.app-bookings.index', compact('bookings', 'counts', 'status'));
    }

    public function show(CareBooking $booking)
    {
        $booking->load(['patient.allergies', 'booker', 'professional.user', 'payments', 'offers.professional.user', 'careType']);
        $candidates = $booking->status === 'matching' ? $this->matching->candidates($booking)->take(8) : collect();

        Audit::log('app_booking.viewed', $booking, ['reference' => $booking->reference]);

        return view('adminos.app-bookings.show', ['b' => $booking, 'candidates' => $candidates]);
    }

    /** Send an offer to a specific qualified caregiver. */
    public function offer(Request $request, CareBooking $booking)
    {
        $data = $request->validate(['professional_id' => ['required', 'uuid']]);
        abort_unless($booking->status === 'matching', 409, 'This booking is not looking for a caregiver.');

        $pro = Professional::with(['user', 'credentials', 'availabilityRules'])->findOrFail($data['professional_id']);
        if (! $this->matching->qualifies($pro, $booking)) {
            return back()->with('os_flash', ['attn', $pro->name . ' doesn\'t qualify for this booking (role, checks, area, days or a clash).']);
        }

        // One live offer at a time: withdraw any open one first.
        $booking->offers()->where('status', 'open')->update(['status' => 'withdrawn', 'responded_at' => now()]);
        $this->matching->offerTo($booking, $pro, $request->user());

        Audit::log('app_booking.offer_sent', $booking, ['professional' => $pro->id]);

        return back()->with('os_flash', ['stable', 'Offer sent to ' . $pro->name . '. It stays open for 15 minutes (2 hours when the start is more than 3 days away).']);
    }

    /** Post it as an open shift for every qualified caregiver. */
    public function open(Request $request, CareBooking $booking)
    {
        abort_unless($booking->status === 'matching', 409, 'This booking is not looking for a caregiver.');

        $booking->offers()->where('status', 'open')->where('source', 'assigned')->update(['status' => 'withdrawn', 'responded_at' => now()]);
        $this->matching->openShift($booking, $request->user());

        Audit::log('app_booking.opened', $booking);

        return back()->with('os_flash', ['stable', 'Posted as an open shift. The first qualified caregiver to accept gets it.']);
    }
}
