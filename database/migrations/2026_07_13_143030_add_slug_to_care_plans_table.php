<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\CarePlan;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('care_plans', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
        });

        // Back-fill slugs for any care plans that already exist.
        // Appends the ID to guarantee uniqueness even if names are similar.
        CarePlan::withTrashed()->get()->each(function (CarePlan $plan) {
            $plan->slug = Str::slug($plan->name) . '-' . $plan->id;
            $plan->saveQuietly();
        });

        // Now that every row has a slug we can make the column non-nullable.
        Schema::table('care_plans', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('care_plans', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};