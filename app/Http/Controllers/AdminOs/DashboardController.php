<?php

namespace App\Http\Controllers\AdminOs;

use App\Http\Controllers\Controller;
use App\Models\Care\CareBooking;
use App\Models\Care\Concern;
use App\Models\Care\Credential;
use App\Models\Care\SafetyAlert;
use App\Models\Care\Visit;
use App\Models\Care\VisitCheckIn;
use App\Models\CarePlan;
use App\Models\CareType;
use App\Models\PreBooking;
use App\PreBookingStatus;
use Illuminate\Http\Request;

/**
 * The coordinator's first screen (design: "Agency dashboard"): today's
 * visits, what needs a person now, new bookings, and money this month.
 */
class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $monthStart = now()->startOfMonth();
        $dayStart = now('Africa/Lagos')->startOfDay()->utc();
        $dayEnd = $dayStart->copy()->addDay();

        $today = Visit::with(['patient', 'professional.user', 'checkIn'])
            ->whereBetween('scheduled_start', [$dayStart, $dayEnd])->orderBy('scheduled_start')->get();

        $websitePaid = PreBooking::whereIn('status', [PreBookingStatus::PAID, PreBookingStatus::CONVERTED])->where('updated_at', '>=', $monthStart);
        $appPaid = CareBooking::whereNotNull('paid_at')->where('paid_at', '>=', $monthStart)->where('status', '!=', 'cancelled');

        $stats = [
            'visits_today'    => $today->count(),
            'visits_done'     => $today->where('state', 'checked_out')->count(),
            'visits_live'     => $today->whereIn('state', ['en_route', 'checked_in'])->count(),
            'visits_upcoming' => $today->where('state', 'scheduled')->count(),
            'matching'        => CareBooking::where('status', 'matching')->count(),
            'awaiting_intake' => PreBooking::where('status', PreBookingStatus::PAID)->count(),
            'revenue_month'   => (int) ((clone $websitePaid)->sum('amount_paid') + (clone $appPaid)->sum('total_kobo')) / 100,
            'bookings_month'  => (clone $websitePaid)->count() + (clone $appPaid)->count(),
            'credentials'     => Credential::where('status', 'in_progress')->count(),
            'care_types'      => CareType::count(),
            'care_plans'      => CarePlan::count(),
        ];

        // Everything that needs a person, most urgent first.
        $alerts = collect();
        foreach (SafetyAlert::with('professional.user')->whereNull('acknowledged_at')->latest()->get() as $a) {
            $alerts->push(['urgent', 'Safety alert · ' . $a->professional?->name, $a->note ?: ($a->call_me ? 'Asked to be called.' : 'Raised a safety alert.'), route('os.visits.index')]);
        }
        foreach (Concern::with('visit.patient')->whereNull('resolved_at')->orderByRaw("FIELD(severity, 'urgent', 'attention')")->get() as $c) {
            $alerts->push([$c->severity === 'urgent' ? 'urgent' : 'attn', ucfirst($c->severity) . ' · ' . ($c->visit->patient->display_name ?: $c->visit->patient->full_name),
                ucfirst(str_replace('_', ' ', $c->kind)) . ($c->where_on_body ? ' (' . $c->where_on_body . ')' : '') . '. ' . ($c->acknowledged_at ? 'Acknowledged.' : 'Not acknowledged yet.'), route('os.visits.show', $c->visit_id)]);
        }
        $disputed = VisitCheckIn::with('visit.patient', 'visit.professional.user')->whereNull('resolved_at')
            ->where(fn ($q) => $q->where('disputed', true)->orWhere(fn ($w) => $w->where('method', 'manual')->whereNull('confirmation_answer')->where('confirmation_asked_at', '<=', now()->subMinutes(15))))->get();
        foreach ($disputed as $ci) {
            $alerts->push(['urgent', ($ci->disputed ? 'Arrival disputed' : 'Arrival not confirmed') . ' · ' . ($ci->visit->patient->display_name ?: $ci->visit->patient->full_name),
                $ci->visit->professional?->name . ' checked in manually. Call the patient.', route('os.visits.index')]);
        }
        foreach (Visit::with(['patient', 'professional.user'])->whereIn('state', ['scheduled', 'en_route'])->where('scheduled_start', '<=', now()->subMinutes(30))->where('scheduled_start', '>=', now()->subDay())->get() as $v) {
            $alerts->push(['attn', 'Caregiver late · ' . ($v->patient->display_name ?: $v->patient->full_name), ($v->professional?->name ?? 'Caregiver') . ' has not checked in.', route('os.visits.show', $v)]);
        }
        foreach (CareBooking::with('patient')->where('status', 'matching')->where('paid_at', '<=', now()->subHours(2))->get() as $b) {
            $alerts->push(['attn', 'No caregiver yet · ' . ($b->patient->display_name ?: $b->patient->full_name), 'Paid ' . $b->paid_at->diffForHumans() . ', starts ' . $b->start_date->format('j M') . '.', route('os.app-bookings.show', $b)]);
        }
        foreach (PreBooking::where('status', PreBookingStatus::PAID)->where('updated_at', '<=', now()->subDay())->latest('updated_at')->limit(5)->get() as $pb) {
            $alerts->push(['attn', 'Intake overdue · ' . ($pb->booker_details['name'] ?? $pb->reference), 'Paid ' . $pb->updated_at->diffForHumans() . ' on the website, care details not sent.', route('os.bookings.show', $pb->reference)]);
        }

        $recent = PreBooking::with(['carePlan', 'careType'])->latest()->limit(5)->get();

        return view('adminos.dashboard', compact('stats', 'today', 'alerts', 'recent'));
    }
}
