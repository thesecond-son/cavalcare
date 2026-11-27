<?php

namespace App\Services\Care;

use App\Models\Booking;
use App\Models\Care\Patient;
use App\Models\Care\PatientMember;
use App\Models\PreBooking;
use App\Support\Phone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Turns a website intake form (bookings.patient_details) into a client:
 * a Patient with allergies, conditions, medicines and an emergency contact.
 *
 * Safe to run more than once: each booking creates at most one patient
 * (patients.source_booking_id is unique). The website booking keeps its
 * original JSON; this only adds the structured copy the apps and OS use.
 */
class WebsiteIntakeImporter
{
    public function import(Booking $booking): Patient
    {
        return DB::transaction(function () use ($booking) {
            $existing = Patient::withTrashed()->where('source_booking_id', $booking->id)->first();
            if ($existing) {
                $this->link($booking, $existing);
                return $existing;
            }

            $p = $booking->patient_details ?? [];

            $patient = Patient::create([
                'full_name'         => trim($p['full_name'] ?? '') ?: 'Name not given',
                'age_years'         => isset($p['age']) && is_numeric($p['age']) ? (int) $p['age'] : null,
                'sex'               => match (Str::lower($p['gender'] ?? '')) { 'female' => 'female', 'male' => 'male', default => null },
                'address_line'      => $p['address'] ?? null,
                'state'             => $booking->booker_details['state'] ?? null,
                'mobility_level'    => isset($p['mobility_level']) ? Str::lower($p['mobility_level']) : null,
                'source'            => 'website',
                'source_booking_id' => $booking->id,
            ]);

            foreach ($p['allergies'] ?? [] as $row) {
                if ($name = trim($row['allergy'] ?? '')) {
                    $patient->allergies()->create(['name' => $name]);
                }
            }
            foreach ($p['medications'] ?? [] as $row) {
                if ($name = trim($row['medication'] ?? '')) {
                    $patient->medications()->create(['name' => $name]);
                }
            }
            if ($diagnosis = trim($p['diagnosis'] ?? '')) {
                $patient->conditions()->create(['name' => $diagnosis]);
            }

            $contact = trim($p['emergency_contact'] ?? '') ?: trim($booking->booker_details['emergency_number'] ?? '');
            if ($contact !== '') {
                $phone = Phone::toE164($contact);
                $patient->emergencyContacts()->create([
                    'phone_e164' => $phone,
                    'phone_raw'  => $phone ? null : Str::limit($contact, 60, ''),
                ]);
            }

            $this->link($booking, $patient);

            return $patient;
        });
    }

    /**
     * Points the website booking at the client and, when the booking belongs
     * to an app account, makes that account the client's Owner.
     */
    public function link(Booking $booking, Patient $patient): void
    {
        $pre = $booking->preBooking ?? PreBooking::find($booking->pre_booking_id);
        if (! $pre) {
            return;
        }

        if ($pre->patient_id !== $patient->id) {
            $pre->forceFill(['patient_id' => $patient->id])->save();
        }

        if ($pre->user_id) {
            PatientMember::firstOrCreate(
                ['patient_id' => $patient->id, 'user_id' => $pre->user_id],
                ['role' => 'owner', 'invite_status' => 'accepted', 'joined_at' => now()]
            );
        }
    }

    /** Imports every website intake that has no client yet. Returns how many were created. */
    public function importAll(): int
    {
        $created = 0;

        Booking::whereNotIn('id', Patient::withTrashed()->whereNotNull('source_booking_id')->select('source_booking_id'))
            ->orderBy('id')
            ->each(function (Booking $booking) use (&$created) {
                $this->import($booking);
                $created++;
            });

        return $created;
    }
}
