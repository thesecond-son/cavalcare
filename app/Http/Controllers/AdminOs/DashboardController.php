<?php

namespace App\Http\Controllers\AdminOs;

use App\Http\Controllers\Controller;
use App\Models\CarePlan;
use App\Models\CareType;
use App\Models\PreBooking;
use App\PreBookingStatus;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $monthStart = now()->startOfMonth();
        $paidStates = [PreBookingStatus::PAID, PreBookingStatus::CONVERTED];

        $paidThisMonth = PreBooking::whereIn('status', $paidStates)
            ->where('updated_at', '>=', $monthStart);

        $stats = [
            'paid_month'      => (clone $paidThisMonth)->count(),
            'revenue_month'   => (int) (clone $paidThisMonth)->sum('amount_paid') / 100, // kobo to naira
            'awaiting_intake' => PreBooking::where('status', PreBookingStatus::PAID)->count(),
            'abandoned_week'  => PreBooking::whereIn('status', [PreBookingStatus::PENDING_PAYMENT, PreBookingStatus::LEGACY_PENDING])
                                    ->where('created_at', '>=', now()->subDays(7))->count(),
            'care_types'      => CareType::count(),
            'care_plans'      => CarePlan::count(),
        ];

        // Paid but the family has not filled the intake form for over 24 hours.
        $stalledIntake = PreBooking::with('carePlan')
            ->where('status', PreBookingStatus::PAID)
            ->where('updated_at', '<=', now()->subDay())
            ->latest('updated_at')
            ->limit(5)
            ->get();

        $recent = PreBooking::with(['carePlan', 'careType'])
            ->latest()
            ->limit(8)
            ->get();

        return view('adminos.dashboard', compact('stats', 'stalledIntake', 'recent'));
    }
}
