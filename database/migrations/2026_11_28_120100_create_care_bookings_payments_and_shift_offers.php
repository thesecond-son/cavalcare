<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * App bookings, their Paystack payments, and the shift offers that reach
 * caregivers (backend spec 4.5, 4.8, 10, 11). Named care_bookings because
 * "bookings" already holds the website's intake forms. All money in kobo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('care_bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference', 20)->unique();              // CVL-24817
            $table->foreignUuid('patient_id')->constrained('patients');
            $table->foreignId('booked_by')->constrained('users');
            $table->foreignId('care_plan_id')->constrained('care_plans');
            $table->foreignId('care_type_id')->constrained('care_types');
            $table->string('service_level', 20);                    // caretaker | nursing (snapshot)
            $table->string('plan_name');                            // snapshot
            $table->string('billing_cycle', 20);                    // snapshot
            $table->date('start_date');
            $table->unsignedTinyInteger('start_hour')->nullable();  // 0-23, Lagos time
            $table->unsignedSmallInteger('units')->default(1);      // days or nights for per-day plans
            $table->date('end_date')->nullable();
            $table->string('caregiver_choice', 10)->default('match_me'); // match_me | chosen
            $table->foreignUuid('requested_professional_id')->nullable()->constrained('professionals')->nullOnDelete();
            $table->json('preferences')->nullable();                // {"sex": "female", "language": "Yoruba"}
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('price_per_unit_kobo');      // snapshot at booking time
            $table->json('add_ons')->nullable();                    // [{id, name, price_kobo}] snapshot
            $table->unsignedBigInteger('add_ons_total_kobo')->default(0);
            $table->unsignedBigInteger('total_kobo');
            $table->string('status', 20)->default('pending_payment'); // pending_payment|confirmed|matching|matched|active|completed|cancelled
            $table->foreignUuid('professional_id')->nullable()->constrained('professionals')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('matched_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'start_date']);
            $table->index(['patient_id', 'status']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('care_booking_id')->constrained('care_bookings')->cascadeOnDelete();
            $table->foreignId('payer_id')->constrained('users');
            $table->string('provider', 20)->default('paystack');
            $table->string('reference', 60)->unique();
            $table->unsignedBigInteger('amount_kobo');
            $table->string('status', 20)->default('pending');       // pending | success | failed | refunded
            $table->string('channel', 20)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('raw')->nullable();                        // verify response, no card numbers
            $table->timestamps();
        });

        Schema::create('shift_offers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('care_booking_id')->constrained('care_bookings')->cascadeOnDelete();
            $table->foreignUuid('professional_id')->nullable()->constrained('professionals')->nullOnDelete(); // null = open shift
            $table->string('source', 10);                           // assigned | open
            $table->foreignId('matched_by')->nullable()->constrained('users')->nullOnDelete(); // coordinator; null = automatic
            $table->string('why_you')->nullable();
            // Privacy-safe summary: no name, no street address (spec 13.2).
            $table->string('care_type');
            $table->string('age_sex', 40)->nullable();
            $table->string('summary')->nullable();
            $table->string('area')->nullable();
            $table->decimal('distance_km', 6, 1)->nullable();
            $table->json('dates');
            $table->unsignedSmallInteger('visits_count');
            $table->unsignedBigInteger('pay_per_visit_kobo');
            $table->json('skills_required')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('status', 12)->default('open');          // open | accepted | declined | expired | taken | withdrawn
            $table->string('decline_reason')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['professional_id', 'status']);
            $table->index(['source', 'status']);
            $table->index(['care_booking_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_offers');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('care_bookings');
    }
};
