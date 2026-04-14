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
        Schema::create('pre_bookings', function (Blueprint $table) {
            $table->id();

            $table->string('reference')->unique();

            $table->foreignId('care_plan_id')->constrained();
            $table->foreignId('care_type_id')->constrained();

            $table->json('booker_details');

            $table->string('status')->default('pending_payment');
            // pending_payment | paid | converted | cancelled

            $table->json('payment_meta')->nullable(); // receipt, channel, notes

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_bookings');
    }
};
