<?php

namespace App\Services\Care;

use App\Models\AddOn;
use App\Models\CarePlan;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Works out what a booking costs, in kobo. The plan's price is per day,
 * per night or per 24 hours for "unit" plans (times the number of days),
 * and the whole price for week, month and flexible plans.
 */
class BookingPricing
{
    public const MAX_UNITS = 90;

    public static function isPerUnit(CarePlan $plan): bool
    {
        return in_array($plan->billing_cycle, ['per_day', 'per_night', 'per_24h'], true);
    }

    /** @return array{units:int, unit_kobo:int, plan_kobo:int, add_ons:array, add_ons_kobo:int, total_kobo:int, end_date:string} */
    public function quote(CarePlan $plan, string $startDate, ?int $units, array $addOnIds = []): array
    {
        $units = self::isPerUnit($plan) ? max(1, min(self::MAX_UNITS, (int) ($units ?: 1))) : 1;
        $unitKobo = (int) $plan->price * 100;

        /** @var Collection $addOns */
        $addOns = AddOn::where('is_active', true)->whereIn('id', array_unique($addOnIds))->get();
        $addOnRows = $addOns->map(fn (AddOn $a) => ['id' => $a->id, 'name' => $a->name, 'price_kobo' => (int) $a->price * 100])->values()->all();
        $addOnsKobo = array_sum(array_column($addOnRows, 'price_kobo'));

        $start = CarbonImmutable::parse($startDate);
        $end = match ($plan->billing_cycle) {
            'per_week'                    => $start->addDays(6),
            'per_month', 'per_10_visits'  => $start->addDays(29),
            'per_day', 'per_night', 'per_24h' => $start->addDays($units - 1),
            default                       => $start,
        };

        return [
            'units'        => $units,
            'unit_kobo'    => $unitKobo,
            'plan_kobo'    => $unitKobo * $units,
            'add_ons'      => $addOnRows,
            'add_ons_kobo' => $addOnsKobo,
            'total_kobo'   => $unitKobo * $units + $addOnsKobo,
            'end_date'     => $end->toDateString(),
        ];
    }

    public static function naira(int $kobo): string
    {
        return '₦' . number_format(intdiv($kobo, 100));
    }
}
