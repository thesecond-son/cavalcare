<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The Booking model uses SoftDeletes, but some databases are missing the
 * column (the bookings table has no create migration in this repo).
 * Adds it only when absent, so this is safe on every environment.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bookings') && ! Schema::hasColumn('bookings', 'deleted_at')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        // Intentionally empty: the column may predate this migration.
    }
};
