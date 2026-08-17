<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CareType;
use App\Models\CarePlan;
use App\Models\AddOn;

class CareSeeder extends Seeder
{
    public function run(): void
    {
        // ── Wipe existing data (safe for a fresh setup) ──────────────────
        CarePlan::withTrashed()->forceDelete();
        CareType::withTrashed()->forceDelete();
        AddOn::query()->delete();

        // ═══════════════════════════════════════════════════════════════
        //  CARE TYPES + THEIR PLANS
        // ═══════════════════════════════════════════════════════════════

        $types = [

            // ── 1. Post-Surgical Recovery ─────────────────────────────
            [
                'name' => 'Post-Surgical Recovery',
                'plans' => [
                    [
                        'name'          => 'Daily Visit',
                        'price'         => 35000,
                        'billing_cycle' => 'per_day',
                        'description'   => 'A qualified nurse visits your home once daily to monitor your recovery, change dressings, administer medications, and provide post-operative care guidance.',
                    ],
                    [
                        'name'          => 'Weekly Package',
                        'price'         => 200000,
                        'billing_cycle' => 'per_week',
                        'description'   => 'Comprehensive weekly care covering daily nurse visits, recovery monitoring, medication management, and wound care throughout the week.',
                    ],
                    [
                        'name'          => 'Monthly Recovery',
                        'price'         => 700000,
                        'billing_cycle' => 'per_month',
                        'description'   => 'Full-month post-surgical support with dedicated daily nursing care, progress tracking, and coordination with your surgeon or hospital team.',
                    ],
                ],
            ],

            // ── 2. Child Care ─────────────────────────────────────────
            [
                'name' => 'Child Care',
                'plans' => [
                    [
                        'name'          => 'Day Care',
                        'price'         => 35000,
                        'billing_cycle' => 'per_day',
                        'description'   => 'Daytime nursing care for your child — covering feeding, medication, health monitoring, and age-appropriate stimulation during working hours.',
                    ],
                    [
                        'name'          => 'Night Care',
                        'price'         => 45000,
                        'billing_cycle' => 'per_night',
                        'description'   => 'Overnight nursing care so you can rest, while a qualified nurse monitors your child through the night and handles any medical needs.',
                    ],
                    [
                        'name'          => '24-Hour Care',
                        'price'         => 65000,
                        'billing_cycle' => 'per_24h',
                        'description'   => 'Round-the-clock dedicated nursing care for your child, covering all hours with no gaps — ideal for children with complex or high-dependency needs.',
                    ],
                ],
            ],

            // ── 3. Elderly Care ───────────────────────────────────────
            [
                'name' => 'Elderly Care',
                'plans' => [
                    [
                        'name'          => 'Day Care',
                        'price'         => 30000,
                        'billing_cycle' => 'per_day',
                        'description'   => 'Daytime in-home care for elderly loved ones — personal hygiene, mobility assistance, medication reminders, companionship, and health monitoring.',
                    ],
                    [
                        'name'          => 'Night Care',
                        'price'         => 40000,
                        'billing_cycle' => 'per_night',
                        'description'   => 'Overnight care ensuring your elderly family member is safe, comfortable, and monitored throughout the night.',
                    ],
                    [
                        'name'          => '24-Hour Live-In Care',
                        'price'         => 60000,
                        'billing_cycle' => 'per_day',
                        'description'   => 'A dedicated live-in caregiver providing constant presence and care around the clock — the highest level of in-home elderly care.',
                    ],
                    [
                        'name'          => 'Weekly Live-In Care',
                        'price'         => 350000,
                        'billing_cycle' => 'per_week',
                        'description'   => 'A full week of continuous live-in nursing care — cost-effective for families planning ahead or transitioning from hospital care.',
                    ],
                    [
                        'name'          => 'Monthly Live-In Care',
                        'price'         => 1200000,
                        'billing_cycle' => 'per_month',
                        'description'   => 'Long-term live-in care for elderly patients requiring ongoing daily support, with the consistency of the same trusted nurse over the month.',
                    ],
                ],
            ],

            // ── 4. Terminal Illness Care ──────────────────────────────
            [
                'name' => 'Terminal Illness Care',
                'plans' => [
                    [
                        'name'          => 'Initial Assessment',
                        'price'         => 30000,
                        'billing_cycle' => 'flat',
                        'description'   => 'A thorough in-home clinical assessment by a senior nurse to understand the patient\'s condition, care needs, pain levels, and create a personalised care plan.',
                    ],
                    [
                        'name'          => 'Day Care',
                        'price'         => 30000,
                        'billing_cycle' => 'per_day',
                        'description'   => 'Compassionate daytime palliative care — pain and symptom management, personal care, emotional support for both patient and family.',
                    ],
                    [
                        'name'          => 'Night Care',
                        'price'         => 40000,
                        'billing_cycle' => 'per_night',
                        'description'   => 'Overnight palliative nursing care providing comfort, symptom monitoring, and peaceful support through the night.',
                    ],
                    [
                        'name'          => '24-Hour Care',
                        'price'         => 60000,
                        'billing_cycle' => 'per_24h',
                        'description'   => 'Continuous 24-hour palliative care at home — ensuring the patient is never alone and receives attentive, dignified care at all times.',
                    ],
                    [
                        'name'          => 'Weekly Care',
                        'price'         => 300000,
                        'billing_cycle' => 'per_week',
                        'description'   => 'A full week of dedicated palliative care with consistent nursing presence, medication management, and family liaison.',
                    ],
                    [
                        'name'          => 'Monthly Care',
                        'price'         => 1000000,
                        'billing_cycle' => 'per_month',
                        'description'   => 'Long-term terminal illness care ensuring the patient receives consistent, high-quality palliative support from a familiar nursing team throughout the month.',
                    ],
                ],
            ],

            // ── 5. Companionship Services ─────────────────────────────
            [
                'name' => 'Companionship Services',
                'plans' => [
                    [
                        'name'          => '6-Hour Visit',
                        'price'         => 20000,
                        'billing_cycle' => 'flat',
                        'description'   => 'A 6-hour companionship visit — conversation, light activities, reading, and emotional support to reduce loneliness and isolation.',
                    ],
                    [
                        'name'          => '12-Hour Visit',
                        'price'         => 30000,
                        'billing_cycle' => 'flat',
                        'description'   => 'A half-day companionship visit including meaningful engagement, light errands, meal assistance, and social stimulation.',
                    ],
                    [
                        'name'          => 'Weekly Companionship',
                        'price'         => 150000,
                        'billing_cycle' => 'per_week',
                        'description'   => 'Regular weekly companionship care — structured visits throughout the week to provide consistent social connection and light personal support.',
                    ],
                    [
                        'name'          => 'Monthly Companionship',
                        'price'         => 550000,
                        'billing_cycle' => 'per_month',
                        'description'   => 'A full month of scheduled companionship visits — ideal for families abroad or elderly individuals living alone who need regular, caring social contact.',
                    ],
                ],
            ],

        ];

        foreach ($types as $typeData) {
            $careType = CareType::create([
                'name' => $typeData['name'],
            ]);

            foreach ($typeData['plans'] as $planData) {
                CarePlan::create([
                    'care_type_id'  => $careType->id,
                    'name'          => $planData['name'],
                    'price'         => $planData['price'],
                    'billing_cycle' => $planData['billing_cycle'],
                    'description'   => $planData['description'],
                ]);
            }
        }

        // ═══════════════════════════════════════════════════════════════
        //  ADD-ON SERVICES
        // ═══════════════════════════════════════════════════════════════

        $addOns = [
            [
                'name'        => 'Wound Dressing (Simple)',
                'price'       => 25000,
                'price_from'  => false,
                'description' => 'Professional cleaning and dressing of simple, non-infected wounds by a qualified nurse using sterile technique.',
            ],
            [
                'name'        => 'Compound Wound Dressing',
                'price'       => 40000,
                'price_from'  => true,
                'description' => 'Management of complex, deep, or infected wounds requiring advanced dressing techniques, irrigation, and packing. Price varies by wound complexity.',
            ],
            [
                'name'        => 'Catheter Change',
                'price'       => 30000,
                'price_from'  => false,
                'description' => 'Safe, sterile urinary catheter replacement by a trained nurse — includes assessment of catheter site and patient comfort.',
            ],
            [
                'name'        => 'NG Tube Care',
                'price'       => 25000,
                'price_from'  => false,
                'description' => 'Nasogastric tube management including position verification, tube feeding administration, and tube care by a skilled nurse.',
            ],
            [
                'name'        => 'PEG Feeding',
                'price'       => 20000,
                'price_from'  => false,
                'description' => 'Percutaneous endoscopic gastrostomy (PEG) tube feeding — administration of nutrition and medication through the feeding tube with proper site care.',
            ],
            [
                'name'        => 'Nebulisation',
                'price'       => 15000,
                'price_from'  => false,
                'description' => 'Administration of inhaled medication via nebuliser for respiratory conditions such as asthma, COPD, or bronchitis.',
            ],
            [
                'name'        => 'Home Laboratory Services',
                'price'       => 30000,
                'price_from'  => false,
                'description' => 'Sample collection at home (blood, urine, swab, etc.) by a trained phlebotomist. Laboratory analysis fees are billed separately by the lab.',
            ],
        ];

        foreach ($addOns as $addOn) {
            AddOn::create(array_merge($addOn, ['is_active' => true]));
        }
    }
}