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

            $table->string('token', 64)->unique()->nullable();

            $table->foreignId('care_plan_id')->constrained();
            $table->foreignId('care_type_id')->constrained();

            $table->json('booker_details');

            $table->string('status')->default('pending');
            // pending | paid | failed

            $table->json('payment_meta')->nullable(); // receipt, channel, notes

            $table->string('paystack_reference')->nullable();
                
            $table->unsignedBigInteger('amount_paid')->nullable(); 

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
