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
            // Booker's details as a JSON column
            $table->json('booker_details')->nullable();

            // Patient details as a JSON column
            $table->json('patient_details')->nullable();

            // Booking details as a JSON column
            $table->json('booking_details')->nullable();

            // Payment details as a JSON column
            $table->json('payment_details')->nullable();

            $table->string('status')->default('pending'); // pending, confirmed, completed, cancelled
            
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
