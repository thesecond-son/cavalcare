<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Clients: the person being cared for (the "parent" in the family app)
 * and their health basics. Backend spec, section 4.2. UUID keys so the
 * Flutter models (caval_core Patient, Allergy...) map one to one.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('full_name');
            $table->string('display_name')->nullable();          // "Mama Folake"
            $table->string('address_as', 60)->nullable();        // "Mama"
            $table->string('pronoun', 10)->nullable();           // she | he | they
            $table->date('date_of_birth')->nullable();
            $table->unsignedTinyInteger('age_years')->nullable(); // website intake gives age only
            $table->string('sex', 10)->nullable();               // female | male
            $table->string('phone_e164', 20)->nullable();
            $table->string('address_line', 500)->nullable();
            $table->string('address_note', 255)->nullable();     // "Blue gate opposite the church"
            $table->string('area', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('state', 120)->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('place_id')->nullable();
            $table->json('languages')->nullable();
            $table->string('mobility_level', 20)->nullable();    // independent | assisted | bedridden
            $table->timestamp('consent_given_at')->nullable();
            $table->foreignId('consent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source', 10)->default('app');        // website | app | os
            $table->foreignId('source_booking_id')->nullable()->unique()->constrained('bookings')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['lat', 'lng']);
            $table->index('full_name');
        });

        // The family circle: who can see and manage this patient, and how.
        Schema::create('patient_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role', 12)->default('owner');        // owner | caregiver | viewer
            $table->string('relationship', 30)->nullable();      // daughter, son, spouse...
            $table->string('city', 120)->nullable();             // where the family member lives
            $table->boolean('can_view_documents')->default(true);
            $table->string('invite_status', 10)->default('accepted');
            $table->foreignId('invited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['patient_id', 'user_id']);
        });

        Schema::create('allergies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->string('name');
            $table->string('reaction')->nullable();
            $table->boolean('severe')->default(false);
            $table->timestamps();
        });

        Schema::create('conditions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->string('name');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('medications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->string('name');
            $table->string('dose')->nullable();
            $table->text('instructions')->nullable();
            $table->json('schedule')->nullable();                // {"times": ["08:00"], "with_food": true}
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('relation', 60)->nullable();
            $table->string('phone_e164', 20)->nullable();
            $table->string('phone_raw', 60)->nullable();          // as typed, when it isn't a clean number
            $table->string('location')->nullable();
            $table->timestamps();
        });

        // Which client a website booking was for.
        Schema::table('pre_bookings', function (Blueprint $table) {
            $table->foreignUuid('patient_id')->nullable()->after('user_id')->constrained('patients')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pre_bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('patient_id');
        });
        Schema::dropIfExists('emergency_contacts');
        Schema::dropIfExists('medications');
        Schema::dropIfExists('conditions');
        Schema::dropIfExists('allergies');
        Schema::dropIfExists('patient_members');
        Schema::dropIfExists('patients');
    }
};
