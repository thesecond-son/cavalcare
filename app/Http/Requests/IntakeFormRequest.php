<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IntakeFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // ── Booker ──────────────────────────────────────────────
            'booker_details.name'              => ['required', 'string', 'max:255'],
            'booker_details.phone'             => ['required', 'string', 'max:30'],
            'booker_details.state'             => ['required', 'string', 'max:100'],
            'booker_details.address'           => ['required', 'string', 'max:500'],
            'booker_details.emergency_number'  => ['nullable', 'string', 'max:30'],
 
            // ── Patient ─────────────────────────────────────────────
            'patient_details.full_name'          => ['required', 'string', 'max:255'],
            'patient_details.age'                => ['required', 'integer', 'min:1', 'max:120'],
            'patient_details.gender'             => ['required', 'in:Male,Female'],
            'patient_details.mobility_level'     => ['required', 'in:Independent,Assisted,Bedridden'],
            'patient_details.address'            => ['required', 'string', 'max:500'],
            'patient_details.diagnosis'          => ['nullable', 'string', 'max:800'],
            'patient_details.emergency_contact'  => ['nullable', 'string', 'max:30'],
 
            'patient_details.medications'                => ['nullable', 'array'],
            'patient_details.medications.*.medication'   => ['nullable', 'string', 'max:255'],
            'patient_details.daily_tasks'                => ['nullable', 'array'],
            'patient_details.daily_tasks.*.task'         => ['nullable', 'string', 'max:255'],
            'patient_details.allergies'                  => ['nullable', 'array'],
            'patient_details.allergies.*.allergy'        => ['nullable', 'string', 'max:255'],
 
            // ── Care plan / booking ─────────────────────────────────
            'booking_details.care_type_id'   => ['required', 'exists:care_types,id'],
            'booking_details.care_plan_id'   => ['required', 'exists:care_plans,id'],
            'booking_details.start_date'     => ['required', 'date', 'after:today'],
            'booking_details.end_date'       => ['nullable', 'date', 'after:booking_details.start_date'],
        ];
    }
 
    public function messages(): array
    {
        return [
            'booker_details.name.required'          => 'Please enter your full name.',
            'booker_details.phone.required'         => 'Please enter your phone number.',
            'booker_details.state.required'         => 'Please select your state.',
            'booker_details.address.required'       => 'Please enter your home address.',
            'patient_details.full_name.required'    => "Please enter the patient's full name.",
            'patient_details.age.required'          => "Please enter the patient's age.",
            'patient_details.gender.required'       => "Please select the patient's gender.",
            'patient_details.mobility_level.required' => "Please select the patient's mobility level.",
            'patient_details.address.required'      => "Please enter the patient's address.",
            'booking_details.care_type_id.required' => 'Please select a care type.',
            'booking_details.care_plan_id.required' => 'Please select a care plan.',
            'booking_details.start_date.required'   => 'Please select a start date.',
            'booking_details.start_date.after'      => 'The start date must be at least tomorrow.',
        ];
    }
}
