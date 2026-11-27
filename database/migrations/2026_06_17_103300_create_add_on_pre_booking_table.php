<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Baseline for the add-on pivot, created on the live server without a
 * migration. Shape matches the live schema. Only runs when the table is missing.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('add_on_pre_booking')) {
            return;
        }

        Schema::create('add_on_pre_booking', function (Blueprint $table) {
            $table->foreignId('pre_booking_id')->constrained('pre_bookings')->cascadeOnDelete();
            $table->foreignId('add_on_id')->constrained('add_ons')->cascadeOnDelete();
            $table->unsignedBigInteger('price_at_booking');
            $table->primary(['pre_booking_id', 'add_on_id']);
        });
    }

    public function down(): void
    {
        // Intentionally empty: holds live booking data.
    }
};
