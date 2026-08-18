<?php
// app/Http/Controllers/BookingIntakeController.php
// Reads from PreBooking (via token), writes the completed Booking record.

namespace App\Http\Controllers;

use App\Models\PreBooking;
use App\Models\Booking;
use App\Mail\IntakeConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BookingIntakeController extends Controller
{
    /**
     * GET /intake/{preBooking}
     * {preBooking} is resolved by token via PreBooking::getRouteKeyName().
     */
    public function show(PreBooking $preBooking)
{
    $preBooking->load([
        'carePlan',
        'careType',
        'booking',
    ]);

    // If intake has already been submitted,
    // show the already-submitted page instead of the form.
    if ($preBooking->booking) {
        return view('bookings.intake-already-submitted', [
            'booking' => $preBooking->booking,
            'preBooking' => $preBooking,
        ]);
    }

    return view('bookings.intake-form', [
        'booking' => $preBooking,
        'carePlan' => $preBooking->carePlan,
        'careType' => $preBooking->careType,
        'token' => $preBooking->token,
        'alreadySubmitted' => false,
    ]);
}

    /**
     * POST /intake/{preBooking}
     */
    public function submit(Request $request, PreBooking $preBooking)
    {
        // Prevent double-submission
        if ($preBooking->booking()->exists()) {
            return response()->json(
                ['message' => 'This form has already been submitted.'],
                409
            );
        }

        $data = $request->validate([
            // ── Booker ──────────────────────────────────────────
            'booker_details.name'             => ['required', 'string', 'max:255'],
            'booker_details.phone'            => ['required', 'string', 'max:30'],
            'booker_details.state'            => ['required', 'string', 'max:100'],
            'booker_details.address'          => ['required', 'string', 'max:500'],
            'booker_details.emergency_number' => ['nullable', 'string', 'max:30'],

            // ── Patient ─────────────────────────────────────────
            'patient_details.full_name'           => ['required', 'string', 'max:255'],
            'patient_details.age'                 => ['required', 'integer', 'min:1', 'max:120'],
            'patient_details.gender'              => ['required', 'in:Male,Female'],
            'patient_details.mobility_level'      => ['required', 'in:Independent,Assisted,Bedridden'],
            'patient_details.address'             => ['required', 'string', 'max:500'],
            'patient_details.diagnosis'           => ['nullable', 'string', 'max:800'],
            'patient_details.emergency_contact'   => ['nullable', 'string', 'max:30'],

            'patient_details.medications'                => ['nullable', 'array'],
            'patient_details.medications.*.medication'   => ['nullable', 'string', 'max:255'],
            'patient_details.daily_tasks'                => ['nullable', 'array'],
            'patient_details.daily_tasks.*.task'         => ['nullable', 'string', 'max:255'],
            'patient_details.allergies'                  => ['nullable', 'array'],
            'patient_details.allergies.*.allergy'        => ['nullable', 'string', 'max:255'],

            // ── Booking / care plan ──────────────────────────────
            'booking_details.care_type_id' => ['required', 'exists:care_types,id'],
            'booking_details.care_plan_id' => ['required', 'exists:care_plans,id'],
            'booking_details.start_date'   => ['required', 'date', 'after:today'],
            'booking_details.end_date'     => ['nullable', 'date'],
        ]);

        // Strip blank repeater rows
        foreach (['medications' => 'medication', 'daily_tasks' => 'task', 'allergies' => 'allergy'] as $key => $field) {
            if (isset($data['patient_details'][$key])) {
                $data['patient_details'][$key] = array_values(
                    array_filter($data['patient_details'][$key], fn($row) => !empty($row[$field]))
                );
            }
        }

        // Create the full Booking record
        $booking = Booking::create([
            'pre_booking_id'  => $preBooking->id,
            'care_plan_id'    => $preBooking->care_plan_id,
            'care_type_id'    => $preBooking->care_type_id,
            'status'          => 'intake_complete',
            'booker_details'  => $data['booker_details'],
            'patient_details' => $data['patient_details'],
            'booking_details' => $data['booking_details'],
        ]);

        $preBooking->update([
            'status' => 'converted',
        ]);

        // Send confirmation email — get the client email from the pre-booking
        $email = $preBooking->booker_details['email'] ?? null;
        if ($email) {
            Mail::to($email)->send(new IntakeConfirmation($booking, $preBooking));
        }

        return response()->json([
            'success'  => true,
            'redirect' => route('booking.intake.confirmation', $booking->id),
        ], 200);
    }

    public function confirmation(Booking $booking)
    {
        return view('bookings.intake-confirmation', [
            'booking' => $booking,
        ]);
    }
}