<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Baseline for two tables that were created on the live server without a
 * migration. Shapes match the live schema (taken 27 Nov 2026) as it stood
 * before later migrations changed them (bookings.reference, the unique
 * pre_booking_id and deleted_at are added by those later migrations).
 *
 * Only creates a table when it is missing, so existing databases are untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pre_bookings')) {
            Schema::create('pre_bookings', function (Blueprint $table) {
                $table->id();
                $table->string('reference')->unique();
                $table->string('token', 64)->nullable()->unique();
                $table->foreignId('care_plan_id')->constrained('care_plans');
                $table->foreignId('care_type_id')->constrained('care_types');
                $table->json('booker_details');
                $table->string('status')->default('pending');
                $table->json('payment_meta')->nullable();
                $table->string('paystack_reference')->nullable();
                $table->unsignedBigInteger('amount_paid')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pre_booking_id')->constrained('pre_bookings')->cascadeOnDelete();
                $table->foreignId('care_plan_id')->constrained('care_plans');
                $table->foreignId('care_type_id')->constrained('care_types');
                $table->string('status')->default('intake_complete');
                $table->json('booker_details');
                $table->json('patient_details');
                $table->json('booking_details');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Intentionally empty: these tables hold live bookings.
    }
};
