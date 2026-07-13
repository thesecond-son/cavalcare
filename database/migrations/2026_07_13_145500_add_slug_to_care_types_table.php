<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\CareType;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('care_types', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
        });

        // Back-fill slugs for existing rows.
        // Appends the ID to guarantee uniqueness if names are similar.
        CareType::withTrashed()->get()->each(function (CareType $type) {
            $type->slug = Str::slug($type->name) . '-' . $type->id;
            $type->saveQuietly();
        });

        // Now every row has a value, make it non-nullable.
        Schema::table('care_types', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('care_types', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};