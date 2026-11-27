<?php

namespace Database\Seeders;

use App\Models\Care\Patient;
use App\Models\Care\Professional;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Demo clients and caregivers for local development and staging, using the
 * same people as the app designs (Mama Folake, Nurse Adaeze, Tolu in London).
 * All emails are @example.com and phones use the +234 800 000 test range.
 *
 *   php artisan db:seed --class=DemoCareSeeder
 *
 * Refuses to run in production.
 */
class DemoCareSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->error('DemoCareSeeder never runs in production.');
            return;
        }

        $this->caregivers();
        $this->clients();
    }

    private function user(string $name, string $email, string $type, ?string $phone = null): User
    {
        $user = User::firstOrNew(['email' => $email]);
        $user->fill(['name' => $name, 'phone_e164' => $phone]);
        $user->forceFill(['type' => $type, 'email_verified_at' => now()])->save();

        return $user;
    }

    private function caregivers(): void
    {
        $people = [
            // name, email, phone, title, area, status-shape, years, languages, skills, rating, reviews, visits
            ['Adaeze Okafor', 'adaeze', '+2348000000101', 'rn', 'Lekki Phase 1', 'active', 9, ['English', 'Igbo', 'Yoruba'], ['Wound care', 'Diabetes', 'Post-surgery'], 4.9, 38, 212],
            ['Chiamaka Nwosu', 'chiamaka', '+2348000000102', 'rn', 'Surulere', 'expiring', 6, ['English', 'Igbo'], ['Stroke recovery', 'Catheter care'], 4.7, 21, 140],
            ['Halima Sule', 'halima', '+2348000000103', 'carer', 'Ikeja GRA', 'active', 4, ['English', 'Hausa'], ['Companionship', 'Mobility support'], 4.8, 17, 96],
            ['Emeka Nwosu', 'emeka', '+2348000000104', 'carer', 'Victoria Island', 'review', 2, ['English', 'Igbo', 'Pidgin'], ['Companionship'], null, 0, 0],
            ['Sola Adebayo', 'sola', '+2348000000105', 'rn', 'Yaba', 'review', 3, ['English', 'Yoruba'], ['Elderly care'], null, 0, 0],
            ['Funmi Bakare', 'funmi', '+2348000000106', 'carer', 'Magodo', 'applicant', 1, ['English', 'Yoruba'], [], null, 0, 0],
            ['Ibrahim Musa', 'ibrahim', '+2348000000107', 'carer', 'Gbagada', 'suspended', 5, ['English', 'Hausa'], ['Companionship'], 4.1, 9, 60],
        ];

        foreach ($people as [$name, $slug, $phone, $title, $area, $shape, $years, $langs, $skills, $rating, $reviews, $visits]) {
            $user = $this->user($name, "{$slug}@example.com", User::TYPE_PROFESSIONAL, $phone);

            $pro = Professional::updateOrCreate(['user_id' => $user->id], [
                'title' => $title, 'home_area' => $area, 'years_experience' => $years,
                'languages' => $langs, 'skills' => $skills, 'rating_avg' => $rating,
                'reviews_count' => $reviews, 'visits_count' => $visits, 'max_travel_km' => 15,
                'open_to_extra_shifts' => $shape === 'active',
                'bio' => "{$name} has {$years} " . ($years === 1 ? 'year' : 'years') . ' of home care experience in Lagos.',
            ]);
            $pro->credentials()->delete();
            $pro->availabilityRules()->delete();

            $idType = $title === 'rn' ? 'nmcn' : 'nin';
            $verified = fn (string $type, ?string $validTo = null) => $pro->credentials()->create([
                'type' => $type, 'status' => 'verified', 'number' => strtoupper($type) . '-' . random_int(100000, 999999),
                'valid_to' => $validTo, 'verified_at' => now()->subDays(20),
            ]);

            match ($shape) {
                'active', 'suspended' => [$verified($idType, now()->addYear()->toDateString()), $verified('background'), $verified('training'), $verified('bls', now()->addMonths(8)->toDateString())],
                'expiring' => [
                    $pro->credentials()->create(['type' => $idType, 'status' => 'expiring', 'number' => 'NMCN-' . random_int(100000, 999999), 'valid_to' => now()->addDays(20), 'verified_at' => now()->subYear()]),
                    $verified('background'), $verified('training'),
                ],
                'review' => [
                    $pro->credentials()->create(['type' => $idType, 'status' => 'in_progress', 'number' => strtoupper($idType) . '-' . random_int(100000, 999999), 'file_path' => 'demo/' . $slug . '-id.pdf']),
                    $pro->credentials()->create(['type' => 'background', 'status' => 'in_progress']),
                    $verified('training'),
                ],
                'applicant' => null,
            };

            if (in_array($shape, ['active', 'expiring', 'suspended'], true)) {
                foreach ([1, 2, 3, 4, 5] as $day) {
                    $pro->availabilityRules()->create(['weekday' => $day, 'start_time' => '08:00', 'end_time' => '17:00']);
                }
            }

            $pro->forceFill(['status' => $shape === 'suspended' ? 'suspended' : 'applicant',
                             'suspended_reason' => $shape === 'suspended' ? 'Missed two visits without notice (demo)' : null])->save();
            $pro->syncStatusWithCredentials();
        }
    }

    private function clients(): void
    {
        $families = [
            [
                'owner' => ['Tolu Adeyemi', 'tolu', 'daughter', 'London'],
                'patient' => ['full_name' => 'Folake Adeyemi', 'display_name' => 'Mama Folake', 'address_as' => 'Mama', 'pronoun' => 'she',
                    'date_of_birth' => '1948-03-14', 'sex' => 'female', 'address_line' => '12 Admiralty Way', 'area' => 'Lekki Phase 1',
                    'city' => 'Lagos', 'state' => 'Lagos', 'lat' => 6.4474, 'lng' => 3.4721, 'languages' => ['Yoruba', 'English'],
                    'mobility_level' => 'assisted', 'address_note' => 'Blue gate opposite the church'],
                'allergies' => [['Penicillin', 'Rash and swelling', true], ['Groundnuts', null, false]],
                'conditions' => ['Hypertension', 'Type 2 diabetes'],
                'meds' => [['Amlodipine', '5 mg', ['08:00']], ['Metformin', '500 mg', ['08:00', '20:00']]],
                'contacts' => [['Kunle Adeyemi', 'Son', '+2348000000201', 'Ikoyi']],
                'extra' => [['Bayo Adeyemi', 'bayo', 'viewer', 'son', 'Houston']],
            ],
            [
                'owner' => ['Ngozi Eze', 'ngozi', 'daughter', 'Manchester'],
                'patient' => ['full_name' => 'Emmanuel Eze', 'address_as' => 'Papa', 'pronoun' => 'he', 'date_of_birth' => '1951-07-02',
                    'sex' => 'male', 'address_line' => '4 Bode Thomas Street', 'area' => 'Surulere', 'city' => 'Lagos', 'state' => 'Lagos',
                    'languages' => ['Igbo', 'English'], 'mobility_level' => 'bedridden'],
                'allergies' => [],
                'conditions' => ['Post-stroke (left side weakness)'],
                'meds' => [['Aspirin', '75 mg', ['08:00']], ['Atorvastatin', '20 mg', ['21:00']]],
                'contacts' => [['Chidi Eze', 'Nephew', '+2348000000202', 'Surulere']],
                'extra' => [],
            ],
            [
                'owner' => ['Margaret Akande', 'margaret', 'self', 'Lagos'],
                'patient' => ['full_name' => 'Margaret Akande', 'pronoun' => 'she', 'date_of_birth' => '1956-11-20', 'sex' => 'female',
                    'address_line' => '22 Isaac John Street', 'area' => 'Ikeja GRA', 'city' => 'Lagos', 'state' => 'Lagos',
                    'languages' => ['English'], 'mobility_level' => 'independent'],
                'allergies' => [['Sulfa drugs', 'Hives', false]],
                'conditions' => ['Osteoarthritis'],
                'meds' => [],
                'contacts' => [],
                'extra' => [],
            ],
        ];

        foreach ($families as $f) {
            [$ownerName, $ownerSlug, $relationship, $city] = $f['owner'];
            $owner = $this->user($ownerName, "{$ownerSlug}@example.com", User::TYPE_FAMILY);

            $patient = Patient::firstOrNew(['full_name' => $f['patient']['full_name'], 'source' => 'app']);
            $patient->fill($f['patient'] + ['consent_given_at' => now()->subMonth(), 'consent_by' => $owner->id, 'created_by' => $owner->id])->save();

            $patient->members()->delete();
            $patient->allergies()->delete();
            $patient->conditions()->delete();
            $patient->medications()->delete();
            $patient->emergencyContacts()->delete();

            $patient->members()->create(['user_id' => $owner->id, 'role' => 'owner', 'relationship' => $relationship, 'city' => $city, 'joined_at' => now()->subMonth()]);
            foreach ($f['extra'] as [$name, $slug, $role, $rel, $where]) {
                $u = $this->user($name, "{$slug}@example.com", User::TYPE_FAMILY);
                $patient->members()->create(['user_id' => $u->id, 'role' => $role, 'relationship' => $rel, 'city' => $where, 'joined_at' => now()->subWeeks(2)]);
            }
            foreach ($f['allergies'] as [$name, $reaction, $severe]) {
                $patient->allergies()->create(compact('name', 'reaction', 'severe'));
            }
            foreach ($f['conditions'] as $name) {
                $patient->conditions()->create(['name' => $name]);
            }
            foreach ($f['meds'] as [$name, $dose, $times]) {
                $patient->medications()->create(['name' => $name, 'dose' => $dose, 'schedule' => ['times' => $times]]);
            }
            foreach ($f['contacts'] as [$name, $relation, $phone, $location]) {
                $patient->emergencyContacts()->create(['name' => $name, 'relation' => $relation, 'phone_e164' => $phone, 'location' => $location]);
            }
        }
    }
}
