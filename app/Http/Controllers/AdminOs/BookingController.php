<?php

namespace App\Http\Controllers\AdminOs;

use App\Http\Controllers\Controller;
use App\Models\PreBooking;
use App\PreBookingStatus;
use App\Support\AdminOs\Audit;
use Illuminate\Http\Request;

/**
 * Website bookings. A PreBooking is created at checkout; it becomes a
 * Booking once the family submits the intake form.
 */
class BookingController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = trim((string) $request->query('q', ''));

        $bookings = PreBooking::with(['carePlan', 'careType', 'booking'])
            ->when(
                $status && PreBookingStatus::tryFrom($status),
                fn ($q) => $status === PreBookingStatus::PENDING_PAYMENT->value
                    // Older rows use the legacy "pending" name for the same state.
                    ? $q->whereIn('status', [$status, PreBookingStatus::LEGACY_PENDING->value])
                    : $q->where('status', $status)
            )
            ->when($search !== '', function ($q) use ($search) {
                $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $search) . '%';
                $q->where(fn ($w) => $w
                    ->where('reference', 'like', $like)
                    ->orWhere('booker_details->name', 'like', $like)
                    ->orWhere('booker_details->email', 'like', $like));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = PreBooking::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('adminos.bookings.index', compact('bookings', 'counts', 'status', 'search'));
    }

    public function show(PreBooking $preBooking)
    {
        $preBooking->load(['carePlan', 'careType', 'addOns', 'booking', 'user', 'appLinkCodes' => fn ($q) => $q->latest()]);

        // Viewing a booking exposes personal (and possibly health) data.
        Audit::log('booking.viewed', $preBooking, ['reference' => $preBooking->reference]);

        return view('adminos.bookings.show', ['pb' => $preBooking]);
    }
}
