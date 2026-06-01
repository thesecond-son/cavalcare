<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
 
            // Link back to the pre-booking it came from
            $table->foreignId('pre_booking_id')
                  ->constrained('pre_bookings')
                  ->cascadeOnDelete();
 
            // Denormalised for easy access — copied from pre_booking at intake time
            $table->foreignId('care_plan_id')->constrained('care_plans');
            $table->foreignId('care_type_id')->constrained('care_types');
 
            $table->string('status')->default('intake_complete');
 
            // All three JSON blobs from the intake form
            $table->json('booker_details');   // name, phone, state, address, emergency_number
            $table->json('patient_details');  // full_name, age, gender, mobility, diagnosis, meds, tasks, allergies
            $table->json('booking_details');  // care_plan_id, care_type_id, start_date, end_date
 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
