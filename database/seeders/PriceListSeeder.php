<?php

namespace Database\Seeders;

use App\Models\CarePlan;
use App\Models\CareType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Applies the Caretaker / Nursing price list to every care type.
 *
 * Safe on live data: existing plans are SOFT-deleted (bookings keep their
 * care_plan_id), care types and add-ons are left untouched.
 *
 *   php artisan db:seed --class=PriceListSeeder
 */
class PriceListSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            foreach (CareType::all() as $type) {
                CarePlan::where('care_type_id', $type->id)->delete(); // soft delete

                $levels = str_contains(strtolower($type->name), 'companionship')
                    ? ['caretaker']
                    : ['caretaker', 'nursing'];

                foreach ($levels as $level) {
                    foreach ($this->plans()[$level] as $plan) {
                        CarePlan::create(array_merge($plan, [
                            'care_type_id'  => $type->id,
                            'service_level' => $level,
                        ]));
                    }
                }
            }
        });
    }

    private function plans(): array
    {
        $day   = '8 am–5 pm OR 9 am–6 pm';
        $flexi = 'Don’t need full-time care for the whole month? Have a %s come 10 times a month, on the days you need them.';

        return [
            'caretaker' => [
                $this->p('Day Care', 15000, 'per_day', 1, 9, "Single day of care, {$day}. A trained caregiver for companionship and daily living support."),
                $this->p('Weekly Day Care', 80000, 'per_week', 7, 9, "Daily day care for one week, {$day}."),
                $this->p('Monthly Day Care', 250000, 'per_month', 30, 9, "Daily day care for one month, {$day}."),
                $this->p('24-Hour Live-in Care', 25000, 'per_24h', 1, 24, 'A caregiver with your loved one round the clock for one full day.'),
                $this->p('Weekly 24/7 Live-in Care', 120000, 'per_week', 7, 24, 'A live-in caregiver round the clock for seven days.'),
                $this->p('Monthly 24/7 Live-in Care', 400000, 'per_month', 30, 24, 'A live-in caregiver round the clock for a full month.'),
                $this->p('Flexible Monthly Plan', 95000, 'per_10_visits', 30, null, sprintf($flexi, 'caregiver'), ['visits_per_month' => 10, 'is_long_term' => true]),
            ],
            'nursing' => [
                $this->p('Day Care', 25000, 'per_day', 1, 9, "Licensed nurse for clinical care at home, {$day}."),
                $this->p('Night Care', 35000, 'per_night', 1, 15, 'Licensed nurse overnight, 5 pm–8 am.'),
                $this->p('Weekly Day Care', 150000, 'per_week', 7, 9, "Nurse day care daily for a full week, {$day}."),
                $this->p('Monthly Day Care', 500000, 'per_month', 30, 9, "Nurse day care daily for a full month, {$day}."),
                $this->p('24-Hour Live-in Care', 50000, 'per_24h', 1, 24, 'A licensed nurse with your loved one round the clock for one full day.'),
                $this->p('Weekly 24/7 Live-in Care', 300000, 'per_week', 7, 24, 'A live-in licensed nurse round the clock for seven days.'),
                $this->p('Monthly 24/7 Live-in Care', 1000000, 'per_month', 30, 24, 'A live-in licensed nurse round the clock for a full month.'),
                $this->p('Flexible Monthly Plan', 170000, 'per_10_visits', 30, null, sprintf($flexi, 'licensed nurse'), ['visits_per_month' => 10, 'is_long_term' => true]),
            ],
        ];
    }

    private function p(string $name, int $price, string $cycle, int $days, ?int $hours, string $desc, array $meta = []): array
    {
        return [
            'name'          => $name,
            'price'         => $price,
            'billing_cycle' => $cycle,
            'description'   => $desc,
            'meta'          => array_filter(array_merge([
                'duration_days'  => $days,
                'daily_hours'    => $hours,
                'max_caregivers' => 1,
            ], $meta), fn ($v) => $v !== null),
        ];
    }
}
