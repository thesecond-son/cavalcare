<?php

namespace App\Http\Controllers\AdminOs;

use App\Http\Controllers\Controller;

/**
 * Sections whose data comes from the Cavalcare and CavalPro apps. They are
 * listed in the navigation so the OS shows its full shape, and each page
 * says plainly what it will hold and which build phase delivers it.
 */
class SectionController extends Controller
{
    public const SECTIONS = [
        'schedule'   => ['Schedule', 'Week calendar of visits, unfilled shifts and free caregivers nearby. Drag a visit to another caregiver, send an offer or post an open shift.', 'Phase 3: shifts and matching'],
        'visits'     => ['Visits & notes', 'Today\'s check-ins, full visit timelines, vitals with range flags, care notes, concerns and amendments.', 'Phase 3: visit records and sync'],
        'billing'    => ['Billing & payouts', 'Payments, refunds, invoices, revenue splits and weekly caregiver payouts.', 'Phase 4: payments and payouts'],
        'messages'   => ['Family messages', 'One inbox for care-team, coordinator and support threads.', 'Phase 4: messaging'],
        'analytics'  => ['Analytics', 'Revenue, care hours, fill rate, cancellations and caregiver utilisation, with CSV export.', 'Phase 5: reports'],
        'settings'   => ['Settings', 'Admin users and roles, two-factor sign-in, support phone, refund policy and app minimum versions.', 'Phase 1: admin roles and 2FA'],
    ];

    public function __invoke(string $section)
    {
        [$title, $summary, $phase] = self::SECTIONS[$section];

        return view('adminos.section', compact('section', 'title', 'summary', 'phase'));
    }
}
