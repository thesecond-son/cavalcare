<?php

namespace App\Services\Care;

use App\Models\Care\CareBooking;
use App\Models\Care\Professional;
use App\Models\Care\ShiftOffer;
use App\Models\User;
use App\Support\ApiError;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Gets a paid booking to a caregiver (backend spec 11).
 *
 * - "Chosen": the offer goes to the caregiver the family picked.
 * - "Match me": the best-scored qualified caregiver gets an offer; on
 *   decline or expiry the next one does, up to 3. Then it becomes an open
 *   shift any qualified caregiver can take, first come first served.
 * - A coordinator can send an offer to anyone qualified, or post it open.
 * - Accepting is atomic: the first accept wins, everyone else gets "taken".
 */
class MatchingService
{
    public const MAX_ASSIGNED = 3;

    // ── Starting and moving on ──────────────────────────────────────────

    public function start(CareBooking $booking): void
    {
        if (! in_array($booking->status, ['confirmed', 'matching'], true)) {
            return;
        }
        $booking->update(['status' => 'matching']);

        if ($booking->caregiver_choice === 'chosen' && $booking->requested_professional_id) {
            $pro = Professional::with(['user', 'credentials', 'availabilityRules'])->find($booking->requested_professional_id);
            if ($pro && $this->qualifies($pro, $booking)) {
                $this->offerTo($booking, $pro, null, 'The family chose you');
                return;
            }
        }

        $this->offerNext($booking);
    }

    /** Offers the booking to the next best caregiver, or opens it after 3 tries. */
    public function offerNext(CareBooking $booking): void
    {
        $booking->refresh();
        if ($booking->status !== 'matching') {
            return;
        }
        if ($booking->offers()->where('status', 'open')->exists()) {
            return;    // something is already waiting for an answer
        }

        $tried = $booking->offers()->where('source', 'assigned')->pluck('professional_id')->filter()->all();
        if (count($tried) >= self::MAX_ASSIGNED) {
            $this->openShift($booking);
            return;
        }

        $best = $this->candidates($booking)->reject(fn ($c) => in_array($c['pro']->id, $tried, true))->first();
        $best ? $this->offerTo($booking, $best['pro'], null, $best['why']) : $this->openShift($booking);
    }

    public function offerTo(CareBooking $booking, Professional $pro, ?User $coordinator = null, ?string $why = null): ShiftOffer
    {
        return $booking->offers()->create($this->offerFields($booking, $pro) + [
            'professional_id' => $pro->id,
            'source'          => 'assigned',
            'matched_by'      => $coordinator?->id,
            'why_you'         => $why ?? ($coordinator ? 'Chosen for you by CavalCare' : 'Matched by CavalCare'),
            'expires_at'      => $booking->start_date->isAfter(now()->addDays(3)) ? now()->addHours(2) : now()->addMinutes(15),
        ]);
    }

    /** Posts the booking as an open shift for every qualified caregiver (spec 11.3). */
    public function openShift(CareBooking $booking, ?User $coordinator = null): ShiftOffer
    {
        $existing = $booking->offers()->where('source', 'open')->where('status', 'open')->first();

        return $existing ?? $booking->offers()->create($this->offerFields($booking, null) + [
            'professional_id' => null,
            'source'          => 'open',
            'matched_by'      => $coordinator?->id,
            'expires_at'      => null,
        ]);
    }

    /** Scheduled every minute: expires unanswered offers and moves on. */
    public function expireDue(): int
    {
        $due = ShiftOffer::with('booking')->where('status', 'open')->whereNotNull('expires_at')->where('expires_at', '<=', now())->get();

        foreach ($due as $offer) {
            $offer->update(['status' => 'expired', 'responded_at' => now()]);
            if ($offer->booking && $offer->booking->status === 'matching') {
                $this->offerNext($offer->booking);
            }
        }

        return $due->count();
    }

    // ── Caregiver answers ───────────────────────────────────────────────

    /** Atomic accept (spec 11.4). Throws an API error the app can show. */
    public function accept(ShiftOffer $offer, Professional $pro): CareBooking
    {
        if ($pro->status !== 'active' || ! $pro->isFullyVerified()) {
            abort(ApiError::make('not_verified', 'Finish your checks before taking shifts.', 403));
        }

        return DB::transaction(function () use ($offer, $pro) {
            $booking = CareBooking::lockForUpdate()->findOrFail($offer->care_booking_id);
            $offer = ShiftOffer::lockForUpdate()->findOrFail($offer->id);

            if ($offer->source === 'assigned' && $offer->professional_id !== $pro->id) {
                abort(404);
            }
            if ($booking->status !== 'matching' || in_array($offer->status, ['taken', 'withdrawn', 'accepted'], true)) {
                abort(ApiError::make('shift_taken', 'Another caregiver accepted this shift first.', 409));
            }
            if (! $offer->isLive()) {
                abort(ApiError::make('shift_expired', 'This offer has expired.', 409));
            }
            if (! $offer->professional_id && ! $this->qualifies($pro->loadMissing(['credentials', 'availabilityRules']), $booking)) {
                abort(ApiError::make('not_eligible', 'This shift needs a different role, skill or area.', 403));
            }

            $offer->update(['status' => 'accepted', 'professional_id' => $pro->id, 'responded_at' => now()]);
            $booking->offers()->whereKeyNot($offer->id)->where('status', 'open')->update(['status' => 'taken', 'responded_at' => now()]);
            $booking->update(['status' => 'matched', 'professional_id' => $pro->id, 'matched_at' => now()]);

            return $booking;
        });
    }

    public function decline(ShiftOffer $offer, Professional $pro, ?string $reason): void
    {
        abort_unless($offer->professional_id === $pro->id && $offer->source === 'assigned', 404);
        if (! $offer->isLive()) {
            abort(ApiError::make('shift_expired', 'This offer has already closed.', 409));
        }

        $offer->update(['status' => 'declined', 'decline_reason' => $reason, 'responded_at' => now()]);
        $this->offerNext($offer->booking);
    }

    // ── Who qualifies, and in what order ────────────────────────────────

    /** Hard filters (spec 11.1). */
    public function qualifies(Professional $pro, CareBooking $booking): bool
    {
        if ($pro->status !== 'active' || ! $pro->isFullyVerified()) {
            return false;
        }
        if ($booking->service_level === 'nursing' && $pro->title !== 'rn') {
            return false;     // Licensed Nurse plans exclude carers
        }
        if (! empty($pro->care_types) && ! in_array($booking->care_type_id, array_map('intval', $pro->care_types), true)) {
            return false;
        }

        $prefs = $booking->preferences ?? [];
        if (! empty($prefs['sex']) && $pro->sex && $pro->sex !== $prefs['sex']) {
            return false;
        }
        if (! empty($prefs['language']) && ! in_array(mb_strtolower($prefs['language']), array_map('mb_strtolower', $pro->languages ?? []), true)) {
            return false;
        }

        $km = $this->distanceKm($pro, $booking);
        if ($km !== null && $km > $pro->max_travel_km) {
            return false;
        }

        // Works on every day of the booking (weekly pattern), when a pattern is set.
        if ($pro->availabilityRules->isNotEmpty()) {
            $days = $pro->availabilityRules->pluck('weekday')->unique()->all();
            foreach (array_slice($booking->visitDates(), 0, 14) as $date) {
                if (! in_array(\Carbon\Carbon::parse($date)->dayOfWeekIso, $days, true)) {
                    return false;
                }
            }
        }

        // No clash with another booking they already have.
        $clash = CareBooking::where('professional_id', $pro->id)->whereIn('status', ['matched', 'active'])
            ->whereKeyNot($booking->id)
            ->where('start_date', '<=', $booking->end_date ?? $booking->start_date)
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $booking->start_date))
            ->exists();

        return ! $clash;
    }

    /** Qualified caregivers, best first, each with a short "why you" (spec 11.2). */
    public function candidates(CareBooking $booking): Collection
    {
        $knows = CareBooking::where('patient_id', $booking->patient_id)->whereNotNull('professional_id')
            ->whereKeyNot($booking->id)->pluck('professional_id')->unique()->all();
        $language = mb_strtolower($booking->preferences['language'] ?? '') ?: mb_strtolower($booking->patient?->languages[0] ?? '');
        $weekLoad = CareBooking::whereIn('status', ['matched', 'active'])->where('start_date', '>=', now()->startOfWeek())
            ->selectRaw('professional_id, count(*) as n')->groupBy('professional_id')->pluck('n', 'professional_id');

        return Professional::with(['user', 'credentials', 'availabilityRules'])
            ->where('status', 'active')
            ->get()
            ->filter(fn (Professional $p) => $this->qualifies($p, $booking))
            ->map(function (Professional $p) use ($booking, $knows, $language, $weekLoad) {
                $km = $this->distanceKm($p, $booking);
                $why = [];
                $score = 0;

                if (in_array($p->id, $knows, true)) { $score += 30; $why[] = 'You have cared for this client before'; }
                if ($km !== null) { $score += max(0, 25 * (1 - $km / max(1, $p->max_travel_km))); $why[] = number_format($km, 1) . ' km away'; }
                $score += 15 * (($p->rating_avg ?? 4.0) / 5);
                if ($language && in_array($language, array_map('mb_strtolower', $p->languages ?? []), true)) { $score += 10; $why[] = 'Speaks ' . ucfirst($language); }
                $score += 10 * min(1, $p->visits_count / 100);
                $score += max(0, 10 - 2 * (int) ($weekLoad[$p->id] ?? 0));

                return ['pro' => $p, 'score' => round($score, 1), 'distance_km' => $km, 'why' => implode(' · ', array_slice($why, 0, 2)) ?: 'Matched by CavalCare'];
            })
            ->sortByDesc('score')
            ->values();
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    public function distanceKm(Professional $pro, CareBooking $booking): ?float
    {
        $p = $booking->patient;
        if ($pro->lat === null || $pro->lng === null || ! $p || $p->lat === null || $p->lng === null) {
            return null;
        }

        $r = 6371.0;
        $dLat = deg2rad($p->lat - $pro->lat);
        $dLng = deg2rad($p->lng - $pro->lng);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($pro->lat)) * cos(deg2rad($p->lat)) * sin($dLng / 2) ** 2;

        return round($r * 2 * atan2(sqrt($a), sqrt(1 - $a)), 1);
    }

    /** What a caregiver is paid per visit, snapshotted on the offer (spec 10.4). Rounded to the nearest ₦100. */
    public static function payPerVisitKobo(CareBooking $booking): int
    {
        // Per-day plans: the unit price is one visit. Week/month/flexible plans: the plan price split across its visits.
        $perVisitFamily = in_array($booking->billing_cycle, ['per_day', 'per_night', 'per_24h'], true)
            ? $booking->price_per_unit_kobo
            : intdiv($booking->price_per_unit_kobo, max(1, self::visitsCount($booking)));
        $share = (float) config('cavalcare.default_professional_share_pct', 70);

        return (int) (round($perVisitFamily * $share / 100 / 10000) * 10000);
    }

    public static function visitsCount(CareBooking $booking): int
    {
        return match ($booking->billing_cycle) {
            'per_day', 'per_night', 'per_24h' => $booking->units,
            'per_week'      => 7,
            'per_month'     => 30,
            'per_10_visits' => 10,
            default         => 1,
        };
    }

    /** The privacy-safe offer content: no name, no street address, no family price. */
    private function offerFields(CareBooking $booking, ?Professional $pro): array
    {
        $patient = $booking->patient()->with('conditions')->first();

        return [
            'care_type'          => trim(($booking->careType?->name ?? 'Care') . ' · ' . $booking->plan_name),
            'age_sex'            => $patient?->age_sex,
            'summary'            => $patient?->conditions->pluck('name')->take(2)->join(', ') ?: null,
            'area'               => $patient?->area ?: $patient?->city,
            'distance_km'        => $pro ? $this->distanceKm($pro, $booking) : null,
            'dates'              => $booking->visitDates(),
            'visits_count'       => self::visitsCount($booking),
            'pay_per_visit_kobo' => self::payPerVisitKobo($booking),
            'skills_required'    => $booking->service_level === 'nursing' ? ['Registered nurse'] : [],
        ];
    }
}
