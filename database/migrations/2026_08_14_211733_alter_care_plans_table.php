<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('care_plans', function (Blueprint $table) {
            // Link each plan to its parent care type
            $table->foreignId('care_type_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('care_types')
                  ->nullOnDelete();

            // Human-readable description shown on the booking page
            $table->text('description')->nullable()->after('price');

            // How the price is billed — drives the label shown in the UI
            // e.g. "₦35,000/day", "₦200,000/week", "₦30,000 (flat fee)"
            $table->string('billing_cycle', 30)
                  ->default('per_day')
                  ->after('description')
                  ->comment('per_day | per_night | per_24h | per_week | per_month | flat');
        });
    }

    public function down(): void
    {
        Schema::table('care_plans', function (Blueprint $table) {
            $table->dropForeign(['care_type_id']);
            $table->dropColumn(['care_type_id', 'description', 'billing_cycle']);
        });
    }
};