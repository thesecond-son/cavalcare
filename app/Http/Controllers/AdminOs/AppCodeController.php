<?php

namespace App\Http\Controllers\AdminOs;

use App\Http\Controllers\Controller;
use App\Models\PreBooking;
use App\PreBookingStatus;
use App\Services\Auth\AppLinkCodeService;
use App\Support\AdminOs\Audit;
use Illuminate\Http\Request;

/**
 * Sends a family a new Cavalcare app code from Caval OS (e.g. the 17
 * families who paid before codes existed, or a family who lost theirs).
 * The code is emailed to the booker only; staff never see it.
 */
class AppCodeController extends Controller
{
    public function store(Request $request, PreBooking $preBooking, AppLinkCodeService $links)
    {
        $paid = in_array($preBooking->status, [PreBookingStatus::PAID, PreBookingStatus::CONVERTED], true);

        if (! $paid) {
            return back()->with('os_flash', ['attn', 'Only paid bookings can get an app code.']);
        }
        if ($preBooking->user_id) {
            return back()->with('os_flash', ['attn', 'This booking is already linked to an app account.']);
        }

        try {
            $sent = $links->issueAndEmail($preBooking, $request->user());
        } catch (\Throwable $e) {
            report($e);
            return back()->with('os_flash', ['urgent', 'The email could not be sent. Try again in a moment.']);
        }

        if (! $sent) {
            return back()->with('os_flash', ['attn', 'This booking has no email address to send the code to.']);
        }

        Audit::log('booking.app_code_sent', $preBooking, ['reference' => $preBooking->reference]);

        return back()->with('os_flash', ['stable', 'A new app code was emailed to ' . $preBooking->booker_details['email'] . '. Any earlier code stopped working.']);
    }
}
