<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('care_plans', function (Blueprint $table) {
            // Who delivers the plan: a trained caretaker or a licensed nurse.
            // Existing plans were all nurse-delivered, so default to nursing.
            $table->string('service_level', 20)
                  ->default('nursing')
                  ->after('care_type_id')
                  ->index()
                  ->comment('caretaker | nursing');
        });
    }

    public function down(): void
    {
        Schema::table('care_plans', function (Blueprint $table) {
            $table->dropIndex(['service_level']);
            $table->dropColumn('service_level');
        });
    }
};
