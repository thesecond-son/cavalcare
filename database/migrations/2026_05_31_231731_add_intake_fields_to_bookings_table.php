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
        Schema::table('bookings', function (Blueprint $table) {
            // Secure token for the intake link — never expose the booking ID
            $table->string('token', 64)->unique()->nullable()->after('id');
 
            // Intake form submission timestamp — null means not yet submitted
            $table->timestamp('intake_submitted_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'token',
                'intake_submitted_at',
            ]);
        });
    }
};
