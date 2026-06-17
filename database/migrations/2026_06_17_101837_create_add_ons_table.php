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
        Schema::create('add_ons', function (Blueprint $table) {
            $table->id();
            $table->string('name');                         // e.g. "Physiotherapy sessions"
            $table->unsignedBigInteger('price');            // in Naira (whole numbers)
            $table->boolean('is_active')->default(true);   // lets you hide add-ons without deleting
            $table->timestamps();
        });
 
        // Pivot table — stores which add-ons were selected per pre-booking
        Schema::create('add_on_pre_booking', function (Blueprint $table) {
            $table->foreignId('pre_booking_id')->constrained('pre_bookings')->cascadeOnDelete();
            $table->foreignId('add_on_id')->constrained('add_ons')->cascadeOnDelete();
            $table->unsignedBigInteger('price_at_booking'); // snapshot price at time of booking
            $table->primary(['pre_booking_id', 'add_on_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('add_on_pre_booking');
        Schema::dropIfExists('add_ons');
    }
};
