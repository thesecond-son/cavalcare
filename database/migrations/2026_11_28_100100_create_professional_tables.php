<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Caregivers (CavalPro): nurses and carers, their credentials and weekly
 * availability. Backend spec, section 4.4. Replaces the old JSON-only
 * "caregivers" table, which is left in place (empty) until Filament goes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professionals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('title', 10);                         // rn | carer
            $table->text('bio')->nullable();
            $table->unsignedTinyInteger('years_experience')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('sex', 10)->nullable();
            $table->json('languages')->nullable();
            $table->json('skills')->nullable();
            $table->json('specialties')->nullable();
            $table->json('care_types')->nullable();              // care_types.id list
            $table->string('home_area', 120)->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->unsignedSmallInteger('max_travel_km')->default(15);
            $table->decimal('rating_avg', 3, 2)->nullable();
            $table->unsignedInteger('reviews_count')->default(0);
            $table->unsignedInteger('visits_count')->default(0);
            $table->string('employment', 10)->default('caval');  // caval | agency
            $table->uuid('agency_id')->nullable();               // agencies table arrives with payouts
            $table->string('payout_to', 15)->default('professional');
            $table->string('status', 12)->default('applicant');  // applicant | verifying | active | suspended
            $table->string('suspended_reason')->nullable();
            $table->timestamp('status_changed_at')->nullable();
            $table->boolean('open_to_extra_shifts')->default(false);
            $table->boolean('data_saver')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'title']);
            $table->index(['lat', 'lng']);
        });

        Schema::create('credentials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('professional_id')->constrained('professionals')->cascadeOnDelete();
            $table->string('type', 12);                          // nmcn | nin | bls | background | reference | training
            $table->string('status', 12)->default('to_do');      // to_do | in_progress | verified | expiring | expired | rejected
            $table->text('number')->nullable();                  // encrypted (licence / NIN numbers)
            $table->date('valid_to')->nullable();
            $table->string('file_path')->nullable();             // private storage, signed URLs only
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamps();

            $table->unique(['professional_id', 'type']);
            $table->index(['status', 'valid_to']);
        });

        Schema::create('availability_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('professional_id')->constrained('professionals')->cascadeOnDelete();
            $table->unsignedTinyInteger('weekday');              // 1 = Monday ... 7 = Sunday
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();

            $table->index(['professional_id', 'weekday']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availability_rules');
        Schema::dropIfExists('credentials');
        Schema::dropIfExists('professionals');
    }
};
